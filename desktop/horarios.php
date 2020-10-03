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

include("../common_files/clases/seguridad.php");

if(!isset($_SESSION)) { session_start(); }

require_once "../common_files/clases/base_datos.php";
$clsBaseDatos = new Base_Datos();
$sth = $clsBaseDatos->usuarios_listado_puertas($_SESSION['usuario']['idusuario']);

?>
<h1 class="text-bold"><?= WEBPAGE_TITLE; ?></h1>
<section class="content-header">
    <h1>&nbsp;</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Horarios</a></li>
    </ol>
</section>
<div class="row">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-2"><label>Lunes:</label><div class="input-group">
                    <input type="text" class="form-control timepicker" id="tmrL1">
                    <div class="input-group-addon">
                        <i class="fa fa-clock"></i>
                    </div>
                </div></div>
            <script>
                $(function() {
                    $('#tmrL1').timepicker();
                });
            </script>
            <div class="col-md-4"><label>Apellido Paterno:</label><input type="text" id="txtApPaterno"
                                                                         class="form-control"
                                                                         placeholder="Apellido Paterno"></div>
        </div>
        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-4"><label>Apellido Materno:</label><input type="text" id="txtApMaterno"
                                                                         class="form-control"
                                                                         placeholder="Apellido Materno"></div>
            <div class="col-md-4"><label>Correo:</label><input type="text" id="txtCorreo" class="form-control"
                                                               placeholder="eMail"></div>
        </div>
        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-4"><label>Tel&eacute;fono:</label><input type="text" id="txtTelefono" class="form-control" placeholder="Tel&eacute;fono"></div>
        </div>
    </div>
</div>
<br><br>
<div class="row">
<div class="col-sm-12">
    <div class="box box-default box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">Listado de horarios</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <table id="lstentradas" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>Lunes</th>
                    <th>Martes</th>
                    <th>Miercoles</th>
                    <th>Jueves</th>
                    <th>Viernes</th>
                    <th>Sabado</th>
                    <th>Domingo</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $sth = $clsBaseDatos->horarios_listado();
                while ($row = $sth->fetch()) {
                    ?>
                    <tr>
                        <td><?= $row[1]; ?></td>
                        <td><?= $row[2]; ?></td>
                        <td><?= $row[3]; ?></td>
                        <td><?= $row[4]; ?></td>
                        <td><?= $row[5]; ?></td>
                        <td><?= $row[6]; ?></td>
                        <td><?= $row[7]; ?></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
