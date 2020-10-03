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
    include "qrlib.php";
    $clsBaseDatos = new Base_Datos();
    $token = "";
$version = time();
if (null !== (filter_input(INPUT_GET, 't'))) { $token = filter_input(INPUT_GET, 't'); }
?>
<!doctype html>
<html lang="es">
    <head>
        <?php    include '../common_files/meta_tags.php'; ?>
        <link href="<?= SERVER_URL; ?>bootstrap/css/bootstrap.css" rel="stylesheet">
        <link href="<?= SERVER_URL; ?>common_files/css/sign-in.css" rel="stylesheet">
        <link href="flipclock/css/flipclock.css" rel="stylesheet">
        <style>
            .copyr {
                position:fixed;
                bottom: 10px;
                right: 10px;
            }

        </style>
        <script src="<?= SERVER_URL; ?>common_files/java/jquery-latest.min.js?<?= $version; ?>"></script>
        <script src="<?= SERVER_URL; ?>common_files/java/jquery.min.js?<?= $version; ?>"></script>
        <script src="flipclock/js/flipclock.min.js?<?= $version; ?>"></script>
    </head>
    <body class="text-center">
    <?php
        $datos = $clsBaseDatos->busca_codigo($token);
        if ($datos == ""){
            $datos = $clsBaseDatos->busca_codigo_vigencia($token);
            if ($datos !== ""){
                $interval =  strtotime(date(explode(";",$datos)[2])) - strtotime(date("Y-m-d H:i:s")) ; ?>

                <form action="index.php" method="post">
                    <div class="lockscreen-logo">
                        <img src="<?= SERVER_URL; ?>common_files/img/no-logo.png" width="300" height="200">
                    </div>
                    <div class="row">
                        <img src="<?= explode(";",$datos)[1]; ?>" style="border-radius: 50%" width="100" height="100"> <label><?= explode(";",$datos)[7]; ?></label> (<label><?= explode(";",$datos)[3]; ?></label>  <label><?= explode(";",$datos)[4]; ?></label>)
                    </div><br><br>
                    <label>Token valido en:</label>
                    <div class="clock"></div>

                    <script type="text/javascript">
                        var clock = $('.clock').FlipClock(<?= $interval; ?>, {
                            clockFace: 'DailyCounter',
                            language: 'es',
                            countdown: true
                        });
                    </script>
                </form>
            <?php } else { ?>
                <form action="index.php" method="post">
                    <div class="lockscreen-logo">
                        <img src="<?= SERVER_URL; ?>common_files/img/no-logo.png" width="300" height="200">
                    </div>
                    <label>Token caducado !</label>
                </form>

                <?php }
        } else {
                $interval = strtotime(date(explode(";",$datos)[2])) - strtotime(date("Y-m-d H:i:s"));
                QRcode::png($token, $token . ".png"); ?>
                <form action="index.php" method="post">
                    <div class="lockscreen-logo">
                        <img src="<?= SERVER_URL; ?>common_files/img/no-logo.png" width="300" height="200">
                    </div>
                    <div class="row">
                        <img src="<?= explode(";",$datos)[1]; ?>" style="border-radius: 50%" width="100" height="100"> <label><?= explode(";",$datos)[0]; ?></label> (<label><?= explode(";",$datos)[3]; ?> </label>  <label><?= explode(";",$datos)[4]; ?></label>)
                    </div><br><br>
                    <img src="<?= $token . ".png"; ?>" width="400" height="300"><br><br>
                    <div class="clock"></div>

                    <script type="text/javascript">
                        var clock = $('.clock').FlipClock(<?= $interval; ?>, {
                            clockFace: 'DailyCounter',
                            language: 'es',
                            countdown: true
                        });
                    </script>
                </form>
    <?php } ?>
                <div class="copyr">
                    <!-- stuff -->
                    <label>SmartDoor </label>
                    <h7><a href="../politica_priv.html" target="_blank">  Pol&iacute;tica de privacidad</a></h7>
                </div>

        <script src="<?= SERVER_URL; ?>common_files/java/popper.js"></script>
        <script src="<?= SERVER_URL; ?>bootstrap/js/bootstrap.min.js"></script>
        <script src="<?= SERVER_URL; ?>common_files/java/ie10-viewport-bug-workaround.js"></script>
    </body>
</html>
