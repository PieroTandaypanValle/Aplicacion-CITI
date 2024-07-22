<?php
// Conexion a la base de datos
include('conexion.php');

// Eliminar administrador (Desactivar)
if (isset($_GET['eliminar'])) {
    $rut_persona = $_GET['eliminar'];

    // Actualizar el estado del administrador a "inactivo" en lugar de eliminarlo
    $update_query = "UPDATE administrador SET delete_all = 'inactivo' WHERE rut_persona = '$rut_persona'";
    if (pg_query($conn, $update_query)) {
        echo "<script>alert('Administrador desactivado exitosamente.'); window.location.href='admin.php';</script>";
    } else {
        echo "<script>alert('Error: " . $update_query . "<br>" . pg_last_error($conn) . "');</script>";
    }
}

// Actualizar administrador
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar'])) {
    $rut_persona = $_POST['rutActualizar'];
    $nombre = $_POST['nombreActualizar'];
    $apellido = $_POST['apellidoActualizar'];
    $email = $_POST['emailActualizar'];
    $telefono = $_POST['telefonoActualizar'];
    $fecha_nacimiento = $_POST['fecha_nacimientoActualizar'];
    $direccion = $_POST['direccionActualizar'];
    $genero = $_POST['generoActualizar'];
    $area_especialidad = $_POST['area_especialidadActualizar'];
    $anos_experiencia = $_POST['anos_experienciaActualizar'];
    $salario = $_POST['salarioActualizar'];
    $tipo_administrador = $_POST['tipo_administradorActualizar'];
    $delete_all = $_POST['delete_allActualizar'];
    $contrasena = $_POST['contrasenaActualizar'];

    $update_query = "UPDATE administrador SET 
                    nombre='$nombre',
                    apellido='$apellido',
                    email='$email',
                    telefono='$telefono',
                    fecha_nacimiento='$fecha_nacimiento',
                    direccion='$direccion',
                    genero='$genero',
                    area_especialidad='$area_especialidad',
                    anos_experiencia='$anos_experiencia',
                    salario='$salario',
                    tipo_administrador='$tipo_administrador',
                    delete_all='$delete_all',
                    contrasena='$contrasena'
                    WHERE rut_persona='$rut_persona'";

    if (pg_query($conn, $update_query)) {
        echo "<script>alert('Administrador actualizado exitosamente.'); window.location.href='admin.php';</script>";
    } else {
        echo "<script>alert('Error: " . $update_query . "<br>" . pg_last_error($conn) . "');</script>";
    }
}

// Consulta para obtener los datos de los administradores
$query = "SELECT * FROM administrador WHERE delete_all = 'activo'"; // Filtra por administradores activos
$result = pg_query($conn, $query);

// Consulta para obtener el total de administradores
$total_query = "SELECT COUNT(*) AS total FROM administrador WHERE delete_all = 'activo'"; // Filtra por administradores activos
$total_result = pg_query($conn, $total_query);
$total_row = pg_fetch_assoc($total_result);
$total_administradores = $total_row['total'];

// Consulta para obtener la suma de los salarios de todos los administradores
$suma_salarios_query = "SELECT SUM(salario) AS total_ingresos FROM administrador WHERE delete_all = 'activo'"; // Filtra por administradores activos
$suma_salarios_result = pg_query($conn, $suma_salarios_query);
$suma_salarios_row = pg_fetch_assoc($suma_salarios_result);
$total_ingresos = $suma_salarios_row['total_ingresos'];


// Generar reporte de administradores
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generarReporte'])) {
    // Crear el archivo Excel
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="Reporte_Administradores.xls"');

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
            <Worksheet ss:Name='Reporte Administradores'>
                <Table>
                    <Row ss:StyleID='s1'>
                        <Cell ss:MergeAcross='12'><Data ss:Type='String'>Reporte de Administración de Personal y Finanzas de la Empresa CITI</Data></Cell>
                    </Row>
                    <Row ss:StyleID='s1'>
                        <Cell ss:MergeAcross='12'><Data ss:Type='String'>EMPRESA: CITI</Data></Cell>
                    </Row>
                    <Row ss:StyleID='s1'>
                        <Cell ss:MergeAcross='12'><Data ss:Type='String'>FECHA: " . date('d/m/Y') . "</Data></Cell>
                    </Row>
                    <Row ss:StyleID='s1'>
                        <Cell><Data ss:Type='String'>RUT</Data></Cell>
                        <Cell><Data ss:Type='String'>Nombre</Data></Cell>
                        <Cell><Data ss:Type='String'>Apellido</Data></Cell>
                        <Cell><Data ss:Type='String'>Email</Data></Cell>
                        <Cell><Data ss:Type='String'>Telefono</Data></Cell>
                        <Cell><Data ss:Type='String'>Fecha Nacimiento</Data></Cell>
                        <Cell><Data ss:Type='String'>Direccion</Data></Cell>
                        <Cell><Data ss:Type='String'>Genero</Data></Cell>
                        <Cell><Data ss:Type='String'>Area Especialidad</Data></Cell>
                        <Cell><Data ss:Type='String'>Años Experiencia</Data></Cell>
                        <Cell><Data ss:Type='String'>Salario</Data></Cell>
                        <Cell><Data ss:Type='String'>Tipo Administrador</Data></Cell>
                    </Row>";

    // Contador para las filas
    $i = 1;

    // Agregar los administradores al Excel
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_assoc($result)) {
            $excel_content .= "<Row ss:StyleID='s2'>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['rut_persona'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['nombre'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['apellido'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['email'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['telefono'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['fecha_nacimiento'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['direccion'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['genero'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['area_especialidad'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['anos_experiencia'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='Number'>" . $row['salario'] . "</Data></Cell>";
            $excel_content .= "<Cell><Data ss:Type='String'>" . $row['tipo_administrador'] . "</Data></Cell>";
            $excel_content .= "</Row>";
            $i++;
        }
    } else {
        $excel_content .= "<Row ss:StyleID='s2'><Cell ss:MergeAcross='12'><Data ss:Type='String'>No hay administradores registrados.</Data></Cell></Row>";
    }

    // Agregar el total de administradores al Excel
    $excel_content .= "<Row ss:StyleID='s2'>";
    $excel_content .= "<Cell ss:MergeAcross='11'><Data ss:Type='String'>Total Administradores:</Data></Cell>";
    $excel_content .= "<Cell><Data ss:Type='Number'>" . $total_administradores . "</Data></Cell>";
    $excel_content .= "</Row>";

    // Agregar el total de ingresos al Excel
    $excel_content .= "<Row ss:StyleID='s2'>";
    $excel_content .= "<Cell ss:MergeAcross='11'><Data ss:Type='String'>Total Ingresos:</Data></Cell>";
    $excel_content .= "<Cell><Data ss:Type='Number'>" . $total_ingresos . "</Data></Cell>";
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
            <li><a class="active" href="#">Admin</a></li>
            <li><a href="menu.php">Clientes</a></li>
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
            <h3>Administrar Administradores</h3>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <button id="btnGenerarReportes" type="button" class="btn btn-success" data-toggle="modal" data-target="#modalGenerarReportes">Generar Reporte <i class="material-icons">assessment</i></button>
                </div>
            </div>
        </div>

        <!-- Modal Actualizar -->
        <div class="modal fade" id="modalActualizar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Actualizar Administrador</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formActualizar" method="post" action="admin.php">
                            <input type="hidden" name="actualizar" value="1">
                            <div class="form-group">
                                <label for="rutActualizar">RUT</label>
                                <input type="text" class="form-control" id="rutActualizar" name="rutActualizar" required readonly>
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
                                <label for="area_especialidadActualizar">Area Especialidad</label>
                                <input type="text" class="form-control" id="area_especialidadActualizar" name="area_especialidadActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="anos_experienciaActualizar">Años Experiencia</label>
                                <input type="number" class="form-control" id="anos_experienciaActualizar" name="anos_experienciaActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="salarioActualizar">Salario</label>
                                <input type="number" class="form-control" id="salarioActualizar" name="salarioActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="tipo_administradorActualizar">Tipo Administrador</label>
                                <input type="text" class="form-control" id="tipo_administradorActualizar" name="tipo_administradorActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="delete_allActualizar">Delete All</label>
                                <input type="text" class="form-control" id="delete_allActualizar" name="delete_allActualizar" required>
                            </div>
                            <div class="form-group">
                                <label for="contrasenaActualizar">Contraseña</label>
                                <input type="password" class="form-control" id="contrasenaActualizar" name="contrasenaActualizar" required>
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
                        <h5 class="modal-title" id="exampleModalLabel">Desactivar Administrador</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>¿Está seguro de que desea desactivar este administrador?</p>
                        <form id="formEliminar" method="get" action="admin.php">
                            <input type="hidden" name="eliminar" id="eliminarRut" value="">
                            <button type="submit" class="btn btn-danger" id="confirmarEliminar">Desactivar</button>
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
                        <h5 class="modal-title" id="exampleModalLabel">Generar Reporte</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formGenerarReporte" method="post" action="admin.php">
                            <input type="hidden" name="generarReporte" value="1">
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
                                <option value="rut_persona">RUT</option>
                                <option value="nombre">Nombre</option>
                                <option value="apellido">Apellido</option>
                                <option value="email">Email</option>
                                <option value="genero">Género</option>
                                <option value="area_especialidad">Área Administración</option>
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
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                    <th>Email</th>
                                    <th>Telefono</th>
                                    <th>Fecha Nacimiento</th>
                                    <th>Direccion</th>
                                    <th>Genero</th>
                                    <th>Area Especialidad</th>
                                    <th>Años Experiencia</th>
                                    <th>Salario</th>
                                    <th>Tipo Administrador</th>
                                    <th>Delete All</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <?php
                                if (pg_num_rows($result) > 0) {
                                    while($row = pg_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>" . $row["rut_persona"] . "</td>";
                                        echo "<td>" . $row["nombre"] . "</td>";
                                        echo "<td>" . $row["apellido"] . "</td>";
                                        echo "<td>" . $row["email"] . "</td>";
                                        echo "<td>" . $row["telefono"] . "</td>";
                                        echo "<td>" . $row["fecha_nacimiento"] . "</td>";
                                        echo "<td>" . $row["direccion"] . "</td>";
                                        echo "<td>" . $row["genero"] . "</td>";
                                        echo "<td>" . $row["area_especialidad"] . "</td>";
                                        echo "<td>" . $row["anos_experiencia"] . "</td>";
                                        echo "<td>" . $row["salario"] . "</td>";
                                        echo "<td>" . $row["tipo_administrador"] . "</td>";
                                        echo "<td>" . $row["delete_all"] . "</td>"; // Muestra el estado (activo/inactivo)
                                        echo "<td><a href='#' class='btn btn-warning btn-sm' data-toggle='modal' data-target='#modalEliminar' onclick='setEliminarRut(\"" . $row["rut_persona"] . "\")'>Desactivar</a> | 
                                              <a href='#' class='btn btn-primary btn-sm' data-toggle='modal' data-target='#modalActualizar' onclick='setActualizarDatos(" . json_encode($row) . ")'>Editar</a></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='14'>No hay administradores</td></tr>";
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
            <p>Permite ordenar la administracion de la informacion relacionada con los administradores.</p>
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

        function setActualizarDatos(administrador) {
            document.getElementById('rutActualizar').value = administrador.rut_persona;
            document.getElementById('nombreActualizar').value = administrador.nombre;
            document.getElementById('apellidoActualizar').value = administrador.apellido;
            document.getElementById('emailActualizar').value = administrador.email;
            document.getElementById('telefonoActualizar').value = administrador.telefono;
            document.getElementById('fecha_nacimientoActualizar').value = administrador.fecha_nacimiento;
            document.getElementById('direccionActualizar').value = administrador.direccion;
            document.getElementById('generoActualizar').value = administrador.genero;
            document.getElementById('area_especialidadActualizar').value = administrador.area_especialidad;
            document.getElementById('anos_experienciaActualizar').value = administrador.anos_experiencia;
            document.getElementById('salarioActualizar').value = administrador.salario;
            document.getElementById('delete_allActualizar').value = administrador.delete_all;
            document.getElementById('tipo_administradorActualizar').value = administrador.tipo_administrador;
            document.getElementById('contrasenaActualizar').value = administrador.contrasena;
        }

        // Función para generar el reporte de administradores
        document.getElementById('formGenerarReporte').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.blob())
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'Reporte_Administradores.xls');
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
                const apellido = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                const email = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
                const genero = row.querySelector('td:nth-child(8)').textContent.toLowerCase();
                const area = row.querySelector('td:nth-child(9)').textContent.toLowerCase();

                if (searchTerm === '') {
                    row.style.display = '';
                } else if (selectedFilter === 'rut_persona' && rut.includes(searchTerm)) {
                    row.style.display = '';
                } else if (selectedFilter === 'nombre' && name.includes(searchTerm)) {
                    row.style.display = '';
                } else if (selectedFilter === 'apellido' && apellido.includes(searchTerm)) {
                    row.style.display = '';
                } else if (selectedFilter === 'email' && email.includes(searchTerm)) {
                    row.style.display = '';
                } else if (selectedFilter === 'genero' && genero.includes(searchTerm)) {
                    row.style.display = '';
                } else if (selectedFilter === 'area_especialidad' && area.includes(searchTerm)) {
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