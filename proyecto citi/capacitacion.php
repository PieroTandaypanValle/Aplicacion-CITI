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
            <li><a href="menu.php">Clientes</a></li>
            <li><a href="relator.php">Relator</a></li>
            <li><a href="estudiantes.php">Estudiantes</a></li>
            <li><a class="active" href="#">Capacitacion</a></li>
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
            <h3>Administrar Capacitaciones</h3>
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
                        <h5 class="modal-title" id="exampleModalLabel">Agregar Capacitacion</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formAgregar" method="post" action="capacitacion.php">
                            <input type="hidden" name="crear" value="1">
                            <div class="form-group">
                                <label for="id">ID</label>
                                <input type="text" class="form-control" id="id" name="id" required>
                            </div>
                            <div class="form-group">
                                <label for="rut_persona">RUT Relator</label>
                                <input type="text" class="form-control" id="rut_persona" name="rut_persona" required>
                            </div>
                            <div class="form-group">
                                <label for="adm_rut_persona">RUT Admin</label>
                                <input type="text" class="form-control" id="adm_rut_persona" name="adm_rut_persona" required>
                            </div>
                            <div class="form-group">
                                <label for="direccion">Direccion</label>
                                <input type="text" class="form-control" id="direccion" name="direccion" required>
                            </div>
                            <div class="form-group">
                                <label for="nombre_capacitacion">Nombre Capacitacion</label>
                                <input type="text" class="form-control" id="nombre_capacitacion" name="nombre_capacitacion" required>
                            </div>
                            <div class="form-group">
                                <label for="nombre_otec">Nombre OTEC</label>
                                <input type="text" class="form-control" id="nombre_otec" name="nombre_otec" required>
                            </div>
                            <div class="form-group">
                                <label for="fecha_inicio">Fecha Inicio</label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                            </div>
                            <div class="form-group">
                                <label for="fecha_fin">Fecha Fin</label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                            </div>
                            <div class="form-group">
                                <label for="modalidad">Modalidad</label>
                                <input type="text" class="form-control" id="modalidad" name="modalidad" required>
                            </div>
                            <div class="form-group">
                                <label for="horario">Horario</label>
                                <input type="text" class="form-control" id="horario" name="horario" required>
                            </div>
                            <div class="form-group">
                                <label for="costo">Costo</label>
                                <input type="number" class="form-control" id="costo" name="costo" required>
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
                        <h5 class="modal-title" id="exampleModalLabel">Actualizar Capacitacion</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formActualizar" method="post" action="capacitacion.php">
                            <input type="hidden" name="actualizar" value="1">
                            <div class="form-group">
                                <label for="idActualizar">ID</label>
                                <input type="text" class="form-control" id="idActualizar" name="idActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="rut_personaActualizar">RUT Relator</label>
                                <input type="text" class="form-control" id="rut_personaActualizar" name="rut_personaActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="adm_rut_personaActualizar">RUT Admin</label>
                                <input type="text" class="form-control" id="adm_rut_personaActualizar" name="adm_rut_personaActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="direccionActualizar">Direccion</label>
                                <input type="text" class="form-control" id="direccionActualizar" name="direccionActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="nombre_capacitacionActualizar">Nombre Capacitacion</label>
                                <input type="text" class="form-control" id="nombre_capacitacionActualizar" name="nombre_capacitacionActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="nombre_otecActualizar">Nombre OTEC</label>
                                <input type="text" class="form-control" id="nombre_otecActualizar" name="nombre_otecActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="fecha_inicioActualizar">Fecha Inicio</label>
                                <input type="date" class="form-control" id="fecha_inicioActualizar" name="fecha_inicioActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="fecha_finActualizar">Fecha Fin</label>
                                <input type="date" class="form-control" id="fecha_finActualizar" name="fecha_finActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="modalidadActualizar">Modalidad</label>
                                <input type="text" class="form-control" id="modalidadActualizar" name="modalidadActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="horarioActualizar">Horario</label>
                                <input type="text" class="form-control" id="horarioActualizar" name="horarioActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="costoActualizar">Costo</label>
                                <input type="number" class="form-control" id="costoActualizar" name="costoActualizar" required>
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
                        <h5 class="modal-title" id="exampleModalLabel">Eliminar Capacitacion</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>¿Está seguro de que desea eliminar esta capacitacion?</p>
                        <form id="formEliminar" method="get" action="capacitacion.php">
                            <input type="hidden" name="eliminar" id="eliminarId" value="">
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
                    <input id="searchInput" type="text" class="form-control" placeholder="Buscar por Direccion o Nombre de capacitacion">
                </div>
            </div>
        </div>

        <br>

        <div class="container caja">
            <div class="row">
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table id="tablaCapacitaciones" class="table table-striped table-bordered table-condensed" style="width:100%">
                            <thead class="text-center">
                                <tr>
                                    <th>ID</th>
                                    <th>RUT Relator</th>
                                    <th>RUT Admin</th>
                                    <th>Direccion</th>
                                    <th>Nombre_capacitacion</th>
                                    <th>Nombre_OTEC</th>
                                    <th>Fecha_inicio</th>
                                    <th>Fecha_fin</th>
                                    <th>Modalidad</th>
                                    <th>Horario</th>
                                    <th>Costo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Conexion a la base de datos
                                include('conexion.php');

                                // Insertar capacitacion
                                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear'])) {
                                    $id_capacitacion = $_POST['id'];
                                    $rut_persona = $_POST['rut_persona'];
                                    $adm_rut_persona = $_POST['adm_rut_persona'];
                                    $direccion = $_POST['direccion'];
                                    $nombre_capacitacion = $_POST['nombre_capacitacion'];
                                    $nombre_otec = $_POST['nombre_otec'];
                                    $fecha_inicio = $_POST['fecha_inicio'];
                                    $fecha_fin = $_POST['fecha_fin'];
                                    $modalidad = $_POST['modalidad'];
                                    $horario = $_POST['horario'];
                                    $costo = $_POST['costo'];

                                    $insert_capacitacion = "INSERT INTO capacitacion (id_capacitacion, rut_persona, adm_rut_persona, direccion, nombre_capacitacion, nombre_otec, fecha_inicio, fecha_fin, modalidad, horario, costo)
                                                            VALUES ('$id_capacitacion', '$rut_persona', '$adm_rut_persona', '$direccion', '$nombre_capacitacion', '$nombre_otec', '$fecha_inicio', '$fecha_fin', '$modalidad', '$horario', '$costo')";

                                    if (pg_query($conn, $insert_capacitacion)) {
                                        echo "<script>alert('Nueva capacitacion creada exitosamente.'); window.location.href='capacitacion.php';</script>";
                                    } else {
                                        echo "<script>alert('Error: " . pg_last_error($conn) . "');</script>";
                                    }
                                }

                                // Eliminar capacitacion
                                if (isset($_GET['eliminar'])) {
                                    $id_capacitacion = $_GET['eliminar'];

                                    $delete_query = "DELETE FROM capacitacion WHERE id_capacitacion = '$id_capacitacion'";
                                    if (pg_query($conn, $delete_query)) {
                                        echo "<script>alert('Capacitacion eliminada exitosamente.'); window.location.href='capacitacion.php';</script>";
                                    } else {
                                        echo "<script>alert('Error: " . $delete_query . "<br>" . pg_last_error($conn) . "');</script>";
                                    }
                                }

                                // Actualizar capacitacion
                                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar'])) {
                                    $id_capacitacion = $_POST['idActualizar'];
                                    $rut_persona = $_POST['rut_personaActualizar'];
                                    $adm_rut_persona = $_POST['adm_rut_personaActualizar'];
                                    $direccion = $_POST['direccionActualizar'];
                                    $nombre_capacitacion = $_POST['nombre_capacitacionActualizar'];
                                    $nombre_otec = $_POST['nombre_otecActualizar'];
                                    $fecha_inicio = $_POST['fecha_inicioActualizar'];
                                    $fecha_fin = $_POST['fecha_finActualizar'];
                                    $modalidad = $_POST['modalidadActualizar'];
                                    $horario = $_POST['horarioActualizar'];
                                    $costo = $_POST['costoActualizar'];

                                    $update_capacitacion = "UPDATE capacitacion SET 
                                                            rut_persona='$rut_persona',
                                                            adm_rut_persona='$adm_rut_persona',
                                                            direccion='$direccion',
                                                            nombre_capacitacion='$nombre_capacitacion',
                                                            nombre_otec='$nombre_otec',
                                                            fecha_inicio='$fecha_inicio',
                                                            fecha_fin='$fecha_fin',
                                                            modalidad='$modalidad',
                                                            horario='$horario',
                                                            costo='$costo'
                                                            WHERE id_capacitacion='$id_capacitacion'";

                                    if (pg_query($conn, $update_capacitacion)) {
                                        echo "<script>alert('Capacitacion actualizada exitosamente.'); window.location.href='capacitacion.php';</script>";
                                    } else {
                                        echo "<script>alert('Error: " . pg_last_error($conn) . "');</script>";
                                    }
                                }

                                // Consulta para obtener los datos de las capacitaciones
                                $query = "SELECT * FROM capacitacion";
                                $result = pg_query($conn, $query);

                                // Consulta para obtener el total de personas que realizan la capacitacion con id 1
                                $total_query = "SELECT COUNT(*) AS total FROM realiza WHERE id_capacitacion = 1";
                                $total_result = pg_query($conn, $total_query);
                                $total_row = pg_fetch_assoc($total_result);
                                $total_personas = $total_row['total'];

                                if (pg_num_rows($result) > 0) {
                                    while($row = pg_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>" . $row["id_capacitacion"] . "</td>";
                                        echo "<td>" . $row["rut_persona"] . "</td>";
                                        echo "<td>" . $row["adm_rut_persona"] . "</td>";
                                        echo "<td>" . $row["direccion"] . "</td>";
                                        echo "<td>" . $row["nombre_capacitacion"] . "</td>";
                                        echo "<td>" . $row["nombre_otec"] . "</td>";
                                        echo "<td>" . $row["fecha_inicio"] . "</td>";
                                        echo "<td>" . $row["fecha_fin"] . "</td>";
                                        echo "<td>" . $row["modalidad"] . "</td>";
                                        echo "<td>" . $row["horario"] . "</td>";
                                        echo "<td>" . $row["costo"] . "</td>";
                                        echo "<td><a href='#' class='btn btn-danger btn-sm' data-toggle='modal' data-target='#modalEliminar' onclick='setEliminarId(" . $row["id_capacitacion"] . ")'>Eliminar</a> | 
                                              <a href='#' class='btn btn-primary btn-sm' data-toggle='modal' data-target='#modalActualizar' onclick='setActualizarDatos(" . json_encode($row) . ")'>Editar</a></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='12'>No hay capacitaciones</td></tr>";
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
            <p>Permite ordenar la administracion de la informacion relacionada con las capacitaciones.</p>
        </div>
        <div class="footer-bottom">
            <p>copyright &copy; <a href="#">UNAP</a> </p>
        </div>
    </footer>
    <!-- jQuery, Popper.js, Bootstrap JS -->
    <script src="assets/jquery/jquery-3.3.1.min.js"></script>
    <script src="assets/popper/popper.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function setEliminarId(id) {
            document.getElementById('eliminarId').value = id;
        }

        function setActualizarDatos(capacitacion) {
            document.getElementById('idActualizar').value = capacitacion.id_capacitacion;
            document.getElementById('rut_personaActualizar').value = capacitacion.rut_persona;
            document.getElementById('adm_rut_personaActualizar').value = capacitacion.adm_rut_persona;
            document.getElementById('direccionActualizar').value = capacitacion.direccion;
            document.getElementById('nombre_capacitacionActualizar').value = capacitacion.nombre_capacitacion;
            document.getElementById('nombre_otecActualizar').value = capacitacion.nombre_otec;
            document.getElementById('fecha_inicioActualizar').value = capacitacion.fecha_inicio;
            document.getElementById('fecha_finActualizar').value = capacitacion.fecha_fin;
            document.getElementById('modalidadActualizar').value = capacitacion.modalidad;
            document.getElementById('horarioActualizar').value = capacitacion.horario;
            document.getElementById('costoActualizar').value = capacitacion.costo;
        }

        // Configuración del gráfico y botón de descarga
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('reporteGrafico').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Total de Personas en Capacitacion 1'],
                    datasets: [{
                        label: '# de Personas',
                        data: [<?php echo $total_personas; ?>],
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
                link.download = 'reporte_capacitacion.png';
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
