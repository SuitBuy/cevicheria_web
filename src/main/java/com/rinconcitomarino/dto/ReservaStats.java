package com.rinconcitomarino.dto;

public record ReservaStats(
        long pendientes,
        long reservasHoy,
        long confirmadasHoy,
        long atendidas,
        long noAsistio,
        long canceladasCliente,
        long rechazadas,
        long expiradas,
        long personasHoy,
        long reservasMes,
        long opiniones,
        String horarioMasReservado
) {
}
