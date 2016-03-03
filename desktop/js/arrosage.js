
/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

$('.eqLogicAttr[data-l1key=configuration][data-l2key=sendMode]').on('change', function () {
    $('.sendMode').hide();
    $('.sendMode.' + $(this).value()).show();
});

$("#table_cmd").delegate(".listEquipementInfo", 'click', function () {
    var el = $(this);
    jeedom.cmd.getSelectModal({cmd: {type: 'info'}}, function (result) {
        var calcul = el.closest('tr').find('.cmdAttr[data-l1key=configuration][data-l2key=' + el.data('input') + ']');
        calcul.atCaret('insert', result.human);
    });
});
/*
 $("#arrosage").delegate(".listEquipementAction", 'click', function () {
    var el = $(this);
    var subtype = $(this).closest('.cmd').find('.cmdAttr[data-l1key=subType]').value();
    jeedom.cmd.getSelectModal({cmd: {type: 'action', subType: subtype}}, function (result) {
        var calcul = el.closest('tr').find('.cmdAttr[data-l1key=configuration][data-l2key=' + el.attr('data-input') + ']');
        calcul.atCaret('insert', result.human);
    });
});
*/
$("#arrosage_config").delegate(".listEquipementAction", 'click', function() {
    var el = $(this);
    jeedom.cmd.getSelectModal({cmd: {type: 'action'}}, function(result) {
        var calcul = el.closest('div').find('.eqLogicAttr[data-l1key=configuration]');
        
        calcul.atCaret('insert', result.human);
    });
});

$("#arrosage_config").delegate(".listEquipementInfo", 'click', function() {
    var el = $(this);
    jeedom.cmd.getSelectModal({cmd: {type: 'info'}}, function(result) {
        var calcul = el.closest('div').find('.eqLogicAttr[data-l1key=configuration]');
        
        calcul.atCaret('insert', result.human);
    });
});


function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = {configuration: {}};
    }
    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
    tr += '<td>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="id" style="display : none;">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="name"></td>';
    tr += '<td><input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="duration" placeholder="00:00"></td>';

    tr += '<td><input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="startTime" placeholder="00:00"></td>';

    tr += '<td>';
    tr += '<label class="checkbox-inline" style="margin-right:1em;"><input class="cmdAttr" data-l1key="configuration" data-l2key="cbDay1" type="checkbox" name="startDays" id="mon" value="1">L</label>';
    tr += '<label class="checkbox-inline" style="margin-right:1em;"><input class="cmdAttr" data-l1key="configuration" data-l2key="cbDay2" type="checkbox" name="startDays" id="tue" value="2">M</label>';
    tr += '<label class="checkbox-inline" style="margin-right:1em;"><input class="cmdAttr" data-l1key="configuration" data-l2key="cbDay3" type="checkbox" name="startDays" id="wed" value="3">M</label>';
    tr += '<label class="checkbox-inline" style="margin-right:1em;"><input class="cmdAttr" data-l1key="configuration" data-l2key="cbDay4" type="checkbox" name="startDays" id="thu" value="4">J</label>';
    tr += '<label class="checkbox-inline" style="margin-right:1em;"><input class="cmdAttr" data-l1key="configuration" data-l2key="cbDay5" type="checkbox" name="startDays" id="fri" value="5">V</label>';
    tr += '<label class="checkbox-inline" style="margin-right:1em;"><input class="cmdAttr" data-l1key="configuration" data-l2key="cbDay6" type="checkbox" name="startDays" id="sat" value="6">S</label>';
    tr += '<label class="checkbox-inline" style="margin-right:1em;"><input class="cmdAttr" data-l1key="configuration" data-l2key="cbDay7" type="checkbox" name="startDays" id="sun" value="7">D</label>';
    tr += '</td>';

/*
    tr += '<td>';

  tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="infoName" placeholder="{{Nom information}}" style="margin-bottom : 5px;width : 70%; display : inline-block;">';
    tr += '<a class="btn btn-default btn-sm cursor listEquipementAction" data-input="infoName" style="margin-left : 5px;"><i class="fa fa-list-alt "></i> {{Rechercher équipement}}</a>';
    tr += '</td>';
*/ 


 
    tr += '<td>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="type" value="action" style="display : none;">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="subType" value="superbox" style="display : none;">';
    if (is_numeric(_cmd.id)) {
        tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fa fa-cogs"></i></a> ';
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
    }
    tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i></td>';
    tr += '</tr>';
    $('#table_cmd tbody').append(tr);
    $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
}