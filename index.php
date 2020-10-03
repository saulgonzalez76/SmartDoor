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
    include("common_files/clases/base_datos.php");
    $clsBaseDatos = new Base_Datos();
    $errorMessage = "";
    if (null !== (filter_input(INPUT_POST,'login'))) { $nick = filter_input(INPUT_POST,'login'); }
    if (null !== (filter_input(INPUT_POST,'password'))) { $pass = filter_input(INPUT_POST,'password'); }
    if (isset($nick) && isset($pass)) {
            $privilegio = $clsBaseDatos->login($nick,$pass);
            switch ($privilegio){
                case -1:
                    header('Location: desktop/cambio_pass.php?usuario=' . $nick);
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
<!doctype html>
<html lang="es">
    <head>
        <?php    include 'common_files/meta_tags.php'; ?>
        <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
        <link href="common_files/css/sign-in.css" rel="stylesheet">
        <style>
            .copyr {
                position:fixed;
                bottom: 10px;
                right: 10px;
            }

        </style>

    </head>
    <body class="text-center">
        <form action="index.php" method="post">
            <h1 class="h3 mb-3 font-weight-normal"><img src="<?= LOGO_PATH; ?>" width="300" height="200"><br><font color="maroon"> <?= $errorMessage; ?></font></h1>
            <input type="text" id="login" name="login" class="form-control" required="" autofocus="" placeholder="Usuario">
            <br>
            <input type="password" id="password" name="password" class="form-control" placeholder="Contraseña" required="">
            <br>
            <button class="btn btn-lg btn-primary btn-block" type="submit">Accesar</button>
        </form>

        <div class="copyr">
            <!-- stuff -->
            <label>SmartDoor </label>
            <h7><a href="politica_priv.html" target="_blank">  Pol&iacute;tica de privacidad</a></h7>
        </div>

        <script src="common_files/java/jquery.min.js"></script>
        <script src="common_files/java/popper.js"></script>
        <script src="bootstrap/js/bootstrap.min.js"></script>
        <script src="common_files/java/ie10-viewport-bug-workaround.js"></script>
    </body>
</html>
