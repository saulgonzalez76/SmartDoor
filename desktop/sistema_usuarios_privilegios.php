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
include ("../common_files/constantes.php");
require_once "../common_files/clases/base_datos.php";
$clsBaseDatos = new Base_Datos();
if (!$clsBaseDatos->privilegio_modulo("usuarios privilegios")) { ?>
    <div class="row">
        <div class="col-md-12 text-center">
            <h1><strong><?= $const_error_privilegio; ?></strong></h1>
        </div>
    </div>
    <?php
    exit;
}

$accion = "accion=3&";
$nombre = "";
$email = "";
$login = "";
$privilegios = "";
if (null !== (filter_input(\INPUT_GET,'accion'))) {
    $accion = filter_input(\INPUT_GET, 'accion');
    switch ($accion) {
        case 1:
            $idtemp = filter_input(\INPUT_GET, 'idtrabajador');
            $sth = $clsBaseDatos->usuarios_listado($idtemp);
            $row = $sth->fetch();
            $nombre = $row[1];
            $correo = $row[2];
            $login = $row[3];
            $strpriv = "";
            $sth = $clsBaseDatos->usuarios_privilegios($idtemp);
            while ($row = $sth->fetch()) {
                if ($strpriv != "") {
                    $strpriv .= ",";
                }
                $strpriv .= $row[0];
            }
            $accion = "accion=2&idtrabajador=$idtemp&";
            break;
        case 2:
            $idtemp = filter_input(\INPUT_GET, 'idtrabajador');
            $privilegios = filter_input(\INPUT_GET, 'privilegios');
            $clsBaseDatos->usuarios_editar_privilegios($privilegios, $idtemp);
            break;
    }
}
?>
<section class="content-header">
    <h1>
        Privilegios de Usuarios
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
        <li><a href="#">Usuarios</a></li>
        <li class="active">Privilegios</li>
    </ol>
</section>


<form id="datosusuario" action="subir_archivos.php?fotousuario=1" method="POST" enctype="multipart/form-data">
    <div class="row">
        <!-- left column -->
        <div class="col-md-9">
            <!-- /.box -->
            <!-- general form elements disabled -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Registro de usuarios</h3>
                </div>
                <div class="box-body">

                        <input type="hidden" name="idcliente" value="<?= $idcliente; ?>" id="idcliente" />
                        <div class="form-group has-error">
                            <div class="row">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label>Privilegios: </label><h7> ("Ctrl" para seleccionar varios.)</h7>
                                        <select multiple class="form-control" size="20" id="privilegios">
                                            <?php $sth = $clsBaseDatos->sistema_modulos();
                                            while ($row = $sth->fetch()) { ?>
                                                <option value="<?= $row[0]; ?>" <?php if (strpos($strpriv, $row[0]) !== false) { echo "selected"; } ?>><?= $row[1]; ?>  (<?= $row[2]; ?>)</option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                </div>
                <div class="box-footer">
                    <button type="button" class="btn btn-info pull-right" onclick="javascript:ajaxpage('sistema_usuarios_privilegios.php?<?= $accion; ?>privilegios=' + $('#privilegios').val(),'contenido');tmrFormatoTabla=setInterval(function() {formatoTabla(true,true,false,true,false,true,true);},1000);">Guardar</button>
                </div>

            </div>

        </div>

        <div class="col-md-3">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="form-group">
                        <div class="row">
                            <div id="busca_cliente">
                                <table id="tabla_busqueda" class="table table-bordered table-hover">
                                    <thead><tr><th><font size="4">Ayuda:</font></th></tr></thead><tbody>
                                    <tr><td>
                                            <label>
                                                Ayuda
                                            </label>
                                            <p>Al mover el mouse arriba de un campo aparecera una breve descripcin de sus datos.</p>
                                        </td></tr>
                                    </tbody></table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="row">
    <div class="col-md-12 table-responsive">
        <div class="box box-default box-solid">
            <div class="box-header with-border">
                <h3 class="box-title">Listado de usuarios</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
                <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <table id="lstentradas" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Login</th>
                        <th>Ultimo Acceso</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sth = $clsBaseDatos->usuarios_listado(0);
                    while ($row = $sth->fetch()) {
                        ?><tr onclick="javascript=ajaxpage('sistema_usuarios_privilegios.php?accion=1&idtrabajador=<?= $row[0]; ?>','contenido');tmrFormatoTabla=setInterval(function() {formatoTabla(true,true,false,true,false,true,true);},1000);"><td><?= $row[1]; ?></td><td><?= $row[2]; ?></td><td><?= $row[3]; ?></td><td><?php if ($row[4] == "1900-01-01 00:00:00") { echo "NUNCA"; } else { echo $row[4]; } ?></td>
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