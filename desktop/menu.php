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

include("../common_files/clases/seguridad.php");

$idcliente = 0;
$menu = 1;
$submenu = 0;
if (isset($_REQUEST['menu'])) { $menu = $_REQUEST['menu']; }
if (isset($_REQUEST['submenu'])) { $submenu = $_REQUEST['submenu']; }
if (isset($_SESSION['cliente_id'])) { $idcliente = $_SESSION['cliente_id'];}

$menuid = 1;
$submenuid = 1;
?>

<li class="treeview active"><a href="javascript:menu_inicio();"><i class="fa fa-dashboard fa-home" style="color: #000000; text-shadow: 1px 1px 1px #ffffff; font-size: 1em;"></i> <span>Inicio</span></a></li>
<li class="treeview active"><a href="javascript:menu_horarios();"><i class="fa fa-dashboard fa-clock" style="color: #000000; text-shadow: 1px 1px 1px #ffffff; font-size: 1em;"></i> <span>Horarios</span></a></li>


