<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'php/config/Database.php';

$db = new Database();
$conn = $db->getConnection();

if ($conn) {
    echo "✅ Conexión exitosa a la base de datos.";
} else {
    echo "❌ Error al conectar.";
}  