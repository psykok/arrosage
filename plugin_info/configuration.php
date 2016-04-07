<?php

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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
if (!isConnect()) {
    include_file('desktop', '404', 'php');
    die();
}
?>



<form class="form-horizontal">
  <div class="form-group"i id="rain_config">
    <fieldset>


  <div class="form-group" >
        <label class="col-sm-2 control-label">{{Sonde pluie}}</label>
        <div class="col-sm-6">

                <input class="configKey form-control input-sm" data-l1key="rainSensor" placeholder="{{Nom sonde}}" style="margin-bottom : 5px;width : 300px; display : inline-block;">
                <a class="btn btn-default btn-sm cursor rainSensorInfo" data-input="infoName" style="margin-left : 5px;"><i class="fa fa-list-alt "></i> {{Rechercher équipement}}</a>

        </div>

  </div>

  <div class="form-group" >
        <label class="col-sm-2 control-label">{{Sonde vent}}</label>
        <div class="col-sm-6">

                <input class="configKey form-control input-sm" data-l1key="windSensor" placeholder="{{Nom sonde}}" style="margin-bottom : 5px;width : 300px; display : inline-block;">
                <a class="btn btn-default btn-sm cursor windSensorInfo" data-input="infoName" style="margin-left : 5px;"><i class="fa fa-list-alt "></i> {{Rechercher équipement}}</a>

        </div>

  </div>

   <div class="form-group">
          <label class="col-sm-2 control-label">{{Master Vanne}}</label>
          <div class="col-sm-2">
          <input type="checkbox" class="configKey bootstrapSwitch" data-l1key='masterValve' />
          </div>

   </div>

  <div class="form-group">
          <label class="col-sm-2 control-label">{{Master Vanne On}}</label>
          <div class="col-sm-6">

                  <input class="configKey form-control input-sm"  data-l1key="masterValveOn" placeholder="{{Nom Commande On}}" style="margin-bottom : 5px;width : 300px; display : inline-block;">
                  <a class="btn btn-default btn-sm cursor valveOnAction" data-input="infoName" style="margin-left : 5px;"><i class="fa fa-list-alt "></i> {{Rechercher équipement}}</a>

          </div>

    </div>


    <div class="form-group">
          <label class="col-sm-2 control-label">{{Master Vanne Off}}</label>
          <div class="col-sm-6">

                  <input class="configKey form-control input-sm"  data-l1key="masterValveOff" placeholder="{{Nom Commande Off}}" style="margin-bottom : 5px;width : 300px; display : inline-block;">
                  <a class="btn btn-default btn-sm cursor valveOffAction" data-input="infoName" style="margin-left : 5px;"><i class="fa fa-list-alt "></i> {{Rechercher équipement}}</a>

          </div>

    </div>


   <div class="form-group">
          <label class="col-sm-2 control-label">{{Master Vanne Status}}</label>
          <div class="col-sm-6">

                  <input class="configKey form-control input-sm"  data-l1key="masterValveStatus" placeholder="{{Nom Commande Status }}" style="margin-bottom : 5px;width : 300px; display : inline-block;">
                  <a class="btn btn-default btn-sm cursor valveStatusInfo" data-input="infoName" style="margin-left : 5px;"><i class="fa fa-list-alt "></i> {{Rechercher équipement}}</a>

          </div>

    </div>


  <script>
	

     function searchEquipment(idName, funName, controlName, equipmentType) {


       $(idName).delegate(funName, 'click', function() {
            var el = $(this);
            jeedom.cmd.getSelectModal({cmd: {type: equipmentType}}, function(result) {
                var calcul = el.closest('div').find('.configKey[data-l1key=' + controlName + ']');

                calcul.atCaret('insert', result.human);
            });
        });
}


        searchEquipment ("#rain_config",".rainSensorInfo","rainSensor","info");
        searchEquipment ("#rain_config",".windSensorInfo","windSensor","info");

 	searchEquipment ("#rain_config",".valveOnAction","masterValveOn","action");
        searchEquipment ("#rain_config",".valveOffAction","masterValveOff","action");
        searchEquipment ("#rain_config",".valveStatusInfo","masterValveStatus","info");





  </script>

</div>
</fieldset>
</form>

