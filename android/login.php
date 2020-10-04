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
include($_SERVER['DOCUMENT_ROOT'] . '/config.php');
include("../common_files/clases/base_datos.php");
$clsBaseDatos = new Base_Datos();
$estaciones = "";
if (null !== (filter_input(INPUT_GET, 'id'))) { $id = filter_input(INPUT_GET, 'id'); }
if (null !== (filter_input(INPUT_GET, 'acceso'))) { $acceso = filter_input(INPUT_GET, 'acceso'); }
if (null !== (filter_input(INPUT_GET, 'nombre'))) { $nombre = filter_input(INPUT_GET, 'nombre'); }
if (null !== (filter_input(INPUT_GET, 'apellido'))) { $apellido = filter_input(INPUT_GET, 'apellido'); }
if (null !== (filter_input(INPUT_GET, 'foto'))) { $foto = filter_input(INPUT_GET, 'foto'); }
if (null !== (filter_input(INPUT_GET, 'email'))) { $email = filter_input(INPUT_GET, 'email'); }
if (null !== (filter_input(INPUT_GET, 'token'))) { $token = filter_input(INPUT_GET, 'token'); }
if (null !== (filter_input(INPUT_GET, 'version'))) { $version = filter_input(INPUT_GET, 'version'); }
if($id !== ""){
    $estaciones = $clsBaseDatos->login_android_gid($id,$acceso,$nombre,$foto,$email,$apellido,$token,$version);
}
echo $estaciones;
?>