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
        $this->confirmado = $args['confirmado'] ?? '';
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
}