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

$ds = DIRECTORY_SEPARATOR;
$idusuario = $_SESSION['usuario']['idusuario'];
$rutainicial = "archivos";

if (!is_dir("../" . $rutainicial)) { creaDir("../" . $rutainicial);}

//SUBIR IMAGEN DE USUARIO AVATAR
if (null !== (filter_input(INPUT_POST, 'avatar'))) {
    if (null !== (filter_input(INPUT_POST, 'idcliente'))) {
        $idcliente = filter_input(INPUT_POST, 'idcliente');
    }

    if (!empty($_FILES)) {
        if (!is_dir("../" . $rutainicial . $ds . $idcliente)) {
            creaDir("../" . $rutainicial . $ds . $idcliente);
        }
        $tempFile = $_FILES['archavatarcliente']['tmp_name'];
        $archivolocal = "../" . $rutainicial . $ds . $idcliente . $ds . "avatar.png";
        imagepng(imagecreatefromstring(file_get_contents($tempFile)), $archivolocal);
        //header("Refresh:0; url=main.php");
    }
}

//  SUBIR FOTO DEL USUARIO
if (null !== (filter_input(INPUT_POST, 'fotousuario'))) {
    if (!empty($_FILES)) {
        $tempFile = $_FILES['archavatarusr']['tmp_name'];
        $archivolocal = "../common_files/img/usr" . $ds . $idusuario . ".png";
        imagepng(imagecreatefromstring(file_get_contents($tempFile)), $archivolocal);
    }
   //header("Refresh:0; url=main.php");
}


?>


