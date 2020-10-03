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
ini_set("date.timezone","America/Mexico_City");
session_start();
$jsondata = array();
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > WEBPAGE_TIMEOUT)) {
    $jsondata['logout'] = "1";
} else {
    $jsondata['logout'] = "0";
}
echo json_encode($jsondata);
exit;
?>