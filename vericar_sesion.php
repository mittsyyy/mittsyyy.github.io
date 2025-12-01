<?php
session_start();

function usuarioLogueado() {
    return isset($_SESSION['usuario_id']);
}

function obtenerUsuario() {
    if (usuarioLogueado()) {
        return [
            'id' => $_SESSION['usuario_id'],
            'nombre' => $_SESSION['usuario_nombre'],
            'email' => $_SESSION['usuario_email'],
            'rol' => $_SESSION['usuario_rol']
        ];
    }
    return null;
}
?>