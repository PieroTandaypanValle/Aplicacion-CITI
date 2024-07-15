<?php
// Conexion a la base de datos
include('conexion.php');

// Insertar cotización
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear'])) {
    $nro_cotizacion = $_POST['nro_cotizacion'];
    $folio = $_POST['folio'];
    $rut_cliente = $_POST['rut_cliente'];
    $rut_persona = $_POST['rut_persona'];
    $fecha_creacion = $_POST['fecha_creacion'];
    $fecha_vencimiento = $_POST['fecha_vencimiento'];
    $descripcion = $_POST['descripcion'];
    $total = $_POST['total'];
    $condiciones_pago = $_POST['condiciones_pago'];

    $insert_query = "INSERT INTO cotizacion (nro_cotizacion, folio, rut_cliente, rut_persona, fecha_creacion, fecha_vencimiento, descripcion, total, condiciones_pago)
                     VALUES ('$nro_cotizacion', '$folio', '$rut_cliente', '$rut_persona', '$fecha_creacion', '$fecha_vencimiento', '$descripcion', '$total', '$condiciones_pago')";

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
    $folio = $_POST['folioActualizar'];
    $rut_cliente = $_POST['rut_clienteActualizar'];
    $rut_persona = $_POST['rut_personaActualizar'];
    $fecha_creacion = $_POST['fecha_creacionActualizar'];
    $fecha_vencimiento = $_POST['fecha_vencimientoActualizar'];
    $descripcion = $_POST['descripcionActualizar'];
    $total = $_POST['totalActualizar'];
    $condiciones_pago = $_POST['condiciones_pagoActualizar'];

    $update_query = "UPDATE cotizacion SET 
                    folio='$folio',
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
                            <span aria-hidden="true">&times;</span>
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
                            <span aria-hidden="true">&times;</span>
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
                            <span aria-hidden="true">&times;</span>
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
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <canvas id="reporteGrafico" width="400" height="400"></canvas>
                        <button id="downloadChart" class="btn btn-primary">Descargar como PNG</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barra de búsqueda -->
        <div class="container search-input">
            <div class="row">
                <div class="col-lg-12">
                    <input id="searchInput" type="text" class="form-control" placeholder="Buscar por Nro de Cotización, Fecha Creación">
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
                                    <th>Folio</th>
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
                            <tbody>
                                <?php
                                if (pg_num_rows($result) > 0) {
                                    while($row = pg_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>" . $row["nro_cotizacion"] . "</td>";
                                        echo "<td>" . $row["folio"] . "</td>";
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
                                    echo "<tr><td colspan='10'>No hay cotizaciones</td></tr>";
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
            <p>copyright &copy; <a href="#">UNAP</a> </p>
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
            document.getElementById('folioActualizar').value = cotizacion.folio;
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
    </script>
</body>

</html>

<?php
// Cerrar la conexion
pg_close($conn);
?>
