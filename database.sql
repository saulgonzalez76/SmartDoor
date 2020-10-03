create database smartdoor;
use smartdoor;
create table tblCliente
(
    idcliente int auto_increment
        primary key,
    idusuario int default 2 not null,
    email varchar(100) not null,
    nombre varchar(100) not null,
    apellido_paterno varchar(100) not null,
    apellido_materno varchar(100) not null,
    sexo tinyint(1) not null,
    fb_id varchar(50) not null,
    go_id varchar(50) not null,
    saldo double(15,2) not null,
    go_foto varchar(200) not null,
    go_token varchar(100) not null,
    actualizado datetime default '1900-01-01 00:00:00' not null,
    sync tinyint(1) not null
);

create table tblClientePuerta
(
    idregistro int auto_increment
        primary key,
    idcliente int not null,
    idpuerta int not null,
    codigo varchar(100) not null,
    fecha_hora datetime default '1900-01-01 00:00:00' not null,
    vigencia datetime default '1900-01-01 00:00:00' not null,
    permanente tinyint(1) not null,
    invitado varchar(20) not null,
    idhorario int default 0 not null,
    actualizado datetime default '1900-01-01 00:00:00' not null,
    sync tinyint(1) default 0 not null
);

create table tblEstacion
(
    idraspberry varchar(20) not null
        primary key,
    ubicacion varchar(100) not null,
    cliente_admin int not null,
    ssh_conn varchar(50) not null,
    last_sync_db datetime default '1900-01-01 00:00:00' not null,
    last_sync_php datetime default '1900-01-01 00:00:00' not null,
    last_sync_sql datetime default '1900-01-01 00:00:00' not null,
    hardware varchar(100) not null,
    version_actual int not null,
    version int not null,
    nombre varchar(100) not null
);

create table tblHorarioPuerta
(
    idhorario int auto_increment
        primary key,
    lunes varchar(19) default '00:00:00,00:00:00' not null,
    martes varchar(19) default '00:00:00,00:00:00' not null,
    miercoles varchar(19) default '00:00:00,00:00:00' not null,
    jueves varchar(19) default '00:00:00,00:00:00' not null,
    viernes varchar(19) default '00:00:00,00:00:00' not null,
    sabado varchar(19) default '00:00:00,00:00:00' not null,
    domingo varchar(19) default '00:00:00,00:00:00' not null,
    actualizado datetime default '1900-01-01 00:00:00' not null,
    sync tinyint(1) default 0 not null
);

create table tblLog
(
    estacion varchar(30) null,
    datos varchar(5000) null
);

create table tblLogin
(
    idusuario tinyint auto_increment,
    login varchar(100) default '' not null,
    password varchar(100) default '' not null,
    nombre varchar(100) not null,
    correo varchar(100) not null,
    pass varchar(250) not null,
    session varchar(250) not null,
    conectado tinyint(1) default 0 not null,
    cancelado tinyint(1) default 0 not null,
    pass_renew tinyint(1) default 1 not null,
    fecha_acceso datetime default CURRENT_TIMESTAMP not null,
    whatsapp varchar(15) not null,
    constraint idusuario
        unique (idusuario)
)
    engine=MyISAM;

create table tblPuerta
(
    idregistro int auto_increment
        primary key,
    idestacion varchar(100) not null,
    nombre varchar(100) not null,
    tiempo_apertura int not null,
    pin_apertura tinyint(2) not null,
    actualizado datetime default '1900-01-01 00:00:00' not null,
    sync tinyint(1) not null
);

create table tblQuery
(
    idregistro int auto_increment
        primary key,
    query varchar(1000) not null,
    actualizado datetime default '1900-01-01 00:00:00' not null,
    sync tinyint(1) default 1 not null
);

create table tblRegistro
(
    idregistro int auto_increment
        primary key,
    idestacion varchar(20) not null,
    idcliente int not null,
    hora datetime default '1900-01-01 00:00:00' not null,
    idpuerta int not null,
    actualizado datetime default '1900-01-01 00:00:00' not null,
    sync tinyint(1) not null
);

create table tblSisArchivos
(
    idregistro int auto_increment
        primary key,
    directorio varchar(500) not null,
    archivo varchar(500) not null,
    actualizado datetime not null,
    sync tinyint(1) not null
);

