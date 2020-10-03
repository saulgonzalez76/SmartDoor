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

$dblocal = new PDO ("mysql:host=".DATABASE_SERVER.";dbname=".DATABASE, DATABASE_USER, DATABASE_PASSWORD);
$dbserver = new PDO ("mysql:host=".DATABASE_SERVER_REMOTE.";dbname=".DATABASE_REMOTE, DATABASE_USER_REMOTE, DATABASE_PASSWORD_REMOTE);
$key = "";
$escliente = 0;
$keyindex = -1;
$esclienteindice = -1;
$keytype = -1;
$ejecutado = false;
$ahora = date("Y-m-d H:i:s");
$sth_remoto = $dbserver->prepare("select last_sync_db from tblEstacion where idraspberry = '$id_raspberry'");
$sth_remoto->execute ();
$row = $sth_remoto->fetch();
$fecha_sync = $row[0];
$sthtablas = $dblocal->prepare("show tables");
$sthtablas->execute();
print "\n\n<--------------- DATOS LOCALES -------------> \n\n";
while ($reftablas = $sthtablas->fetch()) {
    if (($reftablas[0] == "tblCliente") || ($reftablas[0] == "tblRegistro") || ($reftablas[0] == "tblPuerta")  || ($reftablas[0] == "tblClientePuerta") || ($reftablas[0] == "tblHorarioPuerta")){
        $sthkey = $dblocal->prepare("SHOW KEYS FROM " . $reftablas[0] . " WHERE Key_name = 'PRIMARY'");
        $sthkey->execute();
        $refkey = $sthkey->fetch();
        $key = $refkey[4];
        $keyindex = -1;
        $keytype = -1;
        $sthkey = $dblocal->prepare("show fields from " . $reftablas[0]);
        $sthkey->execute();
        while (($refkey = $sthkey->fetch()) && ($keytype < 0)) {
            $keyindex ++;
            if (stripos($refkey[0], $key) !== false) {
                if (stripos($refkey[1], "int") !== false) {
                    $keytype = 0;
                }
                else {
                    $keytype = 1;
                }
            }
        }
        $sthkey->execute();
        $escliente = 0;
        $esclienteindice = -1;
        while (($refkey = $sthkey->fetch()) && ($escliente == 0)) {
            $esclienteindice ++;
            if (stripos($refkey[0], "idcliente") !== false) { $escliente = 1;	}
        }
        $sqlserver = "";
        //echo "select * from " . $reftablas[0] . " where sync > 0\n";
        $sthregistros = $dblocal->prepare("select * from " . $reftablas[0] . " where sync > 0");
        $sthregistros->execute();
        while ($refregistros = $sthregistros->fetch(PDO::FETCH_BOTH)) {
            $tiposync = $refregistros['sync'];
            $ahora = date("Y-m-d H:i:s");
            $sqlkey = "";
            if ($keytype == 0) {
                $sqlkey = "select * from " . $reftablas[0] . " where " . $key . " = " . $refregistros[0];
            } else {
                $sqlkey = "select * from " . $reftablas[0] . " where " . $key . " = '" . $refregistros[0] . "'";
            }
            echo $sqlkey . "\n";
            $sthserver = $dbserver->prepare($sqlkey);
            $sthserver->execute();
            echo "encontrados " . $sthserver->rowCount() . "\n";
            if ($refserver = $sthserver->fetch()) {
                #si existe el registro en el servidor actualizo los datos
                #necesito ver si este registro le pertenece al mismo cliente
                if (($escliente == 1) && ($tiposync == 1)) {
                    #si la tabla tiene el campo idcliente entonces
                    if ($refregistros[$esclienteindice] != $refserver[$esclienteindice]) {
                        #si no es el mismo cliente/productor agrego un registro nuevo con los datos del servidor
                        $sqlserver = "insert into " . $reftablas[0] . " values (";
                        $idcampo = 0;
                        $sthcampos = $dblocal->prepare("show fields from " . $reftablas[0]);
                        $sthcampos->execute();
                        while ($refcampos = $sthcampos->fetch()) {
                            if ($sqlserver != ("insert into " . $reftablas[0] . " values (")) {$sqlserver .= ",";}
                            if ($idcampo == $keyindex) { $sqlserver .= "0"; } else {
                                if (stripos($refcampos[1], "int") !== false) {
                                    if (stripos($refcampos[0], "sync") !== false) {
                                        $sqlserver .= "1";
                                    }
                                    else {
                                        $sqlserver .= $refserver[$idcampo];
                                    }
                                }
                                else {
                                    if (stripos($refcampos[0], "actualizado") !== false) {
                                        $sqlserver .= "'" . $ahora . "'";
                                    }
                                    else {
                                        $sqlserver .= "'" . $refserver[$idcampo] . "'";
                                    }
                                }
                            }
                                $idcampo++;
                        }
                        $sqlserver .= ")";
                        $sthserver = $dbserver->prepare($sqlserver);
                        $sthserver->execute();
                    }
                }
                $sqlserver = "update " . $reftablas[0] . " set ";
                $idcampo = 0;
                $sthcampos = $dblocal->prepare("show fields from " . $reftablas[0]);
                $sthcampos->execute();
                while ($refcampos = $sthcampos->fetch()) {
                    if ($sqlserver != ("update " . $reftablas[0] . " set ")) {$sqlserver .= " ,";}
                    if (stripos($refcampos[1], "int") !== false) {
                        if (stripos($refcampos[0], "sync") !== false) { $sqlserver .= "sync = 0"; } else { $sqlserver .= $refcampos[0] . " = " . $refregistros[$idcampo]; }
                    } else {
                        if (stripos($refcampos[0], "actualizado") !== false) {
                            $sqlserver .= $refcampos[0] . " = '" . $ahora . "'";
                        } else {
                            $sqlserver .= $refcampos[0] . " = '" . $refregistros[$idcampo] . "'";
                        }
                    }
                    $idcampo++;
                }
                if ($keytype == 0) {
                    $sqlserver .= " where " . $key . " = " . $refregistros[0];
                } else {
                    $sqlserver .= " where " . $key . " = '" . $refregistros[0] . "'";
                }


        } else {
                #si no existe el registro en el servidor doy de alta
                $sqlserver = "insert into " . $reftablas[0] . " values (";
                $idcampo = 0;
                $sthcampos = $dblocal->prepare("show fields from " . $reftablas[0]);
                $sthcampos->execute();
                while ($refcampos = $sthcampos->fetch()) {
                    if ($sqlserver != ("insert into " . $reftablas[0] . " values (")) {$sqlserver .= ",";}
                    if (stripos($refcampos[1], "int") !== false) {
                        if (stripos($refcampos[0], "sync") !== false) {
                            $sqlserver .= "0";
                        }
                        else {
                            $sqlserver .= $refregistros[$idcampo];
                        }
                    }
                    else {
                        if (stripos($refcampos[0], "actualizado") !== false) {
                            $sqlserver .= "'" . $ahora . "'";
                        }
                        else {
                            $sqlserver .= "'" . $refregistros[$idcampo] . "'";
                        }
                    }
                    $idcampo++;
                }
                $sqlserver .= ")";
            }
            echo $sqlserver . "\n";
            $sthserver = $dbserver->prepare($sqlserver);
            $sthserver->execute();
            if ($keytype == 0) {
                $ejecutado = true;
                print "update " . $reftablas[0] . " set sync = 0 where " . $key . " = " . $refregistros[0] . "\n";
                $sthlocal = $dblocal->prepare("update " . $reftablas[0] . " set sync = 0 where " . $key . " = " . $refregistros[0]);
                $sthlocal->execute();
            } else {
                $ejecutado = true;
                print "update " . $reftablas[0] . " set sync = 0 where " . $key . " = '" . $refregistros[0] . "'\n";
                $sthlocal = $dblocal->prepare("update " . $reftablas[0] . " set sync = 0 where " . $key . " = '" . $refregistros[0] . "'");
                $sthlocal->execute();
            }
        }
    }
}

print "\n\n<--------------- DATOS DEL SERVIDOR -------------> \n\n";
#ACTUALIZO LOS REGISTROS DEL SERVIDOR EN EL DISPOSITIVO LOCAL
$keytype = -1;
$key = "";
$ahora = date("Y-m-d H:i:s");
$sthtablas = $dbserver->prepare("show tables");
$sthtablas->execute();
while ($reftablas = $sthtablas->fetch()) {
    #print "Procesando: " . $reftablas[0] . "\n";
    if (($reftablas[0] == "tblCliente") || ($reftablas[0] == "tblRegistro") || ($reftablas[0] == "tblPuerta")  || ($reftablas[0] == "tblClientePuerta") || ($reftablas[0] == "tblHorarioPuerta")) {
        $sthkey = $dbserver->prepare("SHOW KEYS FROM " . $reftablas[0] . " WHERE Key_name = 'PRIMARY'");
        $sthkey->execute();
        $refkey = $sthkey->fetch();
        $key = $refkey[4];
        $keytype = -1;
    $keyindex = -1;
        $sthkey = $dbserver->prepare("show fields from " . $reftablas[0]);
        $sthkey->execute();
        while (($refkey = $sthkey->fetch()) && ($keytype < 0)) {
            $keyindex ++;
            if (stripos($refkey[0], $key) !== false) {
                if (stripos($refkey[1], "int") !== false) {
                    $keytype = 0;
                }
                else {
                    $keytype = 1;
                }
            }
        }
        $sthkey->execute();
        $escliente = 0;
        $esclienteindice = -1;
        while (($refkey = $sthkey->fetch()) && ($escliente == 0)) {
            $esclienteindice ++;
            if (stripos($refkey[0], "idcliente") !== false) { $escliente = 1;     }
        }
        $sqlserver = "";
        //echo "select * from " . $reftablas[0] . " where sync > 0\n";
        $sthregistros = $dbserver->prepare("select * from " . $reftablas[0] . " where actualizado > '$fecha_sync'");
        $sthregistros->execute();
        while ($refregistros = $sthregistros->fetch(PDO::FETCH_BOTH)) {
            $tiposync = $refregistros['sync'];
            $ahora = date("Y-m-d H:i:s");
            $sqlkey = "";
            if ($keytype == 0) {
                $sqlkey = "select * from " . $reftablas[0] . " where " . $key . " = " . $refregistros[0];
            }
            else {
                $sqlkey = "select * from " . $reftablas[0] . " where " . $key . " = '" . $refregistros[0] . "'";
            }
            $sthlocal = $dblocal->prepare($sqlkey);
            $sthlocal->execute();
            echo "encontrados " . $sthlocal->rowCount() . "\n";
            if ($reflocal = $sthlocal->fetch()) {
                echo "creando update\n";
                #si existe el registro actualizo los datos
                    if (($escliente == 1) && ($tiposync == 1)) {
                    #si la tabla tiene el campo idcliente entonces
                    if ($refregistros[$esclienteindice] != $reflocal[$esclienteindice]) {
                        #si no es el mismo cliente/productor agrego un registro nuevo con los datos del servidor
                        $sqlserver = "insert into " . $reftablas[0] . " values (";
                        $idcampo = 0;
                                $sthcampos = $dblocal->prepare("show fields from " . $reftablas[0]);
                                $sthcampos->execute();
                                while ($refcampos = $sthcampos->fetch()) {
                            if ($sqlserver != ("insert into " . $reftablas[0] . " values (")) {$sqlserver .= ",";}
                                        if ($idcampo == $keyindex) { $sqlserver .= "0"; } else {
                                if (stripos($refcampos[1], "int") !== false) {
                                    if (stripos($refcampos[0], "sync") !== false) {
                                        $sqlserver .= "1";
                                    }
                                                else {
                                        $sqlserver .= $reflocal[$idcampo];
                                    }
                                        }
                                        else {
                                    if (stripos($refcampos[0], "actualizado") !== false) {
                                        $sqlserver .= "'" . $ahora . "'";
                                    }
                                                else {
                                        $sqlserver .= "'" . $reflocal[$idcampo] . "'";
                                    }
                                        }
                                        }
                                        $idcampo++;
                                }
                                $sqlserver .= ")";
                print "copiando registro en local.\n";
                                print $sqlserver . "\n";
                        $sthlocal = $dblocal->prepare($sqlserver);
                        $sthlocal->execute();
                        }
                }


                $sqlserver = "update " . $reftablas[0] . " set ";
                $idcampo = 0;
                $sthcampos = $dbserver->prepare("show fields from " . $reftablas[0]);
                $sthcampos->execute();
                while ($refcampos = $sthcampos->fetch()) {
                    if ($sqlserver != ("update " . $reftablas[0] . " set ")) {$sqlserver .= " ,";}
                    if (stripos($refcampos[1], "int") !== false) {
                        if (stripos($refcampos[0], "sync") !== false) {
                            $sqlserver .= "sync = 0";
                        }
                        else {
                            $sqlserver .= $refcampos[0] . " = " . $refregistros[$idcampo];
                        }
                    }
                    else {
                        if (stripos($refcampos[0], "actualizado") !== false) {
                            $sqlserver .= $refcampos[0] . " = '" . $ahora . "'";
                        }
                        else {
                            $sqlserver .= $refcampos[0] . " = '" . $refregistros[$idcampo] . "'";
                        }
                    }
                    $idcampo++;
                }
                if ($keytype == 0) {
                    $sqlserver .= " where " . $key . " = " . $refregistros[0];
                }
                else {
                    $sqlserver .= " where " . $key . " = '" . $refregistros[0] . "'";
                }
            }
            else {
                #si no existe el registro en el servidor doy de alta
                $sqlserver = "insert into " . $reftablas[0] . " values (";
                $idcampo = 0;
                $sthcampos = $dblocal->prepare("show fields from " . $reftablas[0]);
                $sthcampos->execute();
                while ($refcampos = $sthcampos->fetch()) {
                    if ($sqlserver != ("insert into " . $reftablas[0] . " values (")) {$sqlserver .= ",";}
                    if (stripos($refcampos[1], "int") !== false) {
                        if (stripos($refcampos[0], "sync") !== false) {
                            $sqlserver .= "0";
                        }
                        else {
                            $sqlserver .= $refregistros[$idcampo];
                        }
                    }
                    else {
                        if (stripos($refcampos[0], "actualizado") !== false) {
                            $sqlserver .= "'" . $ahora . "'";
                        }
                        else {
                            $sqlserver .= "'" . $refregistros[$idcampo] . "'";
                        }
                    }
                    $idcampo++;
                }
                $sqlserver .= ")";
            }
            print $sqlserver . "\n";
            $sthlocal = $dblocal->prepare($sqlserver);
            $sthlocal->execute();
            if ($keytype == 0) {
                $ejecutado = true;
                print "update " . $reftablas[0] . " set sync = 0 where " . $key . " = " . $refregistros[0] . "\n";
                $sthserver = $dbserver->prepare("update " . $reftablas[0] . " set sync = 0 where " . $key . " = " . $refregistros[0]);
                $sthserver->execute();
            } else {
                $ejecutado = true;
                print "update " . $reftablas[0] . " set sync = 0 where " . $key . " = '" . $refregistros[0] . "'\n";
                $sthserver = $dbserver->prepare("update " . $reftablas[0] . " set sync = 0 where " . $key . " = '" . $refregistros[0] . "'");
                $sthserver->execute();
            }
        }
    }
}
if ($ejecutado) {
    $sth_remoto = $dbserver->prepare("update tblEstacion set  last_sync_db = '" . date("Y-m-d H:i:s") . "' where idraspberry = '$id_raspberry'");
    $sth_remoto->execute();
}

exit;
