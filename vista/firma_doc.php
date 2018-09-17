  <link rel="stylesheet" href="../estilos/w3.css">
  <link rel="stylesheet" href="../estilos/formulario.css">
  <script src="../librerias/sweetalert/dist/sweetalert.min.js"></script>
  <link rel="stylesheet" type="text/css" href="../librerias/sweetalert/dist/sweetalert.css">
  <script src="../js/data.js"></script>
  <button class="botonn" onclick="docs.firma()">Firma</button>
  <div id="id01_tad" class="w3-modal">
    <div class="w3-modal-content w3-animate-opacity">

    <header class="w3-container w3-green"> 
      <span onclick="document.getElementById('id01_tad').style.display='none'" 
      class="w3-button w3-display-topright">&times;</span>
      <h2>Subir Fotografia de la Firma</h2>
    </header>
    <div class="w3-container" id="documentos_totales_torales_firma_subida">
      <form method="POST" id="foto_subida_total" class="form-iniciar" action="../../acciones/adminactions/firma.php" enctype="multipart/form-data">
        <input type="file" name="foto" id="foto"/>
        <input type="hidden" name="OPERACION">
        <input type="submit" name="submit" id="botonazo" value="Subir la Firma"/>
      </form>
    </div>
    <footer class="w3-container w3-green">
      <p></p>
    </footer>

    </div>
  </div>
  <div id="id01_t" class="w3-modal">
    <div class="w3-modal-content w3-animate-opacity">
    <header class="w3-container w3-green"> 
      <span onclick="document.getElementById('id01_t').style.display='none'" 
      class="w3-button w3-display-topright">&times;</span>
      <h2>Firma del Jefe de Carrera <div id="firma_jefe_De_Carrera"></div></h2>
    </header>
    <div class="w3-container" id="formulario_renew">
      <h1>
        <div id="message" style="font-size: 0.8em;">
        
        </div>
      </h1>
      <table>
        <thead>
          <tr>
            <th>
              Fecha de Emision
            </th>
            <th>
              Fotografia
            </th>
          </tr>
        </thead>
        <tbody>
         <tr>
          <td data-label="Fecha de Emision">
          <div id="fecha_de_emision">  
          </div>    
          </td>
          <td data-label="Fotografia">
          <div id="dirreccion">
          </div>
          </td>
        </tr>
        </tbody>
      </table>
      <div id="NuevaModificar">
        
      </div>
    </div>
    <footer class="w3-container w3-green">
      <p></p>
    </footer>
  </div>