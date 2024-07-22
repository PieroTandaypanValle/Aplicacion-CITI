<?php
// Conexion a la base de datos
include('conexion.php');

date_default_timezone_set('America/Santiago');

// Generar reporte de capacitaciones contratadas
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generarReporte'])) {
    // Consulta para obtener las capacitaciones contratadas
    $query = "SELECT 
              c.nombre_capacitacion, 
              r.rut_persona, 
              r.nombre, 
              r.apellido
            FROM capacitacion c
            JOIN contrata con ON c.id_capacitacion = con.id_capacitacion
            JOIN relator r ON c.rut_persona = r.rut_persona
            ORDER BY r.rut_persona, c.nombre_capacitacion";
    $result = pg_query($conn, $query);

    // Obtener el total de relatores
    $total_relatores_query = "SELECT COUNT(DISTINCT rut_persona) AS total FROM relator WHERE delete_all = 'activo'"; // Filtra por relatores activos
    $total_relatores_result = pg_query($conn, $total_relatores_query);
    $total_relatores_row = pg_fetch_assoc($total_relatores_result);
    $total_relatores = $total_relatores_row['total'];

    // Crear el archivo Excel
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="Reporte_Relatores_Capacitaciones.xls"');

    // Generar el contenido del archivo Excel
    $excel_content = "
    <xml xmlns:x='urn:schemas-microsoft-com:office:excel' xmlns:ss='urn:schemas-microsoft-com:office:spreadsheet'>
        <Workbook xmlns='urn:schemas-microsoft-com:office:spreadsheet'>
            <Styles>
                <Style ss:ID='s1'>
                    <Font ss:FontName='Arial' ss:Size='12' ss:Bold='1'/>
                    <Alignment ss:Horizontal='Center' ss:Vertical='Center'/>
                </Style>
                <Style ss:ID='s2'>
                    <Font ss:FontName='Arial' ss:Size='10'/>
                </Style>
            </Styles>
            <Worksheet ss:Name='Reporte Relatores'>
                <Table>
                    <Row ss:StyleID='s1'>
                        <Cell ss:MergeAcross='4'><Data ss:Type='String'>Reporte de Relatores y Capacitaciones Realizadas</Data></Cell>
                    </Row>
                    <Row ss:StyleID='s1'>
                        <Cell ss:MergeAcross='4'><Data ss:Type='String'>EMPRESA: CITI</Data></Cell>
                    </Row>
                    <Row ss:StyleID='s1'>
                        <Cell ss:MergeAcross='4'><Data ss:Type='String'>FECHA: " . date('d/m/Y') . "</Data></Cell>
                    </Row>
                    <Row ss:StyleID='s1'>
                        <Cell><Data ss:Type='String'>Numero</Data></Cell>
                        <Cell><Data ss:Type='String'>Nombre de Capacitación</Data></Cell>
                        <Cell><Data ss:Type='String'>RUT Relator</Data></Cell>
                        <Cell><Data ss:Type='String'>Nombre Relator</Data></Cell>
                        <Cell><Data ss:Type='String'>Apellido Relator</Data></Cell>
                        <Cell><Data ss:Type='String'>Cantidad de Capacitaciones</Data></Cell>
                    </Row>";

    // Contador para las filas
    $i = 1;
    $last_rut = null;
    $capacitaciones_relator = 0;
    $totals_relatores = [];

    // Agregar las capacitaciones contratadas al Excel
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_assoc($result)) {
            $excel_content .= "<Row ss:StyleID='s2'>";
            $excel_content .= "<Cell><Data ss:Type='Number'>" . $i . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['nombre_capacitacion'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['rut_persona'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['nombre'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['apellido'] . "</Data></Cell>";
            $excel_content .= "</Row>";

            // Incrementamos el contador de capacitaciones del relator actual
            if ($last_rut != $row['rut_persona']) {
                $capacitaciones_relator = 1;
            } else {
                $capacitaciones_relator++;
            }

            // Registramos el total de capacitaciones del relator actual
            $totals_relatores[$row['rut_persona']] = $capacitaciones_relator;

            $last_rut = $row['rut_persona'];
            $i++;
        }
    } else {
        $excel_content .= "<Row ss:StyleID='s2'><Cell ss:MergeAcross='5'><Data ss:Type='String'>No hay capacitaciones contratadas.</Data></Cell></Row>";
    }

    // Agregar el total de relatores al Excel
    $excel_content .= "<Row ss:StyleID='s2'>";
    $excel_content .= "<Cell ss:MergeAcross='4'><Data ss:Type='String'>Total Relatores:</Data></Cell>";
    $excel_content .= "<Cell><Data ss:Type='Number'>" . $total_relatores . "</Data></Cell>";
    $excel_content .= "</Row>";

    // Agregar el total de capacitaciones por relator
    foreach ($totals_relatores as $rut => $total) {
        $excel_content .= "<Row ss:StyleID='s2'>";
        $excel_content .= "<Cell ss:MergeAcross='4'><Data ss:Type='String'>Total Capacitaciones Relator " . $rut . ":</Data></Cell>";
        $excel_content .= "<Cell><Data ss:Type='Number'>" . $total . "</Data></Cell>";
        $excel_content .= "</Row>";
    }

    $excel_content .= "
                </Table>
            </Worksheet>
        </Workbook>
    </xml>";

    echo $excel_content;
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Aplicacion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="css/main.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .btn-group {
            display: flex;
            gap: 5px;
        }
    </style>
</head>

<body>
    <nav>
        <input type="checkbox" id="check">
        <label for="check" class="checkbtn">
            <i class="fas fa-bars"></i>
        </label>
        <label class="logo">
            <img src="images/citilogo.png" alt="Logo de la empresa">
        </label>
        <ul>
            <li><a href="admin.php">Admin</a></li>
            <li><a href="menu.php">Clientes</a></li>
            <li><a class="active" href="#">Relator</a></li>
            <li><a href="estudiantes.php">Estudiantes</a></li>
            <li><a href="capacitacion.php">Capacitacion</a></li>
            <li><a href="materiales.php">Materiales</a></li>
            <li><a href="evaluaciones.php">Evaluaciones</a></li>
            <li><a href="ordenpago.php">Orden pago</a></li>
            <li><a href="cotizacion.php">Cotizacion</a></li>
            <li><a href="estado.php">Estado</a></li>
            <li><a href="index.php" onclick="logout()">Salir</a></li>
        </ul>
    </nav>
    <main>
        <div class="center">
            <h3>Administrar Relator</h3>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <button id="btnNuevo" type="button" class="btn btn-info" data-toggle="modal" data-target="#modalAgregar">Agregar <i class="material-icons">library_add</i></button>
                    <button id="btnGenerarReportes" type="button" class="btn btn-success" data-toggle="modal" data-target="#modalGenerarReportes">Generar Reportes <i class="material-icons">assessment</i></button>
                </div>
            </div>
        </div>

        <!-- Modal Agregar -->
        <div class="modal fade" id="modalAgregar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Agregar Relator</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formAgregar" method="post" action="relator.php">
                            <input type="hidden" name="crear" value="1">
                            <div class="form-group">
                                <label for="rut">RUT</label>
                                <input type="text" class="form-control" id="rut" name="rut" required>
                            </div>
                            <div class="form-group">
                                <label for="nombre">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                            <div class="form-group">
                                <label for="apellido">Apellido</label>
                                <input type="text" class="form-control" id="apellido" name="apellido" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="telefono">Telefono</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" required>
                            </div>
                            <div class="form-group">
                                <label for="fecha_nacimiento">Fecha Nacimiento</label>
                                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
                            </div>
                            <div class="form-group">
                                <label for="direccion">Direccion</label>
                                <input type="text" class="form-control" id="direccion" name="direccion" required>
                            </div>
                            <div class="form-group">
                                <label for="genero">Genero</label>
                                <input type="text" class="form-control" id="genero" name="genero" required>
                            </div>
                            <div class="form-group">
                                <label for="area_especialidad">Area Especialidad</label>
                                <input type="text" class="form-control" id="area_especialidad" name="area_especialidad" required>
                            </div>
                            <div class="form-group">
                                <label for="anos_experiencia">Años Experiencia</label>
                                <input type="number" class="form-control" id="anos_experiencia" name="anos_experiencia" required>
                            </div>
                            <div class="form-group">
                                <label for="salario">Salario</label>
                                <input type="number" class="form-control" id="salario" name="salario" required>
                            </div>
                            <div class="form-group">
                                <label for="metodologia_ensenanza">Metodologia Enseñanza</label>
                                <input type="text" class="form-control" id="metodologia_ensenanza" name="metodologia_ensenanza" required>
                            </div>
                            <div class="form-group">
                                <label for="delete_all">Eliminar Todo</label>
                                <input type="text" class="form-control" id="delete_all" name="delete_all" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Agregar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Actualizar -->
        <div class="modal fade" id="modalActualizar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Actualizar Relator</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formActualizar" method="post" action="relator.php">
                            <input type="hidden" name="actualizar" value="1">
                            <div class="form-group">
                                <label for="rutActualizar">RUT</label>
                                <input type="text" class="form-control" id="rutActualizar" name="rutActualizar" required readonly>
                            </div>
                            <div class="form-group">
                                <label for="nombreActualizar">Nombre</label>
                                <input type="text" class="form-control" id="nombreActualizar" name="nombreActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="apellidoActualizar">Apellido</label>
                                <input type="text" class="form-control" id="apellidoActualizar" name="apellidoActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="emailActualizar">Email</label>
                                <input type="email" class="form-control" id="emailActualizar" name="emailActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="telefonoActualizar">Telefono</label>
                                <input type="text" class="form-control" id="telefonoActualizar" name="telefonoActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="fecha_nacimientoActualizar">Fecha Nacimiento</label>
                                <input type="date" class="form-control" id="fecha_nacimientoActualizar" name="fecha_nacimientoActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="direccionActualizar">Direccion</label>
                                <input type="text" class="form-control" id="direccionActualizar" name="direccionActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="generoActualizar">Genero</label>
                                <input type="text" class="form-control" id="generoActualizar" name="generoActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="area_especialidadActualizar">Area Especialidad</label>
                                <input type="text" class="form-control" id="area_especialidadActualizar" name="area_especialidadActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="anos_experienciaActualizar">Años Experiencia</label>
                                <input type="number" class="form-control" id="anos_experienciaActualizar" name="anos_experienciaActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="salarioActualizar">Salario</label>
                                <input type="number" class="form-control" id="salarioActualizar" name="salarioActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="metodologia_ensenanzaActualizar">Metodologia Enseñanza</label>
                                <input type="text" class="form-control" id="metodologia_ensenanzaActualizar" name="metodologia_ensenanzaActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="delete_allActualizar">Eliminar Todo</label>
                                <input type="text" class="form-control" id="delete_allActualizar" name="delete_allActualizar" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Actualizar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Eliminar -->
        <div class="modal fade" id="modalEliminar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Desactivar Relator</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>¿Está seguro de que desea desactivar este relator?</p>
                        <form id="formEliminar" method="get" action="relator.php">
                            <input type="hidden" name="eliminar" id="eliminarRut" value="">
                            <button type="submit" class="btn btn-danger" id="confirmarEliminar">Desactivar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Generar Reportes -->
        <div class="modal fade" id="modalGenerarReportes" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Generar Reportes</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formGenerarReporte" method="post" action="relator.php">
                            <input type="hidden" name="generarReporte" value="1">
                            <button type="submit" class="btn btn-primary">Generar Reporte</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

         <!-- Barra de búsqueda -->
         <div class="container search-input">
            <div class="row">
                <div class="col-lg-12">
                    <div class="input-group mb-3">
                        <input id="searchInput" type="text" class="form-control" placeholder="Buscar...">
                        <div class="input-group-append">
                            <select id="searchBy" class="form-control">
                                <option value="nombre">Nombre</option>
                                <option value="apellido">Apellido</option>
                                <option value="email">Email</option>
                                <option value="genero">Género</option>
                                <option value="area_especialidad">Área Especialidad</option>
                                <option value="rut_persona">RUT Relator</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <br>

        <div class="container caja">
            <div class="row">
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table id="tablaRelatores" class="table table-striped table-bordered table-condensed" style="width:100%">
                            <thead class="text-center">
                                <tr>
                                    <th>RUT</th>
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                    <th>Email</th>
                                    <th>Telefono</th>
                                    <th>Fecha_nacimiento</th>
                                    <th>Direccion</th>
                                    <th>Genero</th>
                                    <th>Area_especialidad</th>
                                    <th>Años Experiencia</th>
                                    <th>Salario</th>
                                    <th>Metodologia_enseñanza</th>
                                    <th>Eliminar Todo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <?php
                                // Conexion a la base de datos
                                include('conexion.php');

                                // Insertar relator
                                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear'])) {
                                    $rut_persona = $_POST['rut'];
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
                                    $metodologia_ensenanza = $_POST['metodologia_ensenanza'];
                                    $delete_all = $_POST['delete_all'];

                                    $insert_persona = "INSERT INTO persona (rut_persona, nombre, apellido, email, telefono, fecha_nacimiento, direccion, genero)
                                                       VALUES ('$rut_persona', '$nombre', '$apellido', '$email', '$telefono', '$fecha_nacimiento', '$direccion', '$genero')";

                                    $insert_empleado = "INSERT INTO empleado (rut_persona, nombre, apellido, email, telefono, fecha_nacimiento, direccion, genero, area_especialidad, anos_experiencia, salario, delete_all)
                                                       VALUES ('$rut_persona', '$nombre', '$apellido', '$email', '$telefono', '$fecha_nacimiento', '$direccion', '$genero', '$area_especialidad', '$anos_experiencia', '$salario', '$delete_all')";

                                    $insert_relator = "INSERT INTO relator (rut_persona, nombre, apellido, email, telefono, fecha_nacimiento, direccion, genero, area_especialidad, anos_experiencia, salario, metodologia_ensenanza, delete_all)
                                                       VALUES ('$rut_persona', '$nombre', '$apellido', '$email', '$telefono', '$fecha_nacimiento', '$direccion', '$genero', '$area_especialidad', '$anos_experiencia', '$salario', '$metodologia_ensenanza', '$delete_all')";

                                    if (pg_query($conn, $insert_persona) && pg_query($conn, $insert_empleado) && pg_query($conn, $insert_relator)) {
                                        echo "<script>alert('Nuevo relator creado exitosamente.'); window.location.href='relator.php';</script>";
                                    } else {
                                        echo "<script>alert('Error: " . pg_last_error($conn) . "');</script>";
                                    }
                                }

                                // Eliminar relator (Desactivar)
                                if (isset($_GET['eliminar'])) {
                                    $rut_persona = $_GET['eliminar'];

                                    // Actualizar el estado del relator a "inactivo" en lugar de eliminarlo
                                    $update_query = "UPDATE relator SET delete_all = 'inactivo' WHERE rut_persona = '$rut_persona'";
                                    if (pg_query($conn, $update_query)) {
                                        echo "<script>alert('Relator desactivado exitosamente.'); window.location.href='relator.php';</script>";
                                    } else {
                                        echo "<script>alert('Error: " . $update_query . "<br>" . pg_last_error($conn) . "');</script>";
                                    }
                                }

                                // Actualizar relator
                                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar'])) {
                                    $rut_persona = $_POST['rutActualizar'];
                                    $nombre = $_POST['nombreActualizar'];
                                    $apellido = $_POST['apellidoActualizar'];
                                    $email = $_POST['emailActualizar'];
                                    $telefono = $_POST['telefonoActualizar'];
                                    $fecha_nacimiento = $_POST['fecha_nacimientoActualizar'];
                                    $direccion = $_POST['direccionActualizar'];
                                    $genero = $_POST['generoActualizar'];
                                    $area_especialidad = $_POST['area_especialidadActualizar'];
                                    $anos_experiencia = $_POST['anos_experienciaActualizar'];
                                    $salario = $_POST['salarioActualizar'];
                                    $metodologia_ensenanza = $_POST['metodologia_ensenanzaActualizar'];
                                    $delete_all = $_POST['delete_allActualizar'];

                                    $update_persona = "UPDATE persona SET 
                                                       nombre='$nombre',
                                                       apellido='$apellido',
                                                       email='$email',
                                                       telefono='$telefono',
                                                       fecha_nacimiento='$fecha_nacimiento',
                                                       direccion='$direccion',
                                                       genero='$genero'
                                                       WHERE rut_persona='$rut_persona'";

                                    $update_empleado = "UPDATE empleado SET 
                                                       nombre='$nombre',
                                                       apellido='$apellido',
                                                       email='$email',
                                                       telefono='$telefono',
                                                       fecha_nacimiento='$fecha_nacimiento',
                                                       direccion='$direccion',
                                                       genero='$genero',
                                                       area_especialidad='$area_especialidad',
                                                       anos_experiencia='$anos_experiencia',
                                                       salario='$salario',
                                                       delete_all='$delete_all'
                                                       WHERE rut_persona='$rut_persona'";

                                    $update_relator = "UPDATE relator SET 
                                                       nombre='$nombre',
                                                       apellido='$apellido',
                                                       email='$email',
                                                       telefono='$telefono',
                                                       fecha_nacimiento='$fecha_nacimiento',
                                                       direccion='$direccion',
                                                       genero='$genero',
                                                       area_especialidad='$area_especialidad',
                                                       anos_experiencia='$anos_experiencia',
                                                       salario='$salario',
                                                       metodologia_ensenanza='$metodologia_ensenanza',
                                                       delete_all='$delete_all'
                                                       WHERE rut_persona='$rut_persona'";

                                    if (pg_query($conn, $update_persona) && pg_query($conn, $update_empleado) && pg_query($conn, $update_relator)) {
                                        echo "<script>alert('Relator actualizado exitosamente.'); window.location.href='relator.php';</script>";
                                    } else {
                                        echo "<script>alert('Error: " . pg_last_error($conn) . "');</script>";
                                    }
                                }

                                // Consulta para obtener los datos de los relatores
                                $query = "SELECT * FROM relator WHERE delete_all = 'activo'"; // Filtra por relatores activos
                                $result = pg_query($conn, $query);

                                // Consulta para obtener el total de relatores
                                $total_query = "SELECT COUNT(*) AS total FROM relator WHERE delete_all = 'activo'"; // Filtra por relatores activos
                                $total_result = pg_query($conn, $total_query);
                                $total_row = pg_fetch_assoc($total_result);
                                $total_relatores = $total_row['total'];

                                if (pg_num_rows($result) > 0) {
                                    while($row = pg_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>" . $row["rut_persona"] . "</td>";
                                        echo "<td>" . $row["nombre"] . "</td>";
                                        echo "<td>" . $row["apellido"] . "</td>";
                                        echo "<td>" . $row["email"] . "</td>";
                                        echo "<td>" . $row["telefono"] . "</td>";
                                        echo "<td>" . $row["fecha_nacimiento"] . "</td>";
                                        echo "<td>" . $row["direccion"] . "</td>";
                                        echo "<td>" . $row["genero"] . "</td>";
                                        echo "<td>" . $row["area_especialidad"] . "</td>";
                                        echo "<td>" . $row["anos_experiencia"] . "</td>";
                                        echo "<td>" . $row["salario"] . "</td>";
                                        echo "<td>" . $row["metodologia_ensenanza"] . "</td>";
                                        echo "<td>" . $row["delete_all"] . "</td>"; // Muestra el estado (activo/inactivo)
                                        echo "<td><a href='#' class='btn btn-warning btn-sm' data-toggle='modal' data-target='#modalEliminar' onclick='setEliminarRut(\"" . $row["rut_persona"] . "\")'>Desactivar</a> | 
                                              <a href='#' class='btn btn-primary btn-sm' data-toggle='modal' data-target='#modalActualizar' onclick='setActualizarDatos(" . json_encode($row) . ")'>Editar</a></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='14'>No hay relatores</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    <footer>
        <div class="footer-content">
            <h3>Administrador</h3>
            <p>Permite ordenar la administracion de la informacion relacionada con los relatores.</p>
        </div>
        <div class="footer-bottom">
            <p>copyright © <a href="#">UNAP</a> </p>
        </div>
    </footer>
    <!-- jQuery, Popper.js, Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function setEliminarRut(rut) {
            document.getElementById('eliminarRut').value = rut;
        }

        function setActualizarDatos(relator) {
            document.getElementById('rutActualizar').value = relator.rut_persona;
            document.getElementById('nombreActualizar').value = relator.nombre;
            document.getElementById('apellidoActualizar').value = relator.apellido;
            document.getElementById('emailActualizar').value = relator.email;
            document.getElementById('telefonoActualizar').value = relator.telefono;
            document.getElementById('fecha_nacimientoActualizar').value = relator.fecha_nacimiento;
            document.getElementById('direccionActualizar').value = relator.direccion;
            document.getElementById('generoActualizar').value = relator.genero;
            document.getElementById('area_especialidadActualizar').value = relator.area_especialidad;
            document.getElementById('anos_experienciaActualizar').value = relator.anos_experiencia;
            document.getElementById('salarioActualizar').value = relator.salario;
            document.getElementById('metodologia_ensenanzaActualizar').value = relator.metodologia_ensenanza;
            document.getElementById('delete_allActualizar').value = relator.delete_all;
        }

        // Filtro de búsqueda
        const searchInput = document.getElementById('searchInput');
        const searchBy = document.getElementById('searchBy');
        const tableBody = document.getElementById('tableBody');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const selectedFilter = searchBy.value;

            const rows = tableBody.querySelectorAll('tr');
            rows.forEach(row => {
                const nombre = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const apellido = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                const email = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
                const genero = row.querySelector('td:nth-child(8)').textContent.toLowerCase();
                const areaEspecialidad = row.querySelector('td:nth-child(9)').textContent.toLowerCase();
                const rutPersona = row.querySelector('td:nth-child(1)').textContent.toLowerCase(); // Se agrega la indexación para rut_persona

                if (searchTerm === '') {
                    row.style.display = '';
                } else if (selectedFilter === 'nombre' && nombre.includes(searchTerm)) {
                    row.style.display = '';
                } else if (selectedFilter === 'apellido' && apellido.includes(searchTerm)) {
                    row.style.display = '';
                } else if (selectedFilter === 'email' && email.includes(searchTerm)) {
                    row.style.display = '';
                } else if (selectedFilter === 'genero' && genero.includes(searchTerm)) {
                    row.style.display = '';
                } else if (selectedFilter === 'area_especialidad' && areaEspecialidad.includes(searchTerm)) {
                    row.style.display = '';
                } else if (selectedFilter === 'rut_persona' && rutPersona.includes(searchTerm)) { // Se agrega la condición para rut_persona
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>

<?php
// Cerrar la conexion
pg_close($conn);
?>