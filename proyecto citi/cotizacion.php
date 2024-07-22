<?php
// Conexion a la base de datos
include('conexion.php');

// Insertar cotización
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear'])) {
    $nro_cotizacion = $_POST['nro_cotizacion'];
    $rut_cliente = $_POST['rut_cliente'];
    $rut_persona = $_POST['rut_persona'];
    $fecha_creacion = $_POST['fecha_creacion'];
    $fecha_vencimiento = $_POST['fecha_vencimiento'];
    $descripcion = $_POST['descripcion'];
    $total = $_POST['total'];
    $condiciones_pago = $_POST['condiciones_pago'];

    $insert_query = "INSERT INTO cotizacion (nro_cotizacion, rut_cliente, rut_persona, fecha_creacion, fecha_vencimiento, descripcion, total, condiciones_pago)
                     VALUES ('$nro_cotizacion', '$rut_cliente', '$rut_persona', '$fecha_creacion', '$fecha_vencimiento', '$descripcion', '$total', '$condiciones_pago')";

    if (pg_query($conn, $insert_query)) {
        echo "<script>alert('Nueva cotización creada exitosamente.'); window.location.href='cotizacion.php';</script>";
    } else {
        echo "<script>alert('Error: " . $insert_query . "<br>" . pg_last_error($conn) . "');</script>";
    }
}

// Eliminar cotización
if (isset($_GET['eliminar'])) {
    $nro_cotizacion = $_GET['eliminar'];

    $delete_query = "DELETE FROM cotizacion WHERE nro_cotizacion = '$nro_cotizacion'";
    if (pg_query($conn, $delete_query)) {
        echo "<script>alert('Cotización eliminada exitosamente.'); window.location.href='cotizacion.php';</script>";
    } else {
        echo "<script>alert('Error: " . $delete_query . "<br>" . pg_last_error($conn) . "');</script>";
    }
}

// Actualizar cotización
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar'])) {
    $nro_cotizacion = $_POST['nro_cotizacionActualizar'];
    $rut_cliente = $_POST['rut_clienteActualizar'];
    $rut_persona = $_POST['rut_personaActualizar'];
    $fecha_creacion = $_POST['fecha_creacionActualizar'];
    $fecha_vencimiento = $_POST['fecha_vencimientoActualizar'];
    $descripcion = $_POST['descripcionActualizar'];
    $total = $_POST['totalActualizar'];
    $condiciones_pago = $_POST['condiciones_pagoActualizar'];

    $update_query = "UPDATE cotizacion SET 
                    rut_cliente='$rut_cliente',
                    rut_persona='$rut_persona',
                    fecha_creacion='$fecha_creacion',
                    fecha_vencimiento='$fecha_vencimiento',
                    descripcion='$descripcion',
                    total='$total',
                    condiciones_pago='$condiciones_pago'
                    WHERE nro_cotizacion='$nro_cotizacion'";

    if (pg_query($conn, $update_query)) {
        echo "<script>alert('Cotización actualizada exitosamente.'); window.location.href='cotizacion.php';</script>";
    } else {
        echo "<script>alert('Error: " . $update_query . "<br>" . pg_last_error($conn) . "');</script>";
    }
}

// Agregar estado a cotización
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar_estado'])) {
    $id_estado = $_POST['id_estado'];
    $nro_cotizacion = $_POST['nro_cotizacion'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];

    $insert_halla = "INSERT INTO halla (id_estado, nro_cotizacion, fecha_inicio, fecha_fin) VALUES ('$id_estado', '$nro_cotizacion', '$fecha_inicio', '$fecha_fin')";

    if (pg_query($conn, $insert_halla)) {
        echo "<script>alert('Estado agregado a la cotización exitosamente.'); window.location.href='cotizacion.php';</script>";
    } else {
        echo "<script>alert('Error: " . pg_last_error($conn) . "');</script>";
    }
}

// Consulta para obtener los datos de las cotizaciones
$query = "SELECT * FROM cotizacion ORDER BY nro_cotizacion ASC";
$result = pg_query($conn, $query);

// Consulta para obtener los datos para el reporte
$reporte_query = "SELECT to_char(fecha_creacion, 'Month') AS mes, COUNT(*) AS cantidad FROM cotizacion GROUP BY mes ORDER BY mes";
$reporte_result = pg_query($conn, $reporte_query);
$reporte_data = [];
while ($row = pg_fetch_assoc($reporte_result)) {
    $reporte_data[] = $row;
}

// Generar reporte de cotizaciones con rango de fechas
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generarReporte'])) {
    // Obtener las fechas de inicio y fin del rango
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];

    // Consulta para obtener las cotizaciones en el rango de fechas
    $query = "SELECT 
              c.nro_cotizacion,
              c.fecha_creacion,
              c.fecha_vencimiento,
              c.total,
              cl.rut_cliente,
              cl.nombre_empresa,
              a.rut_persona,
              a.nombre AS nombre_administrador,
              a.email AS email_administrador
            FROM cotizacion c
            JOIN cliente cl ON c.rut_cliente = cl.rut_cliente
            JOIN administrador a ON c.rut_persona = a.rut_persona
            WHERE c.fecha_creacion >= '$fecha_inicio' AND c.fecha_creacion <= '$fecha_fin'
            ORDER BY c.nro_cotizacion";
    $result = pg_query($conn, $query);

    // Calcular el total de cotizaciones, el total por administrador y el total de ingresos
    $total_cotizaciones = pg_num_rows($result);
    $total_cotizaciones_administrador = [];
    $total_ingresos = 0;

    // Crear el archivo Excel
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="Reporte_Cotizaciones.xls"');

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
            <Worksheet ss:Name='Reporte Cotizaciones'>
                <Table>
                    <Row ss:StyleID='s1'>
                        <Cell ss:MergeAcross='9'><Data ss:Type='String'>Reporte de Cotizaciones e Ingresos de la Empresa CITI</Data></Cell>
                    </Row>
                    <Row ss:StyleID='s1'>
                        <Cell ss:MergeAcross='9'><Data ss:Type='String'>EMPRESA: CITI</Data></Cell>
                    </Row>
                    <Row ss:StyleID='s1'>
                        <Cell ss:MergeAcross='9'><Data ss:Type='String'>FECHA: " . date('d/m/Y') . "</Data></Cell>
                    </Row>
                    <Row ss:StyleID='s1'>
                        <Cell><Data ss:Type='String'>Nro Cotizacion</Data></Cell>
                        <Cell><Data ss:Type='String'>Fecha Creacion</Data></Cell>
                        <Cell><Data ss:Type='String'>Fecha Vencimiento</Data></Cell>
                        <Cell><Data ss:Type='String'>Total</Data></Cell>
                        <Cell><Data ss:Type='String'>RUT Cliente</Data></Cell>
                        <Cell><Data ss:Type='String'>Nombre Empresa</Data></Cell>
                        <Cell><Data ss:Type='String'>RUT Persona</Data></Cell>
                        <Cell><Data ss:Type='String'>Nombre Administrador</Data></Cell>
                        <Cell><Data ss:Type='String'>Email Administrador</Data></Cell>
                    </Row>";

    // Agregar las cotizaciones al Excel
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_assoc($result)) {
            $excel_content .= "<Row ss:StyleID='s2'>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['nro_cotizacion'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['fecha_creacion'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['fecha_vencimiento'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='Number'>" . $row['total'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['rut_cliente'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['nombre_empresa'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['rut_persona'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['nombre_administrador'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['email_administrador'] . "</Data></Cell>";
            $excel_content .= "</Row>";

            // Contar las cotizaciones por administrador
            if (!isset($total_cotizaciones_administrador[$row['rut_persona']])) {
                $total_cotizaciones_administrador[$row['rut_persona']] = 0;
            }
            $total_cotizaciones_administrador[$row['rut_persona']] += 1;

            // Sumar el total de la cotización a los ingresos totales
            $total_ingresos += $row['total'];
        }
    } else {
        $excel_content .= "<Row ss:StyleID='s2'><Cell ss:MergeAcross='9'><Data ss:Type='String'>No hay cotizaciones en el rango de fechas seleccionado.</Data></Cell></Row>";
    }

    // Agregar los totales al Excel
    $excel_content .= "<Row ss:StyleID='s1'>";
    $excel_content .= "<Cell ss:MergeAcross='8'><Data ss:Type='String'>Total de Cotizaciones:</Data></Cell>";
    $excel_content .= "<Cell><Data ss:Type='Number'>" . $total_cotizaciones . "</Data></Cell>";
    $excel_content .= "</Row>";
    $excel_content .= "<Row ss:StyleID='s1'>";
    $excel_content .= "<Cell ss:MergeAcross='8'><Data ss:Type='String'>Ingresos Totales:</Data></Cell>";
    $excel_content .= "<Cell><Data ss:Type='Number'>" . $total_ingresos . "</Data></Cell>";
    $excel_content .= "</Row>";

    // Agregar totales por administrador
    $excel_content .= "<Row ss:StyleID='s1'>";
    $excel_content .= "<Cell ss:MergeAcross='8'><Data ss:Type='String'>Total de Cotizaciones por Administrador:</Data></Cell>";
    $excel_content .= "</Row>";
    foreach ($total_cotizaciones_administrador as $rut_persona => $cantidad) {
        $excel_content .= "<Row ss:StyleID='s2'>";
        $excel_content .= "<Cell ss:MergeAcross='8'><Data ss:Type='String'>RUT " . $rut_persona . ":</Data></Cell>";
        $excel_content .= "<Cell><Data ss:Type='Number'>" . $cantidad . "</Data></Cell>";
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
    <title>App contador</title>
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
    <link rel="stylesheet" type="text/css"
        href="assets/datatables/DataTables-1.10.18/css/dataTables.bootstrap4.min.css">
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
            <li><a href="estudiantes.php">Estudiantes</a></li>
            <li><a href="capacitacion.php">Capacitacion</a></li>
            <li><a href="materiales.php">Materiales</a></li>
            <li><a href="evaluaciones.php">Evaluaciones</a></li>
            <li><a href="ordenpago.php">Orden pago</a></li>
            <li><a class="active" href="#">Cotizacion</a></li>
            <li><a href="estado.php">Estado</a></li>
            <li><a href="index.php" onclick="logout()">Salir</a></li>
        </ul>
    </nav>
    <main>
        <div class="center">
            <h3>Administrar Cotización</h3>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <button id="btnNuevo" type="button" class="btn btn-info" data-toggle="modal" data-target="#modalAgregar">Agregar <i class="material-icons">library_add</i></button>
                    <button id="btnGenerarReportes" type="button" class="btn btn-success" data-toggle="modal" data-target="#modalGenerarReportes">Generar Reportes <i class="material-icons">assessment</i></button>
                    <button id="btnAgregarEstado" type="button" class="btn btn-warning" data-toggle="modal" data-target="#modalAgregarEstado">Agregar Estado <i class="material-icons">add_circle</i></button>
                </div>
            </div>
        </div>

        <!-- Modal Agregar -->
        <div class="modal fade" id="modalAgregar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Agregar Cotización</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formAgregar" method="post" action="cotizacion.php">
                            <input type="hidden" name="crear" value="1">
                            <div class="form-group">
                                <label for="nro_cotizacion">Nro de Cotización</label>
                                <input type="text" class="form-control" id="nro_cotizacion" name="nro_cotizacion" required>
                            </div>
                            <div class="form-group">
                                <label for="rut_cliente">RUT Cliente</label>
                                <input type="text" class="form-control" id="rut_cliente" name="rut_cliente" required>
                            </div>
                            <div class="form-group">
                                <label for="rut_persona">RUT Persona</label>
                                <input type="text" class="form-control" id="rut_persona" name="rut_persona" required>
                            </div>
                            <div class="form-group">
                                <label for="fecha_creacion">Fecha de Creación</label>
                                <input type="date" class="form-control" id="fecha_creacion" name="fecha_creacion" required>
                            </div>
                            <div class="form-group">
                                <label for="fecha_vencimiento">Fecha de Vencimiento</label>
                                <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" required>
                            </div>
                            <div class="form-group">
                                <label for="descripcion">Descripción</label>
                                <input type="text" class="form-control" id="descripcion" name="descripcion" required>
                            </div>
                            <div class="form-group">
                                <label for="total">Total</label>
                                <input type="number" class="form-control" id="total" name="total" required>
                            </div>
                            <div class="form-group">
                                <label for="condiciones_pago">Condiciones de Pago</label>
                                <input type="text" class="form-control" id="condiciones_pago" name="condiciones_pago" required>
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
                        <h5 class="modal-title" id="exampleModalLabel">Actualizar Cotización</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formActualizar" method="post" action="cotizacion.php">
                            <input type="hidden" name="actualizar" value="1">
                            <div class="form-group">
                                <label for="nro_cotizacionActualizar">Nro de Cotización</label>
                                <input type="text" class="form-control" id="nro_cotizacionActualizar" name="nro_cotizacionActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="rut_clienteActualizar">RUT Cliente</label>
                                <input type="text" class="form-control" id="rut_clienteActualizar" name="rut_clienteActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="rut_personaActualizar">RUT Persona</label>
                                <input type="text" class="form-control" id="rut_personaActualizar" name="rut_personaActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="fecha_creacionActualizar">Fecha de Creación</label>
                                <input type="date" class="form-control" id="fecha_creacionActualizar" name="fecha_creacionActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="fecha_vencimientoActualizar">Fecha de Vencimiento</label>
                                <input type="date" class="form-control" id="fecha_vencimientoActualizar" name="fecha_vencimientoActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="descripcionActualizar">Descripción</label>
                                <input type="text" class="form-control" id="descripcionActualizar" name="descripcionActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="totalActualizar">Total</label>
                                <input type="number" class="form-control" id="totalActualizar" name="totalActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="condiciones_pagoActualizar">Condiciones de Pago</label>
                                <input type="text" class="form-control" id="condiciones_pagoActualizar" name="condiciones_pagoActualizar" required>
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
                        <h5 class="modal-title" id="exampleModalLabel">Eliminar Cotización</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>¿Está seguro de que desea eliminar esta cotización?</p>
                        <form id="formEliminar" method="get" action="cotizacion.php">
                            <input type="hidden" name="eliminar" id="eliminarNroCotizacion" value="">
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
                        <form id="formGenerarReporte" method="post" action="cotizacion.php">
                            <input type="hidden" name="generarReporte" value="1">
                            <div class="form-group">
                                <label for="fecha_inicio">Fecha Inicio:</label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                            </div>
                            <div class="form-group">
                                <label for="fecha_fin">Fecha Fin:</label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Generar Reporte</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Agregar Estado -->
        <div class="modal fade" id="modalAgregarEstado" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Agregar Estado a Cotización</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formAgregarEstado" method="post" action="cotizacion.php">
                            <input type="hidden" name="agregar_estado" value="1">
                            <div class="form-group">
                                <label for="id_estado">Tipo de Estado:</label>
                                <select class="form-control" id="id_estado" name="id_estado" required>
                                    <?php
                                    // Obtener los estados de la base de datos
                                    include('conexion.php');
                                    $query_estados = "SELECT id_estado, tipo_estado FROM estado";
                                    $result_estados = pg_query($conn, $query_estados);
                                    while ($row_estado = pg_fetch_assoc($result_estados)) {
                                        echo "<option value='" . $row_estado['id_estado'] . "'>" . $row_estado['tipo_estado'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="nro_cotizacion">Nro de Cotización:</label>
                                <select class="form-control" id="nro_cotizacion" name="nro_cotizacion" required>
                                    <?php
                                    // Obtener las cotizaciones de la base de datos
                                    include('conexion.php');
                                    $query_cotizaciones = "SELECT nro_cotizacion FROM cotizacion";
                                    $result_cotizaciones = pg_query($conn, $query_cotizaciones);
                                    while ($row_cotizacion = pg_fetch_assoc($result_cotizaciones)) {
                                        echo "<option value='" . $row_cotizacion['nro_cotizacion'] . "'>" . $row_cotizacion['nro_cotizacion'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="fecha_inicio">Fecha Inicio:</label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                            </div>
                            <div class="form-group">
                                <label for="fecha_fin">Fecha Fin:</label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Agregar Estado</button>
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
                                <option value="nro_cotizacion">Nro de Cotización</option>
                                <option value="fecha_creacion">Fecha de Creación</option>
                                <option value="total">Total</option>
                                <option value="condiciones_pago">Condiciones de Pago</option>
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
                        <table id="tablaCotizaciones" class="table table-striped table-bordered table-condensed" style="width:100%">
                            <thead class="text-center">
                                <tr>
                                    <th>Nro Cotización</th>
                                    <th>RUT Cliente</th>
                                    <th>RUT Persona</th>
                                    <th>Fecha de Creación</th>
                                    <th>Fecha de Vencimiento</th>
                                    <th>Descripción</th>
                                    <th>Total</th>
                                    <th>Condiciones de Pago</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <?php
                                if (pg_num_rows($result) > 0) {
                                    while($row = pg_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>" . $row["nro_cotizacion"] . "</td>";
                                        echo "<td>" . $row["rut_cliente"] . "</td>";
                                        echo "<td>" . $row["rut_persona"] . "</td>";
                                        echo "<td>" . $row["fecha_creacion"] . "</td>";
                                        echo "<td>" . $row["fecha_vencimiento"] . "</td>";
                                        echo "<td>" . $row["descripcion"] . "</td>";
                                        echo "<td>" . $row["total"] . "</td>";
                                        echo "<td>" . $row["condiciones_pago"] . "</td>";
                                        echo "<td><a href='#' class='btn btn-danger btn-sm' data-toggle='modal' data-target='#modalEliminar' onclick='setEliminarNroCotizacion(\"" . $row["nro_cotizacion"] . "\")'>Eliminar</a> | 
                                              <a href='#' class='btn btn-primary btn-sm' data-toggle='modal' data-target='#modalActualizar' onclick='setActualizarDatos(" . json_encode($row) . ")'>Editar</a></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='9'>No hay cotizaciones</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </main>
    <footer>
        <div class="footer-content">
            <h3>Admin Contador</h3>
            <p>Permite ordenar la administracion de la informacion relacionada con las cotizaciones.</p>
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
        function setEliminarNroCotizacion(nro_cotizacion) {
            document.getElementById('eliminarNroCotizacion').value = nro_cotizacion;
        }

        function setActualizarDatos(cotizacion) {
            document.getElementById('nro_cotizacionActualizar').value = cotizacion.nro_cotizacion;
            document.getElementById('rut_clienteActualizar').value = cotizacion.rut_cliente;
            document.getElementById('rut_personaActualizar').value = cotizacion.rut_persona;
            document.getElementById('fecha_creacionActualizar').value = cotizacion.fecha_creacion;
            document.getElementById('fecha_vencimientoActualizar').value = cotizacion.fecha_vencimiento;
            document.getElementById('descripcionActualizar').value = cotizacion.descripcion;
            document.getElementById('totalActualizar').value = cotizacion.total;
            document.getElementById('condiciones_pagoActualizar').value = cotizacion.condiciones_pago;
        }

        // Configuración del gráfico y botón de descarga
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('reporteGrafico').getContext('2d');
            const labels = [];
            const data = [];
            <?php foreach ($reporte_data as $data) { ?>
                labels.push('<?php echo $data['mes']; ?>');
                data.push('<?php echo $data['cantidad']; ?>');
            <?php } ?>
            
            const mesesEspañol = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

            // Convertir nombres de meses a español
            for (let i = 0; i < labels.length; i++) {
                switch (labels[i].trim()) {
                    case 'January':
                        labels[i] = mesesEspañol[0];
                        break;
                    case 'February':
                        labels[i] = mesesEspañol[1];
                        break;
                    case 'March':
                        labels[i] = mesesEspañol[2];
                        break;
                    case 'April':
                        labels[i] = mesesEspañol[3];
                        break;
                    case 'May':
                        labels[i] = mesesEspañol[4];
                        break;
                    case 'June':
                        labels[i] = mesesEspañol[5];
                        break;
                    case 'July':
                        labels[i] = mesesEspañol[6];
                        break;
                    case 'August':
                        labels[i] = mesesEspañol[7];
                        break;
                    case 'September':
                        labels[i] = mesesEspañol[8];
                        break;
                    case 'October':
                        labels[i] = mesesEspañol[9];
                        break;
                    case 'November':
                        labels[i] = mesesEspañol[10];
                        break;
                    case 'December':
                        labels[i] = mesesEspañol[11];
                        break;
                }
            }

            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '# de Cotizaciones',
                        data: data,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
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
                link.download = 'reporte_cotizaciones.png';
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
                const nroCotizacion = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                const fechaCreacion = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
                const total = row.querySelector('td:nth-child(7)').textContent.toLowerCase();
                const condicionesPago = row.querySelector('td:nth-child(8)').textContent.toLowerCase();

                if (searchTerm === '') {
                    row.style.display = '';
                } else if (selectedFilter === 'nro_cotizacion' && nroCotizacion.includes(searchTerm)) {
                    row.style.display = '';
                } else if (selectedFilter === 'fecha_creacion' && fechaCreacion.includes(searchTerm)) {
                    row.style.display = '';
                } else if (selectedFilter === 'total' && total.includes(searchTerm)) {
                    row.style.display = '';
                } else if (selectedFilter === 'condiciones_pago' && condicionesPago.includes(searchTerm)) {
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