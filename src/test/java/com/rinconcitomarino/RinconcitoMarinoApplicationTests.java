package com.rinconcitomarino;

import com.rinconcitomarino.model.Reserva;
import jakarta.validation.ConstraintViolation;
import jakarta.validation.Validator;
import org.junit.jupiter.api.Test;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.test.context.SpringBootTest;
import org.springframework.test.context.ActiveProfiles;

import java.time.LocalDate;
import java.util.Set;

import static org.assertj.core.api.Assertions.assertThat;

@SpringBootTest(properties = {
        "spring.datasource.url=jdbc:h2:mem:rm_test;MODE=MySQL;DB_CLOSE_DELAY=-1;DATABASE_TO_LOWER=TRUE",
        "spring.datasource.driver-class-name=org.h2.Driver",
        "spring.datasource.username=sa",
        "spring.datasource.password=",
        "spring.jpa.hibernate.ddl-auto=create-drop",
        "spring.jpa.database-platform=org.hibernate.dialect.H2Dialect",
        "app.jwt.secret=test-secret-change-me-rinconcito-marino-test-2026"
})
@ActiveProfiles("test")
class RinconcitoMarinoApplicationTests {

    @Autowired
    private Validator validator;

    @Test
    void contextLoads() {
    }

    @Test
    void reservaInvalidaDebeReportarDniCorreoYEdad() {
        Reserva reserva = new Reserva();
        reserva.setNombres("Ana");
        reserva.setApellidos("Mar");
        reserva.setDni("123");
        reserva.setEdad(15);
        reserva.setEmail("correo-invalido");
        reserva.setTelefono("999999999");
        reserva.setPersonas(2);
        reserva.setFecha(LocalDate.now().plusDays(1));
        reserva.setHora("12:00 PM");

        Set<ConstraintViolation<Reserva>> violations = validator.validate(reserva);

        assertThat(violations)
                .extracting(violation -> violation.getPropertyPath().toString())
                .contains("dni", "edad", "email");
    }
}
