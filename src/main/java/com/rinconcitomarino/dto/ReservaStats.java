package com.rinconcitomarino.dto;

public record ReservaStats(
        long pendientes,
        long confirmadasHoy,
        long rechazadas,
        long expiradas,
        long personasHoy,
        long reservasMes,
        long opiniones,
        String horarioMasReservado
) {
}
