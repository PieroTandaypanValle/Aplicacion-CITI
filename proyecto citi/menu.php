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

    $insert_query = "INSERT INTO cliente (rut_cliente, nombre_empresa, servicios_prestados, direccion, email, telefono, descripcion_empresa)
                     VALUES ('$rut_cliente', '$nombre_empresa', '$servicios_prestados', '$direccion', '$email', '$telefono', '$descripcion_empresa')";

    if (pg_query($conn, $insert_query)) {
        echo "<script>alert('Nuevo cliente creado exitosamente.'); window.location.href='menu.php';</script>";
    } else {
        echo "<script>alert('Error: " . $insert_query . "<br>" . pg_last_error($conn) . "');</script>";
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
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
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
                            <span aria-hidden="true">&times;</span>
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
                        <h5 class="modal-title" id="exampleModalLabel">Actualizar Cliente</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
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
                            <span aria-hidden="true">&times;</span>
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
                    <input id="searchInput" type="text" class="form-control" placeholder="Buscar por RUT, Nombre_empresa o Email">
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
                            <tbody>
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
            <p>copyright &copy; <a href="#">UNAP</a> </p>
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

        // Nueva función para agregar cliente
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

        // Configuración del gráfico y botón de descarga
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('reporteGrafico').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Total de Clientes'],
                    datasets: [{
                        label: '# de Clientes',
                        data: [<?php echo $total_clientes; ?>],
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
                link.download = 'reporte_clientes.png';
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
