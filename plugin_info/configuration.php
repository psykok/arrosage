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
  <div class="form-group">
    <fieldset>


  <div class="form-group"  id="rain_config">
        <label class="col-sm-2 control-label">{{Sonde pluie}}</label>
        <div class="col-sm-6">

                <input class="configKey form-control input-sm" data-l1key="rainSensor" placeholder="{{Nom sonde}}" style="margin-bottom : 5px;width : 300px; display : inline-block;">
                <a class="btn btn-default btn-sm cursor listRainSensor" data-input="infoName" style="margin-left : 5px;"><i class="fa fa-list-alt "></i> {{Rechercher équipement}}</a>

        </div>

  </div>

  <div class="form-group"  id="wind_config">
        <label class="col-sm-2 control-label">{{Sonde vent}}</label>
        <div class="col-sm-6">

                <input class="configKey form-control input-sm" data-l1key="windSensor" placeholder="{{Nom sonde}}" style="margin-bottom : 5px;width : 300px; display : inline-block;">
                <a class="btn btn-default btn-sm cursor listWindSensor" data-input="infoName" style="margin-left : 5px;"><i class="fa fa-list-alt "></i> {{Rechercher équipement}}</a>

        </div>

  </div>
  <script>
	$("#wind_config").delegate(".listWindSensor", 'click', function() {
	    var el = $(this);
	    jeedom.cmd.getSelectModal({cmd: {type: 'info'}}, function(result) {
	        var calcul = el.closest('div').find('.configKey[data-l1key=windSensor]');

	        calcul.atCaret('insert', result.human);
	    });
	});


        $("#rain_config").delegate(".listRainSensor", 'click', function() {
            var el = $(this);
            jeedom.cmd.getSelectModal({cmd: {type: 'info'}}, function(result) {
                var calcul = el.closest('div').find('.configKey[data-l1key=rainSensor]');

                calcul.atCaret('insert', result.human);
            });
        });

  </script>

</div>
</fieldset>
</form>

