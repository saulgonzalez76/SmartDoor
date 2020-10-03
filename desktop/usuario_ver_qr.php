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
include "../common_files/clases/qrcode/qrlib.php";

if(!isset($_SESSION)) { session_start(); }

require_once "../common_files/clases/base_datos.php";
$clsBaseDatos = new Base_Datos();
$idusuario = 0;
$idpuerta = "";
if (null !== (filter_input(INPUT_GET,'idcliente'))) {
    $idcliente = filter_input(INPUT_GET,'idcliente');
    $idpuerta = filter_input(INPUT_GET,'idpuerta');
}
$sth = $clsBaseDatos->usuarios_puerta_datos($idcliente,$idpuerta);
?>
<h1 class="text-bold"><?= WEBPAGE_TITLE; ?></h1>
<section class="content-header">
    <h1>
    </h1>
    <ol class="breadcrumb">
        <li><a href="javascript:ajaxpage('inicio.php','contenido');"><i class="fa fa-dashboard"></i> Inicio</a></li>
        <li><a href="#">QR Usuario</a></li>
    </ol>
</section>
<?php if ($row = $sth->fetch()){
    QRcode::png($row[2], '../common_files/clases/qrcode/cache/' . $row[2] . '.png','Q',30); ?>
<div class="col-md-6">
    <div class="box box-danger">
        <div class="box-header with-border">
            <h3 class="box-title">C&oacute;digo de: "<label><?= $row[3]; ?></label>" para puerta: "<label><?= $row[0]; ?> - <?= $row[1]; ?></label>"</h3>
        </div>
        <div class="box-body form-group text-center">
            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-10"><img src="../common_files/clases/qrcode/cache/<?= $row[2]; ?>.png" width="400" height="400"><br><br></div>
                <div class="col-md-1"></div>
            </div>
        </div>
        <div class="box-footer text-center">
            <div class="box-tools pull-right">
                <div id="descarga"></div>
                <a href="descarga.php?tipo=1&archivo=<?= $row[2]; ?>"><button type="button" class="btn btn-primary" >Descargar</button></a>

            </div>
        </div></div></div>
<?php }

?>
