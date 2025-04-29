<?php
$actual = $_SERVER['PHP_SELF'];
?>
<nav class="navbar navbar-expand-md navbar-light fixed-top bg-light decoracion-abajo">
  <a class="navbar-brand" href="https://www.gob.mx/conabio" target="_blank"><img src="/snibmx/images/SEMARNAT_CONABIO.png" alt="" height="40" width="95" align="left"></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse"
    data-target="#navbarCollapse" aria-controls="navbarCollapse"
    aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarCollapse">
    <ul class="navbar-nav mr-auto">
      <li <?php if($actual == '/index.php') echo 'class="nav-item active"'; ?>>
        <a class="nav-link" href="/"><strong>SNIB</strong></a>
      </li>
      <li <?php if($actual == '/ejemplares/proceso/index.php') echo 'class="nav-item active"'; ?>>
        <a class="nav-link" href="/ejemplares/proceso"><strong>Procesos</strong></a>
      </li>
      <li <?php if($actual == '/ejemplares/descarga/index.php') echo 'class="nav-item active"'; ?>>
        <a class="nav-link" href="/ejemplares/descarga"><strong>Ejemplares</strong></a>
      </li>
	  <li <?php if($actual == '/taxonomia/descarga/index.php') echo 'class="nav-item active"'; ?>>
        <a class="nav-link" href="/taxonomia/descarga"><strong>Catálogo taxonómico</strong></a>
      </li>
	  <li <?php if($actual == '/taxonomia/validacion/index.php' || $actual == '/taxonomia/validacion/archivo/index.php') echo 'class="nav-item active"'; ?>>
        <a class="nav-link" href="/taxonomia/validacion"><strong>Validador taxonómico</strong></a>
      </li>
	  <li <?php if($actual == '/cartografia/index.php') echo 'class="nav-item active"'; ?>>
        <a class="nav-link" href="/cartografia"><strong>Cartografía de especies</strong></a>
      </li>
	  <li <?php if($actual == '/ejemplares/docs/index.php') echo 'class="nav-item active"'; ?>>
        <a class="nav-link" href="/ejemplares/docs"><strong>Documentos SNIB</strong></a>
      </li>      
	  <li <?php if($actual == '/ejemplares/faq/index.php') echo 'class="nav-item active"'; ?>>
        <a class="nav-link" href="/ejemplares/faq"><strong>Preguntas frecuentes</strong></a>
      </li>
	  <!--li <?php if($actual == '/desarrollo/index.php') echo 'class="nav-item active"'; ?>>
        <a class="nav-link" href="https://www.snib.mx/desarrollo"><strong>Herramientas de desarrollador</strong></a>
      </li-->
	  <!--li <?php if($actual == '/estadisticas/ejemplares/index.php') echo 'class="nav-item active"'; ?>>
        <a class="nav-link" href="https://www.snib.mx/estadisticas/ejemplares"><strong>Estadisticas</strong></a>
      </li-->
    </ul>
  </div>
</nav>
