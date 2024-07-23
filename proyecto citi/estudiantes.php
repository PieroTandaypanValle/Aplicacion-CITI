<?php
// Conexion a la base de datos
include('conexion.php');

date_default_timezone_set('America/Santiago');
//Generar reporte de estudiantes por cliente
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generarReporteEstudiantes'])) {
    // Consulta para obtener los estudiantes por cliente
    $query = "SELECT 
              e.rut_persona,
              e.nombre,
              e.apellido,
              e.rut_cliente,
              c.nombre_empresa
            FROM estudiante e
            LEFT JOIN cliente c ON e.rut_cliente = c.rut_cliente
            ORDER BY e.rut_cliente";
    $result = pg_query($conn, $query);

    // Obtener el total de estudiantes
    $total_estudiantes_query = "SELECT COUNT(*) AS total FROM estudiante";
    $total_estudiantes_result = pg_query($conn, $total_estudiantes_query);
    $total_estudiantes_row = pg_fetch_assoc($total_estudiantes_result);
    $total_estudiantes = $total_estudiantes_row['total'];

    // Crear el archivo Excel
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="Reporte_Estudiantes_Cliente.xls"');

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
            <Worksheet ss:Name='Reporte Estudiantes'>
                <Table>
                    <Row ss:StyleID='s1'>
                        <Cell ss:MergeAcross='4'><Data ss:Type='String'>Reporte de Estudiantes Asignados por Cliente de la Empresa CITI</Data></Cell>
                    </Row>
                    <Row ss:StyleID='s1'>
                        <Cell ss:MergeAcross='4'><Data ss:Type='String'>EMPRESA: CITI</Data></Cell>
                    </Row>
                    <Row ss:StyleID='s1'>
                        <Cell ss:MergeAcross='4'><Data ss:Type='String'>FECHA: " . date('d/m/Y') . "</Data></Cell>
                    </Row>
                    <Row ss:StyleID='s1'>
                        <Cell><Data ss:Type='String'>Numero</Data></Cell>
                        <Cell><Data ss:Type='String'>RUT Estudiante</Data></Cell>
                        <Cell><Data ss:Type='String'>Nombre Estudiante</Data></Cell>
                        <Cell><Data ss:Type='String'>Apellido Estudiante</Data></Cell>
                        <Cell><Data ss:Type='String'>RUT Cliente</Data></Cell>
                        <Cell><Data ss:Type='String'>Nombre Empresa</Data></Cell>
                    </Row>";

    // Contador para las filas
    $i = 1;
    $last_rut_cliente = null;
    $estudiantes_cliente = 0;
    $totals_clientes = [];

    // Agregar los estudiantes por cliente al Excel
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_assoc($result)) {
            $excel_content .= "<Row ss:StyleID='s2'>";
            $excel_content .= "<Cell><Data ss:Type='Number'>" . $i . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['rut_persona'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['nombre'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['apellido'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['rut_cliente'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['nombre_empresa'] . "</Data></Cell>";
            $excel_content .= "</Row>";

            // Incrementamos el contador de estudiantes del cliente actual
            if ($last_rut_cliente != $row['rut_cliente']) {
                $estudiantes_cliente = 1;
            } else {
                $estudiantes_cliente++;
            }

            // Registramos el total de estudiantes del cliente actual
            $totals_clientes[$row['rut_cliente']] = $estudiantes_cliente;

            $last_rut_cliente = $row['rut_cliente'];
            $i++;
        }
    } else {
        $excel_content .= "<Row ss:StyleID='s2'><Cell ss:MergeAcross='5'><Data ss:Type='String'>No hay estudiantes.</Data></Cell></Row>";
    }

    // Agregar el total de estudiantes al Excel
    $excel_content .= "<Row ss:StyleID='s2'>";
    $excel_content .= "<Cell ss:MergeAcross='4'><Data ss:Type='String'>Total Estudiantes:</Data></Cell>";
    $excel_content .= "<Cell><Data ss:Type='Number'>" . $total_estudiantes . "</Data></Cell>";
    $excel_content .= "</Row>";

    // Agregar el total de estudiantes por cliente
    foreach ($totals_clientes as $rut_cliente => $total) {
        $excel_content .= "<Row ss:StyleID='s2'>";
        $excel_content .= "<Cell ss:MergeAcross='4'><Data ss:Type='String'>Total Estudiantes Cliente " . $rut_cliente . ":</Data></Cell>";
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
<html>

<head>
    <meta charset="utf-8">
    <title>App Contador</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="css/main.css">
    <!--datables CSS básico-->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
    <!--datables estilo bootstrap 4 CSS-->
    <link rel="stylesheet" type="text/css" href="assets/datatables/DataTables-1.10.18/css/dataTables.bootstrap4.min.css">
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
            <li><a href="relator.php">Relator</a></li>
            <li><a class="active" href="#">Estudiantes</a></li>
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
            <h3>Administrar Estudiantes</h3>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <button id="btnNuevo" type="button" class="btn btn-info" data-toggle="modal" data-target="#modalAgregar">Agregar <i class="material-icons">library_add</i></button>
                    <button id="btnGenerarReportes" type="button" class="btn btn-success" data-toggle="modal" data-target="#modalGenerarReportes">Generar Reportes <i class="material-icons">assessment</i></button>
                    <button id="btnAgregarCapacitacion" type="button" class="btn btn-warning" data-toggle="modal" data-target="#modalAgregarCapacitacion">Agregar Capacitación a Estudiante <i class="material-icons">add_circle</i></button>
                </div>
            </div>
        </div>

        <!-- Modal Agregar -->
        <div class="modal fade" id="modalAgregar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Agregar Estudiante</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formAgregar" method="post" action="estudiantes.php">
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
                                <label for="cargo">Cargo</label>
                                <input type="text" class="form-control" id="cargo" name="cargo" required>
                            </div>
                            <div class="form-group">
                                <label for="nivel_escolaridad">Nivel Escolaridad</label>
                                <input type="text" class="form-control" id="nivel_escolaridad" name="nivel_escolaridad" required>
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
                                <label for="rut_cliente">RUT Cliente</label>
                                <input type="text" class="form-control" id="rut_cliente" name="rut_cliente" required>
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
                        <h5 class="modal-title" id="exampleModalLabel">Actualizar Estudiante</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formActualizar" method="post" action="estudiantes.php">
                            <input type="hidden" name="actualizar" value="1">
                            <div class="form-group">
                                <label for="rutActualizar">RUT</label>
                                <input type="text" class="form-control" id="rutActualizar" name="rutActualizar" required>
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
                                <label for="cargoActualizar">Cargo</label>
                                <input type="text" class="form-control" id="cargoActualizar" name="cargoActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="nivel_escolaridadActualizar">Nivel Escolaridad</label>
                                <input type="text" class="form-control" id="nivel_escolaridadActualizar" name="nivel_escolaridadActualizar" required>
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
                                <label for="rut_clienteActualizar">RUT Cliente</label>
                                <input type="text" class="form-control" id="rut_clienteActualizar" name="rut_clienteActualizar" required>
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
                        <h5 class="modal-title" id="exampleModalLabel">Eliminar Estudiante</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>¿Está seguro de que desea eliminar este estudiante?</p>
                        <form id="formEliminar" method="get" action="estudiantes.php">
                            <input type="hidden" name="eliminar" id="eliminarRut" value="">
                            <button type="submit" class="btn btn-danger" id="confirmarEliminar">Eliminar</button>
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
                        <form id="formGenerarReporteEstudiantes" method="post" action="estudiantes.php">
                            <input type="hidden" name="generarReporteEstudiantes" value="1">
                            <button type="submit" class="btn btn-primary">Generar Reporte de Estudiantes por Cliente</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Agregar Capacitación -->
        <div class="modal fade" id="modalAgregarCapacitacion" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Agregar Capacitación a Estudiante</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formAgregarCapacitacion" method="post" action="estudiantes.php">
                            <input type="hidden" name="agregarCapacitacion" value="1">
                            <div class="form-group">
                                <label for="rutEstudiante">RUT Estudiante:</label>
                                <select class="form-control" id="rutEstudiante" name="rutEstudiante">
                                    <?php
                                    // Obtener los estudiantes de la base de datos
                                    include('conexion.php');
                                    $queryEstudiantes = "SELECT rut_persona, nombre FROM estudiante";
                                    $resultEstudiantes = pg_query($conn, $queryEstudiantes);

                                    if (pg_num_rows($resultEstudiantes) > 0) {
                                        while ($rowEstudiante = pg_fetch_assoc($resultEstudiantes)) {
                                            echo "<option value='" . $rowEstudiante["rut_persona"] . "'>" . $rowEstudiante["rut_persona"] . " - " . $rowEstudiante["nombre"] . "</option>";
                                        }
                                    } else {
                                        echo "<option value=''>No hay estudiantes disponibles</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="capacitacion">Capacitación:</label>
                                <select class="form-control" id="capacitacion" name="capacitacion">
                                    <?php
                                    // Obtener las capacitaciones de la base de datos
                                    $queryCapacitaciones = "SELECT id_capacitacion, nombre_capacitacion FROM capacitacion";
                                    $resultCapacitaciones = pg_query($conn, $queryCapacitaciones);

                                    if (pg_num_rows($resultCapacitaciones) > 0) {
                                        while ($rowCapacitacion = pg_fetch_assoc($resultCapacitaciones)) {
                                            echo "<option value='" . $rowCapacitacion["id_capacitacion"] . "'>" . $rowCapacitacion["nombre_capacitacion"] . "</option>";
                                        }
                                    } else {
                                        echo "<option value=''>No hay capacitaciones disponibles</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Agregar Capacitación</button>
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
                                <option value="rut_persona">RUT</option>
                                <option value="nombre">Nombre</option>
                                <option value="apellido">Apellido</option>
                                <option value="email">Email</option>
                                <option value="genero">Género</option>
                                <option value="cargo">Cargo</option>
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
                        <table id="tablaEstudiantes" class="table table-striped table-bordered table-condensed" style="width:100%">
                            <thead class="text-center">
                                <tr>
                                    <th>RUT</th>
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                    <th>Cargo</th>
                                    <th>Nivel_escolaridad</th>
                                    <th>Email</th>
                                    <th>Telefono</th>
                                    <th>Fecha_nacimiento</th>
                                    <th>Direccion</th>
                                    <th>Genero</th>
                                    <th>RUT Cliente</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <?php
                                // Conexion a la base de datos
                                include('conexion.php');

                                // Insertar estudiante
                                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear'])) {
                                    $rut_persona = $_POST['rut'];
                                    $nombre = $_POST['nombre'];
                                    $apellido = $_POST['apellido'];
                                    $email = $_POST['email'];
                                    $telefono = $_POST['telefono'];
                                    $fecha_nacimiento = $_POST['fecha_nacimiento'];
                                    $direccion = $_POST['direccion'];
                                    $genero = $_POST['genero'];
                                    $cargo = $_POST['cargo'];
                                    $nivel_escolaridad = $_POST['nivel_escolaridad'];
                                    $rut_cliente = $_POST['rut_cliente'];

                                    // Inserta el estudiante en la tabla persona
                                    $insert_persona = "INSERT INTO persona (rut_persona, nombre, apellido, email, telefono, fecha_nacimiento, direccion, genero)
                                                       VALUES ('$rut_persona', '$nombre', '$apellido', '$email', '$telefono', '$fecha_nacimiento', '$direccion', '$genero')";

                                    // Inserta el estudiante en la tabla estudiante
                                    $insert_estudiante = "INSERT INTO estudiante (rut_persona, nombre, apellido, email, telefono, fecha_nacimiento, direccion, genero, cargo, nivel_escolaridad, rut_cliente)
                                                         VALUES ('$rut_persona', '$nombre', '$apellido', '$email', '$telefono', '$fecha_nacimiento', '$direccion', '$genero', '$cargo', '$nivel_escolaridad', '$rut_cliente')";

                                    if (pg_query($conn, $insert_persona) && pg_query($conn, $insert_estudiante)) {
                                        echo "<script>alert('Nuevo estudiante creado exitosamente.'); window.location.href='estudiantes.php';</script>";
                                    } else {
                                        echo "<script>alert('Error: " . pg_last_error($conn) . "');</script>";
                                    }
                                }

                                // Eliminar estudiante
                                if (isset($_GET['eliminar'])) {
                                    $rut_persona = $_GET['eliminar'];

                                    // Elimina el estudiante de la tabla estudiante
                                    $delete_estudiante_query = "DELETE FROM estudiante WHERE rut_persona = '$rut_persona'";

                                    // Elimina el estudiante de la tabla persona
                                    $delete_persona_query = "DELETE FROM persona WHERE rut_persona = '$rut_persona'";

                                    if (pg_query($conn, $delete_estudiante_query) && pg_query($conn, $delete_persona_query)) {
                                        echo "<script>alert('Estudiante eliminado exitosamente.'); window.location.href='estudiantes.php';</script>";
                                    } else {
                                        echo "<script>alert('Error: " . pg_last_error($conn) . "');</script>";
                                    }
                                }

                                // Actualizar estudiante
                                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar'])) {
                                    $rut_persona = $_POST['rutActualizar'];
                                    $nombre = $_POST['nombreActualizar'];
                                    $apellido = $_POST['apellidoActualizar'];
                                    $email = $_POST['emailActualizar'];
                                    $telefono = $_POST['telefonoActualizar'];
                                    $fecha_nacimiento = $_POST['fecha_nacimientoActualizar'];
                                    $direccion = $_POST['direccionActualizar'];
                                    $genero = $_POST['generoActualizar'];
                                    $cargo = $_POST['cargoActualizar'];
                                    $nivel_escolaridad = $_POST['nivel_escolaridadActualizar'];
                                    $rut_cliente = $_POST['rut_clienteActualizar'];

                                    $update_persona = "UPDATE persona SET 
                                                       nombre='$nombre',
                                                       apellido='$apellido',
                                                       email='$email',
                                                       telefono='$telefono',
                                                       fecha_nacimiento='$fecha_nacimiento',
                                                       direccion='$direccion',
                                                       genero='$genero'
                                                       WHERE rut_persona='$rut_persona'";

                                    $update_estudiante = "UPDATE estudiante SET 
                                                          nombre='$nombre',
                                                          apellido='$apellido',
                                                          email='$email',
                                                          telefono='$telefono',
                                                          fecha_nacimiento='$fecha_nacimiento',
                                                          direccion='$direccion',
                                                          genero='$genero',
                                                          cargo='$cargo',
                                                          nivel_escolaridad='$nivel_escolaridad',
                                                          rut_cliente='$rut_cliente'
                                                          WHERE rut_persona='$rut_persona'";

                                    if (pg_query($conn, $update_persona) && pg_query($conn, $update_estudiante)) {
                                        echo "<script>alert('Estudiante actualizado exitosamente.'); window.location.href='estudiantes.php';</script>";
                                    } else {
                                        echo "<script>alert('Error: " . pg_last_error($conn) . "');</script>";
                                    }
                                }

                                // Agregar capacitacion al estudiante
                                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregarCapacitacion'])) {
                                    $rut_persona = $_POST['rutEstudiante'];
                                    $id_capacitacion = $_POST['capacitacion'];

                                    $insert_realiza = "INSERT INTO realiza (rut_persona, id_capacitacion) VALUES ('$rut_persona', '$id_capacitacion')";

                                    if (pg_query($conn, $insert_realiza)) {
                                        echo "<script>alert('Capacitación agregada al estudiante exitosamente.'); window.location.href='estudiantes.php';</script>";
                                    } else {
                                        echo "<script>alert('Error: " . pg_last_error($conn) . "');</script>";
                                    }
                                }

                                // Consulta para obtener los datos de los estudiantes
                                $query = "SELECT e.*, p.rut_persona, p.nombre, p.apellido FROM estudiante e
                                          LEFT JOIN persona p ON e.rut_persona = p.rut_persona";
                                $result = pg_query($conn, $query);

                                // Consulta para obtener el total de estudiantes
                                $total_query = "SELECT COUNT(*) AS total FROM estudiante";
                                $total_result = pg_query($conn, $total_query);
                                $total_row = pg_fetch_assoc($total_result);
                                $total_estudiantes = $total_row['total'];

                                // Consulta para obtener el total de estudiantes por rut_cliente
                                $clientes_query = "SELECT rut_cliente, COUNT(*) AS total FROM estudiante GROUP BY rut_cliente";
                                $clientes_result = pg_query($conn, $clientes_query);

                                if (pg_num_rows($result) > 0) {
                                    while($row = pg_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>" . $row["rut_persona"] . "</td>";
                                        echo "<td>" . $row["nombre"] . "</td>";
                                        echo "<td>" . $row["apellido"] . "</td>";
                                        echo "<td>" . $row["cargo"] . "</td>";
                                        echo "<td>" . $row["nivel_escolaridad"] . "</td>";
                                        echo "<td>" . $row["email"] . "</td>";
                                        echo "<td>" . $row["telefono"] . "</td>";
                                        echo "<td>" . $row["fecha_nacimiento"] . "</td>";
                                        echo "<td>" . $row["direccion"] . "</td>";
                                        echo "<td>" . $row["genero"] . "</td>";
                                        echo "<td>" . $row["rut_cliente"] . "</td>";
                                        echo "<td><a href='#' class='btn btn-danger btn-sm' data-toggle='modal' data-target='#modalEliminar' onclick='setEliminarRut(\"" . $row["rut_persona"] . "\")'>Eliminar</a> | 
                                              <a href='#' class='btn btn-primary btn-sm' data-toggle='modal' data-target='#modalActualizar' onclick='setActualizarDatos(" . json_encode($row) . ")'>Editar</a></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='13'>No hay estudiantes</td></tr>";
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
            <h3>Admin Contador</h3>
            <p>Permite ordenar la administracion de la informacion relacionada con los estudiantes.</p>
        </div>
        <div class="footer-bottom">
            <p>copyright © <a href="#">UNAP</a> </p>
        </div>
    </footer>
    <!-- jQuery, Popper.js, Bootstrap JS -->
    <script src="assets/jquery/jquery-3.3.1.min.js"></script>
    <script src="assets/popper/popper.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function setEliminarRut(rut) {
            document.getElementById('eliminarRut').value = rut;
        }

        function setActualizarDatos(estudiante) {
            document.getElementById('rutActualizar').value = estudiante.rut_persona;
            document.getElementById('nombreActualizar').value = estudiante.nombre;
            document.getElementById('apellidoActualizar').value = estudiante.apellido;
            document.getElementById('cargoActualizar').value = estudiante.cargo;
            document.getElementById('nivel_escolaridadActualizar').value = estudiante.nivel_escolaridad;
            document.getElementById('emailActualizar').value = estudiante.email;
            document.getElementById('telefonoActualizar').value = estudiante.telefono;
            document.getElementById('fecha_nacimientoActualizar').value = estudiante.fecha_nacimiento;
            document.getElementById('direccionActualizar').value = estudiante.direccion;
            document.getElementById('generoActualizar').value = estudiante.genero;
            document.getElementById('rut_clienteActualizar').value = estudiante.rut_cliente;
        }

        // Nueva función para agregar cliente y capacitación
        document.getElementById('formAgregar').addEventListener('submit', function(event) {
            event.preventDefault();
            const rut = document.getElementById('rut').value;
            const telefono = document.getElementById('telefono').value;
            const fechaNacimiento = document.getElementById('fecha_nacimiento').value;

            if (!rut.match(/^\d{9}$/)) {
                alert('El RUT debe tener 9 dígitos.');
                return;
            }
            if (!telefono.match(/^\d{9}$/)) {
                alert('El teléfono debe tener 9 dígitos.');
                return;
            }
            if (new Date(fechaNacimiento) >= new Date('2005-01-01')) {
                alert('La fecha de nacimiento debe ser menor a 2005.');
                return;
            }

            const formData = new FormData(this);
            fetch('relator.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert('Nuevo relator creado exitosamente.');
                window.location.href = 'relator.php';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al crear relator.');
            });
        });

        // Función para actualizar cliente
        document.getElementById('formActualizar').addEventListener('submit', function(event) {
            event.preventDefault();
            const rut = document.getElementById('rutActualizar').value;
            const telefono = document.getElementById('telefonoActualizar').value;
            const fechaNacimiento = document.getElementById('fecha_nacimientoActualizar').value;

            if (!rut.match(/^\d{9}$/)) {
                alert('El RUT debe tener 9 dígitos.');
                return;
            }
            if (!telefono.match(/^\d{9}$/)) {
                alert('El teléfono debe tener 9 dígitos.');
                return;
            }
            if (new Date(fechaNacimiento) >= new Date('2005-01-01')) {
                alert('La fecha de nacimiento debe ser menor a 2005.');
                return;
            }

            const formData = new FormData(this);
            fetch('relator.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert('Relator actualizado exitosamente.');
                window.location.href = 'relator.php';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar relator.');
            });
        });

        // Configuración del gráfico y botón de descarga
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('reporteGrafico').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Total de Estudiantes'],
                    datasets: [{
                        label: '# de Estudiantes',
                        data: [<?php echo $total_estudiantes; ?>],
                        backgroundColor: ['rgba(54, 162, 235, 0.2)'],
                        borderColor: ['rgba(54, 162, 235, 1)'],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            document.getElementById('downloadChart').addEventListener('click', function() {
                const link = document.createElement('a');
                link.href = chart.toBase64Image();
                link.download = 'reporte_estudiantes.png';
                link.click();
            });
        });

        // Filtro de búsqueda
        const searchInput = document.getElementById('searchInput');
        const searchBy = document.getElementById('searchBy');
        const tableBody = document.getElementById('tableBody');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const selectedFilter = searchBy.value;

            const rows = tableBody.querySelectorAll('tr');
            rows.forEach(row => {
                const rut = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                const name = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const apellido = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                const email = row.querySelector('td:nth-child(6)').textContent.toLowerCase();
                const genero = row.querySelector('td:nth-child(10)').textContent.toLowerCase();
                const cargo = row.querySelector('td:nth-child(4)').textContent.toLowerCase();

                if (searchTerm === '') {
                    row.style.display = '';
                } else if (selectedFilter === 'rut_persona' && rut.includes(searchTerm)) {
                    row.style.display = '';
                } else if (selectedFilter === 'nombre' && name.includes(searchTerm)) {
                    row.style.display = '';
                } else if (selectedFilter === 'apellido' && apellido.includes(searchTerm)) {
                    row.style.display = '';
                } else if (selectedFilter === 'email' && email.includes(searchTerm)) {
                    row.style.display = '';
                } else if (selectedFilter === 'genero' && genero.includes(searchTerm)) {
                    row.style.display = '';
                } else if (selectedFilter === 'cargo' && cargo.includes(searchTerm)) {
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