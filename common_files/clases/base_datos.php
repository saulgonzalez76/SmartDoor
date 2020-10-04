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

$_SESSION['LAST_ACTIVITY'] = time();

class Base_Datos {
    private $conexion;

    public function __construct(){
        if(!isset($this->conexion)){
            $this->conexion = new PDO ("mysql:host=".DATABASE_SERVER.";dbname=".DATABASE, DATABASE_USER, DATABASE_PASSWORD);
        }
    }

	public function logout($idusuario) {
		$sql = "update tblLogin set conectado = 0, session = '' where idtrabajador = $idusuario";
                $sth = $this->conexion->prepare($sql);
                $sth->execute();
		return 0;
	}

	public function cambio_password($idusuario,$password) {
		$sql = "update tblLogin set password = '$password', pass = sha2('$password',256), pass_renew = 0 where idusuario = $idusuario";
		$sth = $this->conexion->prepare($sql);
		$sth->execute();
		return 0;
	}

    public function registro_nuevo($id,$idcliente) {
        $sql = "insert into tblRegistro values (0,'$id',$idcliente,'" . date("Y-m-d H:i:s") . "','" . date("Y-m-d H:i:s") . "',1)";
        $sth = $this->conexion->prepare($sql);
        $sth->execute();
        return 0;
    }

    public function logphp($estacion,$datos) {
        $sql = "insert into tblLog values ('$estacion','$datos')";
        $sth = $this->conexion->prepare($sql);
        $sth->execute();
        return 0;
    }

    public function getTokenInvitado($gid,$idpuerta,$vigencia,$invitado,$codigo) {
        $sql = "select idcliente from tblCliente where go_id = '$gid'";
        $sth = $this->conexion->prepare($sql);
        $sth->execute();
        $sth->setFetchMode(PDO::FETCH_NUM);
        $row = $sth->fetch();
        $idcliente = $row[0];

        $sql = "select idpuerta from tblClientePuerta where codigo = '$idpuerta'";
        $sth = $this->conexion->prepare($sql);
        $sth->execute();
        $sth->setFetchMode(PDO::FETCH_NUM);
        $row = $sth->fetch();
        $idpuerta = $row[0];

        $sql = "insert into tblClientePuerta values (0,$idcliente,$idpuerta,'$codigo','$vigencia',date_add('$vigencia', INTERVAL 10 HOUR),0,'$invitado','" . date("Y-m-d H:i:s") . "',1)";
        $sth = $this->conexion->prepare($sql);
        $sth->execute();
        return $this->conexion->lastInsertId();
    }


    public function busca_codigo($codigo) {
        $retorno = "";
        $sql = "select concat(tblCliente.nombre,' ',tblCliente.apellido_paterno,' ',tblCliente.apellido_materno) as nombre, tblCliente.go_foto, tblClientePuerta.vigencia, tblPuerta.nombre, tblEstacion.nombre, tblEstacion.ubicacion from tblCliente, tblClientePuerta, tblPuerta, tblEstacion where tblClientePuerta.codigo = '$codigo' and (tblClientePuerta.fecha_hora < now() and now() < tblClientePuerta.vigencia) and tblPuerta.idregistro = tblClientePuerta.idpuerta and tblEstacion.idraspberry = tblPuerta.idestacion and tblCliente.idcliente = tblClientePuerta.idcliente";
        $sth = $this->conexion->prepare($sql); $sth->execute(); $sth->setFetchMode(PDO::FETCH_NUM);
        if ($row = $sth->fetch()) {
           $retorno = $row[0] . ";" . $row[1] . ";" . $row[2] . ";". $row[3] . ";" . $row[4] . ";" . $row[5];
        }
        return $retorno;
    }

    public function horarios_nuevo($lunes,$martes,$miercoles,$jueves,$viernes,$sabado,$domingo) {
        $sql = "insert into tblHorarioPuerta values (0,'$lunes','$martes','$miercoles','$jueves','$viernes','$sabado','$domingo','" . date("Y-m-d H:i:s") . "',1)";
        $sth = $this->conexion->prepare($sql);
        $sth->execute();
        return 1;
    }

    public function horarios_listado() {
        $sql = "select * from tblHorarioPuerta";
        $sth = $this->conexion->prepare($sql); $sth->execute(); $sth->setFetchMode(PDO::FETCH_NUM);
        return $sth;
    }

    public function busca_codigo_vigencia($codigo) {
        $retorno = "";
        $sql = "select concat(tblCliente.nombre,' ',tblCliente.apellido_paterno,' ',tblCliente.apellido_materno) as nombre, tblCliente.go_foto, tblClientePuerta.fecha_hora, tblPuerta.nombre, tblEstacion.nombre, tblEstacion.ubicacion from tblCliente, tblClientePuerta, tblPuerta, tblEstacion where tblClientePuerta.codigo = '$codigo' and tblClientePuerta.fecha_hora > now() and tblPuerta.idregistro = tblClientePuerta.idpuerta and tblEstacion.idraspberry = tblPuerta.idestacion and tblCliente.idcliente = tblClientePuerta.idcliente";
        $sth = $this->conexion->prepare($sql); $sth->execute(); $sth->setFetchMode(PDO::FETCH_NUM);
        if ($row = $sth->fetch()) {
            $retorno = $row[0] . ";" . $row[1] . ";" . $row[2] . ";" . $row[3] . ";" . $row[4] . ";" . $row[5];
        }
        return $retorno;
    }

    public function login_android_registro_gid($id,$acceso,$nombre,$foto,$email,$apellido,$token,$version,$idcliente){
        switch ($acceso) {
            case 1:
                // Facebook Login
                // records user data to mysql if new user
                if ($idcliente > 0) {
                    $sql = "update tblCliente set email = '$email', fb_foto = '$foto', fb_token = '$token', app_version = '$version', actualizado = '" . date("Y-m-d H:i:s") . "', sync = 1 where idcliente = $idcliente";
                    $sth = $this->conexion->prepare($sql);
                    $sth->execute();
                } else {
                    $sql = "insert into tblCliente values (0,0,'$email','$nombre','$apellido','',0,'$id','','0.00','','','$foto','$token','$version','" . date("Y-m-d H:i:s") . "',0)";
                    $sth = $this->conexion->prepare($sql);
                    $sth->execute();
                }
                break;
            case 2:
                // Google Login
                // records user data to mysql if new user
                if ($idcliente > 0) {
                    $sql = "update tblCliente set email = '$email', go_foto = '$foto', go_token = '$token', app_version = '$version', actualizado = '" . date("Y-m-d H:i:s") . "', sync = 1 where idcliente = $idcliente";
                    $sth = $this->conexion->prepare($sql);
                    $sth->execute();
                } else {
                    $sql = "insert into tblCliente values (0,0,'$email','$nombre','$apellido','',0,'','$id','0.00','$foto','$token','','','$version','" . date("Y-m-d H:i:s") . "',0)";
                    $sth = $this->conexion->prepare($sql);
                    $sth->execute();
                }
                break;
        }
        return 0;
    }

    public function login_android_gid($id,$acceso,$nombre,$foto,$email,$apellido,$token,$version){
        $idcliente = 0;
        // search email address to see if the user has an account
        $sql = "select idcliente from tblCliente where email = '$email'";
        $sth = $this->conexion->prepare($sql); $sth->execute();
        if ($row = $sth->fetch()){
            // if client exists then use the client id
            $idcliente = $row[0];
        }
        // update client info or create a new record
        $this->login_android_registro_gid($id,$acceso,$nombre,$foto,$email,$apellido,$token,$version,$idcliente);

        // get all the access qr codes for client
        $retorno = "";
        switch ($acceso){
            case 2:
                // Google Login
                $sql = "select tblClientePuerta.codigo, tblPuerta.nombre, tblEstacion.nombre, tblEstacion.idraspberry from tblEstacion, tblCliente, tblClientePuerta, tblPuerta where tblCliente.go_id = '$id' and tblClientePuerta.idcliente = tblCliente.idcliente and tblClientePuerta.invitado = '' and (tblClientePuerta.permanente = 1 || tblClientePuerta.vigencia > now()) and tblPuerta.idregistro = tblClientePuerta.idpuerta and tblEstacion.idraspberry = tblPuerta.idestacion";
                break;
                // Facebook Login
            case 1:
                $sql = "select tblClientePuerta.codigo, tblPuerta.nombre, tblEstacion.nombre, tblEstacion.idraspberry from tblEstacion, tblCliente, tblClientePuerta, tblPuerta where tblCliente.fb_id = '$id' and tblClientePuerta.idcliente = tblCliente.idcliente and tblClientePuerta.invitado = '' and (tblClientePuerta.permanente = 1 || tblClientePuerta.vigencia > now()) and tblPuerta.idregistro = tblClientePuerta.idpuerta and tblEstacion.idraspberry = tblPuerta.idestacion";
                break;
        }
        $sth = $this->conexion->prepare($sql);
        $sth->execute();
        $sth->setFetchMode(PDO::FETCH_NUM);
        while ($row = $sth->fetch()) {
            if ($retorno !== "") {$retorno .= ";"; }
            $retorno .= $row[0] . "," . $row[1] . "," . $row[2];
        }
        return $retorno;
    }

	public function login($usuario,$password) {
		$sql = "select idusuario,correo,pass_renew,nombre,fecha_acceso from tblLogin where tblLogin.login = '$usuario' and tblLogin.pass = sha2('$password',256)";
		$sth = $this->conexion->prepare($sql);
		$sth->execute();
		$sth->setFetchMode(PDO::FETCH_NUM);
		if ($row = $sth->fetch()) {
			$_SESSION['usuario']['idusuario'] = $row[0];           //id del trabajador o usuario del sistema
			$_SESSION['usuario']['nickname'] = $usuario;              //login del sistema
			$_SESSION['usuario']['email'] = $row[1];               //email del usuario
            $_SESSION['usuario']['nombre'] = $row[3];      //nombre completo del usuario
			$_SESSION['usuario']['fecha_acceso'] = $row[4];      //fecha de ultimo acceso del usuario
            $_SESSION['usuario']['sessionid'] = session_id();
			$cambia_pass = $row[2];                     //si es la primera ves que entra tiene que cambiar el password
			$session = session_id();                    //session del servidor
			//actualizo para guardar que si esta conectado el usuario
			$sql = "update tblLogin set tblLogin.session = '$session', tblLogin.conectado = 1, fecha_acceso = '" . date("Y-m-d H:i:s") . "' where tblLogin.idusuario = " . $_SESSION['usuario']['idusuario'];
			$sth = $this->conexion->prepare($sql);
            $sth->execute();
            if ($cambia_pass == 1) {
                return -1;
            }
            return 1;
		} else {
			return 0;
		}
	}

	public function usuario_info($idusuario,$tipo_usuario) {
		switch ($tipo_usuario) {
			case 0:
				//si es comprador o productor entonces
				$sql = "select tblLogin.login, tblLogin.nombre, tblLogin.correo, tblCliente.busqueda from tblLogin, tblCliente where tblLogin.idtrabajador = $idusuario and tblCliente.idcliente = tblLogin.idcliente";
				break;
			case 1:
				//si es usuario con privilegio
				$sql = "select tblLogin.login, tblLogin.nombre, tblLogin.correo, concat('Todo') as empresa from tblLogin where tblLogin.idtrabajador = $idusuario";
				break;
		}
		$sth = $this->conexion->prepare($sql);
                $sth->execute();
                $sth->setFetchMode(PDO::FETCH_NUM);
		return $sth;
	}

	public function usuarios_actualiza_datos($idusuario,$nombre,$email) {
		$sql = "update tblLogin set nombre = '$nombre', correo = '$email' where idtrabajador = $idusuario";
		$sth = $this->conexion->prepare($sql);                $sth->execute();                $sth->setFetchMode(PDO::FETCH_NUM);
		return "0";
	}

    public function estacion_version($idestacion,$version) {
        $sql = "update tblEstacion set version = $version, last_sync_db = now() where idraspberry = '$idestacion'";
        $sth = $this->conexion->prepare($sql);                $sth->execute();
        return 1;
    }

    public function estacion_puertas($idestacion) {
        $sql = "select idregistro, nombre from tblPuerta where idestacion = '$idestacion'";
        $sth = $this->conexion->prepare($sql);                $sth->execute();
        return $sth;
    }

    public function keygen($length=10) {
        $key = '';
        list($usec, $sec) = explode(' ', microtime());
        mt_srand((float) $sec + ((float) $usec * 100000));
        $inputs = array_merge(range('z','a'),range(0,9),range('A','Z'));
        for($i=0; $i<$length; $i++) {
            $key .= $inputs{mt_rand(0,61)};
        }
        return $key;
    }

    public function cliente_nuevo($idpuerta, $nombre, $appaterno, $apmaterno, $telefono, $email) {
        $codigo = $this->keygen(29);
        $sql = "insert into tblCliente values (0," . $_SESSION['usuario']['idusuario'] . ",'$email','$nombre','$appaterno','$apmaterno',0,'','','0.00','','','" . date("Y-m-d H:i:s") . "',1)";
        $sth = $this->conexion->prepare($sql);                $sth->execute();
        $idcliente = $this->conexion->lastInsertId();
        $sql = "insert into tblClientePuerta values (0,$idcliente,$idpuerta,'$codigo','" . date("Y-m-d H:i:s") . "','" . date("Y-m-d H:i:s") . "',1,'$telefono','" . date("Y-m-d H:i:s") . "',1)";
        $sth = $this->conexion->prepare($sql);                $sth->execute();
        if ($this->conexion->lastInsertId() > 0){
            return $codigo;
        } else {
            return "";
        }
    }

    public function estacion_update($idestacion,$version) {
        $sql = "select version_actual from tblEstacion where idraspberry = '$idestacion'";
        $sth = $this->conexion->prepare($sql);                $sth->execute();
        $sth->setFetchMode(PDO::FETCH_NUM);
        if ($row = $sth->fetch()) {
            if ($row[0] > $version) {
                return "http://intellidoor.firmware.intellibasc.com/ESP01" . $row[0] . ".bin";
            }
        } else {
            return "";
        }
    }

    public function privilegio_modulo($modulo) {
        $this->conexion->exec("set names utf8");
        $sql = "select idregistro from tblPrivilegio where idtrabajador = " . $_SESSION['usuario']['idusuariosuc'] . " and forma = '$modulo'";
        $sth = $this->conexion->prepare($sql);                $sth->execute();
        $sth->setFetchMode(PDO::FETCH_NUM);
        if ($sth->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /* ------------------------------------------------------------------------------------------------------------------------------------------------------
         USUARIOS - LISTADO DE PRIVILEGIOS     */
    public function usuarios_privilegios($idtrabajador) {
        $this->conexion->exec("set names utf8");
        $sql = "select forma from tblPrivilegio where idtrabajador = $idtrabajador";
        $sth = $this->conexion->prepare($sql);                $sth->execute();
        return $sth;
    }

    public function usuarios_listado_puertas($idusuario) {
        $this->conexion->exec("set names utf8");
        $sql = "select tblPuerta.nombre, tblEstacion.nombre, tblCliente.nombre, tblEstacion.idraspberry, tblPuerta.idregistro, tblCliente.idcliente from tblCliente, tblPuerta, tblEstacion, tblClientePuerta where tblEstacion.cliente_admin = $idusuario and tblPuerta.idestacion = tblEstacion.idraspberry and tblClientePuerta.idpuerta = tblPuerta.idregistro and tblClientePuerta.permanente = 1 and tblCliente.idcliente = tblClientePuerta.idcliente order by tblEstacion.idraspberry";
        $sth = $this->conexion->prepare($sql);                $sth->execute();
        return $sth;
    }

    public function usuarios_puerta_datos($idcliente,$idpuerta) {
        $this->conexion->exec("set names utf8");
        $sql = "select tblEstacion.nombre, tblPuerta.nombre, tblClientePuerta.codigo, concat(tblCliente.nombre,' ',tblCliente.apellido_paterno,' ',tblCliente.apellido_materno) as nombre from tblCliente, tblPuerta, tblClientePuerta, tblEstacion where tblPuerta.idregistro = $idpuerta and tblEstacion.idraspberry = tblPuerta.idestacion and tblCliente.idcliente = $idcliente and tblClientePuerta.idcliente = tblCliente.idcliente and tblClientePuerta.idpuerta = tblPuerta.idregistro";
        $sth = $this->conexion->prepare($sql);                $sth->execute();
        return $sth;
    }

    /* ------------------------------------------------------------------------------------------------------------------------------------------------------
     SISTEMA - LISTA DE MODULOS O FORMAS O PRIVILEGIOS DEL SISTEMA     */
    public function sistema_modulos() {
        $this->conexion->exec("set names utf8");
        $sql = "select forma,nombre,descripcion from tblModuloSistema order by nombre";
        $sth = $this->conexion->prepare($sql);                $sth->execute();
        return $sth;
    }

    /* ------------------------------------------------------------------------------------------------------------------------------------------------------
     USUARIOS - CREAR UN USUARIO NUEVO PARA EL SISTEMA     */
    public function usuarios_nuevo($nombre,$password,$email,$login,$tipo,$privilegios,$idcliente) {
        $this->conexion->exec("set names utf8");
        $sql = "insert into tblLogin values (0,'$login','$password','$nombre','$email','','',0,$tipo,0,1,'1900-01-01 00:00:00',$idcliente,'" . date("Y-m-d H:i:s") . "',1)";
        $sth = $this->conexion->prepare($sql);                $sth->execute();
        $arrpriv = explode(",",$privilegios);
        $sql = "select last_insert_id()";
        $sth = $this->conexion->prepare($sql);
        $sth->execute();
        $sth->setFetchMode(PDO::FETCH_NUM);
        $row = $sth->fetch();
        $idtemp = $row[0];
        $sql = "update tblLogin set pass = sha2('$password',256) where idtrabajador = $idtemp";
        $sth = $this->conexion->prepare($sql);                $sth->execute();
        for ($i=0;$i<count($arrpriv);$i++) {
            $sql = "insert into tblPrivilegio values (0,$idtemp,'$arrpriv[$i]','" . date("Y-m-d H:i:s") . "',1)";
            $sth = $this->conexion->prepare($sql);                $sth->execute();
        }
        return 1;
    }

    public function usuarios_editar($nombre,$password,$email,$login,$tipo,$privilegios,$idtrabajador) {
        $this->conexion->exec("set names utf8");
        $sql = "update tblLogin set login = '$login', password = '$password', nombre = '$nombre', correo = '$email', pass = sha2('$password',256), privilegio_web = $tipo, actualizado = '" . date("Y-m-d H:i:s") . "', sync = 1 where idtrabajador = $idtrabajador";
        $sth = $this->conexion->prepare($sql);                $sth->execute();
        $sql = "delete from tblPrivilegio where idtrabajador = $idtrabajador";
        $sth = $this->conexion->prepare($sql);                $sth->execute();
        $arrpriv = explode(",",$privilegios);
        for ($i=0;$i<count($arrpriv);$i++) {
            $sql = "insert into tblPrivilegio values (0,$idtrabajador,'$arrpriv[$i]','" . date("Y-m-d H:i:s") . "',1)";
            $sth = $this->conexion->prepare($sql);                $sth->execute();
        }
        return 1;
    }

    public function usuarios_editar_privilegios($privilegios,$idtrabajador) {
        $this->conexion->exec("set names utf8");
        $sql = "delete from tblPrivilegio where idtrabajador = $idtrabajador";
        $sth = $this->conexion->prepare($sql);                $sth->execute();
        $arrpriv = explode(",",$privilegios);
        for ($i=0;$i<count($arrpriv);$i++) {
            $sql = "insert into tblPrivilegio values (0,$idtrabajador,'$arrpriv[$i]','" . date("Y-m-d H:i:s") . "',1)";
            $sth = $this->conexion->prepare($sql);                $sth->execute();
        }
        return 1;
    }

    public function usuarios_cancelar($idtrabajador) {
        $this->conexion->exec("set names utf8");
        $sql = "update tblLogin set cancelado = 1, actualizado = '" . date("Y-m-d H:i:s") . "', sync = 1 where idtrabajador = $idtrabajador";
        $sth = $this->conexion->prepare($sql);                $sth->execute();
        $sql = "delete from tblPrivilegio where idtrabajador = $idtrabajador";
        $sth = $this->conexion->prepare($sql);                $sth->execute();
        return 1;
    }

} // FIN DE CLASE