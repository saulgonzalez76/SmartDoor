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
if (null !== (filter_input(INPUT_GET,'id'))) {
    $id = filter_input(INPUT_GET,'id');
    $estacion = filter_input(INPUT_GET,'estacion');
}

if (null !== (filter_input(INPUT_GET,'guardar'))) {
    $id = filter_input(INPUT_GET,'id');
    $idpuerta = filter_input(INPUT_GET,'idpuerta');
    $estacion = filter_input(INPUT_GET,'estacion');
    $nombre = filter_input(INPUT_GET,'nombre');
    $appaterno = filter_input(INPUT_GET,'appaterno');
    $apmaterno = filter_input(INPUT_GET,'apmaterno');
    $telefono = filter_input(INPUT_GET,'telefono');
    $email = filter_input(INPUT_GET,'email');
    $codigo = $clsBaseDatos->cliente_nuevo($idpuerta,$nombre,$appaterno,$apmaterno,$telefono,$email);
    if ($codigo !== "") {
        QRcode::png($codigo, '../common_files/clases/qrcode/cache/' . $codigo . '.png','Q',30);
    }
}

$sth = $clsBaseDatos->estacion_puertas($id);

?>
<h1 class="text-bold"><?= WEBPAGE_TITLE; ?></h1>
<section class="content-header">
    <h1>
        Nuevo Usuario en <label><?= $estacion; ?></label>
    </h1>
    <ol class="breadcrumb">
        <li><a href="javascript:ajaxpage('inicio.php','contenido');"><i class="fa fa-dashboard"></i> Inicio</a></li>
        <li><a href="#">Nuevo Usuario</a></li>
    </ol>
</section>

<?php
if (null == (filter_input(INPUT_GET,'guardar'))) {
    $idpuerta = 0;
    while ($row = $sth->fetch()) {
        if ($idpuerta !== $row[0]) {
            if ($idpuerta > 0) { ?>
                </div>
                <div class="box-footer text-center">
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-primary" onclick="ajaxpage('usuario_nuevo.php?guardar=1&idpuerta=<?= $idpuerta; ?>&id=<?= $id; ?>&estacion=<?= $estacion; ?>&nombre=' + txtNombre.value + '&appaterno=' + txtApPaterno.value + '&apmaterno=' + txtApMaterno.value + '&telefono=' + txtTelefono.value + '&email=' + txtCorreo.value,'contenido');">
                            Agregar Usuario
                        </button>
                    </div>
                </div></div></div>
            <?php }
            $idpuerta = $row[0]; ?>
            <div class="col-md-6">
            <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Para puerta: "<label><?= $row[1]; ?></label>"</h3>
            </div>
            <div class="box-body form-group">
        <?php } ?>
        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-4"><label>Nombre:</label><input type="text" id="txtNombre" class="form-control"
                                                               placeholder="Nombre"></div>
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
            <div class="col-md-4"><label>Tel&eacute;fono:</label><input type="text" id="txtTelefono"
                                                                        class="form-control"
                                                                        placeholder="Tel&eacute;fono"></div>
        </div>
    <?php }
    if ($idpuerta > 0) { ?>
        </div>
        <div class="box-footer text-center">
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-primary" onclick="ajaxpage('usuario_nuevo.php?guardar=1&idpuerta=<?= $idpuerta; ?>&id=<?= $id; ?>&estacion=<?= $estacion; ?>&nombre=' + txtNombre.value + '&appaterno=' + txtApPaterno.value + '&apmaterno=' + txtApMaterno.value + '&telefono=' + txtTelefono.value + '&email=' + txtCorreo.value,'contenido');">Agregar
                    Usuario
                </button>
            </div>
        </div></div></div>
    <?php }
} else { ?>
<div class="col-md-6">
    <div class="box box-danger">
        <div class="box-header with-border">
            <h3 class="box-title">C&oacute;digo para: "<label><?= $nombre . " " . $appaterno . " " . $apmaterno; ?></label>"</h3>
        </div>
        <div class="box-body form-group text-center">
            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-10"><img src="../common_files/clases/qrcode/cache/<?= $codigo; ?>.png" width="400" height="400"><br><br></div>
                <div class="col-md-1"></div>
            </div>
        </div>
        <div class="box-footer text-center">
            <div class="box-tools pull-right">
                <div id="descarga"></div>
                <a href="descarga.php?tipo=1&archivo=<?= $codigo; ?>"><button type="button" class="btn btn-primary" >Descargar</button></a>

            </div>
        </div></div></div>
<?php }

?>
