<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <link rel="stylesheet" href="../../assets/css/style.css">
    

    <title>Login y Registro - FACILISIMO</title>
    
</head>
<body>
    <?php
    $mostrarRegistro = false;
    if (isset($_GET['message']) && $_GET['message'] === 'registered') {
        $mostrarRegistro = true;
    }
    ?>
    <div class="container">
        <div class="form-container <?= $mostrarRegistro ? 'hidden' : '' ?>" id="loginForm">
            <h2>Iniciar Sesión</h2>
            <form action="../index.php?action=login" method="post">
                <input type="email" name="correo" placeholder="Correo Electrónico" required>
                <input type="password" name="contrasena" placeholder="Contraseña" required>
                <button type="submit">Entrar</button>
            </form>
            <?php
            if (isset($_GET['error'])) {
                echo '<p style="color:red;">Error: Datos inválidos o vacíos.</p>';
            }
            if (isset($_GET['message']) && $_GET['message'] === 'logout' ) {
                echo '<p style="color: green;">Sesión cerrada correctamente.</p>';
            }
            ?>
        </div>

        <div class="form-container <?= $mostrarRegistro ? '' : 'hidden' ?>" id="registerForm">
            <h2>Registrarse</h2>
            <form action="../index.php?action=register" method="post">
                <input type="text" name="nombre" placeholder="Nombre completo" required>
                <input type="email" name="correo" placeholder="Correo Electrónico" required>
                <input type="text" placeholder="Usuario" name = "usuario">
                <input type="password" name="contrasena" placeholder="Contraseña" required>
                
                <button type="submit">Registrarse</button>
            </form>
            <?php
            if (isset($_GET['error'])) {
                echo '<p style="color:red;">Error: Datos inválidos o vacíos.</p>';
            }
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
