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

require_once "../common_files/clases/base_datos.php";
$clsBaseDatos = new Base_Datos();


function enviaHeaders($archivo,$nombre){
    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Cache-Control: private', false); // required for certain browsers
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $nombre . '";');
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: ' . filesize($archivo));
    readfile($archivo);
}

if (null !== (filter_input(\INPUT_GET, 'tipo'))) { $tipo = filter_input(\INPUT_GET, 'tipo'); }
switch ($tipo) {
    case 1:  // descarga archivos generados, pdf como facturas
        $archivo = filter_input(\INPUT_GET, 'archivo');
        enviaHeaders("../common_files/clases/qrcode/cache/" . $archivo . ".png",basename($archivo . ".png"));
        break;
}
exit;