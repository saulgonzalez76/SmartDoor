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
 * for raspberry pi only
 */
$id_raspberry = trim(shell_exec("cat /proc/cpuinfo | grep Serial | cut -d ' ' -f 2"));
if (php_sapi_name() !== 'cli') { echo "This script is made to be run on a terminal or as a cronjob !"; exit; }
if ($id_raspberry !== "") { echo "This script is made to be run on raspberry pi, not the server !"; exit; }

include($_SERVER['DOCUMENT_ROOT'] . '/config.php');
function creaDir($ruta)
{
    $oldmask = umask(0);
    mkdir($ruta, 0777, true);
    umask($oldmask);
}

$update = 0;

$conexion = new PDO ("mysql:host=" . DATABASE_SERVER . ";dbname=" . DATABASE, DATABASE_USER, DATABASE_PASSWORD);
$conexion_server = new PDO ("mysql:host=" . DATABASE_SERVER_REMOTE . ";dbname=" . DATABASE_REMOTE, DATABASE_USER_REMOTE, DATABASE_PASSWORD_REMOTE);


$sth_remoto = $conexion_server->prepare("select last_sync_php from tblEstacion where idraspberry = '$id_raspberry'");
$sth_remoto->execute();
$row = $sth_remoto->fetch();
$fecha_sync = $row[0];
echo "ultima actualizacion: $fecha_sync\n";

$sth_remoto = $conexion_server->prepare("select * from tblSisArchivos where directorio like '%puerta_scripts%' and actualizado > '$fecha_sync'");
$sth_remoto->execute();

$connId = ftp_connect(FTP_SERVER) or die("Couldn't connect to " . FTP_SERVER);
if (@ftp_login($connId, FTP_USER, FTP_PASSWORD)) {
    echo "Conectado a ftp " . FTP_SERVER . "\n";
} else {
    echo "No me pude conectar...\n";
    exit;
}
ftp_pasv($connId, true);
ftp_chdir($connId, "puerta_scripts");
while ($row = $sth_remoto->fetch()) {
    $update = 1;
    $ftpsize = 0;
    $archsize = 0;
    $nombrearch = "/var/www/html/" . $row[1] . $row[2];
    $nombrearchtmp = $nombrearch . ".save";
    $tam_ftp = ftp_size($connId, $row[2]);
    if (ftp_get($connId, $nombrearchtmp, $row[2], FTP_BINARY)) {
        $tam_loc = filesize($nombrearchtmp);
        if ($tam_ftp == $tam_loc) {
            echo "Archivo correcto, guardando...\n";
            $sth2 = $conexion->prepare("insert into tblSisArchivos values (0,'$row[1]','$row[2]','" . date("Y-m-d H:i:s") . "',0)");
            $sth2->execute();
//            $sth2 = $conexion_server->prepare("update tblSisArchivos set sync = 0 where directorio = '$row[1]' and archivo = '$row[2]'");
//            $sth2->execute();
            rename($nombrearchtmp, $nombrearch);
        } else {
            echo "Archivo incorrecto, intentar de nuevo.\n";
        }
    }
}


$sth_remoto = $conexion_server->prepare("select * from tblSisArchivos where directorio like '%server%' and actualizado > '$fecha_sync'");
$sth_remoto->execute();
ftp_chdir($connId, "../server");
while ($row = $sth_remoto->fetch()) {
    $update = 1;
    $ftpsize = 0;
    $archsize = 0;
    $nombrearch = "/var/www/html/" . $row[1] . $row[2];
    $nombrearchtmp = $nombrearch . ".save";
    $tam_ftp = ftp_size($connId, $row[2]);
    if (ftp_get($connId, $nombrearchtmp, $row[2], FTP_BINARY)) {
        $tam_loc = filesize($nombrearchtmp);
        if ($tam_ftp == $tam_loc) {
            echo "Archivo correcto, guardando...\n";
            $sth2 = $conexion->prepare("insert into tblSisArchivos values (0,'$row[1]','$row[2]','" . date("Y-m-d H:i:s") . "',0)");
            $sth2->execute();
            rename($nombrearchtmp, $nombrearch);
        } else {
            echo "Archivo incorrecto, intentar de nuevo.\n";
        }
    }
}
ftp_close($connId);
if ($update > 0) {
    $sth_remoto = $conexion_server->prepare("update tblEstacion set  last_sync_php = '" . date("Y-m-d H:i:s") . "' where idraspberry = '$id_raspberry'");
    $sth_remoto->execute();
}
exit;

