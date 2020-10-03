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
 *
 * gets esp8266 firmware version and returns the last firmware version on server, if it has a new version the esp will download the latest firmware
 */
include($_SERVER['DOCUMENT_ROOT'] . '/config.php');
include("../common_files/clases/base_datos.php");
$clsBaseDatos = new Base_Datos();
$req1 = file_get_contents("php://input");
$req2 = json_decode($req1);
$version = $req2 ->version;
$id = $req2 ->id;
$clsBaseDatos->estacion_version($id,$version);
echo $clsBaseDatos->estacion_update($id,$version);
?>