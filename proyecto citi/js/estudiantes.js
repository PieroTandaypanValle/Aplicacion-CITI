document.addEventListener('DOMContentLoaded', function () {
    // Obtener referencias a los elementos del DOM
    const btnNuevo = document.getElementById('btnNuevo');
    const btnGenerarReportes = document.getElementById('btnGenerarReportes');
    const searchInput = document.getElementById('searchInput');

    // Funciones para abrir los modales
    function abrirModalAgregar() {
        $('#modalAgregar').modal('show');
    }

    function abrirModalGenerarReportes() {
        $('#modalGenerarReportes').modal('show');
    }

    // Event listeners para los botones
    btnNuevo.addEventListener('click', abrirModalAgregar);
    btnGenerarReportes.addEventListener('click', abrirModalGenerarReportes);

    // Función para agregar un estudiante
    document.getElementById('formAgregar').addEventListener('submit', function (event) {
        event.preventDefault();
        // Obtener los valores de los inputs
        const rut = document.getElementById('rut').value;
        const nombre = document.getElementById('nombre').value;
        const apellido = document.getElementById('apellido').value;
        const cargo = document.getElementById('cargo').value;
        const nivelEscolaridad = document.getElementById('nivel_escolaridad').value;
        const email = document.getElementById('email').value;
        const telefono = document.getElementById('telefono').value;
        const fechaNacimiento = document.getElementById('fecha_nacimiento').value;
        const direccion = document.getElementById('direccion').value;

        // Lógica para agregar los datos a la tabla
        const table = document.getElementById('tablaDeudas').getElementsByTagName('tbody')[0];
        const newRow = table.insertRow();
        newRow.innerHTML = `
            <td>${rut}</td>
            <td>${nombre}</td>
            <td>${apellido}</td>
            <td>${cargo}</td>
            <td>${nivelEscolaridad}</td>
            <td>${email}</td>
            <td>${telefono}</td>
            <td>${fechaNacimiento}</td>
            <td>${direccion}</td>
            <td>
                <div class="btn-group">
                    <button class="btn btn-warning btnActualizar" data-toggle="modal" data-target="#modalActualizar">Actualizar</button>
                    <button class="btn btn-danger btnEliminar" data-toggle="modal" data-target="#modalEliminar">Eliminar</button>
                </div>
            </td>
        `;

        // Cerrar el modal
        $('#modalAgregar').modal('hide');
        // Resetear el formulario
        document.getElementById('formAgregar').reset();
        addEventListenersToButtons(); // Añadir event listeners a los nuevos botones
    });

    // Función para añadir event listeners a los botones "Actualizar" y "Eliminar"
    function addEventListenersToButtons() {
        document.querySelectorAll('.btnActualizar').forEach(function (button) {
            button.addEventListener('click', function () {
                const row = this.closest('tr');
                document.getElementById('rutActualizar').value = row.cells[0].innerText;
                document.getElementById('nombreActualizar').value = row.cells[1].innerText;
                document.getElementById('apellidoActualizar').value = row.cells[2].innerText;
                document.getElementById('cargoActualizar').value = row.cells[3].innerText;
                document.getElementById('nivel_escolaridadActualizar').value = row.cells[4].innerText;
                document.getElementById('emailActualizar').value = row.cells[5].innerText;
                document.getElementById('telefonoActualizar').value = row.cells[6].innerText;
                document.getElementById('fecha_nacimientoActualizar').value = row.cells[7].innerText;
                document.getElementById('direccionActualizar').value = row.cells[8].innerText;

                document.getElementById('formActualizar').onsubmit = function (event) {
                    event.preventDefault();
                    row.cells[0].innerText = document.getElementById('rutActualizar').value;
                    row.cells[1].innerText = document.getElementById('nombreActualizar').value;
                    row.cells[2].innerText = document.getElementById('apellidoActualizar').value;
                    row.cells[3].innerText = document.getElementById('cargoActualizar').value;
                    row.cells[4].innerText = document.getElementById('nivel_escolaridadActualizar').value;
                    row.cells[5].innerText = document.getElementById('emailActualizar').value;
                    row.cells[6].innerText = document.getElementById('telefonoActualizar').value;
                    row.cells[7].innerText = document.getElementById('fecha_nacimientoActualizar').value;
                    row.cells[8].innerText = document.getElementById('direccionActualizar').value;
                    $('#modalActualizar').modal('hide');
                };
            });
        });

        document.querySelectorAll('.btnEliminar').forEach(function (button) {
            button.addEventListener('click', function () {
                const row = this.closest('tr');
                $('#modalEliminar').modal('show');
                document.getElementById('confirmarEliminar').onclick = function () {
                    row.remove();
                    $('#modalEliminar').modal('hide');
                };
            });
        });
    }

    // Llamar a la función para añadir event listeners a los botones existentes
    addEventListenersToButtons();

    // Función para generar reportes
    function generarReportes() {
        // Ejemplo: Generar un gráfico de barras
        const ctx = document.getElementById('reporteGrafico').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Estudiante 1', 'Estudiante 2', 'Estudiante 3'],
                datasets: [{
                    label: '# of Sessions',
                    data: [12, 19, 3],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)'
                    ],
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
        $('#modalGenerarReportes').modal('show');
    }

    btnGenerarReportes.addEventListener('click', generarReportes);

    // Función para filtrar la tabla
    searchInput.addEventListener('keyup', function () {
        const filter = searchInput.value.toLowerCase();
        const rows = document.querySelectorAll('#tablaDeudas tbody tr');

        rows.forEach(row => {
            const rut = row.cells[0].innerText.toLowerCase();
            const nombre = row.cells[1].innerText.toLowerCase();
            const email = row.cells[5].innerText.toLowerCase();

            if (rut.includes(filter) || nombre.includes(filter) || email.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
