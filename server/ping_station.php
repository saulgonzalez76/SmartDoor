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
$conexion = new PDO ("mysql:host=".DATABASE_SERVER_REMOTE.";dbname=".DATABASE_REMOTE, DATABASE_USER_REMOTE, DATABASE_PASSWORD_REMOTE);
$ssid = trim(shell_exec("tmate -S /tmp/tmate.sock display -p '#{tmate_ssh_ro}'"));
if ($ssid == "") {
    shell_exec("tmate -S /tmp/tmate.sock new-session -d");
    $ssid = trim(shell_exec("tmate -S /tmp/tmate.sock display -p '#{tmate_ssh}'"));
}
$sth = $conexion->prepare("update tblEstacion set actualizado = '" . date("Y-m-d H:i:s") . "', ssh_conn = '$ssid' where idraspberry = '$id_raspberry'");
$sth->execute();
exit;
