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
include("../common_files/clases/seguridad.php");
include($_SERVER['DOCUMENT_ROOT'] . '/config.php');
require_once "../common_files/clases/base_datos.php";
$clsBaseDatos = new Base_Datos();
$version = time();
?>
<!DOCTYPE html>
<html>
    <head>
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-143061410-3"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'UA-143061410-3');
        </script>

        <?php include '../common_files/meta_tags.php'; ?>
        <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css?<?= $version; ?>">
        <link rel="stylesheet" href="../common_files/css/all.css?<?= $version; ?>">
        <link rel="stylesheet" href="../common_files/css/ionicons.min.css?<?= $version; ?>">
        <link rel="stylesheet" href="../dist/css/AdminLTE.min.css?<?= $version; ?>">
        <link rel="stylesheet" href="../dist/css/skins/skin-blue.min.css?<?= $version; ?>">
        <link rel="stylesheet" href="../dist/css/skins/_all-skins.min.css?<?= $version; ?>">
        <link rel="stylesheet" href="../plugins/fullcalendar/fullcalendar.min.css?<?= $version; ?>">
        <link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.css?<?= $version; ?>">
        <link rel="stylesheet" href="../plugins/iCheck/flat/blue.css?<?= $version; ?>">
        <link rel="stylesheet" href="../plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css?<?= $version; ?>">
        <link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker.css?<?= $version; ?>">
        <link rel="stylesheet" href="../plugins/datepicker/datepicker3.css?<?= $version; ?>">
        <link rel="stylesheet" href="../plugins/iCheck/all.css?<?= $version; ?>">
        <link rel="stylesheet" href="../plugins/colorpicker/bootstrap-colorpicker.min.css?<?= $version; ?>">
        <link rel="stylesheet" href="../plugins/timepicker/bootstrap-timepicker.min.css?<?= $version; ?>">
        <link rel="stylesheet" href="../plugins/select2/select2.min.css?<?= $version; ?>">
        <link rel="stylesheet" href="../plugins/morris/morris.css?<?= $version; ?>">
        <link rel="stylesheet" href="../plugins/ionslider/ion.rangeSlider.css?<?= $version; ?>">
        <link rel="stylesheet" href="../plugins/ionslider/ion.rangeSlider.skinNice.css?<?= $version; ?>">
        <link rel="stylesheet" href="../common_files/css/estilos.css?<?= $version; ?>">
        <link rel="stylesheet" href="../common_files/css/particles/particles.css?<?= $version; ?>">
        <link rel="stylesheet" href="../common_files/css/fotos_bascula.css?<?= $version; ?>">
        <script src="../common_files/java/codigo.js?<?= $version; ?>"></script>
        <script src="../common_files/java/jquery-latest.min.js?<?= $version; ?>"></script>
        <script src="../common_files/java/jquery.min.js?<?= $version; ?>"></script>
        <script src="../common_files/java/main_desktop.min.js?<?= $version; ?>"></script>
        <script src="../common_files/java/javascript.util.min.js?<?= $version; ?>" type="text/javascript"></script>
        <script src="../common_files/java/menu_desktop.min.js?<?= $version; ?>" type="text/javascript"></script>
        <script src="../common_files/java/jsts.min.js?<?= $version; ?>" type="text/javascript"></script>
        <script src="../common_files/java/sweetalert.js?<?= $version; ?>" type="text/javascript"></script>
        <script>
            // CHAT
            /*
            (function(d, w, c) {
                w.ChatraID = 'izcTT7iYQ93EBGWKw';
                var s = d.createElement('script');
                w[c] = w[c] || function() {
                    (w[c].q = w[c].q || []).push(arguments);
                };
                s.async = true;
                s.src = 'https://call.chatra.io/chatra.js';
                if (d.head) d.head.appendChild(s);
            })(document, window, 'Chatra');
            */
        </script>
        <style>
            .swal2-container {
                zoom: 1.5;
            }
            .swal2-icon {
                width: 5em !important;
                height: 5em !important;
                border-width: .25em !important;
            }
        </style>
    </head>

    <body class="hold-transition skin-black-light sidebar-mini">
    <div id="divseguridad"></div>
            <header class="main-header">
                <a href="main.php" class="logo">
                    <span class="logo-mini"><b>sD</b></span>
                    <span class="logo-lg"><b>smart</b>DOOR</span>
                </a>
                <nav class="navbar navbar-static-top" role="navigation">
                    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button"><span class="sr-only"></span> </a>
                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                            <li class="dropdown user user-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <img src="../common_files/img/usr/<?= $_SESSION['usuario']['idusuario']; ?>.png" onError="this.onerror=null;this.src='../common_files/img/0.png';" id="imgavatarusr" class="user-image" alt="Imagen de usuario">
                                    <span class="hidden-xs"><?= $_SESSION['usuario']['nombre']; ?></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="user-header">
                                        <form id="frmfotoavatarusuario" action="#" method="POST" enctype="multipart/form-data">
                                            <input type="file" id="archavatarusr" name="archavatarusr" style="display: none">
                                        </form>
                                        <img style="cursor: pointer" src="../common_files/img/usr/<?= $_SESSION['usuario']['idusuario']; ?>.png" id="imgavatarusr2" onclick="cambiaavatar('imgavatarusr2','fotousuario',0,'archavatarusr');archavatarusr.click();" onError="this.onerror=null;this.src='../common_files/img/0.png';" class="img-circle" alt="Imagen de usuario">
                                        <p> <?= $_SESSION['usuario']['nombre']; ?> <small>Ultimo Acceso: <?= $_SESSION['usuario']['fecha_acceso']; ?></small> </p>
                                    </li>
                                    <li class="user-footer">
                                        <div class="pull-left">
                                            <a href="javascript:ajaxpage('registro_config.php' ,'contenido');" class="btn btn-default btn-flat"><i class="fas fa-cogs"></i></a>
                                            <a href="../lock.php" class="btn btn-default btn-flat"><i class="fas fa-lock"></i></a>
                                        </div>
                                        <div class="pull-right">
                                            <a href="salir.php" class="btn btn-default btn-flat"><i class="fas fa-sign-out-alt"></i></a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
            <aside class="main-sidebar">
                <section class="sidebar">
                    <ul class="sidebar-menu" data-widget="tree">
                        <div id="menujav" class="sidebar-menu">
                            <?php include("menu.php"); ?>
                        </div>
                    </ul>
                </section>
            </aside>
            <div class="content-wrapper">
                <section class="content">
                    <div class="row">
                        <div class="col-xs-12">
                            <div id="contenido">
                                <?php require_once "inicio.php"; ?>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <?php include ("../common_files/footer.php"); ?>
        </div>
    <script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
    <script src="../bootstrap/js/bootstrap.min.js"></script>
    <script src="../plugins/select2/select2.full.min.js"></script>
    <script src="../plugins/datepicker/bootstrap-datepicker.js"></script>
    <script src="../plugins/fastclick/fastclick.js"></script>
    <script src="../dist/js/app.min.js"></script>
    <script src="../plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
    <script src="../common_files/java/anime/anime.min.js"></script>
    <script src="../common_files/java/demo.js"></script>
    <script src="../common_files/java/jszip.min.js"></script>
    <script src="../common_files/java/vfs_fonts.js"></script>
    <script src="../common_files/java/buttons.html5.min.js"></script>
    <script src="../common_files/java/buttons.print.min.js"></script>
    </body>
</html>
