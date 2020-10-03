/*
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

var tmrFormatoTabla;
var tmrDatePicker;
var tmrfotosreportes;
var tmrCargaHora =  setInterval(lblhora, 1000);
var tmrSeguridad =  setInterval(seguridadjs, 5000);
var url="";
var map;
var darection;

function seguridadjs() {
    var data;
    $.ajax({
        dataType: "json",
        url: '../common_files/clases/seguridad_main_js.php',
        data: data,
        success: function (data) {
            if (data.logout > 0) {
                location.href = "../lock.php";
            }
        }
    });
}

function lblhora (){
    var now = new Date();
    var nombreMes = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
    var nombreDia = ["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado"];
    _("lblfecha").innerHTML = nombreDia[now.getDay()] + " " + now.getDate() + " de " + nombreMes[now.getMonth()] + " del " + now.getFullYear() + "  " + (now.getHours()<10?'0'+now.getHours():now.getHours()) + ":" + (now.getMinutes()<10?'0'+now.getMinutes():now.getMinutes()) + ":" + (now.getSeconds()<10?'0'+now.getSeconds():now.getSeconds());
    _("txtHora").value = nombreDia[now.getDay()] + " " + now.getDate() + " de " + nombreMes[now.getMonth()] + " del " + now.getFullYear() + "  " + (now.getHours()<10?'0'+now.getHours():now.getHours()) + ":" + (now.getMinutes()<10?'0'+now.getMinutes():now.getMinutes()) + ":" + (now.getSeconds()<10?'0'+now.getSeconds():now.getSeconds());
}

function formatoTabla(paging,retrieve,lengthChange,searching,ordering,info,autoWidth,botones) {
    var tablas = document.getElementsByClassName("table");
    for (i = 0; i < tablas.length; i++) {
        if (tablas[i].getAttribute("id").match(/lst.*/)) {
            $("table#" + tablas[i].getAttribute("id")).DataTable({
                "stateSave": true,
                "paging": paging,
                "retrieve": retrieve,
                "lengthChange": lengthChange,
                "searching": searching,
                "ordering": ordering,
                "info": info,
                "autoWidth": autoWidth,
                "dom": 'Bfrtip',
                "buttons": [botones],
                "language": {
                    "buttons": {
                        "print": "  Imprimir"
                    },
                    "search": "Buscar:",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    "emptyTable": "No se encontraron registros",
                    "infoEmpty": "No se encontraron registros",
                    "zeroRecords": "No se encontraron registros",
                    "paginate": {
                        "previous": "Anterior",
                        "next": "Siguiente"
                    }
                }
            });
            clearInterval(tmrFormatoTabla);
        }
    }
    clearInterval(tmrFormatoTabla);
}

function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

function validaUsuarioNuevo() {
    res = "";
    if (_('txtNombre').value === "") { res = "Falta el nombre completo del usuario.<br>"; }
    if (_('txtLogin').value === "") { res += "Falta el nombre de usuario para acceder al sistema.<br>"; }
    if (_('txtPassword').value === "") { res += "Falta la contraseña del usuario.<br>"; }
    if (_('txtEmail').value === "") { res += "Falta el correo electronico del usuario.<br>"; }
    if ((_('txtEmail').value !== "") && (!validateEmail(_('txtEmail').value))) { res += "El correo electronico no es valido.<br>"; }
    if (_('txtPassword2').value === "") { res += "Falta la confirmacion de la contraseña del usuario.<br>"; }
    if (_('txtPassword2').value !== _('txtPassword').value) { res += "La contraseña y la confirmacion no coinciden.<br>"; }
    if ((_('txtPassword2').value !== "") && (_('txtPassword').value !== "") && (_('txtPassword2').value === _('txtPassword').value)) {
        if (_('txtPassword').value.length < 5) { res += "La contraseña debe de ser de almenos 6 caracteres.<br>"; }
    }
    var privilegios = $('#privilegios').val();
    if ((!privilegios) && (_('tipo').value === 1)) { res += "Falta seleccionar al menos un privilegio de la lista.<br>"; }

    if (res !== "") {
        Swal.fire('Error!',"Se encontraron algunos errores:<br><br>" + res + "<br>Favor de corregirlos antes de guardar.",'error');
        return false;
    } else { return true; }
}

function tipoUsuarioNuevo(valor,cliente) {
    if (valor === 5) {
        _('lblCliente').innerHTML = "<strong>" + cliente + "</strong>";
    } else {
        _('lblCliente').innerHTML = "";
    }
}

function valorListaChk(tabla, limpia){
        var chkArray = [];
        var table = $('#' + tabla).DataTable();
        table.rows().nodes().to$().find('input[type="checkbox"]').each(function(){
            if(this.checked){
                if (limpia) { this.checked = false; } else { chkArray.push($(this).val()); }
            }
        });
        var selected = "";
        selected = chkArray.join(',') ;
        return selected;
    }

function _(el){
    return document.getElementById(el);
}

function uploadFile(tipo,archivo, idcliente, progress, objfile, idregistro, uuid){
    var cant = _(objfile).files.length;
    if (cant === 0) { Swal.fire('Error!',"Error, debe seleccionar al menos un archivo !",'error');  return; }
    for (i=0;i<cant;i++) {
        var file = _(objfile).files[i];
        var formdata = new FormData();
        formdata.append(objfile, file);
        formdata.append(tipo,archivo);
        formdata.append("idcliente",idcliente);
        if (tipo === 'anticipo') { formdata.append("idregistro",idregistro); formdata.append("uuid",uuid); }
        var ajax = new XMLHttpRequest();
        if (progress) {
            ajax.upload.addEventListener("progress", progressHandler, false);
            ajax.addEventListener("load", completeHandler, false);
            ajax.addEventListener("error", errorHandler, false);
            ajax.addEventListener("abort", abortHandler, false);
        }
        ajax.open("POST", "subir_archivos.php");
        ajax.send(formdata);
    }
}

function progressHandler(event){
    _("loaded_n_total").innerHTML = "Uploaded "+event.loaded+" bytes of "+event.total;
    var percent = (event.loaded / event.total) * 100;
    _("progressBar").value = Math.round(percent);
    _("status").innerHTML = Math.round(percent)+"% Subiendo archivo, por favor espere...";
}

function completeHandler(event){
    _("status").innerHTML = event.target.responseText;
    _("progressBar").value = 0;
    if (url !== "") { ajaxpage(url,'contenido'); url = ""; }
}

function errorHandler(event){
    _("status").innerHTML = "Fallo la carga !";
}

function abortHandler(event){
    _("status").innerHTML = "Carga abortada !";
}

function setDatePicker(){
    if (_('datepicker')) {
        $('#datepicker').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "yyyy-mm-dd"
        });
        $('#datepicker').datepicker()
            .on('changeDate', function (e) {
                getPrecios(e.format("yyyy-mm-dd"));
                _('lblfacturafecha').innerHTML = e.format("yyyy-mm-dd");
                _('txtfacturafecha').value = e.format("yyyy-mm-dd");
                tmrFormatoTabla=setInterval(function() {formatoTabla(true,true,false,true,false,true,true);},1000);
            });
        clearInterval(tmrDatePicker);
    }
}

function formatMoneda(n, c, d, t) {
    var c = isNaN(c = Math.abs(c)) ? 2 : c,
        d = d === undefined ? "." : d,
        t = t === undefined ? "," : t,
        s = n < 0 ? "-" : "",
        i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
        j = (j = i.length) > 3 ? j % 3 : 0;

    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}

function carga_camara() {
    Webcam.set({
        width: 640,
        height: 480,
        image_format: 'png',
        jpeg_quality: 100
    });
    Webcam.attach('#camara');
}

function take_snapshot() {
    Webcam.snap( function(data_uri) {
        _('camara').innerHTML = '<img id="imgfoto_identificacion" src="'+data_uri+'"/>';
        //_('btnCamara_verificaion').style = "display: inline;";
        _('imgavatar').src = data_uri;
        guardar_imagen_camara();
    } );
}

function guardar_imagen_camara() {
    var myImg = _("imgfoto_identificacion").src;
    $.ajax({
        type: "POST",
        url: "subir_archivos.php",
        data: {
            foto_identidad: 1,
            imagen: myImg
        }
    }).done(function(data) {
        _('lblStatus').innerHTML = "Im&aacute;gen guardada.";
        _('divReiniciaCamara').style = "display: inline";
    });
}


function cambiaavatar(elemento,tipo,idcliente,objfile) {
    _(objfile).onchange = function (evt) {
        var tgt = evt.target || window.event.srcElement,
            files = tgt.files;
        // FileReader support
        if (FileReader && files && files.length) {
            var fr = new FileReader();
            fr.onload = function () {
                _(elemento).src = fr.result;
                if (elemento === 'imgavatarusr2') { _('imgavatarusr').src = fr.result; }
                if (elemento === 'imgavatar') { _('imgavatarid').src = fr.result; }
                uploadFile(tipo,1,idcliente,false,objfile,0,'');
            };
            fr.readAsDataURL(files[0]);
        }
    }
}

function confirmacion(tipo, url, contenido, tiene_tabla, tiene_fecha, params, titulo, mensaje, objetos=''){
    Swal.fire({
        title: titulo,
        text: mensaje,
        type: 'warning',
        heightAuto: false,
        cancelButtonText: 'Cancelar',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, estoy seguro!'
    }).then((result) => {
        if (result.value) {
            Swal.fire( 'Hecho!', '', 'success' );
            switch (tipo) {
                case 1: //imprime un recibo de pago parcial o total de un anticipo
                    window.open('print.php?tipo=5&arrinteres=' + arrlistaabono_interes.join(';') + '&idanticipo=' + arrlistaabono.join(';') + '&abonoparcial=' + abonoparcial + '&cantidad=' + _('txtcantidad').value + '&fecha=' + _('datepickeranticipospago').value,'_blank');
                    if (abonoparcial > 0){
                        window.open('print.php?tipo=6&idanticipo=' + arrlistaabono[arrlistaabono.length-1] + '&cantidad=' + abonoapagar + '&fecha=' + _('datepickeranticipospago').value,'_blank');
                    }
                    abonoapagar = 0;
                    abonoparcial = 0;
                    arrlistaabono_interes = [];
                    arrlistaabono = [];
                    break;
                case 2: // boton de imprimir anticipo, pasa a el div con informacion para guardar el cheque
                    _(objetos.split('#')[0]).style='display:none';
                    _(objetos.split('#')[1]).style='display:none';
                    break;
            }
            ajaxpage(url + params ,contenido);
            if (tiene_tabla) {tmrFormatoTabla=setInterval(function() {formatoTabla(true,true,false,true,false,true,true);},1000);}
            if (tiene_fecha) {tmrDatePicker=setInterval(setDatePicker,500);}
        }
    })
}


