package com.rinconcitomarino.service;

import com.rinconcitomarino.dto.ReservaStats;
import com.rinconcitomarino.model.EstadoReserva;
import com.rinconcitomarino.model.Reserva;
import com.rinconcitomarino.repository.OpinionRepository;
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
    private final String whatsappNumber;

    public ReservaService(
            ReservaRepository reservaRepository,
            OpinionRepository opinionRepository,
            @Value("${app.restaurant.whatsapp-number:}") String whatsappNumber
    ) {
        this.reservaRepository = reservaRepository;
        this.opinionRepository = opinionRepository;
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
        return listarReservas(busqueda, null, null);
    }

    @Transactional(readOnly = true)
    public List<Reserva> listarReservas(String busqueda, EstadoReserva estado, LocalDate fecha) {
        List<Reserva> reservas = reservaRepository.findAll(specBusqueda(busqueda));
        if (estado != null) {
            reservas = reservas.stream()
                    .filter(reserva -> reserva.getEstado() == estado)
                    .toList();
        }
        if (fecha != null) {
            reservas = reservas.stream()
                    .filter(reserva -> fecha.equals(reserva.getFecha()))
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
        Reserva reserva = obtenerPorId(id);
        reserva.setEstado(estado);
        return reservaRepository.save(reserva);
    }

    @Transactional
    public void eliminar(Long id) {
        if (!reservaRepository.existsById(id)) {
            throw new IllegalArgumentException("Reserva no encontrada: " + id);
        }
        reservaRepository.deleteById(id);
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
                reservaRepository.countByFechaAndEstado(hoy, EstadoReserva.CONFIRMADO),
                reservaRepository.countByEstado(EstadoReserva.RECHAZADO),
                reservaRepository.countByEstado(EstadoReserva.EXPIRADO),
                personasHoy,
                reservasMes,
                opinionRepository.count(),
                horarioMasReservado
        );
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
