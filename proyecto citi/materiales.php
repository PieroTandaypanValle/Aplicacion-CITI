<?php
// Conexion a la base de datos
include('conexion.php');

// Insertar material
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear'])) {
    $cod_materiales = $_POST['cod_materiales'];
    $nombre_material = $_POST['nombre_material'];
    $costo = $_POST['costo'];
    $tipo = $_POST['tipo'];
    $disponibilidad = $_POST['disponibilidad'];
    $descripcion = $_POST['descripcion'];
    $cantidad = $_POST['cantidad'];

    $insert_query = "INSERT INTO materiales (cod_materiales, nombre_material, costo, tipo, disponibilidad, descripcion, cantidad)
                     VALUES ('$cod_materiales', '$nombre_material', '$costo', '$tipo', '$disponibilidad', '$descripcion', '$cantidad')";

    if (pg_query($conn, $insert_query)) {
        echo "<script>alert('Nuevo material creado exitosamente.'); window.location.href='materiales.php';</script>";
    } else {
        echo "<script>alert('Error: " . $insert_query . "<br>" . pg_last_error($conn) . "');</script>";
    }
}

// Eliminar material
if (isset($_GET['eliminar'])) {
    $cod_materiales = $_GET['eliminar'];

    $delete_query = "DELETE FROM materiales WHERE cod_materiales = '$cod_materiales'";
    if (pg_query($conn, $delete_query)) {
        echo "<script>alert('Material eliminado exitosamente.'); window.location.href='materiales.php';</script>";
    } else {
        echo "<script>alert('Error: " . $delete_query . "<br>" . pg_last_error($conn) . "');</script>";
    }
}

// Actualizar material
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar'])) {
    $cod_materiales = $_POST['cod_materialesActualizar'];
    $nombre_material = $_POST['nombre_materialActualizar'];
    $costo = $_POST['costoActualizar'];
    $tipo = $_POST['tipoActualizar'];
    $disponibilidad = $_POST['disponibilidadActualizar'];
    $descripcion = $_POST['descripcionActualizar'];
    $cantidad = $_POST['cantidadActualizar'];

    $update_query = "UPDATE materiales SET 
                    nombre_material='$nombre_material',
                    costo='$costo',
                    tipo='$tipo',
                    disponibilidad='$disponibilidad',
                    descripcion='$descripcion',
                    cantidad='$cantidad'
                    WHERE cod_materiales='$cod_materiales'";

    if (pg_query($conn, $update_query)) {
        echo "<script>alert('Material actualizado exitosamente.'); window.location.href='materiales.php';</script>";
    } else {
        echo "<script>alert('Error: " . $update_query . "<br>" . pg_last_error($conn) . "');</script>";
    }
}

// Consulta para obtener los datos de los materiales
$query = "SELECT * FROM materiales ORDER BY cod_materiales ASC";
$result = pg_query($conn, $query);

// Consulta para obtener el total de cantidad de materiales
$total_query = "SELECT SUM(cantidad) AS total FROM materiales";
$total_result = pg_query($conn, $total_query);
$total_row = pg_fetch_assoc($total_result);
$total_cantidad = $total_row['total'];
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
            <li><a class="active" href="#">Materiales</a></li>
            <li><a href="evaluaciones.php">Evaluaciones</a></li>
            <li><a href="ordenpago.php">Orden pago</a></li>
            <li><a href="cotizacion.php">Cotizacion</a></li>
            <li><a href="estado.php">Estado</a></li>
            <li><a href="index.php" onclick="logout()">Salir</a></li>
        </ul>
    </nav>
    <main>
        <div class="center">
            <h3>Administrar Materiales</h3>
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
                        <h5 class="modal-title" id="exampleModalLabel">Agregar Material</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formAgregar" method="post" action="materiales.php">
                            <input type="hidden" name="crear" value="1">
                            <div class="form-group">
                                <label for="cod_materiales">Cod Materiales</label>
                                <input type="text" class="form-control" id="cod_materiales" name="cod_materiales" required>
                            </div>
                            <div class="form-group">
                                <label for="nombre_material">Nombre Material</label>
                                <input type="text" class="form-control" id="nombre_material" name="nombre_material" required>
                            </div>
                            <div class="form-group">
                                <label for="costo">Costo</label>
                                <input type="number" class="form-control" id="costo" name="costo" required>
                            </div>
                            <div class="form-group">
                                <label for="tipo">Tipo</label>
                                <input type="text" class="form-control" id="tipo" name="tipo" required>
                            </div>
                            <div class="form-group">
                                <label for="disponibilidad">Disponibilidad</label>
                                <input type="text" class="form-control" id="disponibilidad" name="disponibilidad" required>
                            </div>
                            <div class="form-group">
                                <label for="descripcion">Descripcion</label>
                                <input type="text" class="form-control" id="descripcion" name="descripcion" required>
                            </div>
                            <div class="form-group">
                                <label for="cantidad">Cantidad</label>
                                <input type="number" class="form-control" id="cantidad" name="cantidad" required>
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
                        <h5 class="modal-title" id="exampleModalLabel">Actualizar Material</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formActualizar" method="post" action="materiales.php">
                            <input type="hidden" name="actualizar" value="1">
                            <div class="form-group">
                                <label for="cod_materialesActualizar">Cod Materiales</label>
                                <input type="text" class="form-control" id="cod_materialesActualizar" name="cod_materialesActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="nombre_materialActualizar">Nombre Material</label>
                                <input type="text" class="form-control" id="nombre_materialActualizar" name="nombre_materialActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="costoActualizar">Costo</label>
                                <input type="number" class="form-control" id="costoActualizar" name="costoActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="tipoActualizar">Tipo</label>
                                <input type="text" class="form-control" id="tipoActualizar" name="tipoActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="disponibilidadActualizar">Disponibilidad</label>
                                <input type="text" class="form-control" id="disponibilidadActualizar" name="disponibilidadActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="descripcionActualizar">Descripcion</label>
                                <input type="text" class="form-control" id="descripcionActualizar" name="descripcionActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="cantidadActualizar">Cantidad</label>
                                <input type="number" class="form-control" id="cantidadActualizar" name="cantidadActualizar" required>
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
                        <h5 class="modal-title" id="exampleModalLabel">Eliminar Material</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>¿Está seguro de que desea eliminar este material?</p>
                        <form id="formEliminar" method="get" action="materiales.php">
                            <input type="hidden" name="eliminar" id="eliminarCodMateriales" value="">
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
                    <input id="searchInput" type="text" class="form-control" placeholder="Buscar por Nombre de material">
                </div>
            </div>
        </div>

        <br>

        <div class="container caja">
            <div class="row">
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table id="tablaMateriales" class="table table-striped table-bordered table-condensed" style="width:100%">
                            <thead class="text-center">
                                <tr>
                                    <th>Cod_materiales</th>
                                    <th>Nombre_material</th>
                                    <th>Costo</th>
                                    <th>Tipo</th>
                                    <th>Disponibilidad</th>
                                    <th>Descripcion</th>
                                    <th>Cantidad</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (pg_num_rows($result) > 0) {
                                    while($row = pg_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>" . $row["cod_materiales"] . "</td>";
                                        echo "<td>" . $row["nombre_material"] . "</td>";
                                        echo "<td>" . $row["costo"] . "</td>";
                                        echo "<td>" . $row["tipo"] . "</td>";
                                        echo "<td>" . $row["disponibilidad"] . "</td>";
                                        echo "<td>" . $row["descripcion"] . "</td>";
                                        echo "<td>" . $row["cantidad"] . "</td>";
                                        echo "<td><a href='#' class='btn btn-danger btn-sm' data-toggle='modal' data-target='#modalEliminar' onclick='setEliminarCodMateriales(\"" . $row["cod_materiales"] . "\")'>Eliminar</a> | 
                                              <a href='#' class='btn btn-primary btn-sm' data-toggle='modal' data-target='#modalActualizar' onclick='setActualizarDatos(" . json_encode($row) . ")'>Editar</a></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='8'>No hay materiales</td></tr>";
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
            <p>Permite ordenar la administracion de la informacion relacionada con los materiales.</p>
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
        function setEliminarCodMateriales(cod_materiales) {
            document.getElementById('eliminarCodMateriales').value = cod_materiales;
        }

        function setActualizarDatos(material) {
            document.getElementById('cod_materialesActualizar').value = material.cod_materiales;
            document.getElementById('nombre_materialActualizar').value = material.nombre_material;
            document.getElementById('costoActualizar').value = material.costo;
            document.getElementById('tipoActualizar').value = material.tipo;
            document.getElementById('disponibilidadActualizar').value = material.disponibilidad;
            document.getElementById('descripcionActualizar').value = material.descripcion;
            document.getElementById('cantidadActualizar').value = material.cantidad;
        }

        // Configuración del gráfico y botón de descarga
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('reporteGrafico').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Total de Cantidad de Materiales'],
                    datasets: [{
                        label: '# de Materiales',
                        data: [<?php echo $total_cantidad; ?>],
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
                link.download = 'reporte_materiales.png';
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
