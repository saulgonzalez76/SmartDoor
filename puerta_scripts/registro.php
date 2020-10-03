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
 * raspberry pi modules to be used with usb port on the qr sensor
 */
include($_SERVER['DOCUMENT_ROOT'] . '/config.php');

	if ($argc > 1) {
		$id_raspberry = trim(shell_exec("cat /proc/cpuinfo | grep Serial | cut -d ' ' -f 2"));
		$key = urldecode($argv[1]);
		//93b7153d63a53913f4763e53a33f43e3b22716
		$key = str_replace("3a","A",$key);
		$key = str_replace("3b","B",$key);
		$key = str_replace("3c","C",$key);
		$key = str_replace("3d","D",$key);
		$key = str_replace("3e","E",$key);
		$key = str_replace("3f","F",$key);
		$key = str_replace("3g","G",$key);
		$key = str_replace("3h","H",$key);
		$key = str_replace("3i","I",$key);
		$key = str_replace("3j","J",$key);
		$key = str_replace("3k","K",$key);
		$key = str_replace("3l","L",$key);
		$key = str_replace("3m","M",$key);
		$key = str_replace("3n","N",$key);
		$key = str_replace("3o","O",$key);
		$key = str_replace("3p","P",$key);
		$key = str_replace("3q","Q",$key);
		$key = str_replace("3r","R",$key);
		$key = str_replace("3s","S",$key);
		$key = str_replace("3t","T",$key);
		$key = str_replace("3u","U",$key);
		$key = str_replace("3v","V",$key);
		$key = str_replace("3w","W",$key);
		$key = str_replace("3x","X",$key);
		$key = str_replace("3y","Y",$key);
		$key = str_replace("3z","Z",$key);
		//echo $key . PHP_EOL;
		$idcliente = 0;
		$conexion = new PDO ("mysql:host=".DATABASE_SERVER.";dbname=".DATABASE, DATABASE_USER, DATABASE_PASSWORD);
		$sql = "select tblClientePuerta.idcliente, tblPuerta.idregistro, tblPuerta.tiempo_apertura, tblPuerta.pin_apertura, (now() < tblClientePuerta.vigencia), tblClientePuerta.permanente, (now() > tblClientePuerta.fecha_hora), tblClientePuerta.idhorario from tblPuerta, tblClientePuerta where tblPuerta.idestacion = '$id_raspberry' and tblClientePuerta.idpuerta = tblPuerta.idregistro and tblClientePuerta.codigo = '$key'";

		$sth = $conexion->prepare("insert into tblLog values ('$sql')"); $sth->execute();
		$hoy = date("w");
		$sth = $conexion->prepare($sql); $sth->execute();
		if ($row = $sth->fetch()) {
			$horario = false;
			if ($row[7] > 0){
				$sql_horario = "select * from tblHorarioPuerta where idhorario = " . $row[7];
				$sth_horario = $conexion->prepare($sql_horario); $sth_horario->execute();
				$row_horario = $sth_horario->fetch();
				switch ($hoy){
					case 0:
						//domingo
						$horario =  ((strtotime(date(explode(",",$row_horario[7])[0])) < strtotime(date("H:i:s"))) && (strtotime(date(explode(",",$row_horario[7])[1])) > strtotime(date("H:i:s")))) ;
						break;
					case 1:
						//lunes
						$horario =  ((strtotime(date(explode(",",$row_horario[1])[0])) < strtotime(date("H:i:s"))) && (strtotime(date(explode(",",$row_horario[1])[1])) > strtotime(date("H:i:s")))) ;
						break;
					case 2:
						//martes
						$horario =  ((strtotime(date(explode(",",$row_horario[2])[0])) < strtotime(date("H:i:s"))) && (strtotime(date(explode(",",$row_horario[2])[1])) > strtotime(date("H:i:s")))) ;
						break;
					case 3:
						//miercoles
						$horario =  ((strtotime(date(explode(",",$row_horario[3])[0])) < strtotime(date("H:i:s"))) && (strtotime(date(explode(",",$row_horario[3])[1])) > strtotime(date("H:i:s")))) ;
						break;
					case 4:
						//jueves
						$horario =  ((strtotime(date(explode(",",$row_horario[4])[0])) < strtotime(date("H:i:s"))) && (strtotime(date(explode(",",$row_horario[4])[1])) > strtotime(date("H:i:s")))) ;
						break;
					case 5:
						//viernes
						$horario =  ((strtotime(date(explode(",",$row_horario[5])[0])) < strtotime(date("H:i:s"))) && (strtotime(date(explode(",",$row_horario[5])[1])) > strtotime(date("H:i:s")))) ;
						break;
					case 6:
						//sabado
						$horario =  ((strtotime(date(explode(",",$row_horario[6])[0])) < strtotime(date("H:i:s"))) && (strtotime(date(explode(",",$row_horario[6])[1])) > strtotime(date("H:i:s")))) ;
						break;
				}
			}
			if (($row[5] > 0) || ($horario) || (($row[4] > 0) && ($row[6] > 0))){
				$idcliente = $row[0];
				$sth = $conexion->prepare("insert into tblRegistro values (0,'$id_raspberry',$idcliente,'" . date("Y-m-d H:i:s") . "',$row[1],'" . date("Y-m-d H:i:s") . "',1)"); $sth->execute();
				exec("gpio mode $row[3] out");
				sleep($row[2]);
				exec("gpio mode $row[3] in");
			}
		}
	}
?>
