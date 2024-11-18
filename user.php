<?php 
session_start();

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'user') {
    header("Location: index.php"); 
    exit(); 
}

include('templates/header.html'); 
?>

<body>
  <div class="user">
  <h1 class="title">Bienvenido a Bananer</h1>
  <p class="description">El mejor CSI del mundo mundial</p>

  <h2 class="subtitle">Solcicite su reporte</h2>


  <p class="prompt">Reporte: Cantidad de estudiantes vigentes dentro y fuera del nivel:</p>
  <form class="form" action="consultas/consulta_vigencia.php" method="post">
    <br>
    <input class="form-button" type="submit" value="Buscar">
  </form>
  <br>
  <br>


  <p class="prompt">Reporte: Porcentaje de aprobacion del periodo:</p>
  <form class="form" action="consultas/consulta_periodo_aprobacion.php" method="post">
    <input class="form-input" type="text" required placeholder="Ingresa un periodo" periodo="periodo"> 
    <br>
    <input class="form-button" type="submit" value="Buscar">
  </form>
  <br>
  <br>

  <p class="prompt">Reporte: Promedio del porcentaje de aprobacion por profesor:</p>
  <form class="form" action="consultas/consulta_curso_aprobacion.php" method="post">
    <input class="form-input" type="text" required placeholder="Ingresa una sigla de curso" name="artista"> 
    <br>
    <input class="form-button" type="submit" value="Buscar">
  </form>
  <br>
  <br>


  <p class="prompt">Reporte: Proyeccion de cursos 2025 para estudiante:</p>
  <form class="form" action="consultas/consulta_propuesta_ramos.php" method="post">
    <input class="form-input" type="text" required placeholder="Ingresa un numero de alumno" name="numero_estudiante"> 
    <br>
    <input class="form-button" type="submit" value="Buscar">
  </form>
  <br>
  <br>

  <p class="prompt">Reporte: Historial academico estudiante:</p>
  <form class="form" action="consultas/consulta_historial_academico.php" method="post">
    <input class="form-input" type="text" required placeholder="Ingresa un nombre" name="artista"> 
    <br>
    <input class="form-button" type="submit" value="Buscar">
  </form>
  <br>
  <br>


  <p class="prompt">Acta de Notas 2024</p>
  <form class="form" action="Entrega3/transaccion.php" method="post">
    <br>
    <input class="form-button" type="submit" value="Buscar">
  </form>
  <br>
  <br>


  <p class="prompt">Consulta Maestra:</p>
  <form class="form" action="Entrega3/consulta_maestra.php" method="post">
    <label for="attributes">Atributos:</label>
    <input class="form-input" id="attributes" name="attributes" type="text" required placeholder="Ingresa los atributos (ej: nombre, edad)">
    <br><br>

    <label for="table">Tabla:</label>
    <input class="form-input" id="table" name="table" type="text" required placeholder="Ingresa el nombre de la tabla (ej: artista)">
    <br><br>

    <label for="condition">Condición:</label>
    <input class="form-input" id="condition" name="condition" type="text" required placeholder="Ingresa la condición (ej: edad > 30)">
    <br><br>

    <input class="form-button" type="submit" value="Buscar">
  </form>
  <br>
  <br>



  <form method="POST" action="consultas/logout.php">
    <button type="submit" class="form-button">Volver a Iniciar Sesión</button>
  </form>
  </div>
</body>
</html>
