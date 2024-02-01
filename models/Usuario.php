<?php

namespace Model;


class Usuario extends ActiveRecord
{
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'email', 'password', 'token', 'confirmado'];

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
    }

    //Validar el Login de Usuarios
    public function validarLogin()
    {
        if (!$this->email) {
            self::$alertas['error'][] = 'EL email del usuario es obligatorio';
        }
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'Email no Válido';
        }
        if (!$this->password) {
            self::$alertas['error'][] = 'La contraseña no puede ir vacía';
        }
        return self::$alertas;
    }

    //Validación para nueva cuenta
    public function validarNuevaCuenta()
    {
        if (!$this->nombre) {
            self::$alertas['error'][] = 'EL nombre del usuario es obligatorio';
        }
        if (!$this->email) {
            self::$alertas['error'][] = 'EL email del usuario es obligatorio';
        }
        if (!$this->password) {
            self::$alertas['error'][] = 'La contraseña no puede ir vacía';
        }
        if (strlen($this->password) < 6) {
            self::$alertas['error'][] = 'La contraseña debe contener al menos 6 caracteres';
        }
        if ($this->password !== $this->password2) {
            self::$alertas['error'][] = 'Las contraseñas son diferentes';
        }

        return self::$alertas;
    }

    //Valida un Email
    public function validarEmail()
    {
        if (!$this->email) {
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'Email no Válido';
        }

        return self::$alertas;
    }

    //Valida la contraseña
    public function validarPassword()
    {
        if (!$this->password) {
            self::$alertas['error'][] = 'La contraseña no puede ir vacía.';
        }
        if (strlen($this->password) < 6) {
            self::$alertas['error'][] = 'La contraseña debe contener al menos 6 caracteres.';
        }

        return self::$alertas;
    }

    //Hashea la contraseña
    public function hashPassword()
    {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    //Generar el token
    public function crearToken()
    {
        $this->token = uniqid();
    }
}