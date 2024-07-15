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

    // Función para agregar una capacitacion
    document.getElementById('formAgregar').addEventListener('submit', function (event) {
        event.preventDefault();
        // Obtener los valores de los inputs
        const id = document.getElementById('id').value;
        const direccion = document.getElementById('direccion').value;
        const nombreCapacitacion = document.getElementById('nombre_capacitacion').value;
        const nombreOtec = document.getElementById('nombre_otec').value;
        const fechaInicio = document.getElementById('fecha_inicio').value;
        const fechaFin = document.getElementById('fecha_fin').value;
        const modalidad = document.getElementById('modalidad').value;
        const horario = document.getElementById('horario').value;
        const costo = document.getElementById('costo').value;

        // Lógica para agregar los datos a la tabla
        const table = document.getElementById('tablaClientes').getElementsByTagName('tbody')[0];
        const newRow = table.insertRow();
        newRow.innerHTML = `
            <td>${id}</td>
            <td>${direccion}</td>
            <td>${nombreCapacitacion}</td>
            <td>${nombreOtec}</td>
            <td>${fechaInicio}</td>
            <td>${fechaFin}</td>
            <td>${modalidad}</td>
            <td>${horario}</td>
            <td>${costo}</td>
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
                document.getElementById('idActualizar').value = row.cells[0].innerText;
                document.getElementById('direccionActualizar').value = row.cells[1].innerText;
                document.getElementById('nombre_capacitacionActualizar').value = row.cells[2].innerText;
                document.getElementById('nombre_otecActualizar').value = row.cells[3].innerText;
                document.getElementById('fecha_inicioActualizar').value = row.cells[4].innerText;
                document.getElementById('fecha_finActualizar').value = row.cells[5].innerText;
                document.getElementById('modalidadActualizar').value = row.cells[6].innerText;
                document.getElementById('horarioActualizar').value = row.cells[7].innerText;
                document.getElementById('costoActualizar').value = row.cells[8].innerText;

                document.getElementById('formActualizar').onsubmit = function (event) {
                    event.preventDefault();
                    row.cells[0].innerText = document.getElementById('idActualizar').value;
                    row.cells[1].innerText = document.getElementById('direccionActualizar').value;
                    row.cells[2].innerText = document.getElementById('nombre_capacitacionActualizar').value;
                    row.cells[3].innerText = document.getElementById('nombre_otecActualizar').value;
                    row.cells[4].innerText = document.getElementById('fecha_inicioActualizar').value;
                    row.cells[5].innerText = document.getElementById('fecha_finActualizar').value;
                    row.cells[6].innerText = document.getElementById('modalidadActualizar').value;
                    row.cells[7].innerText = document.getElementById('horarioActualizar').value;
                    row.cells[8].innerText = document.getElementById('costoActualizar').value;
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
                labels: ['Capacitacion 1', 'Capacitacion 2', 'Capacitacion 3'],
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
        const rows = document.querySelectorAll('#tablaClientes tbody tr');

        rows.forEach(row => {
            const direccion = row.cells[1].innerText.toLowerCase();
            const nombreCapacitacion = row.cells[2].innerText.toLowerCase();

            if (direccion.includes(filter) || nombreCapacitacion.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
