<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <link rel="stylesheet" href="../../assets/css/style.css">
    

    <title>Login y Registro - FACILISIMO</title>
    
</head>
<body>
    
    
    
    <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid_token'): ?>
        <div class="alert alert-danger">❌ El enlace de verificación no es válido o ha expirado.</div>
    <?php endif; ?>
    <?php
    // Errores relacionados con el formulario de recuperación
    $erroresForgot = [ 'email_not_found', ];
    $erroresReset = [ 'reset_error', 'password_mismatch', 'update_failed' ];
    $mostrarRegistro = false;
    if (isset($_GET['message']) && $_GET['message'] === 'registered') {
        $mostrarRegistro = true;
    }
    
    $mostrarRecuperar = false;
    if (
        (isset($_GET['action']) && $_GET['action'] === 'forgot') ||
        (isset($_GET['error']) && in_array($_GET['error'], $erroresForgot))
    ) {
        $mostrarRecuperar = true;
    }

    $mostrarReset = false;
    if (
        (isset($_GET['action']) && $_GET['action'] === 'reset' && isset($_GET['token'])) ||
        (isset($_GET['error']) && in_array($_GET['error'], $erroresReset))
    ) {
        $mostrarReset = true;
    }
    ?>
    <div class="container">
        <div class="form-container <?= $mostrarRecuperar ? '' : 'hidden' ?>" id="forgotForm">
            <h2>Recuperar Contraseña</h2>
            <form action="../index.php?action=send_reset_link" method="post">
                <input type="email" name="correo" placeholder="Ingresa tu correo" required>
                <button type="submit">Enviar enlace de recuperación</button>
            </form>
            
            <a href="index.php" style="font-size: 0.9em; display: block; margin-top: 10px;">Volver a iniciar sesión</a>
            
        </div>
        <?php if (isset($_GET['action']) && $_GET['action'] === 'reset' && isset($_GET['token'])): ?>
            <div class="form-container <?= $mostrarReset ? '' : 'hidden' ?>" id="resetForm">
                <h2>Restablecer Contraseña</h2>
                <form action="../index.php?action=reset_password" method="post">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token']) ?>">
                    <input type="password" name="new_password" placeholder="Nueva contraseña" required>
                    <input type="password" name="confirm_password" placeholder="Confirmar contraseña" required>
                    <button type="submit">Guardar nueva contraseña</button>
                </form>
                <a href="index.php" style="font-size: 0.9em; display: block; margin-top: 10px;">Volver</a>
                <?php
                if (isset($_GET['error']) && $_GET['error'] === 'empty' ) {
                   echo '<p style="color:red;">Error: Todos los campos son obligatorios.</p>';
                }
             if (isset($_GET['error']) && $_GET['error'] === 'password_mismatch' ) {
                   echo '<p style="color:red;">Error: Las contraseñas no coinciden.</p>';
                }
                if (isset($_GET['error']) && $_GET['error'] === 'email_not_found' ) {
                   echo '<p style="color:red;">Error: El correo ingresado no está registrado.</p>';
                }
                ?>
                <?php if (isset($_GET['error'])): ?>
        <?php
        $errorMessages = [
            'invalid_token' => 'El enlace de recuperación no es válido o ha expirado.',
            'reset_error' => 'No se pudo generar el enlace de recuperación.',
            'update_failed' => 'No se pudo actualizar la contraseña.',
        ];
        $errorKey = $_GET['error'];
        ?>
        <!-- <div class="alert alert-danger">❌ <?= $errorMessages[$errorKey] ?? 'Ha ocurrido un error inesperado.' ?></div> -->
    <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="form-container <?= ($mostrarRegistro || $mostrarRecuperar || (isset($_GET['action']) && $_GET['action'] === 'reset')) ? 'hidden' : '' ?>" id="loginForm">
            <h2>Iniciar Sesión</h2>
            <form action="../index.php?action=login" method="post">
                <input type="email" name="correo" placeholder="Correo Electrónico" required>
                <input type="password" name="contrasena" placeholder="Contraseña" required>
                <button type="submit">Entrar</button>
                <a href="?action=forgot">¿Olvidaste tu contraseña?</a>
            </form>
            <?php
             if (isset($_GET['error']) && $_GET['error'] === 'email_found' ) {
                   echo '<p style="color:red;">Error: El correo ya esta registrado.</p>';
                }
            if (isset($_GET['error']) && $_GET['error'] === 'not_verified' ) {
                   echo '<p style="color:red;">Error: El correo no esta verificado.</p>';
                }
                if (isset($_GET['error']) && $_GET['error'] === 'invalid_data' ) {
                   echo '<p style="color:red;">Error: Correo o contrasena incorrectos.</p>';
                }
            if (isset($_GET['message']) && $_GET['message'] === 'verified'){
                echo '<p style="color:red;">✅ ¡Tu correo ha sido verificado correctamente!</p>';
            }
        
            // elseif (isset($_GET['error'])) {
            //     echo '<p style="color:red;">Error: Datos inválidos o vacíos.</p>';
            // }
            elseif (isset($_GET['message']) && $_GET['message'] === 'logout' ) {
                echo '<p style="color: green;">Sesión cerrada correctamente.</p>';
            }
            ?>
            <?php if (isset($_GET['message']) && $_GET['message'] === 'reset_sent'): ?>
            <div class="alert alert-success">📧 Se ha enviado un enlace de recuperación a tu correo.</div>
            <?php elseif (isset($_GET['message']) && $_GET['message'] === 'password_updated'): ?>
            <div class="alert alert-success">🔒 Contraseña actualizada correctamente. Ya puedes iniciar sesión.</div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
        <?php
        $errorMessages = [
            'empty' => 'Todos los campos son obligatorios.',
            'password_mismatch' => 'Las contraseñas no coinciden.',
            'email_not_found' => 'El correo ingresado no está registrado.',
            'invalid_token' => 'El enlace de recuperación no es válido o ha expirado.',
            'reset_error' => 'No se pudo generar el enlace de recuperación.',
            'update_failed' => 'No se pudo actualizar la contraseña.',
        ];
        $errorKey = $_GET['error'];
        ?>
        <!-- <div class="alert alert-danger">❌ <?= $errorMessages[$errorKey] ?? 'Ha ocurrido un error inesperado.' ?></div> -->
    <?php endif; ?>
        </div>

        <div class="form-container <?= ($mostrarRegistro && !$mostrarRecuperar) ? '' : 'hidden' ?>" id="registerForm">
            <h2>Registrarse</h2>
            <form action="../index.php?action=register" method="post">
                <input type="text" name="nombre" placeholder="Nombre completo" required>
                <input type="email" name="correo" placeholder="Correo Electrónico" required>
                <input type="text" placeholder="Usuario" name = "usuario">
                <input type="password" name="contrasena" placeholder="Contraseña" required>
                
                <button type="submit">Registrarse</button>
            </form>
            <?php
                if (isset($_GET['error']) && $_GET['error'] === 'email_found' ) {
                   echo '<p style="color:red;">Error: El correo ya esta registrado.</p>';
                }
            // elseif (isset($_GET['error'])) {
            //     echo '<p style="color:red;">Error: Datos inválidos o vacíos.</p>';
            // }
            if (isset($_GET['message']) && $_GET['message'] === 'registered') {
                echo '<div class="alert alert-success mt-3">✅ ¡Registro exitoso!</div>';
                echo '<script>
                    setTimeout(() => {
                        window.location.href = "index.php";
                    }, 3000);
                </script>';
            }
            ?>
        </div>

        <div class="side-panel">
            <div id="panelLogin">
                <p>¿Aún no tienes una cuenta?</p>
                <button onclick="mostrarRegistro()">Registrarse</button>
            </div>
            <div id="panelRegistro" class="hidden">
                <p>¿Ya tienes una cuenta?</p>
                <button onclick="mostrarLogin()">Iniciar sesión</button>
            </div>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
</body>
</html>
