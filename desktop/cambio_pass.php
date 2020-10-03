<?php
/**
 * Made by: Saul Gonzalez (saulgonzalez76@gmail.com)
 * Copyright (c) 2019.
 *
 * This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/config.php');
include("../common_files/clases/base_datos.php");
$clsBaseDatos = new Base_Datos();
if (null !== (filter_input(INPUT_GET,'usuario'))) { $nick = filter_input(INPUT_GET, 'usuario'); }
if (null !== (filter_input(INPUT_POST,'usuario'))) { $nick = filter_input(INPUT_POST, 'usuario'); }
if (null !== (filter_input(INPUT_POST,'password'))) {
    $pass = filter_input(INPUT_POST, 'password');
    $idusuario = $_SESSION['usuario']['idusuario'];
    $clsBaseDatos->cambio_password($idusuario, $pass);
    $privilegio = $clsBaseDatos->login($nick,$pass);
    switch ($privilegio){
        case -1:
            header('Location: cambio_pass.php?privilegio=' . $privilegio);
            exit;
            break;
        case 0:
            //login incorrecto
            $errorMessage = 'A ocurrido un error, estamos trabajando en eso...';
            break;
        case 1:
            header('Location: main.php');
            exit;
            break;
    }
}

?>
<!doctype html>
<html lang="es">
    <?php    include '../common_files/meta_tags.php'; ?>
    <!-- Bootstrap core CSS -->
    <link href="../bootstrap/css/bootstrap.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="../common_files/css/sign-in.css" rel="stylesheet">

    <script type="text/javascript">
        function checkform() {
            if (document.cambio_pass.password.value != document.cambio_pass.password2.value) {
                alert("Contraseña no coincide, verifique mayusculas o minusculas.");
                return false;
            } else { 
                if (document.cambio_pass.password.value.length >= 6){
                    return true; 
                } else {
                    alert("Contraseña muy corta, necesita ser al menos 6 caracteres.");
                    return false;
                }
            }
        }
    </script>
</head>

<body class="text-center">
    <form class="form-signin" name="cambio_pass" action="cambio_pass.php" method="post" onSubmit="return checkform()">
        <label>Debe cambiar la contrase&ntilde;a por defecto.</label><br><br>
        <input type="hidden" value="<?= $nick; ?>" name="usuario" id="usuario">
        <h1 class="h3 mb-3 font-weight-normal"><img src="<?= LOGO_PATH; ?>" width="300" height="200"><br><font color="maroon"> <?= $errorMessage; ?></font></h1>
                <input type="password" id="password" name="password" class="form-control" required="" placeholder="Contraseña" autofocus="">
                <br>
                <input type="password" id="password2" name="password2" class="form-control" placeholder="Confirmar contraseña" required="">
                <br>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Actualizar Contrase&ntilde;a</button>
            </table>
    </form>


                <script src="../common_files/java/jquery.min.js"></script>
<script src="../common_files/java/popper.js"></script>
<script src="../bootstrap/js/bootstrap.min.js"></script>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="../common_files/java/ie10-viewport-bug-workaround.js"></script>
</body>
</html>

