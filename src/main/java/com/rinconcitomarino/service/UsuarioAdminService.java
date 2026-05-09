package com.rinconcitomarino.service;

import com.rinconcitomarino.model.UsuarioAdmin;
import com.rinconcitomarino.repository.UsuarioAdminRepository;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

@Service
public class UsuarioAdminService {

    private final UsuarioAdminRepository usuarioAdminRepository;

    public UsuarioAdminService(UsuarioAdminRepository usuarioAdminRepository) {
        this.usuarioAdminRepository = usuarioAdminRepository;
    }

    @Transactional(readOnly = true)
    public UsuarioAdmin obtenerPorUsuario(String usuario) {
        return usuarioAdminRepository.findByUsuario(usuario)
                .orElseThrow(() -> new IllegalArgumentException("Usuario no encontrado"));
    }
}
