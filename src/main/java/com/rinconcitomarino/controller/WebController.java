package com.rinconcitomarino.controller;

import com.rinconcitomarino.model.EstadoReserva;
import com.rinconcitomarino.model.Opinion;
import com.rinconcitomarino.model.Reserva;
import com.rinconcitomarino.service.OpinionService;
import com.rinconcitomarino.service.ReservaService;
import jakarta.validation.Valid;
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

    public WebController(ReservaService reservaService, OpinionService opinionService) {
        this.reservaService = reservaService;
        this.opinionService = opinionService;
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
        model.addAttribute("whatsappNumber", reservaService.getWhatsappNumber());
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
