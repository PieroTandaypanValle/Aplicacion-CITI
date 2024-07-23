<?php
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registrar'])) {
    // Obtener los valores del formulario
    $rut = $_POST['rut'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $genero = $_POST['genero'];
    $nroCelular = $_POST['telefono'];
    $fechaNacimiento = $_POST['fecha_nacimiento'];
    $prevision = $_POST['prevision'];
    $direccion = $_POST['direccion'];
    $email = $_POST['gmail'];
    $habitosConsumo = $_POST['habitos_consumo'];
    $habitosAlimenticios = $_POST['habitos_alimenticios'];
    $estiloVida = $_POST['estilo_vida'];
    $resultadoIcm = $_POST['resultado_icm'];
    $personalSaludRut = $_POST['personal_salud_rut_per_salud'];
    $codigoHabitacion = $_POST['habitacion_codigo_habitacion'];
    $nombreHistorialMedico = $_POST['historial_medico_nombrehm'];

    // Insertar los datos en la base de datos
    $query = "INSERT INTO paciente (rut, nombre, apellido, genero, nro_celular, fecha_nacimiento, prevision, direccion, email, habitos_consumo, habitos_alimenticios, estilo_vida, resultado_icm, personal_salud_rut_per_salud, habitacion_codigo_habitacion, historial_medico_nombrehm)
              VALUES (:rut, :nombre, :apellido, :genero, :nroCelular, :fechaNacimiento, :prevision, :direccion, :email, :habitosConsumo, :habitosAlimenticios, :estiloVida, :resultadoIcm, :personalSaludRut, :codigoHabitacion, :nombreHistorialMedico)";
    $statement = $db->prepare($query);
    $statement->bindValue(':rut', $rut);
    $statement->bindValue(':nombre', $nombre);
    $statement->bindValue(':apellido', $apellido);
    $statement->bindValue(':genero', $genero);
    $statement->bindValue(':nroCelular', $nroCelular);
    $statement->bindValue(':fechaNacimiento', $fechaNacimiento);
    $statement->bindValue(':prevision', $prevision);
    $statement->bindValue(':direccion', $direccion);
    $statement->bindValue(':email', $email);
    $statement->bindValue(':habitosConsumo', $habitosConsumo);
    $statement->bindValue(':habitosAlimenticios', $habitosAlimenticios);
    $statement->bindValue(':estiloVida', $estiloVida);
    $statement->bindValue(':resultadoIcm', $resultadoIcm);
    $statement->bindValue(':personalSaludRut', $personalSaludRut);
    $statement->bindValue(':codigoHabitacion', $codigoHabitacion);
    $statement->bindValue(':nombreHistorialMedico', $nombreHistorialMedico);
    $statement->execute();

    // Redirigir a una página de éxito o realizar alguna acción adicional después de guardar los datos
    header("Location: registro_exitoso.html");
    exit();
} elseif (isset($_POST['eliminar'])) {
    // Obtener el RUT del formulario de eliminación
    $rut = $_POST['rut'];

    // Verificar si el paciente existe en la base de datos
    $queryCheckPaciente = "SELECT COUNT(*) FROM paciente WHERE rut = :rut";
    $statementCheckPaciente = $db->prepare($queryCheckPaciente);
    $statementCheckPaciente->bindValue(':rut', $rut);
    $statementCheckPaciente->execute();
    $count = $statementCheckPaciente->fetchColumn();

    if ($count > 0) {
        // Eliminar al paciente y manejar la restricción de integridad referencial
        $queryDelete = "DELETE FROM paciente WHERE rut = :rut";
        $statementDelete = $db->prepare($queryDelete);
        $statementDelete->bindValue(':rut', $rut);
        $statementDelete->execute();

        // Redirigir a una página de éxito o realizar alguna acción adicional después de eliminar los datos
        header("Location: eliminacion_exitosa.html");
        exit();
    } else {
        $errorMessage = "El paciente ingresado no existe.";
        header("Location: error.php?message=" . urlencode($errorMessage));
        exit();
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['actualizar'])) {
    // Obtener los valores del formulario de actualización
    $rut = $_POST['rut'];
    $nombreColumna = $_POST['nombre_columna'];
    $nuevoDato = $_POST['nuevo_dato'];

    // Verificar si el paciente existe
    $queryCheckPaciente = "SELECT COUNT(*) FROM paciente WHERE rut = :rut";
    $statementCheckPaciente = $db->prepare($queryCheckPaciente);
    $statementCheckPaciente->bindValue(':rut', $rut);
    $statementCheckPaciente->execute();
    $count = $statementCheckPaciente->fetchColumn();

    if ($count > 0) {
        // Actualizar el dato en la tabla "paciente"
        $queryUpdate = "UPDATE paciente SET $nombreColumna = :nuevoDato WHERE rut = :rut";
        $statementUpdate = $db->prepare($queryUpdate);
        $statementUpdate->bindValue(':nuevoDato', $nuevoDato);
        $statementUpdate->bindValue(':rut', $rut);
        $statementUpdate->execute();

        // Redirigir a una página de éxito o realizar alguna acción adicional después de actualizar los datos
        header("Location: actualizacion_exitosa.html");
        exit();
    } else {
        $errorMessage = "El paciente ingresado no existe.";
        header("Location: error.php?message=" . urlencode($errorMessage));
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
    <title>Formulario Pacientes</title>
    <link rel="stylesheet" href="stylef.css">
</head>
<body>
    <h1>Formulario Paciente</h1>
    <?php
    // Verificar en qué paso se encuentra el usuario
    $paso = isset($_GET['paso']) ? $_GET['paso'] : 'registro';

    if ($paso === 'registro') {
        // Paso de registro
        ?>
        <form method="POST" action="?paso=eliminar">
            <label for="rut">RUT:</label>
            <input type="text" name="rut" id="rut" required>
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" required>
            <label for="apellido">Apellido:</label>
            <input type="text" name="apellido" id="apellido" required>
            <label for="genero">Género:</label>
            <select name="genero" id="genero">
                <option value="Masculino">Masculino</option>
                <option value="Femenino">Femenino</option>
                <option value="Otro">Otro</option>
            </select>
            <label for="telefono">Teléfono:</label>
            <input type="text" name="telefono" id="telefono">
            <label for="fecha_nacimiento">Fecha de nacimiento:</label>
            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" required>
            <label for="direccion">Dirección:</label>
            <input type="text" name="direccion" id="direccion">
            <label for="gmail">Gmail:</label>
            <input type="text" name="gmail" id="gmail">
            <label for="prevision">Previsión:</label>
            <input type="text" name="prevision" id="prevision">
            <label for="habitos_consumo">Hábitos de consumo:</label>
            <input type="text" name="habitos_consumo" id="habitos_consumo">
            <label for="habitos_alimenticios">Hábitos alimenticios:</label>
            <input type="text" name="habitos_alimenticios" id="habitos_alimenticios">
            <label for="estilo_vida">Estilo de vida:</label>
            <input type="text" name="estilo_vida" id="estilo_vida">
            <label for="resultado_icm">Resultado ICM:</label>
            <input type="text" name="resultado_icm" id="resultado_icm">
            <label for="personal_salud_rut_per_salud">RUT del personal de salud:</label>
            <input type="text" name="personal_salud_rut_per_salud" id="personal_salud_rut_per_salud">
            <label for="habitacion_codigo_habitacion">Código de habitación:</label>
            <input type="text" name="habitacion_codigo_habitacion" id="habitacion_codigo_habitacion">
            <label for="historial_medico_nombrehm">Nombre del historial médico:</label>
            <input type="text" name="historial_medico_nombrehm" id="historial_medico_nombrehm">
            <div class="botones">
                <input type="submit" name="registrar" value="Registrar">
            </div>
        </form>
        <?php
    } elseif ($paso === 'eliminar') {
        // Paso de eliminación
        ?>
        <form method="POST" action="?paso=eliminar">
            <label for="rut_eliminar">RUT del Paciente a Eliminar:</label>
            <input type="text" name="rut" id="rut_eliminar" required>
            <div class="botones">
                <input type="submit" name="eliminar" value="Eliminar">
            </div>
        </form>
        <?php
    } elseif ($paso === 'actualizar') {
        // Paso de actualización
        ?>
        <form method="POST" action="?paso=actualizar">
            <!-- Contenido del formulario de actualización -->
            <label for="rut_actualizar">RUT del Paciente a Actualizar:</label>
            <input type="text" name="rut" id="rut_actualizar" required>
            <label for="nombre_columna">Nombre de la columna:</label>
            <input type="text" name="nombre_columna" id="nombre_columna" required>
            <label for="nuevo_dato">Nuevo dato:</label>
            <input type="text" name="nuevo_dato" id="nuevo_dato" required>
            <div class="botones">
                <input type="submit" name="actualizar" value="Actualizar">
            </div>
        </form>
        <?php
    } elseif ($paso === 'buscar') {
        // Paso de búsqueda
        ?>
        <form method="POST" action="tablaP.php">
            <!-- Contenido del formulario de búsqueda -->
            <label for="rut_buscar">RUT del Paciente a Buscar:</label>
            <input type="text" name="rut" id="rut_buscar" required>
            <div class="botones">
                <input type="submit" name="buscar" value="Buscar">
            </div>
        </form>
        <?php
    } elseif ($paso === 'reporte') {
        // Paso de generación de reporte
        ?>
        <form method="POST" action="generar_reporte_pacientes.php">
            <!-- Contenido del formulario de generación de reporte -->
            <div class="botones">
                <input type="submit" name="generar_reporte" value="Generar Reporte">
            </div>
        </form>
        <?php
    }
    ?>

    <div class="botones">
        <?php if ($paso === 'registro') : ?>
            <a class="flecha-derecha" href="?paso=eliminar">Eliminar ►</a>
        <?php elseif ($paso === 'eliminar') : ?>
            <a class="flecha-izquierda" href="?paso=registro">◄ Volver al Registro</a>
            <a class="flecha-derecha" href="?paso=actualizar">Actualizar ►</a>
        <?php elseif ($paso === 'actualizar') : ?>
            <a class="flecha-izquierda" href="?paso=eliminar">◄ Eliminar</a>
            <a class="flecha-derecha" href="?paso=buscar">Buscar ►</a>
            <a class="flecha-derecha" href="?paso=reporte">Generar Reporte ►</a>
        <?php elseif ($paso === 'buscar') : ?>
            <a class="flecha-izquierda" href="?paso=actualizar">◄ Volver a Actualizar</a>
            <a class="flecha-derecha" href="?paso=reporte">Generar Reporte ►</a>
        <?php elseif ($paso === 'reporte') : ?>
            <a class="flecha-izquierda" href="?paso=buscar">◄ Volver a Buscar</a>
        <?php endif; ?>
    </div>
</body>
</html>
