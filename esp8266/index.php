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
 * gets the qr code and returns access if success, also returns delay for the relay and records access to mysql
 */

include($_SERVER['DOCUMENT_ROOT'] . '/config.php');
function busca_codigo($codigo,$id) {
    $conexion = new PDO ("mysql:host=".DATABASE_SERVER.";dbname=".DATABASE, DATABASE_USER, DATABASE_PASSWORD);
    $sql = "select tblClientePuerta.idcliente, tblPuerta.idregistro, tblPuerta.tiempo_apertura, tblPuerta.pin_apertura, (now() < tblClientePuerta.vigencia), tblClientePuerta.permanente, (now() > tblClientePuerta.fecha_hora), tblClientePuerta.idhorario from tblPuerta, tblClientePuerta where tblPuerta.idestacion = '$id' and tblClientePuerta.idpuerta = tblPuerta.idregistro and tblClientePuerta.codigo = '$codigo'";
    $sth = $conexion->prepare($sql); $sth->execute(); $sth->setFetchMode(PDO::FETCH_NUM);
    if ($row = $sth->fetch()) {
        $hoy = date("w");
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
            $sql = "insert into tblRegistro values (0,'$id',$idcliente,'" . date("Y-m-d H:i:s") . "',$row[1],'" . date("Y-m-d H:i:s") . "',1)";
            $sth = $conexion->prepare($sql); $sth->execute();
            return $row[3] . ";" . ($row[2] * 1000);
        }
    }
    return "";
}

$req1 = file_get_contents("php://input");
$req2 = json_decode($req1);
$codigo= $req2 ->codigo;
$id=$req2 ->id;
$arrcodigo = explode(" ",$codigo);
$strcodigo="";
for ($i=0;$i<sizeof($arrcodigo);$i++){
    if ($arrcodigo[$i] > 32) {
        $strcodigo .= chr($arrcodigo[$i]);
    }
}

$datos = busca_codigo($strcodigo,$id);
echo $datos;
?>