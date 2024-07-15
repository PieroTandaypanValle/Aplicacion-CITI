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
                            <span aria-hidden="true">&times;</span>
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
                            <span aria-hidden="true">&times;</span>
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
                            <span aria-hidden="true">&times;</span>
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
                    <input id="searchInput" type="text" class="form-control" placeholder="Buscar por RUT, Nombre o Email">
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
                            <tbody>
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

                                    $insert_persona = "INSERT INTO persona (rut_persona, nombre, apellido, email, telefono, fecha_nacimiento, direccion, genero)
                                                       VALUES ('$rut_persona', '$nombre', '$apellido', '$email', '$telefono', '$fecha_nacimiento', '$direccion', '$genero')";

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

                                    $delete_query = "DELETE FROM estudiante WHERE rut_persona = '$rut_persona'";
                                    if (pg_query($conn, $delete_query)) {
                                        echo "<script>alert('Estudiante eliminado exitosamente.'); window.location.href='estudiantes.php';</script>";
                                    } else {
                                        echo "<script>alert('Error: " . $delete_query . "<br>" . pg_last_error($conn) . "');</script>";
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

                                // Consulta para obtener los datos de los estudiantes
                                $query = "SELECT * FROM estudiante";
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
                                    echo "<tr><td colspan='12'>No hay estudiantes</td></tr>";
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
            <p>copyright &copy; <a href="#">UNAP</a> </p>
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
    </script>
</body>

</html>

<?php
// Cerrar la conexion
pg_close($conn);
?>
