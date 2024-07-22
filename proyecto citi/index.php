<?php
session_start();
include 'conexion.php'; // Archivo de conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    ob_start(); // Iniciar el almacenamiento en búfer de salida
    if (isset($_POST['register'])) {
        // Registro de usuario
        $rut_persona = $_POST['rut_persona'];
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $email = $_POST['email'];
        $telefono = $_POST['telefono'];
        $fecha_nacimiento = $_POST['fecha_nacimiento'];
        $direccion = $_POST['direccion'];
        $genero = $_POST['genero'];
        $area_especialidad = $_POST['area_especialidad'];
        $anos_experiencia = $_POST['anos_experiencia'];
        $salario = $_POST['salario'];
        $tipo_administrador = $_POST['tipo_administrador'];
        $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
        $delete_all = $_POST['delete_all'];

        // Insertar en la tabla persona
        $insert_persona = "INSERT INTO persona (rut_persona, nombre, apellido, email, telefono, fecha_nacimiento, direccion, genero)
                           VALUES ('$rut_persona', '$nombre', '$apellido', '$email', '$telefono', '$fecha_nacimiento', '$direccion', '$genero')";

        // Insertar en la tabla empleado
        $insert_empleado = "INSERT INTO empleado (rut_persona, nombre, apellido, email, telefono, fecha_nacimiento, direccion, genero, area_especialidad, anos_experiencia, salario, delete_all)
                            VALUES ('$rut_persona', '$nombre', '$apellido', '$email', '$telefono', '$fecha_nacimiento', '$direccion', '$genero', '$area_especialidad', '$anos_experiencia', '$salario', '$delete_all')";

        // Insertar en la tabla administrador
        $insert_administrador = "INSERT INTO administrador (rut_persona, nombre, apellido, email, telefono, fecha_nacimiento, direccion, genero, area_especialidad, anos_experiencia, salario, tipo_administrador, contrasena, delete_all)
                                 VALUES ('$rut_persona', '$nombre', '$apellido', '$email', '$telefono', '$fecha_nacimiento', '$direccion', '$genero', '$area_especialidad', '$anos_experiencia', '$salario', '$tipo_administrador', '$contrasena', '$delete_all')";

        if (pg_query($conn, $insert_persona)) {
            if (pg_query($conn, $insert_empleado)) {
                if (pg_query($conn, $insert_administrador)) {
                    echo "<script>alert('Registro exitoso');</script>";
                } else {
                    echo "<script>alert('Error: " . pg_last_error($conn) . "');</script>";
                }
            } else {
                echo "<script>alert('Error: " . pg_last_error($conn) . "');</script>";
            }
        } else {
            echo "<script>alert('Error: " . pg_last_error($conn) . "');</script>";
        }
    } elseif (isset($_POST['login'])) {
        // Inicio de sesión
        $email = $_POST['login_email'];
        $contrasena = $_POST['login_contrasena'];

        $sql = "SELECT * FROM administrador WHERE email = '$email'";
        $result = pg_query($conn, $sql);
        if ($result) {
            $admin = pg_fetch_assoc($result);
            if (password_verify($contrasena, $admin['contrasena'])) {
                if ($admin['delete_all'] == 'activo') {
                    // Iniciar sesión y redirigir al menú
                    $_SESSION['admin'] = $admin;
                    header("Location: menu.php");
                    exit(); // Asegura que no se ejecute más código después de la redirección
                } else {
                    echo "<script>alert('Cuenta no está activa.'); window.location.href='index.php';</script>";
                }
            } else {
                echo "<script>alert('Contraseña incorrecta.'); window.location.href='index.php';</script>";
            }
        } else {
            echo "<script>alert('Correo no registrado.'); window.location.href='index.php';</script>";
        }
    }
    ob_end_flush(); // Limpiar el búfer de salida y enviar la salida al navegador
    pg_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario Login y Registro de Usuarios</title>
    <link rel="stylesheet" href="css/main_login.css">
</head>
<body>
    <div class="background"></div>
    <div class="container">
        <div class="form-container">
            <!-- Formularios -->
            <div class="form-content">
                <!-- Links de los formularios -->
                <div class="form-tabs">
                    <li class="tab tab-second active"><a href="#login">Iniciar Sesión</a></li>
                    <li class="tab tab-first"><a href="#registro">Registrarse</a></li>
                </div>

                <!-- Contenido de los Formularios -->
                <div class="tab-content">
                    <!-- Iniciar Sesión -->
                    <div id="login">
                        <h1>Iniciar Sesión</h1>
                        <form action="" method="post">
                            <div class="input-container">
                                <label>Email <span class="req">*</span></label>
                                <input type="email" name="login_email" required>
                            </div>

                            <div class="input-container">
                                <label>Contraseña <span class="req">*</span></label>
                                <input type="password" name="login_contrasena" required>
                            </div>
                            <input type="submit" name="login" class="button button-block" value="Iniciar Sesión">
                        </form>
                    </div>

                    <!-- Registrarse -->
                    <div id="registro">
                        <h1>Registrarse</h1>
                        <form action="" method="post">
                            <div class="row-top">
                                <div class="input-container">
                                    <label>Rut <span class="req">*</span></label>
                                    <input type="text" name="rut_persona" required>
                                </div>
                                <div class="input-container">
                                    <label>Email <span class="req">*</span></label>
                                    <input type="email" name="email" required>
                                </div>
                            </div>
                            <div class="input-container">
                                <label>Nombre Completo <span class="req">*</span></label>
                                <input type="text" name="nombre" required>
                            </div>
                            <div class="input-container">
                                <label>Apellido <span class="req">*</span></label>
                                <input type="text" name="apellido" required>
                            </div>
                            <div class="row-top">
                                <div class="input-container">
                                    <label>Fecha de Nacimiento <span class="req">*</span></label>
                                    <input type="date" name="fecha_nacimiento" required>
                                </div>
                                <div class="input-container">
                                    <label>Teléfono <span class="req">*</span></label>
                                    <input type="tel" name="telefono" required>
                                </div>
                            </div>
                            <div class="input-container">
                                <label>Dirección <span class="req">*</span></label>
                                <input type="text" name="direccion" required>
                            </div>
                            <div class="input-container">
                                <label>Género <span class="req">*</span></label>
                                <input type="text" name="genero" required>
                            </div>
                            <div class="input-container">
                                <label>Área de Especialidad <span class="req">*</span></label>
                                <input type="text" name="area_especialidad" required>
                            </div>
                            <div class="input-container">
                                <label>Años de Experiencia <span class="req">*</span></label>
                                <input type="number" name="anos_experiencia" required>
                            </div>
                            <div class="input-container">
                                <label>Salario <span class="req">*</span></label>
                                <input type="number" name="salario" required>
                            </div>
                            <div class="input-container">
                                <label>Tipo de Administrador <span class="req">*</span></label>
                                <input type="text" name="tipo_administrador" required>
                            </div>
                            <div class="input-container">
                                <label>Estado (Activo/Inactivo) <span class="req">*</span></label>
                                <input type="text" name="delete_all" required>
                            </div>
                            <div class="input-container">
                                <label>Contraseña <span class="req">*</span></label>
                                <input type="password" name="contrasena" required>
                            </div>

                            <input type="submit" name="register" class="button button-block" value="Registrarse">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/login.js"></script>
</body>
</html>
