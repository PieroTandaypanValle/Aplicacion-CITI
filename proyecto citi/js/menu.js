document.addEventListener('DOMContentLoaded', function () {
    // Obtener referencias a los elementos del DOM
    const btnNuevo = document.getElementById('btnNuevo');
    const btnGenerarReportes = document.getElementById('btnGenerarReportes');
    const searchInput = document.getElementById('searchInput');
    const rangoMesInicio = document.getElementById('rangoMesInicio');
    const rangoMesFin = document.getElementById('rangoMesFin');
    const btnGenerarExcel = document.getElementById('btnGenerarExcel');
  
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
  
    // Función para agregar un cliente
    document.getElementById('formAgregar').addEventListener('submit', function (event) {
      event.preventDefault();
      // Obtener los valores de los inputs
      const rut = document.getElementById('rut').value;
      const nombreEmpresa = document.getElementById('nombreEmpresa').value;
      const serviciosPrestados = document.getElementById('serviciosPrestados').value;
      const direccion = document.getElementById('direccion').value;
      const descripcionEmpresa = document.getElementById('descripcionEmpresa').value;
      const email = document.getElementById('email').value;
      const telefono = document.getElementById('telefono').value;
  
      // Lógica para agregar los datos a la tabla
      const table = document.getElementById('tablaClientes').getElementsByTagName('tbody')[0];
      const newRow = table.insertRow();
      newRow.innerHTML = `
              <td>${rut}</td>
              <td>${nombreEmpresa}</td>
              <td>${serviciosPrestados}</td>
              <td>${direccion}</td>
              <td>${descripcionEmpresa}</td>
              <td>${email}</td>
              <td>${telefono}</td>
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
          document.getElementById('nombreEmpresaActualizar').value = row.cells[1].innerText;
          document.getElementById('serviciosPrestadosActualizar').value = row.cells[2].innerText;
          document.getElementById('direccionActualizar').value = row.cells[3].innerText;
          document.getElementById('descripcionEmpresaActualizar').value = row.cells[4].innerText;
          document.getElementById('emailActualizar').value = row.cells[5].innerText;
          document.getElementById('telefonoActualizar').value = row.cells[6].innerText;
  
          document.getElementById('formActualizar').onsubmit = function (event) {
            event.preventDefault();
            row.cells[0].innerText = document.getElementById('rutActualizar').value;
            row.cells[1].innerText = document.getElementById('nombreEmpresaActualizar').value;
            row.cells[2].innerText = document.getElementById('serviciosPrestadosActualizar').value;
            row.cells[3].innerText = document.getElementById('direccionActualizar').value;
            row.cells[4].innerText = document.getElementById('descripcionEmpresaActualizar').value;
            row.cells[5].innerText = document.getElementById('emailActualizar').value;
            row.cells[6].innerText = document.getElementById('telefonoActualizar').value;
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
  
    // Función para generar el reporte de contrataciones
    btnGenerarExcel.addEventListener('click', function () {
      const mesInicio = rangoMesInicio.value;
      const mesFin = rangoMesFin.value;
  
      // Obtener los datos de la tabla "contrata"
      const contrataciones = obtenerContrataciones(mesInicio, mesFin);
  
      // Generar el archivo Excel
      generarExcel(contrataciones);
    });
  
    // Función para obtener los datos de la tabla "contrata"
    function obtenerContrataciones(mesInicio, mesFin) {
      const contrataciones = [];
      // Aquí debería ir la lógica para obtener los datos de la base de datos
      // utilizando el mesInicio y el mesFin como filtros.
      // ...
      return contrataciones;
    }
  
    // Función para generar el archivo Excel
    function generarExcel(contrataciones) {
      // Aquí debería ir la lógica para generar el archivo Excel
      // utilizando la librería de Excel que se decida.
      // ...
  
      // Ejemplo de cómo guardar el archivo Excel:
      const fechaActual = new Date();
      const nombreArchivo = `Reporte Contrataciones CITI - ${fechaActual.toLocaleDateString()} ${fechaActual.toLocaleTimeString()}.xlsx`;
      // ...
    }
  });