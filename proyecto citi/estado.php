<?php
// Conexion a la base de datos
include('conexion.php');

// Insertar estado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear'])) {
    $id_estado = $_POST['id_estado'];
    $rut_persona = $_POST['rut_persona'];
    $tipo_estado = $_POST['tipo_estado'];
    $descripcion = $_POST['descripcion'];

    $insert_query = "INSERT INTO estado (id_estado, rut_persona, tipo_estado, descripcion) VALUES ('$id_estado', '$rut_persona', '$tipo_estado', '$descripcion')";

    if (pg_query($conn, $insert_query)) {
        echo "<script>alert('Nuevo estado creado exitosamente.'); window.location.href='estado.php';</script>";
    } else {
        echo "<script>alert('Error: " . $insert_query . "<br>" . pg_last_error($conn) . "');</script>";
    }
}

// Eliminar estado
if (isset($_GET['eliminar'])) {
    $id_estado = $_GET['eliminar'];

    $delete_query = "DELETE FROM estado WHERE id_estado = '$id_estado'";
    if (pg_query($conn, $delete_query)) {
        echo "<script>alert('Estado eliminado exitosamente.'); window.location.href='estado.php';</script>";
    } else {
        echo "<script>alert('Error: " . $delete_query . "<br>" . pg_last_error($conn) . "');</script>";
    }
}

// Actualizar estado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar'])) {
    $id_estado = $_POST['id_estadoActualizar'];
    $rut_persona = $_POST['rut_personaActualizar'];
    $tipo_estado = $_POST['tipo_estadoActualizar'];
    $descripcion = $_POST['descripcionActualizar'];

    $update_query = "UPDATE estado SET 
                    rut_persona='$rut_persona',
                    tipo_estado='$tipo_estado',
                    descripcion='$descripcion'
                    WHERE id_estado='$id_estado'";

    if (pg_query($conn, $update_query)) {
        echo "<script>alert('Estado actualizado exitosamente.'); window.location.href='estado.php';</script>";
    } else {
        echo "<script>alert('Error: " . $update_query . "<br>" . pg_last_error($conn) . "');</script>";
    }
}

// Consulta para obtener los datos de los estados
$query = "SELECT * FROM estado ORDER BY id_estado ASC";
$result = pg_query($conn, $query);

// Consulta para obtener los datos para el reporte
$reporte_query = "SELECT tipo_estado, COUNT(*) AS cantidad FROM estado GROUP BY tipo_estado ORDER BY tipo_estado";
$reporte_result = pg_query($conn, $reporte_query);
$reporte_data = [];
while ($row = pg_fetch_assoc($reporte_result)) {
    $reporte_data[] = $row;
}

// Generar reporte de estados con rango de fechas
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generarReporte'])) {
    // Obtener las fechas de inicio y fin del rango
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];

    // Obtener los estados de las cotizaciones
    $cotizacion_estados_query = "
        SELECT 
            e.tipo_estado,
            c.nro_cotizacion,
            h.fecha_inicio,
            h.fecha_fin
        FROM estado e
        JOIN halla h ON e.id_estado = h.id_estado
        JOIN cotizacion c ON h.nro_cotizacion = c.nro_cotizacion
        WHERE h.fecha_inicio >= '$fecha_inicio' AND h.fecha_fin <= '$fecha_fin'
    ";
    $cotizacion_estados_result = pg_query($conn, $cotizacion_estados_query);
    $cotizacion_estados_data = [];
    while ($row = pg_fetch_assoc($cotizacion_estados_result)) {
        $cotizacion_estados_data[] = $row;
    }

    // Obtener los estados de las ordenes de pago
    $orden_pago_estados_query = "
        SELECT 
            e.tipo_estado,
            o.folio,
            u.fecha_inicio,
            u.fecha_fin
        FROM estado e
        JOIN ubica u ON e.id_estado = u.id_estado
        JOIN orden_pago o ON u.folio = o.folio
        WHERE u.fecha_inicio >= '$fecha_inicio' AND u.fecha_fin <= '$fecha_fin'
    ";
    $orden_pago_estados_result = pg_query($conn, $orden_pago_estados_query);
    $orden_pago_estados_data = [];
    while ($row = pg_fetch_assoc($orden_pago_estados_result)) {
        $orden_pago_estados_data[] = $row;
    }

    // Obtener los estados de las capacitaciones
    $capacitacion_estados_query = "
        SELECT 
            e.tipo_estado,
            c.nombre_capacitacion,
            en.fecha_inicio,
            en.fecha_fin
        FROM estado e
        JOIN encuentra en ON e.id_estado = en.id_estado
        JOIN capacitacion c ON en.id_capacitacion = c.id_capacitacion
        WHERE en.fecha_inicio >= '$fecha_inicio' AND en.fecha_fin <= '$fecha_fin'
    ";
    $capacitacion_estados_result = pg_query($conn, $capacitacion_estados_query);
    $capacitacion_estados_data = [];
    while ($row = pg_fetch_assoc($capacitacion_estados_result)) {
        $capacitacion_estados_data[] = $row;
    }

    // Crear el archivo Excel
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="Reporte_Estados.xls"');

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
            <Worksheet ss:Name='Cotizaciones'>
                <Table>
                    <Row ss:StyleID='s1'>
                        <Cell ss:MergeAcross='3'><Data ss:Type='String'>Reporte de Estados y Promedio de Tiempo de Cotizaciones</Data></Cell>
                    </Row>
                    <Row ss:StyleID='s1'>
                        <Cell ss:MergeAcross='3'><Data ss:Type='String'>FECHA: " . date('d/m/Y') . "</Data></Cell>
                    </Row>
                    <Row ss:StyleID='s1'>
                        <Cell><Data ss:Type='String'>Tipo Estado</Data></Cell>
                        <Cell><Data ss:Type='String'>Nro Cotizacion</Data></Cell>
                        <Cell><Data ss:Type='String'>Fecha Inicio</Data></Cell>
                        <Cell><Data ss:Type='String'>Fecha Fin</Data></Cell>
                    </Row>";

    // Agregar las cotizaciones al Excel
    if (count($cotizacion_estados_data) > 0) {
        foreach ($cotizacion_estados_data as $row) {
            $excel_content .= "<Row ss:StyleID='s2'>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['tipo_estado'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['nro_cotizacion'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['fecha_inicio'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['fecha_fin'] . "</Data></Cell>";
            $excel_content .= "</Row>";
        }
    } else {
        $excel_content .= "<Row ss:StyleID='s2'><Cell ss:MergeAcross='3'><Data ss:Type='String'>No hay cotizaciones en el rango de fechas seleccionado.</Data></Cell></Row>";
    }

    // Calcular el promedio de tiempo de cambio de estado de las cotizaciones
    $promedio_cotizaciones = calcularPromedioTiempo($cotizacion_estados_data, 'fecha_inicio', 'fecha_fin');

    // Calcular la cantidad de cotizaciones por estado
    $cotizaciones_por_estado = calcularEstados($cotizacion_estados_data, 'tipo_estado');

    // Agregar los totales al Excel
    $excel_content .= "<Row ss:StyleID='s1'>";
    $excel_content .= "<Cell ss:MergeAcross='3'><Data ss:Type='String'>Totales y Promedios de Cotizaciones:</Data></Cell>";
    $excel_content .= "</Row>";
    $excel_content .= "<Row ss:StyleID='s2'>";
    $excel_content .= "<Cell ss:MergeAcross='3'><Data ss:Type='String'>Promedio Tiempo:</Data></Cell>";
    $excel_content .= "<Cell><Data ss:Type='Number'>" . $promedio_cotizaciones . "</Data></Cell>";
    $excel_content .= "</Row>";
    $excel_content .= "<Row ss:StyleID='s2'>";
    $excel_content .= "<Cell ss:MergeAcross='3'><Data ss:Type='String'>Total Activas:</Data></Cell>";
    $excel_content .= "<Cell><Data ss:Type='Number'>" . $cotizaciones_por_estado['Activo'] . "</Data></Cell>"; // Cambio aquí
    $excel_content .= "</Row>";
    $excel_content .= "<Row ss:StyleID='s2'>";
    $excel_content .= "<Cell ss:MergeAcross='3'><Data ss:Type='String'>Total en Proceso:</Data></Cell>";
    $excel_content .= "<Cell><Data ss:Type='Number'>" . $cotizaciones_por_estado['En proceso'] . "</Data></Cell>"; // Cambio aquí
    $excel_content .= "</Row>";
    $excel_content .= "<Row ss:StyleID='s2'>";
    $excel_content .= "<Cell ss:MergeAcross='3'><Data ss:Type='String'>Total Finalizadas:</Data></Cell>";
    $excel_content .= "<Cell><Data ss:Type='Number'>" . $cotizaciones_por_estado['Finalizado'] . "</Data></Cell>"; // Cambio aquí
    $excel_content .= "</Row>";

    $excel_content .= "
                </Table>
            </Worksheet>
            <Worksheet ss:Name='Ordenes de Pago'>
                <Table>
                    <Row ss:StyleID='s1'>
                        <Cell ss:MergeAcross='3'><Data ss:Type='String'>Reporte de Estados y Promedio de Tiempo de Ordenes de Pago</Data></Cell>
                    </Row>
                    <Row ss:StyleID='s1'>
                        <Cell ss:MergeAcross='3'><Data ss:Type='String'>FECHA: " . date('d/m/Y') . "</Data></Cell>
                    </Row>
                    <Row ss:StyleID='s1'>
                        <Cell><Data ss:Type='String'>Tipo Estado</Data></Cell>
                        <Cell><Data ss:Type='String'>Folio</Data></Cell>
                        <Cell><Data ss:Type='String'>Fecha Inicio</Data></Cell>
                        <Cell><Data ss:Type='String'>Fecha Fin</Data></Cell>
                    </Row>";

    // Agregar las ordenes de pago al Excel
    if (count($orden_pago_estados_data) > 0) {
        foreach ($orden_pago_estados_data as $row) {
            $excel_content .= "<Row ss:StyleID='s2'>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['tipo_estado'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['folio'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['fecha_inicio'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['fecha_fin'] . "</Data></Cell>";
            $excel_content .= "</Row>";
        }
    } else {
        $excel_content .= "<Row ss:StyleID='s2'><Cell ss:MergeAcross='3'><Data ss:Type='String'>No hay ordenes de pago en el rango de fechas seleccionado.</Data></Cell></Row>";
    }

    // Calcular el promedio de tiempo de cambio de estado de las ordenes de pago
    $promedio_orden_pago = calcularPromedioTiempo($orden_pago_estados_data, 'fecha_inicio', 'fecha_fin');

    // Calcular la cantidad de ordenes de pago por estado
    $orden_pago_por_estado = calcularEstados($orden_pago_estados_data, 'tipo_estado');

    // Agregar los totales al Excel
    $excel_content .= "<Row ss:StyleID='s1'>";
    $excel_content .= "<Cell ss:MergeAcross='3'><Data ss:Type='String'>Totales y Promedios de Ordenes de Pago:</Data></Cell>";
    $excel_content .= "</Row>";
    $excel_content .= "<Row ss:StyleID='s2'>";
    $excel_content .= "<Cell ss:MergeAcross='3'><Data ss:Type='String'>Promedio Tiempo:</Data></Cell>";
    $excel_content .= "<Cell><Data ss:Type='Number'>" . $promedio_orden_pago . "</Data></Cell>";
    $excel_content .= "</Row>";
    $excel_content .= "<Row ss:StyleID='s2'>";
    $excel_content .= "<Cell ss:MergeAcross='3'><Data ss:Type='String'>Total Activas:</Data></Cell>";
    $excel_content .= "<Cell><Data ss:Type='Number'>" . $orden_pago_por_estado['Activo'] . "</Data></Cell>"; // Cambio aquí
    $excel_content .= "</Row>";
    $excel_content .= "<Row ss:StyleID='s2'>";
    $excel_content .= "<Cell ss:MergeAcross='3'><Data ss:Type='String'>Total en Proceso:</Data></Cell>";
    $excel_content .= "<Cell><Data ss:Type='Number'>" . $orden_pago_por_estado['En proceso'] . "</Data></Cell>"; // Cambio aquí
    $excel_content .= "</Row>";
    $excel_content .= "<Row ss:StyleID='s2'>";
    $excel_content .= "<Cell ss:MergeAcross='3'><Data ss:Type='String'>Total Finalizadas:</Data></Cell>";
    $excel_content .= "<Cell><Data ss:Type='Number'>" . $orden_pago_por_estado['Finalizado'] . "</Data></Cell>"; // Cambio aquí
    $excel_content .= "</Row>";

    $excel_content .= "
                </Table>
            </Worksheet>
            <Worksheet ss:Name='Capacitaciones'>
                <Table>
                    <Row ss:StyleID='s1'>
                        <Cell ss:MergeAcross='3'><Data ss:Type='String'>Reporte de Estados y Promedio de Tiempo de Capacitaciones</Data></Cell>
                    </Row>
                    <Row ss:StyleID='s1'>
                        <Cell ss:MergeAcross='3'><Data ss:Type='String'>FECHA: " . date('d/m/Y') . "</Data></Cell>
                    </Row>
                    <Row ss:StyleID='s1'>
                        <Cell><Data ss:Type='String'>Tipo Estado</Data></Cell>
                        <Cell><Data ss:Type='String'>Nombre Capacitacion</Data></Cell>
                        <Cell><Data ss:Type='String'>Fecha Inicio</Data></Cell>
                        <Cell><Data ss:Type='String'>Fecha Fin</Data></Cell>
                    </Row>";

    // Agregar las capacitaciones al Excel
    if (count($capacitacion_estados_data) > 0) {
        foreach ($capacitacion_estados_data as $row) {
            $excel_content .= "<Row ss:StyleID='s2'>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['tipo_estado'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['nombre_capacitacion'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['fecha_inicio'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['fecha_fin'] . "</Data></Cell>";
            $excel_content .= "</Row>";
        }
    } else {
        $excel_content .= "<Row ss:StyleID='s2'><Cell ss:MergeAcross='3'><Data ss:Type='String'>No hay capacitaciones en el rango de fechas seleccionado.</Data></Cell></Row>";
    }

    // Calcular el promedio de tiempo de cambio de estado de las capacitaciones
    $promedio_capacitacion = calcularPromedioTiempo($capacitacion_estados_data, 'fecha_inicio', 'fecha_fin');

    // Calcular la cantidad de capacitaciones por estado
    $capacitaciones_por_estado = calcularEstados($capacitacion_estados_data, 'tipo_estado');

    // Agregar los totales al Excel
    $excel_content .= "<Row ss:StyleID='s1'>";
    $excel_content .= "<Cell ss:MergeAcross='3'><Data ss:Type='String'>Totales y Promedios de Capacitaciones:</Data></Cell>";
    $excel_content .= "</Row>";
    $excel_content .= "<Row ss:StyleID='s2'>";
    $excel_content .= "<Cell ss:MergeAcross='3'><Data ss:Type='String'>Promedio Tiempo:</Data></Cell>";
    $excel_content .= "<Cell><Data ss:Type='Number'>" . $promedio_capacitacion . "</Data></Cell>";
    $excel_content .= "</Row>";
    $excel_content .= "<Row ss:StyleID='s2'>";
    $excel_content .= "<Cell ss:MergeAcross='3'><Data ss:Type='String'>Total Activas:</Data></Cell>";
    $excel_content .= "<Cell><Data ss:Type='Number'>" . $capacitaciones_por_estado['Activo'] . "</Data></Cell>"; // Cambio aquí
    $excel_content .= "</Row>";
    $excel_content .= "<Row ss:StyleID='s2'>";
    $excel_content .= "<Cell ss:MergeAcross='3'><Data ss:Type='String'>Total en Proceso:</Data></Cell>";
    $excel_content .= "<Cell><Data ss:Type='Number'>" . $capacitaciones_por_estado['En proceso'] . "</Data></Cell>"; // Cambio aquí
    $excel_content .= "</Row>";
    $excel_content .= "<Row ss:StyleID='s2'>";
    $excel_content .= "<Cell ss:MergeAcross='3'><Data ss:Type='String'>Total Finalizadas:</Data></Cell>";
    $excel_content .= "<Cell><Data ss:Type='Number'>" . $capacitaciones_por_estado['Finalizado'] . "</Data></Cell>"; // Cambio aquí
    $excel_content .= "</Row>";

    $excel_content .= "
                </Table>
            </Worksheet>
        </Workbook>
    </xml>";

    echo $excel_content;
    exit;
}

// Función para calcular el promedio de tiempo de cambio de estado
function calcularPromedioTiempo($data, $fecha_inicio_column, $fecha_fin_column) {
    $total_tiempo = 0;
    $cantidad = 0;

    foreach ($data as $row) {
        if (!empty($row[$fecha_inicio_column]) && !empty($row[$fecha_fin_column])) {
            $fecha_inicio = new DateTime($row[$fecha_inicio_column]);
            $fecha_fin = new DateTime($row[$fecha_fin_column]);
            $intervalo = $fecha_inicio->diff($fecha_fin);
            $total_tiempo += $intervalo->days;
            $cantidad++;
        }
    }

    if ($cantidad > 0) {
        return $total_tiempo / $cantidad;
    } else {
        return 0;
    }
}

// Función para calcular la cantidad de elementos por estado
function calcularEstados($data, $estado_column) {
    $estados = ['Activo' => 0, 'En proceso' => 0, 'Finalizado' => 0]; // Cambio aquí

    foreach ($data as $row) {
        if (isset($estados[$row[$estado_column]])) {
            $estados[$row[$estado_column]]++;
        }
    }

    return $estados;
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
            <li><a href="cotizacion.php">Cotizacion</a></li>
            <li><a class="active" href="#">Estado</a></li>
            <li><a href="index.php" onclick="logout()">Salir</a></li>
        </ul>
    </nav>
    <main>
        <div class="center">
            <h3>Administrar Estado</h3>
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
                        <h5 class="modal-title" id="exampleModalLabel">Agregar Estado</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formAgregar" method="post" action="estado.php">
                            <input type="hidden" name="crear" value="1">
                            <div class="form-group">
                                <label for="id_estado">ID Estado</label>
                                <input type="text" class="form-control" id="id_estado" name="id_estado" required>
                            </div>
                            <div class="form-group">
                                <label for="rut_persona">RUT Persona</label>
                                <input type="text" class="form-control" id="rut_persona" name="rut_persona" required>
                            </div>
                            <div class="form-group">
                                <label for="tipo_estado">Tipo de Estado</label>
                                <input type="text" class="form-control" id="tipo_estado" name="tipo_estado" required>
                            </div>
                            <div class="form-group">
                                <label for="descripcion">Descripción</label>
                                <input type="text" class="form-control" id="descripcion" name="descripcion" required>
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
                        <h5 class="modal-title" id="exampleModalLabel">Actualizar Estado</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formActualizar" method="post" action="estado.php">
                            <input type="hidden" name="actualizar" value="1">
                            <div class="form-group">
                                <label for="id_estadoActualizar">ID Estado</label>
                                <input type="text" class="form-control" id="id_estadoActualizar" name="id_estadoActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="rut_personaActualizar">RUT Persona</label>
                                <input type="text" class="form-control" id="rut_personaActualizar" name="rut_personaActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="tipo_estadoActualizar">Tipo de Estado</label>
                                <input type="text" class="form-control" id="tipo_estadoActualizar" name="tipo_estadoActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="descripcionActualizar">Descripción</label>
                                <input type="text" class="form-control" id="descripcionActualizar" name="descripcionActualizar" required>
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
                        <h5 class="modal-title" id="exampleModalLabel">Eliminar Estado</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>¿Está seguro de que desea eliminar este estado?</p>
                        <form id="formEliminar" method="get" action="estado.php">
                            <input type="hidden" name="eliminar" id="eliminarIdEstado" value="">
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
                        <form id="formGenerarReporte" method="post" action="estado.php">
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

        <!-- Barra de búsqueda -->
        <div class="container search-input">
            <div class="row">
                <div class="col-lg-12">
                    <div class="input-group mb-3">
                        <input id="searchInput" type="text" class="form-control" placeholder="Buscar...">
                        <div class="input-group-append">
                            <select id="searchBy" class="form-control">
                                <option value="id_estado">ID Estado</option>
                                <option value="tipo_estado">Tipo de Estado</option>
                                <option value="descripcion">Descripción</option>
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
                        <table id="tablaEstados" class="table table-striped table-bordered table-condensed" style="width:100%">
                            <thead class="text-center">
                                <tr>
                                    <th>ID Estado</th>
                                    <th>RUT Persona</th>
                                    <th>Tipo Estado</th>
                                    <th>Descripción</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <?php
                                if (pg_num_rows($result) > 0) {
                                    while($row = pg_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>" . $row["id_estado"] . "</td>";
                                        echo "<td>" . $row["rut_persona"] . "</td>";
                                        echo "<td>" . $row["tipo_estado"] . "</td>";
                                        echo "<td>" . $row["descripcion"] . "</td>";
                                        echo "<td><a href='#' class='btn btn-danger btn-sm' data-toggle='modal' data-target='#modalEliminar' onclick='setEliminarIdEstado(\"" . $row["id_estado"] . "\")'>Eliminar</a> | 
                                              <a href='#' class='btn btn-primary btn-sm' data-toggle='modal' data-target='#modalActualizar' onclick='setActualizarDatos(" . json_encode($row) . ")'>Editar</a></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5'>No hay estados</td></tr>";
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
            <p>Permite ordenar la administracion de la informacion relacionada con los estados.</p>
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
        function setEliminarIdEstado(id_estado) {
            document.getElementById('eliminarIdEstado').value = id_estado;
        }

        function setActualizarDatos(estado) {
            document.getElementById('id_estadoActualizar').value = estado.id_estado;
            document.getElementById('rut_personaActualizar').value = estado.rut_persona;
            document.getElementById('tipo_estadoActualizar').value = estado.tipo_estado;
            document.getElementById('descripcionActualizar').value = estado.descripcion;
        }

        // Configuración del gráfico y botón de descarga
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('reporteGrafico').getContext('2d');
            const labels = [];
            const data = [];
            <?php foreach ($reporte_data as $data) { ?>
                labels.push('<?php echo $data['tipo_estado']; ?>');
                data.push('<?php echo $data['cantidad']; ?>');
            <?php } ?>
            
            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '# de Estados',
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
                link.download = 'reporte_estados.png';
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
                const idEstado = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                const tipoEstado = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                const descripcion = row.querySelector('td:nth-child(4)').textContent.toLowerCase();

                if (searchTerm === '') {
                    row.style.display = '';
                } else if (selectedFilter === 'id_estado' && idEstado.includes(searchTerm)) {
                    row.style.display = '';
                } else if (selectedFilter === 'tipo_estado' && tipoEstado.includes(searchTerm)) {
                    row.style.display = '';
                } else if (selectedFilter === 'descripcion' && descripcion.includes(searchTerm)) {
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