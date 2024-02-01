<?php


namespace Controllers;

use Model\Usuario;
use MVC\Router;
use Classes\Email;

class LoginController
{
    public static function login(Router $router)
    {
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);

            $alertas = $usuario->validarLogin();

            if (empty($alertas)) {
                //Verificar que el usuario existe
                $usuario = Usuario::where('email', $usuario->email);

                if (!$usuario || !$usuario->confirmado) {
                    Usuario::setAlerta('error', 'El usuario no existe o no está confirmado.');
                } else {
                    //El usuario existe
                    if (password_verify($_POST['password'], $usuario->password)) {
                        //Iniciar la sesión del usuaruo
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        //Redireccionar
                        header('Location: /dashboard');

                    } else {
                        Usuario::setAlerta('error', 'Contraseña incorrecta.');
                    }
                
                }
                
            }
        }

        $alertas = Usuario::getAlertas();

        //Render a la vista
        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesión',
            'alertas' => $alertas
        ]);
    }

    public static function logout()
    {
        session_start();
        $_SESSION = [];
        header('Location: /');
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
                    //Hashear la contraseña
                    $usuario->hashPassword();

                    //Eliminar password2
                    unset($usuario->password2);

                    //Generar el token
                    $usuario->crearToken();

                    //Crear nuevo usuario
                    $resultado = $usuario->guardar();

                    //Enviar email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();
                    

                    if ($resultado) {
                        header('Location: /mensaje');
                    }

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
        $alertas = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if (empty($alertas)) {
                //Buscar el usuario
                $usuario = Usuario::where('email', $usuario->email);
            }
            if($usuario && $usuario->confirmado){
                //Generar un nuevo token
                $usuario->crearToken();
                unset($usuario->password2);

                //Actualizar el usuario
                $usuario->guardar();

                //Enviar el Email
                $email = new Email($usuario->email,$usuario->nombre,$usuario->token);
                $email->enviarInstrucciones();


                //Imprimir la alerta
                Usuario::setAlerta('exito', 'Hemos enviado las instrucciones a tu email.');
            } else {
                Usuario::setAlerta('error', 'El Usuario no existe o no está confirmado.');
            }
        }

        $alertas = Usuario::getAlertas();

        //Muestra la vista 
        $router->render('auth/olvide', [
            'titulo' => 'Olvidé mi contraseña',
            'alertas' => $alertas
        ]);
    }

    public static function reestablecer(Router $router)
    {
        $token = s($_GET['token']);
        $mostrar = true;

        if (!$token) {
            header('Location: /');
        }

        //Identificar el usuario con este token
        $usuario = Usuario::where('token', $token);
        
        if(empty($usuario)){
            Usuario::setAlerta('error', 'Token no válido.');
            $mostrar = false;
        }


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //Añadir la nueva contraseña
            $usuario->sincronizar($_POST);

            //Validar la contraseña
            $alertas = $usuario->validarPassword();

            if (empty($alertas)) {
                //Hashear la contraseña
                $usuario->hashPassword();
                //unset($usuario->password2);

                //Eliminar el token
                $usuario->token = null;

                //Guardar el usuario en la BD
                $resultado = $usuario->guardar();

                //Redireccionar
                if ($resultado) {
                    header('Location: /');
                }
            }

        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/reestablecer', [
            'titulo' => 'Reestablece tu contraseña',
            'alertas' => $alertas,
            'mostrar' => $mostrar
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
        $token = s($_GET['token']);

        if (!$token) {
            header('Location: /');
        }

        //Encontrar al usuario con ese token
        $usuario = Usuario::where('token', $token);

        if (empty($usuario)) {
            //No se encontró usuario con ese token
            Usuario::setAlerta('error', 'Token No Válido');
        } else {
            //Confirmar cuenta
            $usuario->confirmado = 1;
            $usuario->token = null;
            unset($usuario->password2);

            //Guardar en la Base de Datos
            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta Comprobada Correctamente');
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/confirmar', [
            'titulo' => 'Cuenta Confirmada Correctamente',
            'alertas' =>$alertas
        ]);
    }
}
