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

    // Función para agregar una orden de pago
    document.getElementById('formAgregar').addEventListener('submit', function (event) {
        event.preventDefault();
        // Obtener los valores de los inputs
        const folio = document.getElementById('folio').value;
        const fechaPago = document.getElementById('fecha_pago').value;
        const metodoPago = document.getElementById('metodo_pago').value;
        const monto = document.getElementById('monto').value;
        const iva = document.getElementById('iva').value;
        const detallesTransaccion = document.getElementById('detalles_transaccion').value;

        // Lógica para agregar los datos a la tabla
        const table = document.getElementById('tablaClientes').getElementsByTagName('tbody')[0];
        const newRow = table.insertRow();
        newRow.innerHTML = `
            <td>${folio}</td>
            <td>${fechaPago}</td>
            <td>${metodoPago}</td>
            <td>${monto}</td>
            <td>${iva}</td>
            <td>${detallesTransaccion}</td>
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
                document.getElementById('folioActualizar').value = row.cells[0].innerText;
                document.getElementById('fecha_pagoActualizar').value = row.cells[1].innerText;
                document.getElementById('metodo_pagoActualizar').value = row.cells[2].innerText;
                document.getElementById('montoActualizar').value = row.cells[3].innerText;
                document.getElementById('ivaActualizar').value = row.cells[4].innerText;
                document.getElementById('detalles_transaccionActualizar').value = row.cells[5].innerText;

                document.getElementById('formActualizar').onsubmit = function (event) {
                    event.preventDefault();
                    row.cells[0].innerText = document.getElementById('folioActualizar').value;
                    row.cells[1].innerText = document.getElementById('fecha_pagoActualizar').value;
                    row.cells[2].innerText = document.getElementById('metodo_pagoActualizar').value;
                    row.cells[3].innerText = document.getElementById('montoActualizar').value;
                    row.cells[4].innerText = document.getElementById('ivaActualizar').value;
                    row.cells[5].innerText = document.getElementById('detalles_transaccionActualizar').value;
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
                labels: ['Orden 1', 'Orden 2', 'Orden 3'],
                datasets: [{
                    label: '# of Payments',
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
            const folio = row.cells[0].innerText.toLowerCase();
            const fechaPago = row.cells[1].innerText.toLowerCase();

            if (folio.includes(filter) || fechaPago.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
