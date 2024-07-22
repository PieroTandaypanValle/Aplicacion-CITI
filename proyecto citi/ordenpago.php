<?php
// Conexion a la base de datos
include('conexion.php');

// Insertar orden de pago
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear'])) {
    $folio = $_POST['folio'];
    $rut_cliente = $_POST['rut_cliente'];
    $rut_persona = $_POST['rut_persona'];
    $fecha_pago = $_POST['fecha_pago'];
    $metodo_pago = $_POST['metodo_pago'];
    $monto = $_POST['monto'];
    $iva = $_POST['iva'];
    $detalles_transaccion = $_POST['detalles_transaccion'];

    $insert_query = "INSERT INTO orden_pago (folio, rut_cliente, rut_persona, fecha_pago, metodo_pago, monto, iva, detalles_transaccion)
                     VALUES ('$folio', '$rut_cliente', '$rut_persona', '$fecha_pago', '$metodo_pago', '$monto', '$iva', '$detalles_transaccion')";

    if (pg_query($conn, $insert_query)) {
        echo "<script>alert('Nueva orden de pago creada exitosamente.'); window.location.href='ordenpago.php';</script>";
    } else {
        echo "<script>alert('Error: " . $insert_query . "<br>" . pg_last_error($conn) . "');</script>";
    }
}

// Eliminar orden de pago
if (isset($_GET['eliminar'])) {
    $folio = $_GET['eliminar'];

    $delete_query = "DELETE FROM orden_pago WHERE folio = '$folio'";
    if (pg_query($conn, $delete_query)) {
        echo "<script>alert('Orden de pago eliminada exitosamente.'); window.location.href='ordenpago.php';</script>";
    } else {
        echo "<script>alert('Error: " . $delete_query . "<br>" . pg_last_error($conn) . "');</script>";
    }
}

// Actualizar orden de pago
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar'])) {
    $folio = $_POST['folioActualizar'];
    $rut_cliente = $_POST['rut_clienteActualizar'];
    $rut_persona = $_POST['rut_personaActualizar'];
    $fecha_pago = $_POST['fecha_pagoActualizar'];
    $metodo_pago = $_POST['metodo_pagoActualizar'];
    $monto = $_POST['montoActualizar'];
    $iva = $_POST['ivaActualizar'];
    $detalles_transaccion = $_POST['detalles_transaccionActualizar'];

    $update_query = "UPDATE orden_pago SET 
                    rut_cliente='$rut_cliente',
                    rut_persona='$rut_persona',
                    fecha_pago='$fecha_pago',
                    metodo_pago='$metodo_pago',
                    monto='$monto',
                    iva='$iva',
                    detalles_transaccion='$detalles_transaccion'
                    WHERE folio='$folio'";

    if (pg_query($conn, $update_query)) {
        echo "<script>alert('Orden de pago actualizada exitosamente.'); window.location.href='ordenpago.php';</script>";
    } else {
        echo "<script>alert('Error: " . $update_query . "<br>" . pg_last_error($conn) . "');</script>";
    }
}

// Agregar estado a orden de pago
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar_estado'])) {
    $id_estado = $_POST['id_estado'];
    $folio = $_POST['folio'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];

    $insert_ubica = "INSERT INTO ubica (id_estado, folio, fecha_inicio, fecha_fin) VALUES ('$id_estado', '$folio', '$fecha_inicio', '$fecha_fin')";

    if (pg_query($conn, $insert_ubica)) {
        echo "<script>alert('Estado agregado a la orden de pago exitosamente.'); window.location.href='ordenpago.php';</script>";
    } else {
        echo "<script>alert('Error: " . pg_last_error($conn) . "');</script>";
    }
}

// Consulta para obtener los datos de las ordenes de pago
$query = "SELECT * FROM orden_pago ORDER BY folio ASC";
$result = pg_query($conn, $query);

// Consulta para obtener los datos para el reporte
$reporte_query = "SELECT to_char(fecha_pago, 'Month') AS mes, COUNT(*) AS cantidad FROM orden_pago GROUP BY mes ORDER BY mes";
$reporte_result = pg_query($conn, $reporte_query);
$reporte_data = [];
while ($row = pg_fetch_assoc($reporte_result)) {
    $reporte_data[] = $row;

}

// Generar reporte de ordenes de pago
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generarReporte'])) {
    // Obtener las fechas de inicio y fin del rango
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];

    // Consulta para obtener las ordenes de pago en el rango de fechas
    $query = "SELECT 
              op.folio,
              op.fecha_pago,
              op.metodo_pago,
              op.monto,
              op.iva,
              a.rut_persona,
              a.nombre AS nombre_administrador,
              a.email AS email_administrador,
              c.rut_cliente,
              c.nombre_empresa
            FROM orden_pago op
            JOIN administrador a ON op.rut_persona = a.rut_persona
            JOIN cliente c ON op.rut_cliente = c.rut_cliente
            WHERE op.fecha_pago >= '$fecha_inicio' AND op.fecha_pago <= '$fecha_fin'
            ORDER BY op.folio";
    $result = pg_query($conn, $query);

    // Calcular el total de ingresos y total de ordenes de pago
    $total_ingresos = 0;
    $total_ordenes = pg_num_rows($result);

    // Crear el archivo Excel
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="Reporte_Ordenes_de_Pago.xls"');

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
            <Worksheet ss:Name='Reporte Ordenes de Pago'>
                <Table>
                    <Row ss:StyleID='s1'>
                        <Cell ss:MergeAcross='10'><Data ss:Type='String'>Reporte de Órdenes de Pago e Ingresos de la Empresa CITI</Data></Cell>
                    </Row>
                    <Row ss:StyleID='s1'>
                        <Cell ss:MergeAcross='10'><Data ss:Type='String'>EMPRESA: CITI</Data></Cell>
                    </Row>
                    <Row ss:StyleID='s1'>
                        <Cell ss:MergeAcross='10'><Data ss:Type='String'>FECHA: " . date('d/m/Y') . "</Data></Cell>
                    </Row>
                    <Row ss:StyleID='s1'>
                        <Cell><Data ss:Type='String'>Folio</Data></Cell>
                        <Cell><Data ss:Type='String'>Fecha Pago</Data></Cell>
                        <Cell><Data ss:Type='String'>Metodo Pago</Data></Cell>
                        <Cell><Data ss:Type='String'>Monto</Data></Cell>
                        <Cell><Data ss:Type='String'>IVA</Data></Cell>
                        <Cell><Data ss:Type='String'>RUT Persona</Data></Cell>
                        <Cell><Data ss:Type='String'>Nombre Administrador</Data></Cell>
                        <Cell><Data ss:Type='String'>Email Administrador</Data></Cell>
                        <Cell><Data ss:Type='String'>RUT Cliente</Data></Cell>
                        <Cell><Data ss:Type='String'>Nombre Empresa</Data></Cell>
                    </Row>";

    // Agregar las ordenes de pago al Excel
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_assoc($result)) {
            $excel_content .= "<Row ss:StyleID='s2'>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['folio'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['fecha_pago'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['metodo_pago'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='Number'>" . $row['monto'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='Number'>" . $row['iva'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['rut_persona'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['nombre_administrador'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['email_administrador'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['rut_cliente'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['nombre_empresa'] . "</Data></Cell>";
            $excel_content .= "</Row>";

            // Sumar el monto de la orden de pago al total de ingresos
            $total_ingresos += $row['monto'];
        }
    } else {
        $excel_content .= "<Row ss:StyleID='s2'><Cell ss:MergeAcross='10'><Data ss:Type='String'>No hay ordenes de pago en el rango de fechas seleccionado.</Data></Cell></Row>";
    }

    // Agregar los totales al Excel
    $excel_content .= "<Row ss:StyleID='s1'>";
    $excel_content .= "<Cell ss:MergeAcross='9'><Data ss:Type='String'>Total de ingresos:</Data></Cell>";
    $excel_content .= "<Cell><Data ss:Type='Number'>" . $total_ingresos . "</Data></Cell>";
    $excel_content .= "</Row>";
    $excel_content .= "<Row ss:StyleID='s1'>";
    $excel_content .= "<Cell ss:MergeAcross='9'><Data ss:Type='String'>Total de ordenes de pago:</Data></Cell>";
    $excel_content .= "<Cell><Data ss:Type='Number'>" . $total_ordenes . "</Data></Cell>";
    $excel_content .= "</Row>";

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
            <li><a class="active" href="#">Orden pago</a></li>
            <li><a href="cotizacion.php">Cotizacion</a></li>
            <li><a href="estado.php">Estado</a></li>
            <li><a href="index.php" onclick="logout()">Salir</a></li>
        </ul>
    </nav>
    <main>
        <div class="center">
            <h3>Administrar Orden de pago</h3>
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
                        <h5 class="modal-title" id="exampleModalLabel">Agregar Orden de Pago</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formAgregar" method="post" action="ordenpago.php">
                            <input type="hidden" name="crear" value="1">
                            <div class="form-group">
                                <label for="folio">Folio</label>
                                <input type="text" class="form-control" id="folio" name="folio" required>
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
                                <label for="fecha_pago">Fecha Pago</label>
                                <input type="date" class="form-control" id="fecha_pago" name="fecha_pago" required>
                            </div>
                            <div class="form-group">
                                <label for="metodo_pago">Método Pago</label>
                                <input type="text" class="form-control" id="metodo_pago" name="metodo_pago" required>
                            </div>
                            <div class="form-group">
                                <label for="monto">Monto</label>
                                <input type="number" class="form-control" id="monto" name="monto" required>
                            </div>
                            <div class="form-group">
                                <label for="iva">IVA</label>
                                <input type="number" step="0.01" class="form-control" id="iva" name="iva" required>
                            </div>
                            <div class="form-group">
                                <label for="detalles_transaccion">Detalles Transacción</label>
                                <input type="text" class="form-control" id="detalles_transaccion" name="detalles_transaccion" required>
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
                        <h5 class="modal-title" id="exampleModalLabel">Actualizar Orden de Pago</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formActualizar" method="post" action="ordenpago.php">
                            <input type="hidden" name="actualizar" value="1">
                            <div class="form-group">
                                <label for="folioActualizar">Folio</label>
                                <input type="text" class="form-control" id="folioActualizar" name="folioActualizar" required>
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
                                <label for="fecha_pagoActualizar">Fecha Pago</label>
                                <input type="date" class="form-control" id="fecha_pagoActualizar" name="fecha_pagoActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="metodo_pagoActualizar">Método Pago</label>
                                <input type="text" class="form-control" id="metodo_pagoActualizar" name="metodo_pagoActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="montoActualizar">Monto</label>
                                <input type="number" class="form-control" id="montoActualizar" name="montoActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="ivaActualizar">IVA</label>
                                <input type="number" step="0.01" class="form-control" id="ivaActualizar" name="ivaActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="detalles_transaccionActualizar">Detalles Transacción</label>
                                <input type="text" class="form-control" id="detalles_transaccionActualizar" name="detalles_transaccionActualizar" required>
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
                        <h5 class="modal-title" id="exampleModalLabel">Eliminar Orden de Pago</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>¿Está seguro de que desea eliminar esta orden de pago?</p>
                        <form id="formEliminar" method="get" action="ordenpago.php">
                            <input type="hidden" name="eliminar" id="eliminarFolio" value="">
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
                        <form id="formGenerarReporte" method="post" action="ordenpago.php">
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
                        <h5 class="modal-title" id="exampleModalLabel">Agregar Estado a Orden de Pago</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formAgregarEstado" method="post" action="ordenpago.php">
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
                                <label for="folio">Folio:</label>
                                <select class="form-control" id="folio" name="folio" required>
                                    <?php
                                    // Obtener las ordenes de pago de la base de datos
                                    include('conexion.php');
                                    $query_ordenes = "SELECT folio FROM orden_pago";
                                    $result_ordenes = pg_query($conn, $query_ordenes);
                                    while ($row_orden = pg_fetch_assoc($result_ordenes)) {
                                        echo "<option value='" . $row_orden['folio'] . "'>" . $row_orden['folio'] . "</option>";
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
                                <option value="folio">Folio</option>
                                <option value="metodo_pago">Método Pago</option>
                                <option value="detalles_transaccion">Detalles Transacción</option>
                                <option value="fecha_pago">Fecha Pago</option>
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
                        <table id="tablaOrdenPago" class="table table-striped table-bordered table-condensed" style="width:100%">
                            <thead class="text-center">
                                <tr>
                                    <th>Folio</th>
                                    <th>RUT Cliente</th>
                                    <th>RUT Persona</th>
                                    <th>Fecha_pago</th>
                                    <th>Metodo_pago</th>
                                    <th>Monto</th>
                                    <th>Iva</th>
                                    <th>Detalles_transaccion</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <?php
                                if (pg_num_rows($result) > 0) {
                                    while($row = pg_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>" . $row["folio"] . "</td>";
                                        echo "<td>" . $row["rut_cliente"] . "</td>";
                                        echo "<td>" . $row["rut_persona"] . "</td>";
                                        echo "<td>" . $row["fecha_pago"] . "</td>";
                                        echo "<td>" . $row["metodo_pago"] . "</td>";
                                        echo "<td>" . $row["monto"] . "</td>";
                                        echo "<td>" . $row["iva"] . "</td>";
                                        echo "<td>" . $row["detalles_transaccion"] . "</td>";
                                        echo "<td><a href='#' class='btn btn-danger btn-sm' data-toggle='modal' data-target='#modalEliminar' onclick='setEliminarFolio(\"" . $row["folio"] . "\")'>Eliminar</a> | 
                                              <a href='#' class='btn btn-primary btn-sm' data-toggle='modal' data-target='#modalActualizar' onclick='setActualizarDatos(" . json_encode($row) . ")'>Editar</a></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='9'>No hay ordenes de pago</td></tr>";
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
            <p>Permite ordenar la administracion de la informacion relacionada con los clientes.</p>
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
        function setEliminarFolio(folio) {
            document.getElementById('eliminarFolio').value = folio;
        }

        function setActualizarDatos(orden) {
            document.getElementById('folioActualizar').value = orden.folio;
            document.getElementById('rut_clienteActualizar').value = orden.rut_cliente;
            document.getElementById('rut_personaActualizar').value = orden.rut_persona;
            document.getElementById('fecha_pagoActualizar').value = orden.fecha_pago;
            document.getElementById('metodo_pagoActualizar').value = orden.metodo_pago;
            document.getElementById('montoActualizar').value = orden.monto;
            document.getElementById('ivaActualizar').value = orden.iva;
            document.getElementById('detalles_transaccionActualizar').value = orden.detalles_transaccion;
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
                        label: '# de Ordenes de Pago',
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
                link.download = 'reporte_ordenes_pago.png';
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
                const folio = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                const metodo = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
                const detalles = row.querySelector('td:nth-child(8)').textContent.toLowerCase();
                const fecha = row.querySelector('td:nth-child(4)').textContent.toLowerCase();

                if (searchTerm === '') {
                    row.style.display = '';
                } else if (selectedFilter === 'folio' && folio.includes(searchTerm)) {
                    row.style.display = '';
                } else if (selectedFilter === 'metodo_pago' && metodo.includes(searchTerm)) {
                    row.style.display = '';
                } else if (selectedFilter === 'detalles_transaccion' && detalles.includes(searchTerm)) {
                    row.style.display = '';
                } else if (selectedFilter === 'fecha_pago' && fecha.includes(searchTerm)) {
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