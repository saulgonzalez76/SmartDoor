<?php
/**
 * Desarrollado por Saul Gonzalez Villafranca (RFC:GOVS7612304W2)
 * Intellibasc es propiedad de Gonvisa SPR, se prohibe la copia o distribucion de este codigo.
 * Ultima actualizacion 17/02/19 12:23 AM.
 * Copyright (c) 2019. Todos los derechos reservados
 */

session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/config.php');
include("common_files/clases/base_datos.php");
$clsBaseDatos = new Base_Datos();
$errorMessage = "";
if (null !== (filter_input(INPUT_POST,'login'))) { $nick = filter_input(INPUT_POST,'login'); }
if (null !== (filter_input(INPUT_POST,'password'))) { $pass = filter_input(INPUT_POST,'password'); }
if (isset($nick) && isset($pass)) {
    $privilegio = $clsBaseDatos->login($nick,$pass);
    switch ($privilegio){
        case -1:
            header('Location: desktop/cambio_pass.php');
            exit;
            break;
        case 0:
            //login incorrecto
            $errorMessage = 'Usuario y/o contrase&ntilde;a incorrectos.';
            break;
        case 1:
            header('Location: desktop/main.php');
            exit;
            break;
    }
}
// }
//}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php    include 'common_files/meta_tags.php'; ?>
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="common_files/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="common_files/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">


  <!-- Google Font -->
</head>
<style>
    .copyr {
        position:fixed;
        bottom: 10px;
        right: 10px;
    }

</style>
<body class="hold-transition lockscreen text-center">
<!-- Automatic element centering -->
<div class="copyr">
    <!-- stuff -->
    <label>SmartDoor </label>
    <h7><a href="politica_priv.html" target="_blank">Pol&iacute;tica de privacidad</a></h7>
</div>
<div class="lockscreen-wrapper">
  <div class="lockscreen-logo">
      <h1 class="h3 mb-3 font-weight-normal"><img src="<?= LOGO_PATH; ?>" width="300" height="200"><br><font color="maroon"> <?= $errorMessage; ?></font></h1>
  </div>
  <!-- User name -->
  <div class="lockscreen-name"><?= $_SESSION['usuario']['nombre']; ?></div>

  <!-- START LOCK SCREEN ITEM -->
  <div class="lockscreen-item">
    <!-- lockscreen image -->
    <div class="lockscreen-image">
      <img src="common_files/img/usr/<?= $_SESSION['usuario']['idusuario']; ?>.png" onError="this.onerror=null;this.src='common_files/img/0.png';">
    </div>
    <!-- /.lockscreen-image -->

    <!-- lockscreen credentials (contains the form) -->
    <form class="lockscreen-credentials" action="lock.php" method="post">
      <div class="input-group">
          <input type="hidden" name="login" value="<?= $_SESSION['usuario']['nickname']; ?>">
        <input type="password" class="form-control" name="password" placeholder="password">

        <div class="input-group-btn">
          <button type="submit" class="btn"><i class="fa fa-arrow-right text-muted"></i></button>
        </div>
      </div>
    </form>
    <!-- /.lockscreen credentials -->

  </div>
  <!-- /.lockscreen-item -->
  <div class="help-block text-center">
    Ingresa tu contrase&ntilde;a para restablecer tu sesion
  </div>
  <div class="text-center">
    <a href="index.php">Inicia sesion con un usuario diferente</a>
  </div>

</div>


<!-- /.center -->

<!-- jQuery 3 -->
<script src="common_files/java/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
