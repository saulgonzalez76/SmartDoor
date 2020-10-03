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
 */
include($_SERVER['DOCUMENT_ROOT'] . '/config.php');
include("../common_files/clases/base_datos.php");

$clsBaseDatos = new Base_Datos();
if (null !== (filter_input(INPUT_GET, 'gid'))) { $gid = filter_input(INPUT_GET, 'gid'); }
if (null !== (filter_input(INPUT_GET, 'idpuerta'))) { $idpuerta = filter_input(INPUT_GET, 'idpuerta'); }
if (null !== (filter_input(INPUT_GET, 'arrdatos'))) { $datos = filter_input(INPUT_GET, 'arrdatos'); }
$token = "";
if($gid !== ""){
    $arrdatos = explode(",",$datos);
    for($i=0;$i<sizeof($arrdatos);$i++){
        if ($token !== ""){ $token .= ","; }
        $vigencia = explode(";",$arrdatos[$i])[0];
        $invitado = explode(";",$arrdatos[$i])[1];
        $codigo = $clsBaseDatos->keygen(29);
        if ($clsBaseDatos->getTokenInvitado($gid,$idpuerta,$vigencia,$invitado,$codigo) > 0) {
            $token .= $invitado . ":" . $codigo;
        }
    }
}
echo $token;
?>