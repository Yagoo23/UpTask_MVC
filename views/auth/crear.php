<div class="contenedor crear">
    <?php include_once __DIR__ . '/../templates/nombre-sitio.php' ?>
    <div class="contenedor-sm">
        <p class="descripcion-pagina">Crea tu cuenta en UpTask</p>

        <form action="/" method="post" class="formulario">
            <div class="campo">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" placeholder="Tu Nombre" name="nombre" />
            </div>
            <div class="campo">
                <label for="email">Email</label>
                <input type="email" id="email" placeholder="Tu Email" name="email" />
            </div>
            <div class="campo">
                <label for="password">Contraseña</label>
                <input type="password" id="password" placeholder="Tu Contraseña" name="password" />
            </div>
            <div class="campo">
                <label for="password2">Repite Contraseña</label>
                <input type="password" id="password2" placeholder="Repite tu Contraseña" name="password2" />
            </div>
            <input type="submit" class="boton" value="Iniciar Sesión" />
        </form>
        <div class="acciones">
            <a href="/">¿Ya tienes cuenta? Inicia sesión.</a>
            <a href="/olvide">¿Olvidaste tu contraseña?</a>
        </div>
    </div> <!-- contenedor-sm -->
</div>

<!-- npm start || cd public php -S localhost:4000 -->