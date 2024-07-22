<?php
// Conexion a la base de datos
include('conexion.php');


// Insertar cliente
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear'])) {
    $rut_cliente = $_POST['rut'];
    $nombre_empresa = $_POST['nombreEmpresa'];
    $servicios_prestados = $_POST['serviciosPrestados'];
    $direccion = $_POST['direccion'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $descripcion_empresa = $_POST['descripcionEmpresa'];

    $insert_cliente_query = "INSERT INTO cliente (rut_cliente, nombre_empresa, servicios_prestados, direccion, email, telefono, descripcion_empresa)
                             VALUES ('$rut_cliente', '$nombre_empresa', '$servicios_prestados', '$direccion', '$email', '$telefono', '$descripcion_empresa')";

    if (pg_query($conn, $insert_cliente_query)) {
        echo "<script>alert('Nuevo cliente creado exitosamente.'); window.location.href='menu.php';</script>";
    } else {
        echo "<script>alert('Error: " . $insert_cliente_query . "<br>" . pg_last_error($conn) . "');</script>";
    }
}

// Eliminar cliente
if (isset($_GET['eliminar'])) {
    $rut_cliente = $_GET['eliminar'];

    $delete_query = "DELETE FROM cliente WHERE rut_cliente = '$rut_cliente'";
    if (pg_query($conn, $delete_query)) {
        echo "<script>alert('Cliente eliminado exitosamente.'); window.location.href='menu.php';</script>";
    } else {
        echo "<script>alert('Error: " . $delete_query . "<br>" . pg_last_error($conn) . "');</script>";
    }
}

// Actualizar cliente
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar'])) {
    $rut_cliente = $_POST['rutActualizar'];
    $nombre_empresa = $_POST['nombreEmpresaActualizar'];
    $servicios_prestados = $_POST['serviciosPrestadosActualizar'];
    $direccion = $_POST['direccionActualizar'];
    $email = $_POST['emailActualizar'];
    $telefono = $_POST['telefonoActualizar'];
    $descripcion_empresa = $_POST['descripcionEmpresaActualizar'];

    $update_query = "UPDATE cliente SET 
                    nombre_empresa='$nombre_empresa',
                    servicios_prestados='$servicios_prestados',
                    direccion='$direccion',
                    email='$email',
                    telefono='$telefono',
                    descripcion_empresa='$descripcion_empresa'
                    WHERE rut_cliente='$rut_cliente'";

    if (pg_query($conn, $update_query)) {
        echo "<script>alert('Cliente actualizado exitosamente.'); window.location.href='menu.php';</script>";
    } else {
        echo "<script>alert('Error: " . $update_query . "<br>" . pg_last_error($conn) . "');</script>";
    }
}

// Consulta para obtener los datos de los clientes
$query = "SELECT * FROM cliente";
$result = pg_query($conn, $query);

// Consulta para obtener el total de clientes
$total_query = "SELECT COUNT(*) AS total FROM cliente";
$total_result = pg_query($conn, $total_query);
$total_row = pg_fetch_assoc($total_result);
$total_clientes = $total_row['total'];

// Consulta para obtener las capacitaciones y los clientes
$capacitacion_query = "SELECT id_capacitacion, nombre_capacitacion FROM capacitacion";
$capacitacion_result = pg_query($conn, $capacitacion_query);

$clientes_query = "SELECT rut_cliente FROM cliente";
$clientes_result = pg_query($conn, $clientes_query);
$clientes_data = [];
while ($row = pg_fetch_assoc($clientes_result)) {
    $clientes_data[] = $row['rut_cliente'];
}

// Insertar en la tabla contrata
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['contratar'])) {
    $id_capacitacion = $_POST['id_capacitacion'];
    $rut_cliente = $_POST['rut_cliente'];
    $fecha_creacion = $_POST['fecha_creacion'];

    $insert_contrata_query = "INSERT INTO contrata (id_capacitacion, rut_cliente, fecha_creacion)
                             VALUES ('$id_capacitacion', '$rut_cliente', '$fecha_creacion')";

    if (pg_query($conn, $insert_contrata_query)) {
        echo "<script>alert('Capacitación contratada exitosamente.'); window.location.href='menu.php';</script>";
    } else {
        echo "<script>alert('Error: " . $insert_contrata_query . "<br>" . pg_last_error($conn) . "');</script>";
    }
}

// Configurar zona horaria a Santiago de Chile
date_default_timezone_set('America/Santiago');

// Generar reporte de capacitaciones contratadas en un rango de fechas
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generarReporte'])) {
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];

    // Consulta para obtener las capacitaciones contratadas en el rango de fechas
    $query = "SELECT c.nombre_capacitacion, con.rut_cliente, cl.nombre_empresa, con.fecha_creacion
              FROM capacitacion c
              JOIN contrata con ON c.id_capacitacion = con.id_capacitacion
              JOIN cliente cl ON con.rut_cliente = cl.rut_cliente
              WHERE con.fecha_creacion >= '$fecha_inicio' AND con.fecha_creacion <= '$fecha_fin'";
    $result = pg_query($conn, $query);

    // Obtener el total de capacitaciones contratadas
    $total_query = "SELECT COUNT(*) AS total FROM contrata WHERE fecha_creacion >= '$fecha_inicio' AND fecha_creacion <= '$fecha_fin'";
    $total_result = pg_query($conn, $total_query);
    $total_row = pg_fetch_assoc($total_result);
    $total_capacitaciones = $total_row['total'];

    // Crear el archivo Excel
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="Reporte_Capacitaciones_Contratadas.xls"');

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
            <Worksheet ss:Name='Reporte Capacitaciones'>
                <Table>
                    <Row ss:StyleID='s1'>
                        <Cell ss:MergeAcross='4'><Data ss:Type='String'>Reporte de Capacitaciones Contratadas</Data></Cell>
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
                        <Cell><Data ss:Type='String'>RUT Cliente</Data></Cell>
                        <Cell><Data ss:Type='String'>Nombre Empresa</Data></Cell>
                        <Cell><Data ss:Type='String'>Fecha de Creacion</Data></Cell>
                    </Row>";

    // Contador para las filas
    $i = 1;

    // Agregar las capacitaciones contratadas al Excel
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_assoc($result)) {
            $excel_content .= "<Row ss:StyleID='s2'>";
            $excel_content .= "<Cell><Data ss:Type='Number'>" . $i . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['nombre_capacitacion'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['rut_cliente'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['nombre_empresa'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['fecha_creacion'] . "</Data></Cell>";
            $excel_content .= "</Row>";
            $i++;
        }
    } else {
        $excel_content .= "<Row ss:StyleID='s2'><Cell ss:MergeAcross='4'><Data ss:Type='String'>No hay capacitaciones contratadas en este rango de fechas.</Data></Cell></Row>";
    }

    // Agregar el total de capacitaciones contratadas al Excel
    $excel_content .= "<Row ss:StyleID='s2'>";
    $excel_content .= "<Cell ss:MergeAcross='3'><Data ss:Type='String'>Total Capacitaciones Contratadas:</Data></Cell>";
    $excel_content .= "<Cell><Data ss:Type='Number'>" . $total_capacitaciones . "</Data></Cell>";
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
            <li><a class="active" href="#">Clientes</a></li>
            <li><a href="relator.php">Relator</a></li>
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
            <h3>Administrar Clientes</h3>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <button id="btnNuevo" type="button" class="btn btn-info" data-toggle="modal" data-target="#modalAgregar">Agregar <i class="material-icons">library_add</i></button>
                    <button id="btnGenerarReportes" type="button" class="btn btn-success" data-toggle="modal" data-target="#modalGenerarReportes">Generar Reportes <i class="material-icons">assessment</i></button>
                    <button id="btnContratarCapacitacion" type="button" class="btn btn-warning" data-toggle="modal" data-target="#modalContratarCapacitacion">Contratar Capacitación <i class="material-icons">add_circle</i></button>
                </div>
            </div>
        </div>

        <!-- Modal Agregar -->
        <div class="modal fade" id="modalAgregar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Agregar Cliente</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formAgregar" method="post" action="menu.php">
                            <input type="hidden" name="crear" value="1">
                            <div class="form-group">
                                <label for="rut">RUT</label>
                                <input type="text" class="form-control" id="rut" name="rut" required>
                            </div>
                            <div class="form-group">
                                <label for="nombreEmpresa">Nombre Empresa</label>
                                <input type="text" class="form-control" id="nombreEmpresa" name="nombreEmpresa" required>
                            </div>
                            <div class="form-group">
                                <label for="serviciosPrestados">Servicios Prestados</label>
                                <input type="text" class="form-control" id="serviciosPrestados" name="serviciosPrestados" required>
                            </div>
                            <div class="form-group">
                                <label for="direccion">Direccion</label>
                                <input type="text" class="form-control" id="direccion" name="direccion" required>
                            </div>
                            <div class="form-group">
                                <label for="descripcionEmpresa">Descripcion Empresa</label>
                                <input type="text" class="form-control" id="descripcionEmpresa" name="descripcionEmpresa" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="telefono">Telefono</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" required>
                            </div>
                            <button type="submit" class="btn btn-primary" name="crear">Agregar</button>
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
                        <h5 class="modal-title" id="exampleModalLabel">Actualizar Cliente</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formActualizar" method="post" action="menu.php">
                            <input type="hidden" name="actualizar" value="1">
                            <div class="form-group">
                                <label for="rutActualizar">RUT</label>
                                <input type="text" class="form-control" id="rutActualizar" name="rutActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="nombreEmpresaActualizar">Nombre Empresa</label>
                                <input type="text" class="form-control" id="nombreEmpresaActualizar" name="nombreEmpresaActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="serviciosPrestadosActualizar">Servicios Prestados</label>
                                <input type="text" class="form-control" id="serviciosPrestadosActualizar" name="serviciosPrestadosActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="direccionActualizar">Direccion</label>
                                <input type="text" class="form-control" id="direccionActualizar" name="direccionActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="descripcionEmpresaActualizar">Descripcion Empresa</label>
                                <input type="text" class="form-control" id="descripcionEmpresaActualizar" name="descripcionEmpresaActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="emailActualizar">Email</label>
                                <input type="email" class="form-control" id="emailActualizar" name="emailActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="telefonoActualizar">Telefono</label>
                                <input type="text" class="form-control" id="telefonoActualizar" name="telefonoActualizar" required>
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
                        <h5 class="modal-title" id="exampleModalLabel">Eliminar Cliente</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>¿Está seguro de que desea eliminar este cliente?</p>
                        <form id="formEliminar" method="get" action="menu.php">
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
                        <form id="formGenerarReporte" method="post" action="menu.php">
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

        <!-- Modal Contratar Capacitación -->
        <div class="modal fade" id="modalContratarCapacitacion" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Contratar Capacitación</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formContratarCapacitacion" method="post" action="menu.php">
                            <input type="hidden" name="contratar" value="1">
                            <div class="form-group">
                                <label for="rut_cliente">RUT Cliente:</label>
                                <select class="form-control" id="rut_cliente" name="rut_cliente" required>
                                    <?php foreach ($clientes_data as $rut) { ?>
                                        <option value="<?php echo $rut; ?>"><?php echo $rut; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="id_capacitacion">Capacitación:</label>
                                <select class="form-control" id="id_capacitacion" name="id_capacitacion" required>
                                    <?php
                                    while ($row = pg_fetch_assoc($capacitacion_result)) {
                                        echo "<option value='{$row['id_capacitacion']}'>{$row['nombre_capacitacion']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="fecha_creacion">Fecha de Creación:</label>
                                <input type="date" class="form-control" id="fecha_creacion" name="fecha_creacion" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Contratar</button>
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
                                <option value="rut_cliente">RUT Cliente</option>
                                <option value="nombre_empresa">Nombre Empresa</option>
                                <option value="email">Email</option>
                                <option value="telefono">Teléfono</option>
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
                        <table id="tablaClientes" class="table table-striped table-bordered table-condensed" style="width:100%">
                            <thead class="text-center">
                                <tr>
                                    <th>RUT</th>
                                    <th>Nombre_empresa</th>
                                    <th>Servicios_prestados</th>
                                    <th>Direccion</th>
                                    <th>Descripcion_empresa</th>
                                    <th>Email</th>
                                    <th>Telefono</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <?php
                                if (pg_num_rows($result) > 0) {
                                    while($row = pg_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>" . $row["rut_cliente"] . "</td>";
                                        echo "<td>" . $row["nombre_empresa"] . "</td>";
                                        echo "<td>" . $row["servicios_prestados"] . "</td>";
                                        echo "<td>" . $row["direccion"] . "</td>";
                                        echo "<td>" . $row["descripcion_empresa"] . "</td>";
                                        echo "<td>" . $row["email"] . "</td>";
                                        echo "<td>" . $row["telefono"] . "</td>";
                                        echo "<td><a href='#' class='btn btn-danger btn-sm' data-toggle='modal' data-target='#modalEliminar' onclick='setEliminarRut(\"" . $row["rut_cliente"] . "\")'>Eliminar</a> | 
                                              <a href='#' class='btn btn-primary btn-sm' data-toggle='modal' data-target='#modalActualizar' onclick='setActualizarDatos(" . json_encode($row) . ")'>Editar</a></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='8'>No hay clientes</td></tr>";
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
    <script src="js/menu.js"></script>
    <script>
        function setEliminarRut(rut) {
            document.getElementById('eliminarRut').value = rut;
        }

        function setActualizarDatos(cliente) {
            document.getElementById('rutActualizar').value = cliente.rut_cliente;
            document.getElementById('nombreEmpresaActualizar').value = cliente.nombre_empresa;
            document.getElementById('serviciosPrestadosActualizar').value = cliente.servicios_prestados;
            document.getElementById('direccionActualizar').value = cliente.direccion;
            document.getElementById('descripcionEmpresaActualizar').value = cliente.descripcion_empresa;
            document.getElementById('emailActualizar').value = cliente.email;
            document.getElementById('telefonoActualizar').value = cliente.telefono;
        }

        // Nueva función para agregar cliente y capacitación
        document.getElementById('formAgregar').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            fetch('menu.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert('Nuevo cliente creado exitosamente.');
                window.location.href = 'menu.php';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al crear cliente.');
            });
        });

        // Función para contratar capacitación
        document.getElementById('formContratarCapacitacion').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            fetch('menu.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert('Capacitación contratada exitosamente.');
                window.location.href = 'menu.php';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al contratar capacitación.');
            });
        });

        // Función para generar el reporte de capacitaciones contratadas
        document.getElementById('formGenerarReporte').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            fetch('menu.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.blob())
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'Reporte_Capacitaciones_Contratadas.xls');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(url);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al generar reporte.');
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
                const email = row.querySelector('td:nth-child(6)').textContent.toLowerCase();
                const phone = row.querySelector('td:nth-child(7)').textContent.toLowerCase();

                if (searchTerm === '') {
                    row.style.display = '';
                } else if (selectedFilter === 'rut_cliente' && rut.includes(searchTerm)) {
                    row.style.display = '';
                } else if (selectedFilter === 'nombre_empresa' && name.includes(searchTerm)) {
                    row.style.display = '';
                } else if (selectedFilter === 'email' && email.includes(searchTerm)) {
                    row.style.display = '';
                } else if (selectedFilter === 'telefono' && phone.includes(searchTerm)) {
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