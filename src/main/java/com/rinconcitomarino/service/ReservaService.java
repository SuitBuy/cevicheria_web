package com.rinconcitomarino.service;

import com.rinconcitomarino.dto.ReservaStats;
import com.rinconcitomarino.model.EstadoReserva;
import com.rinconcitomarino.model.Reserva;
import com.rinconcitomarino.model.ReservaHistorial;
import com.rinconcitomarino.repository.OpinionRepository;
import com.rinconcitomarino.repository.ReservaHistorialRepository;
import com.rinconcitomarino.repository.ReservaRepository;
import jakarta.persistence.criteria.Predicate;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.data.jpa.domain.Specification;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;


import java.net.URLEncoder;
import java.nio.charset.StandardCharsets;
import java.time.LocalDate;
import java.time.LocalDateTime;
import java.time.ZoneId;
import java.time.YearMonth;
import java.util.Comparator;
import java.util.List;
import java.util.Locale;
import java.util.Map;
import java.util.stream.Collectors;

@Service

public class ReservaService {

    private static final ZoneId LIMA_ZONE = ZoneId.of("America/Lima");

    private final ReservaRepository reservaRepository;
    private final OpinionRepository opinionRepository;
    private final ReservaHistorialRepository reservaHistorialRepository;
    private final String whatsappNumber;

    public ReservaService(
            ReservaRepository reservaRepository,
            OpinionRepository opinionRepository,
            ReservaHistorialRepository reservaHistorialRepository,
            @Value("${app.restaurant.whatsapp-number:}") String whatsappNumber
    ) {
        this.reservaRepository = reservaRepository;
        this.opinionRepository = opinionRepository;
        this.reservaHistorialRepository = reservaHistorialRepository;
        this.whatsappNumber = whatsappNumber;
    }

    @Transactional
    public Reserva crearReserva(Reserva reserva) {
        reserva.setId(null);
        reserva.setEstado(EstadoReserva.PENDIENTE);
        return reservaRepository.save(reserva);
    }

    @Transactional(readOnly = true)
    public Reserva obtenerPorId(Long id) {
        return reservaRepository.findById(id)
                .orElseThrow(() -> new IllegalArgumentException("Reserva no encontrada: " + id));
    }

    @Transactional(readOnly = true)
    public List<Reserva> listarReservas(String busqueda) {
        return listarReservas(busqueda, null, null, null, null, null);
    }

    @Transactional(readOnly = true)
    public List<Reserva> listarReservas(String busqueda, EstadoReserva estado, LocalDate fecha) {
        return listarReservas(busqueda, estado, fecha, fecha, null, null);
    }

    @Transactional(readOnly = true)
    public List<Reserva> listarReservas(
            String busqueda,
            EstadoReserva estado,
            LocalDate fechaDesde,
            LocalDate fechaHasta,
            Integer personas,
            String hora
    ) {
        List<Reserva> reservas = reservaRepository.findAll(specBusqueda(busqueda));
        if (estado != null) {
            reservas = reservas.stream()
                    .filter(reserva -> reserva.getEstado() == estado)
                    .toList();
        }
        if (fechaDesde != null) {
            reservas = reservas.stream()
                    .filter(reserva -> reserva.getFecha() != null && !reserva.getFecha().isBefore(fechaDesde))
                    .toList();
        }
        if (fechaHasta != null) {
            reservas = reservas.stream()
                    .filter(reserva -> reserva.getFecha() != null && !reserva.getFecha().isAfter(fechaHasta))
                    .toList();
        }
        if (personas != null) {
            reservas = reservas.stream()
                    .filter(reserva -> personas.equals(reserva.getPersonas()))
                    .toList();
        }
        if (hora != null && !hora.isBlank()) {
            String horaLimpia = hora.trim();
            reservas = reservas.stream()
                    .filter(reserva -> horaLimpia.equals(reserva.getHora()))
                    .toList();
        }
        ordenarReservas(reservas);
        return reservas;
    }

    @Transactional(readOnly = true)
    public List<Reserva> listarReservasHoy() {
        LocalDate hoy = LocalDate.now(LIMA_ZONE);
        List<Reserva> reservas = reservaRepository.findAll().stream()
                .filter(reserva -> hoy.equals(reserva.getFecha()))
                .toList();
        return ordenarReservasPorHora(reservas);
    }

    @Transactional(readOnly = true)
    public List<Reserva> listarPendientesUrgentes() {
        LocalDate hoy = LocalDate.now(LIMA_ZONE);
        LocalDate manana = hoy.plusDays(1);
        List<Reserva> reservas = reservaRepository.findAll().stream()
                .filter(reserva -> reserva.getEstado() == EstadoReserva.PENDIENTE)
                .filter(reserva -> reserva.getFecha() != null && !reserva.getFecha().isBefore(hoy) && !reserva.getFecha().isAfter(manana))
                .toList();
        return ordenarReservasPorHora(reservas);
    }

    @Transactional
    public Reserva cambiarEstado(Long id, EstadoReserva estado) {
        return cambiarEstado(id, estado, "api");
    }

    @Transactional
    public Reserva cambiarEstado(Long id, EstadoReserva estado, String usuario) {
        Reserva reserva = obtenerPorId(id);
        EstadoReserva anterior = reserva.getEstado();
        reserva.setEstado(estado);
        Reserva guardada = reservaRepository.save(reserva);
        registrarHistorial(guardada.getId(), usuario, "Cambio de estado", anterior.getEtiqueta() + " -> " + estado.getEtiqueta());
        return guardada;
    }

    @Transactional
    public void eliminar(Long id) {
        eliminar(id, "api");
    }

    @Transactional
    public void eliminar(Long id, String usuario) {
        Reserva reserva = obtenerPorId(id);
        registrarHistorial(id, usuario, "Eliminacion", "Reserva de " + reserva.getNombreCompleto());
        reservaRepository.delete(reserva);
    }

    @Transactional
    public int expirarPendientesVencidas() {
        LocalDateTime limite = LocalDateTime.now(LIMA_ZONE).minusMinutes(30);
        List<Reserva> vencidas = reservaRepository.findByEstadoAndFechaRegistroBefore(EstadoReserva.PENDIENTE, limite);
        vencidas.forEach(reserva -> reserva.setEstado(EstadoReserva.EXPIRADO));
        reservaRepository.saveAll(vencidas);
        return vencidas.size();
    }

    @Transactional(readOnly = true)
    public ReservaStats calcularStats() {
        LocalDate hoy = LocalDate.now(LIMA_ZONE);
        YearMonth mesActual = YearMonth.from(hoy);
        List<Reserva> reservas = reservaRepository.findAll();
        long personasHoy = reservas.stream()
                .filter(reserva -> hoy.equals(reserva.getFecha()))
                .filter(reserva -> reserva.getEstado() == EstadoReserva.CONFIRMADO || reserva.getEstado() == EstadoReserva.PENDIENTE)
                .mapToLong(reserva -> reserva.getPersonas() == null ? 0 : reserva.getPersonas())
                .sum();
        long reservasMes = reservas.stream()
                .filter(reserva -> reserva.getFecha() != null && YearMonth.from(reserva.getFecha()).equals(mesActual))
                .count();
        String horarioMasReservado = reservas.stream()
                .filter(reserva -> hoy.equals(reserva.getFecha()))
                .filter(reserva -> reserva.getHora() != null && !reserva.getHora().isBlank())
                .collect(Collectors.groupingBy(Reserva::getHora, Collectors.counting()))
                .entrySet()
                .stream()
                .max(Map.Entry.comparingByValue())
                .map(Map.Entry::getKey)
                .orElse("Sin reservas");
        return new ReservaStats(
                reservaRepository.countByEstado(EstadoReserva.PENDIENTE),
                reservas.stream().filter(reserva -> hoy.equals(reserva.getFecha())).count(),
                reservaRepository.countByFechaAndEstado(hoy, EstadoReserva.CONFIRMADO),
                reservaRepository.countByEstado(EstadoReserva.ATENDIDO),
                reservaRepository.countByEstado(EstadoReserva.NO_ASISTIO),
                reservaRepository.countByEstado(EstadoReserva.CANCELADO_CLIENTE),
                reservaRepository.countByEstado(EstadoReserva.RECHAZADO),
                reservaRepository.countByEstado(EstadoReserva.EXPIRADO),
                personasHoy,
                reservasMes,
                opinionRepository.count(),
                horarioMasReservado
        );
    }

    @Transactional(readOnly = true)
    public List<ReservaHistorial> listarHistorialReciente() {
        return reservaHistorialRepository.findTop20ByOrderByFechaDesc();
    }

    public String generarWhatsappUrl(Reserva reserva) {
        if (whatsappNumber == null || whatsappNumber.isBlank()) {
            return null;
        }
        String mensaje = URLEncoder.encode(reserva.getWhatsappMensaje(), StandardCharsets.UTF_8);
        return "https://wa.me/" + whatsappNumber.trim() + "?text=" + mensaje;
    }

    private Specification<Reserva> specBusqueda(String busqueda) {
        if (busqueda == null || busqueda.isBlank()) {
            return (root, query, cb) -> cb.conjunction();
        }
        String termino = "%" + busqueda.trim().toLowerCase(Locale.ROOT) + "%";
        return (root, query, cb) -> {
            Predicate nombres = cb.like(cb.lower(root.get("nombres")), termino);
            Predicate apellidos = cb.like(cb.lower(root.get("apellidos")), termino);
            Predicate dni = cb.like(cb.lower(root.get("dni")), termino);
            Predicate telefono = cb.like(cb.lower(root.get("telefono")), termino);
            Predicate email = cb.like(cb.lower(root.get("email")), termino);
            return cb.or(nombres, apellidos, dni, telefono, email);
        };
    }

    private void registrarHistorial(Long reservaId, String usuario, String accion, String detalle) {
        ReservaHistorial historial = new ReservaHistorial();
        historial.setReservaId(reservaId);
        historial.setUsuario(usuario == null || usuario.isBlank() ? "sistema" : usuario);
        historial.setAccion(accion);
        historial.setDetalle(detalle);
        historial.setFecha(LocalDateTime.now(LIMA_ZONE));
        reservaHistorialRepository.save(historial);
    }

    private void ordenarReservas(List<Reserva> reservas) {
        reservas.sort(Comparator
                .comparingInt((Reserva reserva) -> reserva.getEstado().getOrden())
                .thenComparing(Reserva::getFecha, Comparator.nullsLast(Comparator.reverseOrder()))
                .thenComparing(Reserva::getHora, Comparator.nullsLast(String::compareTo)));
    }

    private List<Reserva> ordenarReservasPorHora(List<Reserva> reservas) {
        return reservas.stream()
                .sorted(Comparator
                        .comparing(Reserva::getFecha, Comparator.nullsLast(Comparator.naturalOrder()))
                        .thenComparing(Reserva::getHora, Comparator.nullsLast(String::compareTo)))
                .toList();
    }
}
