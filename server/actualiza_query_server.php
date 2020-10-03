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
$id_raspberry = trim(shell_exec("cat /proc/cpuinfo | grep Serial | cut -d ' ' -f 2"));
if (php_sapi_name() !== 'cli') { echo "This script is made to be run on a terminal or as a cronjob !"; exit; }
if ($id_raspberry !== "") { echo "This script is made to be run on raspberry pi, not the server !"; exit; }

include($_SERVER['DOCUMENT_ROOT'] . '/config.php');
$conexion = new PDO ("mysql:host=".DATABASE_SERVER.";dbname=".DATABASE, DATABASE_USER, DATABASE_PASSWORD);
$conexion_server = new PDO ("mysql:host=".DATABASE_SERVER_REMOTE.";dbname=".DATABASE_REMOTE, DATABASE_USER_REMOTE, DATABASE_PASSWORD_REMOTE);

$sth_remoto = $conexion_server->prepare("select last_sync_sql from tblEstacion where idraspberry = '$id_raspberry'");
$sth_remoto->execute ();
$row = $sth_remoto->fetch();
$fecha_sync = $row[0];

$sth_remoto = $conexion_server->prepare("select * from tblQuery where actualizado > '$fecha_sync'");
$sth_remoto->execute();
while ($row = $sth_remoto->fetch()) {
    $sth = $conexion->prepare($row[1]);
    $sth->execute();
}
$sth_remoto = $conexion_server->prepare("update tblEstacion set  last_sync_sql = '" . date("Y-m-d H:i:s") . "' where idraspberry = '$id_raspberry'");
$sth_remoto->execute ();

exit;
