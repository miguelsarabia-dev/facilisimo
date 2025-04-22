<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['usuario'])) {
    echo '
        <script>
            alert("Para acceder debes iniciar sesión");
            window.location = "index.php";
        </script>
    ';
    session_destroy();
    die();
}

$nombre = $_SESSION['usuario'];

require_once '../config/Database.php';
require_once '../models/Pregunta.php';

$db = new Database();
$conn = $db->getConnection();
$preguntaModel = new Pregunta($conn);

$correoUsuario = $_SESSION['usuario'];
$clasesComoMaestro = $preguntaModel->obtenerPorMaestro($correoUsuario);
$clasesComoAlumno = $preguntaModel->obtenerPorAlumno($correoUsuario);
$preguntasDisponibles = $preguntaModel->obtenerSinMaestro();
$preguntasParaUnirse = $preguntaModel->obtenerDisponiblesParaAlumno($correoUsuario);

if (isset($_GET['error'])): ?>
    <div class="alert alert-warning mt-3">
        <?php
        switch ($_GET['error']) {
            case 'tiempo_insuficiente': echo "La clase empieza muy pronto. Debe haber al menos 120 minutos de anticipación."; break;
            case 'ya_asignada': echo "Esta clase ya tiene un maestro asignado."; break;
            case 'conflicto_horario': echo "Ya tienes una clase asignada en ese mismo horario."; break;
            case 'fallo_asignacion': echo "Error al intentar asignar la clase."; break;
            case 'no_permitido': echo "Solo los usuarios con perfil de maestro pueden realizar esta acción."; break;
            case 'limite_alumnos': echo "Se llegó al límite de alumnos para esta clase."; break;
            case 'ya_unido': echo "Ya estás inscrito en esta clase."; break; 
            default: echo "Error desconocido."; break;
        }
        ?>
    </div>

    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        <?php
        switch ($_SESSION['success']) {
            case 'unido':
                echo "Te has unido correctamente a la clase. 🎉";
                break;
            case 'pregunta_creada':
                echo "¡Tu pregunta ha sido registrada exitosamente! 👏";
                break;
            default:
                echo "Operación realizada con éxito.";
                break;
        }
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>FACILISIMO - Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const zona = localStorage.getItem('timezone') || 'America/Mexico_City';

            document.querySelectorAll('.converted-time').forEach(el => {
                const utcDate = el.getAttribute('data-utc');
                if (utcDate) {
                    const localDate = new Date(utcDate + 'Z');
                    const options = {
                        timeZone: zona,
                        year: 'numeric', month: 'short', day: 'numeric',
                        hour: '2-digit', minute: '2-digit',
                        hour12: false
                    };
                    el.textContent = `Hora local: ${localDate.toLocaleString('es-MX', options)}`;
                }
            });
        });
    </script>
</head>
<body class="bg-light">
<div class="container my-5" style="max-width: 1200px;">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-primary">Bienvenido, <?php echo htmlspecialchars($nombre); ?> 👋</h3>
        <a href="../index.php?action=logout" class="btn btn-outline-danger">Cerrar sesión</a>
    </div>

    <div class="text-end mb-4">
        <label for="timezone" class="form-label fw-bold">Zona horaria:</label>
        <select id="timezone" class="form-select w-auto d-inline-block">
            <option value="America/Mexico_City">🇲🇽 Ciudad de México</option>
            <option value="America/Bogota">🇨🇴 Bogotá</option>
            <option value="America/Lima">🇵🇪 Lima</option>
            <option value="America/Argentina/Buenos_Aires">🇦🇷 Buenos Aires</option>
            <option value="America/Santiago">🇨🇱 Santiago</option>
            <option value="Europe/Madrid">🇪🇸 Madrid</option>
            <option value="UTC">🌍 UTC</option>
        </select>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <a href="php/mensaje_perfil_maestro.php" class="btn btn-outline-primary w-100">Solicitar perfil de maestro</a>
        </div>
        <div class="col-md-4">
            <a href="php/mensaje_quejas_sugerencias.php" class="btn btn-outline-secondary w-100">Contactar / Quejas / Sugerencias</a>
        </div>
        <div class="col-md-4">
            <form action="../index.php?action=crear_pregunta" method="POST" onsubmit="return hacerPregunta();">
                <input type="hidden" id="userInput" name="input">
                <button type="submit" class="btn btn-primary w-100">Hacer una pregunta</button>
            </form>
        </div>
    </div>
</div>

<div class="mt-5">
    <h4 class="text-primary mb-3">📚 Clases agendadas como maestro</h4>
    <div class="row">
        <?php if (count($clasesComoMaestro) > 0): ?>
            <?php foreach ($clasesComoMaestro as $clase): ?>
                <div class="col-md-6 mb-3">
                    <div class="card shadow-sm rounded border-0 p-4 bg-white mb-4">
                        <div class="card-body">
                            <h5 class="card-title text-primary fw-bold"><?php echo htmlspecialchars($clase['pregunta']); ?></h5>
                            <p class="card-text text-muted">
                                Alumno: <?php echo htmlspecialchars($clase['alumno']); ?><br>
                                Sesión: <p class="converted-time" data-utc="<?php echo $clase['sesion']; ?>"></p><br>
                                Pagada: <strong><?php echo $clase['pagar'] === 'si' ? '✅ Sí' : '❌ No'; ?></strong>
                            </p>
                            <?php if (!empty($clase['link'])): ?>
                            <a href="<?= htmlspecialchars($clase['link']) ?>" target="_blank" class="btn btn-success btn-sm mt-2">
                                Entrar a la sesión
                            </a>
                            <?php else: ?>
                                <p class="text-muted mt-2">Enlace no disponible aún.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">No tienes clases asignadas todavía.</p>
        <?php endif; ?>
    </div>
</div>

<div class="mt-5">
    <h4 class="text-info mb-3">🎓 Clases donde eres alumno</h4>
    <div class="row">
        <?php if (count($clasesComoAlumno) > 0): ?>
            <?php foreach ($clasesComoAlumno as $clase): ?>
                <div class="col-md-6 mb-3">
                    <div class="card shadow-sm rounded border-0 p-4 bg-white mb-4">
                        <div class="card-body">
                            <h5 class="card-title text-primary fw-bold"><?php echo htmlspecialchars($clase['pregunta']); ?></h5>
                            <p class="card-text text-muted">
                                Maestro: <?php echo $clase['maestro'] ? htmlspecialchars($clase['maestro']) : 'Sin asignar'; ?><br>
                                Sesión: <p class="converted-time" data-utc="<?php echo $clase['sesion']; ?>"></p><br>
                                Pagada: <strong><?php echo $clase['pagar'] === 'si' ? '✅ Sí' : '❌ No'; ?></strong>
                            </p>
                            <?php if (!empty($clase['link'])): ?>
                                <a href="<?= htmlspecialchars($clase['link']) ?>" target="_blank" class="btn btn-success btn-sm mt-2">
                                    Entrar a la sesión
                                </a>
                            <?php else: ?>
                                <p class="text-muted mt-2">Enlace no disponible aún.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">Aún no estás inscrito en ninguna clase.</p>
        <?php endif; ?>
    </div>
</div>

<div class="mt-5">
    <h4 class="text-warning mb-3">🆘 Preguntas sin maestro asignado</h4>
    <div class="row">
        <?php if (count($preguntasDisponibles) > 0): ?>
            <?php foreach ($preguntasDisponibles as $pregunta): ?>
                <div class="col-md-6 mb-3">
                    <div class="card shadow-sm rounded border-0 p-4 bg-white mb-4">
                        <div class="card-body">
                            <h5 class="card-title text-primary fw-bold"><?php echo htmlspecialchars($pregunta['pregunta']); ?></h5>
                            <p class="card-text text-muted">
                                Alumno: <?php echo htmlspecialchars($pregunta['alumno']); ?><br>
                                Sesión: <p class="converted-time" data-utc="<?php echo $pregunta['sesion']; ?>"></p>
                            </p>
                            <form action="../routes/ayudar_como_maestro.php" method="POST">
                                <input type="hidden" name="id" value="<?php echo $pregunta['id']; ?>">
                                <button type="submit" class="btn btn-outline-warning btn-sm w-100">
                                    Ayudar como maestro
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">No hay preguntas disponibles por ahora.</p>
        <?php endif; ?>
    </div>
</div>

<div class="mt-5">
    <h4 class="text-success mb-3">🙋‍♂️ Preguntas con maestro disponibles para unirse</h4>
    <div class="row">
        <?php if (count($preguntasParaUnirse) > 0): ?>
            <?php foreach ($preguntasParaUnirse as $pregunta): ?>
                <div class="col-md-6 mb-3">
                    <div class="card shadow-sm rounded border-0 p-4 bg-white mb-4">
                        <div class="card-body">
                            <h5 class="card-title text-primary fw-bold"><?php echo htmlspecialchars($pregunta['pregunta']); ?></h5>
                            <p class="card-text text-muted">
                                Maestro: <?php echo htmlspecialchars($pregunta['maestro']); ?><br>
                                Sesión: <p class="converted-time" data-utc="<?php echo $pregunta['sesion']; ?>"></p>
                            </p>
                            <form action="../routes/unirse_como_alumno.php" method="POST">
                                <input type="hidden" name="id" value="<?php echo $pregunta['id']; ?>">
                                <button type="submit" class="btn btn-outline-success btn-sm w-100">
                                    Unirse a esta clase
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">No hay clases disponibles para unirse en este momento.</p>
        <?php endif; ?>
    </div>
</div>
        </div>

<script>
function hacerPregunta() {
    const userInput = prompt("Escribe tu pregunta:");
    if (userInput && userInput.trim() !== '') {
        document.getElementById('userInput').value = userInput;
        return true;
    } else {
        alert("No se ingresó ninguna pregunta.");
        return false;
    }
}
</script>
<script>
    const tzSelect = document.getElementById('timezone');
    if (tzSelect) {
        const userZone = localStorage.getItem('timezone');
        if (userZone) tzSelect.value = userZone;

        tzSelect.addEventListener('change', () => {
            localStorage.setItem('timezone', tzSelect.value);
            location.reload();
        });
    }
</script>

<script>
    setTimeout(function () {
        const alert = document.querySelector('.alert');
        if (alert) {
            alert.classList.remove('show');
            alert.classList.add('fade');
        }
    }, 4000);
</script>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>