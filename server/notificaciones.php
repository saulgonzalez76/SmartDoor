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
 * *
 * for raspberry pi only
 */


if (php_sapi_name() == 'cli') {
    include($_SERVER['DOCUMENT_ROOT'] . '/config.php');
    $conexion = new PDO ("mysql:host=".DATABASE_SERVER.";dbname=".DATABASE, DATABASE_USER, DATABASE_PASSWORD);
    $sql = "select tblClientePuerta.idcliente, tblPuerta.idregistro, tblPuerta.tiempo_apertura, tblPuerta.pin_apertura, (now() < tblClientePuerta.vigencia), tblClientePuerta.permanente, (now() > tblClientePuerta.fecha_hora), tblClientePuerta.idhorario from tblPuerta, tblClientePuerta where tblPuerta.idestacion = '$id_raspberry' and tblClientePuerta.idpuerta = tblPuerta.idregistro and tblClientePuerta.codigo = '$key'";
    $sql = "select tblLogin.whatsapp from tblCliente, tblLogin where tblCliente.idcliente = $idcliente and tblLogin.idusuario = tblCliente.idusuario";
    $sth = $conexion->prepare($sql); $sth->execute();
} else{ echo "This script is made to be run on a terminal or as a cronjob !"; exit; }
?>
