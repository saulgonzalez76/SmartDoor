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

function menu_inicio(){
    $('body').removeClass('sidebar-collapse');
    $.ajax({
        url: '../common_files/cargando.html',
        success: function (data) {
            _('contenido').innerHTML = data;
            $.ajax({
                url: 'inicio.php',
                success: function (data) {
                    _('contenido').innerHTML = data;
                }
            });
        }
    });
}

function menu_horarios(){
    $('body').removeClass('sidebar-collapse');
    $.ajax({
        url: '../common_files/cargando.html',
        success: function (data) {
            _('contenido').innerHTML = data;
            $.ajax({
                url: 'horarios.php',
                success: function (data) {
                    _('contenido').innerHTML = data;
                    $('#tmrL1').timepicker({ 'scrollDefault': 'now' });
                }
            });
        }
    });
}
