<?php
// Conexion a la base de datos
include('conexion.php');

// Insertar evaluación
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear'])) {
    $id_evaluaciones = $_POST['id_evaluaciones'];
    $id_capacitacion = $_POST['id_capacitacion'];
    $rut_persona = $_POST['rut_persona'];
    $nota_practica = $_POST['nota_practica'];
    $nota_teorica = $_POST['nota_teorica'];
    $nota_final = $_POST['nota_final'];
    $fecha_diagnostico = $_POST['fecha_diagnostico'];
    $condicion = $_POST['condicion'];
    $descripcion = $_POST['descripcion'];

    $insert_query = "INSERT INTO evaluacion (id_evaluaciones, id_capacitacion, rut_persona, nota_practica, nota_teorica, nota_final, fecha_diagnostico, condicion, descripcion)
                     VALUES ('$id_evaluaciones', '$id_capacitacion', '$rut_persona', '$nota_practica', '$nota_teorica', '$nota_final', '$fecha_diagnostico', '$condicion', '$descripcion')";

    if (pg_query($conn, $insert_query)) {
        echo "<script>alert('Nueva evaluación creada exitosamente.'); window.location.href='evaluaciones.php';</script>";
    } else {
        echo "<script>alert('Error: " . $insert_query . "<br>" . pg_last_error($conn) . "');</script>";
    }
}

// Eliminar evaluación
if (isset($_GET['eliminar'])) {
    $id_evaluaciones = $_GET['eliminar'];

    $delete_query = "DELETE FROM evaluacion WHERE id_evaluaciones = '$id_evaluaciones'";
    if (pg_query($conn, $delete_query)) {
        echo "<script>alert('Evaluación eliminada exitosamente.'); window.location.href='evaluaciones.php';</script>";
    } else {
        echo "<script>alert('Error: " . $delete_query . "<br>" . pg_last_error($conn) . "');</script>";
    }
}

// Actualizar evaluación
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar'])) {
    $id_evaluaciones = $_POST['id_evaluacionesActualizar'];
    $id_capacitacion = $_POST['id_capacitacionActualizar'];
    $rut_persona = $_POST['rut_personaActualizar'];
    $nota_practica = $_POST['nota_practicaActualizar'];
    $nota_teorica = $_POST['nota_teoricaActualizar'];
    $nota_final = $_POST['nota_finalActualizar'];
    $fecha_diagnostico = $_POST['fecha_diagnosticoActualizar'];
    $condicion = $_POST['condicionActualizar'];
    $descripcion = $_POST['descripcionActualizar'];

    $update_query = "UPDATE evaluacion SET 
                    id_capacitacion='$id_capacitacion',
                    rut_persona='$rut_persona',
                    nota_practica='$nota_practica',
                    nota_teorica='$nota_teorica',
                    nota_final='$nota_final',
                    fecha_diagnostico='$fecha_diagnostico',
                    condicion='$condicion',
                    descripcion='$descripcion'
                    WHERE id_evaluaciones='$id_evaluaciones'";

    if (pg_query($conn, $update_query)) {
        echo "<script>alert('Evaluación actualizada exitosamente.'); window.location.href='evaluaciones.php';</script>";
    } else {
        echo "<script>alert('Error: " . $update_query . "<br>" . pg_last_error($conn) . "');</script>";
    }
}

// Consulta para obtener los datos de las evaluaciones
$query = "SELECT * FROM evaluacion ORDER BY id_evaluaciones ASC";
$result = pg_query($conn, $query);

// Consulta para obtener los datos para el reporte
$reporte_query = "SELECT nota_final, COUNT(*) AS cantidad FROM evaluacion GROUP BY nota_final ORDER BY nota_final";
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
            <li><a class="active" href="#">Evaluaciones</a></li>
            <li><a href="ordenpago.php">Orden pago</a></li>
            <li><a href="cotizacion.php">Cotizacion</a></li>
            <li><a href="estado.php">Estado</a></li>
            <li><a href="index.php" onclick="logout()">Salir</a></li>
        </ul>
    </nav>
    <main>
        <div class="center">
            <h3>Administrar Evaluaciones</h3>
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
                        <h5 class="modal-title" id="exampleModalLabel">Agregar Evaluación</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formAgregar" method="post" action="evaluaciones.php">
                            <input type="hidden" name="crear" value="1">
                            <div class="form-group">
                                <label for="id_evaluaciones">ID Evaluaciones</label>
                                <input type="text" class="form-control" id="id_evaluaciones" name="id_evaluaciones" required>
                            </div>
                            <div class="form-group">
                                <label for="id_capacitacion">ID Capacitación</label>
                                <input type="text" class="form-control" id="id_capacitacion" name="id_capacitacion" required>
                            </div>
                            <div class="form-group">
                                <label for="rut_persona">RUT Persona</label>
                                <input type="text" class="form-control" id="rut_persona" name="rut_persona" required>
                            </div>
                            <div class="form-group">
                                <label for="nota_practica">Nota Práctica</label>
                                <input type="text" class="form-control" id="nota_practica" name="nota_practica" required>
                            </div>
                            <div class="form-group">
                                <label for="nota_teorica">Nota Teórica</label>
                                <input type="text" class="form-control" id="nota_teorica" name="nota_teorica" required>
                            </div>
                            <div class="form-group">
                                <label for="nota_final">Nota Final</label>
                                <input type="text" class="form-control" id="nota_final" name="nota_final" required>
                            </div>
                            <div class="form-group">
                                <label for="fecha_diagnostico">Fecha Diagnóstico</label>
                                <input type="date" class="form-control" id="fecha_diagnostico" name="fecha_diagnostico" required>
                            </div>
                            <div class="form-group">
                                <label for="condicion">Condición</label>
                                <input type="text" class="form-control" id="condicion" name="condicion" required>
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
                        <h5 class="modal-title" id="exampleModalLabel">Actualizar Evaluación</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formActualizar" method="post" action="evaluaciones.php">
                            <input type="hidden" name="actualizar" value="1">
                            <div class="form-group">
                                <label for="id_evaluacionesActualizar">ID Evaluaciones</label>
                                <input type="text" class="form-control" id="id_evaluacionesActualizar" name="id_evaluacionesActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="id_capacitacionActualizar">ID Capacitación</label>
                                <input type="text" class="form-control" id="id_capacitacionActualizar" name="id_capacitacionActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="rut_personaActualizar">RUT Persona</label>
                                <input type="text" class="form-control" id="rut_personaActualizar" name="rut_personaActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="nota_practicaActualizar">Nota Práctica</label>
                                <input type="text" class="form-control" id="nota_practicaActualizar" name="nota_practicaActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="nota_teoricaActualizar">Nota Teórica</label>
                                <input type="text" class="form-control" id="nota_teoricaActualizar" name="nota_teoricaActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="nota_finalActualizar">Nota Final</label>
                                <input type="text" class="form-control" id="nota_finalActualizar" name="nota_finalActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="fecha_diagnosticoActualizar">Fecha Diagnóstico</label>
                                <input type="date" class="form-control" id="fecha_diagnosticoActualizar" name="fecha_diagnosticoActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="condicionActualizar">Condición</label>
                                <input type="text" class="form-control" id="condicionActualizar" name="condicionActualizar" required>
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
                        <h5 class="modal-title" id="exampleModalLabel">Eliminar Evaluación</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>¿Está seguro de que desea eliminar esta evaluación?</p>
                        <form id="formEliminar" method="get" action="evaluaciones.php">
                            <input type="hidden" name="eliminar" id="eliminarIdEvaluaciones" value="">
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
                    <input id="searchInput" type="text" class="form-control" placeholder="Buscar por ID o Nota Final">
                </div>
            </div>
        </div>

        <br>

        <div class="container caja">
            <div class="row">
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table id="tablaEvaluaciones" class="table table-striped table-bordered table-condensed" style="width:100%">
                            <thead class="text-center">
                                <tr>
                                    <th>ID Evaluaciones</th>
                                    <th>ID Capacitación</th>
                                    <th>RUT Persona</th>
                                    <th>Nota Práctica</th>
                                    <th>Nota Teórica</th>
                                    <th>Nota Final</th>
                                    <th>Fecha Diagnóstico</th>
                                    <th>Condición</th>
                                    <th>Descripción</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (pg_num_rows($result) > 0) {
                                    while($row = pg_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>" . $row["id_evaluaciones"] . "</td>";
                                        echo "<td>" . $row["id_capacitacion"] . "</td>";
                                        echo "<td>" . $row["rut_persona"] . "</td>";
                                        echo "<td>" . $row["nota_practica"] . "</td>";
                                        echo "<td>" . $row["nota_teorica"] . "</td>";
                                        echo "<td>" . $row["nota_final"] . "</td>";
                                        echo "<td>" . $row["fecha_diagnostico"] . "</td>";
                                        echo "<td>" . $row["condicion"] . "</td>";
                                        echo "<td>" . $row["descripcion"] . "</td>";
                                        echo "<td><a href='#' class='btn btn-danger btn-sm' data-toggle='modal' data-target='#modalEliminar' onclick='setEliminarIdEvaluaciones(\"" . $row["id_evaluaciones"] . "\")'>Eliminar</a> | 
                                              <a href='#' class='btn btn-primary btn-sm' data-toggle='modal' data-target='#modalActualizar' onclick='setActualizarDatos(" . json_encode($row) . ")'>Editar</a></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='10'>No hay evaluaciones</td></tr>";
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
            <p>Permite ordenar la administracion de la informacion relacionada con las evaluaciones.</p>
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
        function setEliminarIdEvaluaciones(id_evaluaciones) {
            document.getElementById('eliminarIdEvaluaciones').value = id_evaluaciones;
        }

        function setActualizarDatos(evaluacion) {
            document.getElementById('id_evaluacionesActualizar').value = evaluacion.id_evaluaciones;
            document.getElementById('id_capacitacionActualizar').value = evaluacion.id_capacitacion;
            document.getElementById('rut_personaActualizar').value = evaluacion.rut_persona;
            document.getElementById('nota_practicaActualizar').value = evaluacion.nota_practica;
            document.getElementById('nota_teoricaActualizar').value = evaluacion.nota_teorica;
            document.getElementById('nota_finalActualizar').value = evaluacion.nota_final;
            document.getElementById('fecha_diagnosticoActualizar').value = evaluacion.fecha_diagnostico;
            document.getElementById('condicionActualizar').value = evaluacion.condicion;
            document.getElementById('descripcionActualizar').value = evaluacion.descripcion;
        }

        // Configuración del gráfico y botón de descarga
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('reporteGrafico').getContext('2d');
            const labels = [];
            const data = [];
            <?php foreach ($reporte_data as $data) { ?>
                labels.push('<?php echo $data['nota_final']; ?>');
                data.push('<?php echo $data['cantidad']; ?>');
            <?php } ?>
            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '# de Personas',
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
                link.download = 'reporte_evaluaciones.png';
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
