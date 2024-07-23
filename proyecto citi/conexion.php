<?php
// Datos de conexión
$host = "146.83.165.21";
$port = "5432";
$dbname = "ptandaypan";
$user = "ptandaypan";
$password = "R4f6T5r363";  

// Crear conexión
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

// Verificar conexión
if (!$conn) {
    die("Conexión fallida: " . pg_last_error());
}
?>