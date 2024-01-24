<?php

namespace Controllers;

use Model\Usuario;
use MVC\Router;

class LoginController
{
    public static function login(Router $router)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        }

        //Render a la vista
        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesión'
        ]);
    }

    public static function logout()
    {
        echo "Desde logout";
    }

    public static function crear(Router $router)
    {
        $alertas = [];
        $usuario = new Usuario;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            if (empty($alertas)) {
                $existeUsuario = Usuario::where('email', $usuario->email);

                if ($existeUsuario) {
                    Usuario::setAlerta('error', 'El usuario ya está registrado');
                    $alertas = Usuario::getAlertas();
                } else {
                    //Crear nuevo usuario
                    
                }
            }
        }

        //Render a la vista
        $router->render('auth/crear', [
            'titulo' => 'Crear tu cuenta en UpTask',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function olvide(Router $router)
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        }

        //Muestra la vista 
        $router->render('auth/olvide', [
            'titulo' => 'Olvidé mi contraseña'
        ]);
    }

    public static function reestablecer(Router $router)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        }
        $router->render('auth/reestablecer', [
            'titulo' => 'Reestablece tu contraseña'
        ]);
    }

    public static function mensaje(Router $router)
    {
        $router->render('auth/mensaje', [
            'titulo' => 'Cuenta Creada Correctamente'
        ]);
    }

    public static function confirmar(Router $router)
    {
        $router->render('auth/confirmar', [
            'titulo' => 'Cuenta Confirmada Correctamente'
        ]);
    }
}
