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
        <li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
    </ol>
</section>

<?php
$estacion = "";
$idestacion = 0;
while ($row = $sth->fetch()){
    if ($estacion !== $row[1]){
        if ($estacion !== ""){ ?>
            </ul>
            </div>
            <div class="box-footer text-center">
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-primary" onclick="ajaxpage('usuario_nuevo.php?estacion=<?= $estacion; ?>&id=<?= $idestacion; ?>','contenido');">
                        Nuevo Usuario
                    </button>
                </div>
            </div>
            </div>
            </div>
        <?php }
        $estacion = $row[1];
        $idestacion = $row[3];
    ?>
    <div class="col-md-6">
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Usuarios para: <?= $estacion; ?></h3>
            </div>
            <div class="box-body no-padding">
                <ul class="users-list clearfix">
                    <?php } ?>
                    <li>
                        <img src="../common_files/img/0.png" alt="User Image">
                        <a class="users-list-name" href="javascript:ajaxpage('usuario_ver_qr.php?idcliente=<?= $row[5]; ?>&idpuerta=<?= $row[4]; ?>','contenido');"><?= $row[2]; ?></a>
                        <span class="users-list-date">Puerta: <?= $row[0]; ?></span>
                    </li>

<?php }
if ($estacion !== ""){ ?>
                </ul>
            </div>
            <div class="box-footer text-center">
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-primary" onclick="ajaxpage('usuario_nuevo.php?estacion=<?= $estacion; ?>&id=<?= $idestacion; ?>','contenido');">
                        Nuevo Usuario
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php }
?>
