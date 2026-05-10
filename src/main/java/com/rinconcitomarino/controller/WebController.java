package com.rinconcitomarino.controller;

import com.rinconcitomarino.model.EstadoReserva;
import com.rinconcitomarino.model.Opinion;
import com.rinconcitomarino.model.Reserva;
import com.rinconcitomarino.model.RolUsuario;
import com.rinconcitomarino.model.UsuarioAdmin;
import com.rinconcitomarino.repository.UsuarioAdminRepository;
import com.rinconcitomarino.service.OpinionService;
import com.rinconcitomarino.service.ReservaService;
import jakarta.validation.Valid;
import org.springframework.security.core.Authentication;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.validation.BindingResult;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.ModelAttribute;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;


import java.util.List;

@Controller
public class WebController {

    private final ReservaService reservaService;
    private final OpinionService opinionService;
    private final UsuarioAdminRepository usuarioAdminRepository;
    private final PasswordEncoder passwordEncoder;

    public WebController(
            ReservaService reservaService,
            OpinionService opinionService,
            UsuarioAdminRepository usuarioAdminRepository,
            PasswordEncoder passwordEncoder
    ) {
        this.reservaService = reservaService;
        this.opinionService = opinionService;
        this.usuarioAdminRepository = usuarioAdminRepository;
        this.passwordEncoder = passwordEncoder;
    }

    @GetMapping("/")
    public String index(@RequestParam(required = false) String opinion, Model model) {
        cargarInicio(model);
        model.addAttribute("opinionOk", "ok".equals(opinion));
        return "index";
    }

    @PostMapping("/opiniones")
    public String guardarOpinion(
            @Valid @ModelAttribute("opinion") Opinion opinion,
            BindingResult bindingResult,
            Model model,
            RedirectAttributes redirectAttributes
    ) {
        if (bindingResult.hasErrors()) {
            cargarInicio(model);
            model.addAttribute("opinionError", true);
            return "index";
        }

        opinionService.guardar(opinion);
        redirectAttributes.addAttribute("opinion", "ok");
        return "redirect:/";
    }

    @GetMapping("/reservas")
    public String reservas(Model model) {
        if (!model.containsAttribute("reserva")) {
            model.addAttribute("reserva", new Reserva());
        }
        return "reservas";
    }

    @PostMapping("/reservas")
    public String guardarReserva(
            @Valid @ModelAttribute("reserva") Reserva reserva,
            BindingResult bindingResult,
            Model model,
            RedirectAttributes redirectAttributes
    ) {
        if (bindingResult.hasErrors()) {
            model.addAttribute("validationError", true);
            return "reservas";
        }

        Reserva creada = reservaService.crearReserva(reserva);
        redirectAttributes.addFlashAttribute("reservaOk", true);
        redirectAttributes.addFlashAttribute("whatsappUrl", reservaService.generarWhatsappUrl(creada));
        return "redirect:/reservas";
    }

    @GetMapping("/login")
    public String login(@RequestParam(required = false) String error,
                        @RequestParam(required = false) String expired,
                        Model model) {
        model.addAttribute("loginRequest", new com.rinconcitomarino.dto.LoginRequest());
        model.addAttribute("loginError", error != null);
        model.addAttribute("sessionExpired", expired != null);
        return "login";
    }

    @GetMapping("/admin")
    public String admin(@RequestParam(required = false) String q, Model model) {
        reservaService.expirarPendientesVencidas();
        model.addAttribute("view", "reservas");
        model.addAttribute("q", q);
        model.addAttribute("reservas", reservaService.listarReservas(q));
        model.addAttribute("stats", reservaService.calcularStats());
        model.addAttribute("estados", EstadoReserva.values());
        return "admin";
    }

    @PostMapping("/admin/reservas/{id}/estado")
    public String cambiarEstadoReserva(@PathVariable Long id, @RequestParam EstadoReserva estado) {
        reservaService.cambiarEstado(id, estado);
        return "redirect:/admin";
    }

    @PostMapping("/admin/reservas/{id}/eliminar")
    public String eliminarReserva(@PathVariable Long id) {
        reservaService.eliminar(id);
        return "redirect:/admin";
    }

    @GetMapping("/admin/opiniones")
    public String adminOpiniones(@RequestParam(required = false) String q, Model model) {
        model.addAttribute("view", "opiniones");
        model.addAttribute("q", q);
        model.addAttribute("opiniones", opinionService.listar(q));
        model.addAttribute("stats", reservaService.calcularStats());
        return "admin";
    }

    @GetMapping("/admin/usuarios")
    public String adminUsuarios(Model model, Authentication authentication) {
        model.addAttribute("view", "usuarios");
        model.addAttribute("usuarios", usuarioAdminRepository.findAll());
        model.addAttribute("roles", RolUsuario.values());
        model.addAttribute("currentUser", authentication == null ? "" : authentication.getName());
        model.addAttribute("stats", reservaService.calcularStats());
        return "admin";
    }

    @PostMapping("/admin/usuarios")
    public String crearUsuario(
            @RequestParam String usuario,
            @RequestParam String password,
            @RequestParam RolUsuario rol,
            RedirectAttributes redirectAttributes
    ) {
        String usuarioLimpio = usuario == null ? "" : usuario.trim();
        if (usuarioLimpio.length() < 3 || password == null || password.length() < 8) {
            redirectAttributes.addFlashAttribute("userError", "Usuario minimo 3 caracteres y contrasena minimo 8 caracteres.");
            return "redirect:/admin/usuarios";
        }
        if (usuarioAdminRepository.findByUsuario(usuarioLimpio).isPresent()) {
            redirectAttributes.addFlashAttribute("userError", "Ese usuario ya existe.");
            return "redirect:/admin/usuarios";
        }

        UsuarioAdmin nuevoUsuario = new UsuarioAdmin();
        nuevoUsuario.setUsuario(usuarioLimpio);
        nuevoUsuario.setPassword(passwordEncoder.encode(password));
        nuevoUsuario.setRol(rol);
        usuarioAdminRepository.save(nuevoUsuario);
        redirectAttributes.addFlashAttribute("userOk", "Usuario creado correctamente.");
        return "redirect:/admin/usuarios";
    }

    @PostMapping("/admin/usuarios/{id}/eliminar")
    public String eliminarUsuario(
            @PathVariable Long id,
            Authentication authentication,
            RedirectAttributes redirectAttributes
    ) {
        UsuarioAdmin usuario = usuarioAdminRepository.findById(id)
                .orElseThrow(() -> new IllegalArgumentException("Usuario no encontrado: " + id));
        String currentUser = authentication == null ? "" : authentication.getName();
        if (usuario.getUsuario().equalsIgnoreCase(currentUser)) {
            redirectAttributes.addFlashAttribute("userError", "No puedes eliminar tu propio usuario.");
            return "redirect:/admin/usuarios";
        }
        usuarioAdminRepository.delete(usuario);
        redirectAttributes.addFlashAttribute("userOk", "Usuario eliminado correctamente.");
        return "redirect:/admin/usuarios";
    }

    @PostMapping("/admin/opiniones/{id}/eliminar")
    public String eliminarOpinion(@PathVariable Long id) {
        opinionService.eliminar(id);
        return "redirect:/admin/opiniones";
    }

    @GetMapping("/carta")
    public String carta() {
        return "redirect:/assets/carta.pdf";
    }

    private void cargarInicio(Model model) {
        if (!model.containsAttribute("opinion")) {
            model.addAttribute("opinion", new Opinion());
        }
        model.addAttribute("platos", List.of(
                new PlatoView("Ceviche Clasico", "Pescado fresco, limon, aji limo y cebolla morada.", "/assets/ceviche.jpg"),
                new PlatoView("Chicharron de Pescado", "Crujiente por fuera, jugoso por dentro y perfecto para compartir.", "/assets/chicharron.jpg"),
                new PlatoView("Causa Marina", "Papa amarilla, crema suave y relleno inspirado en el mar peruano.", "/assets/causa.jpg")
        ));
    }

    public record PlatoView(String nombre, String descripcion, String imagen) {
    }
}
