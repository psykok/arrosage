<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
sendVarToJS('eqType', 'arrosage');
$eqLogics = eqLogic::byType('arrosage');
?>

<div class="row row-overflow">
    <div class="col-lg-2 col-md-3 col-sm-4">
        <div class="bs-sidebar">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
                <a class="btn btn-default eqLogicAction" style="width : 100%;margin-top : 5px;margin-bottom: 5px;" data-action="add"><i class="fa fa-plus-circle"></i> {{Ajouter Zone}}</a>
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
                <?php
foreach ($eqLogics as $eqLogic) {
	echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '"><a>' . $eqLogic->getHumanName(true) . '</a></li>';
}
?>
           </ul>
       </div>
   </div>

   <div class="col-lg-10 col-md-9 col-sm-8 eqLogicThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">
    <legend>{{Mes zones}}
    </legend>
    <div class="eqLogicThumbnailContainer">
      <div class="cursor eqLogicAction" data-action="add" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
       <center>
        <i class="fa fa-plus-circle" style="font-size : 7em;color:#94ca02;"></i>
    </center>
    <span style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#94ca02"><center>Ajouter</center></span>
</div>
<?php
foreach ($eqLogics as $eqLogic) {
	echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >';
	echo "<center>";
	echo '<img src="plugins/arrosage/doc/images/arrosage_icon.png" height="105" width="95" />';
	echo "</center>";
	echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;"><center>' . $eqLogic->getHumanName(true, true) . '</center></span>';
	echo '</div>';
}
?>
</div>

</div>

<div class="col-lg-10 col-md-9 col-sm-8 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
    <div class='row'>
        <div class="col-sm-6">
            <form class="form-horizontal">
                <fieldset>
                    <legend><i class="fa fa-arrow-circle-left eqLogicAction cursor" data-action="returnToThumbnailDisplay"></i> {{Général}}<i class='fa fa-cogs eqLogicAction pull-right cursor expertModeVisible' data-action='configure'></i></legend>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">{{Nom de l'équipement arrosage}}</label>
                        <div class="col-sm-6">
                            <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                            <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement arrosage}}"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label" >{{Objet parent}}</label>
                        <div class="col-sm-6">
                            <select class="eqLogicAttr form-control" data-l1key="object_id">
                                <option value="">{{Aucun}}</option>
                                <?php
foreach (object::all() as $object) {
	echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
}
?>
                           </select>
                       </div>
                   </div>
                   <div class="form-group">
                    <label class="col-sm-4 control-label"></label>
                    <div class="col-sm-8">
                      <input type="checkbox" class="eqLogicAttr bootstrapSwitch" data-label-text="{{Activer}}" data-l1key="isEnable" checked/>
                      <input type="checkbox" class="eqLogicAttr bootstrapSwitch" data-label-text="{{Visible}}" data-l1key="isVisible" checked/>
                  </div>
              </div>
        </fieldset>
    </form>
</div>
<div class="col-sm-6 " id="arrosage_config">
    <form class="form-horizontal">
        <fieldset>
                <legend>{{Configuration Arrosage}}</legend>
                <div class="form-group">
                    <label class="col-sm-2 control-label">{{Hivernage}}</label>
                    <div class="col-sm-6">
                    <input type="checkbox" class="eqLogicAttr bootstrapSwitch" data-l1key='configuration' data-l2key='winterMode' />
                    </div>
                </div>

              <div class="form-group">
                    <label class="col-sm-2 control-label">{{Arret si pluie}}</label>
                    <div class="col-sm-6">
                    <input type="checkbox" class="eqLogicAttr bootstrapSwitch" data-l1key='configuration' data-l2key='rainStop' />
                    </div>
                </div>

              <div class="form-group">
                    <label class="col-sm-2 control-label">{{Arret si vent}}</label>
                    <div class="col-sm-1">
                    <input type="checkbox" class="eqLogicAttr bootstrapSwitch" data-l1key='configuration' data-l2key='windStop' />
                    </div>

	            <label class="col-sm-3 control-label">{{Vitesse max (km/h)}}</label>
                    <div class="col-sm-1">
                    <input class="eqLogicAttr form-control input-sm" data-l1key="configuration" data-l2key="windSpeedMax" placeholder="{{10}}" style="margin-bottom : 5px;width : 50px; display : inline-block;">
                    </div>

                </div>

             <div class="form-group">
                    <label class="col-sm-2 control-label">{{Arret humidité}}</label>
                    <div class="col-sm-1">
                    <input type="checkbox" class="eqLogicAttr bootstrapSwitch" data-l1key='configuration' data-l2key='moistureStop' />
		    </div>

                    <label class="col-sm-1 control-label">{{Min}}</label>
                    <div class="col-sm-1">
                    <input class="eqLogicAttr form-control input-sm" data-l1key="configuration" data-l2key="moistureMin" placeholder="{{10}}" style="margin-bottom : 5px;width : 50px; display : inline-block;">
                    </div>

                    <label class="col-sm-1 control-label">{{Max}}</label>
                    <div class="col-sm-1">
                    <input class="eqLogicAttr form-control input-sm" data-l1key="configuration" data-l2key="moistureMax" placeholder="{{80}}" style="margin-bottom : 5px;width :50 px; display : inline-block;">
                    </div>

              </div>

              <div class="form-group">
                    <label class="col-sm-2 control-label">{{Sonde humidité}}</label>
                    <div class="col-sm-6">

                            <input class="eqLogicAttr form-control input-sm" data-l1key="configuration" data-l2key="moistureSensor" placeholder="{{Nom sonde}}" style="margin-bottom : 5px;width : 300px; display : inline-block;">
                            <a class="btn btn-default btn-sm cursor listEquipementInfo" data-input="infoName" style="margin-left : 5px;"><i class="fa fa-list-alt "></i> {{Rechercher équipement}}</a>

                    </div>

              </div>

	      <div class="form-group">
		    <label class="col-sm-2 control-label">{{Commande On}}</label>
                    <div class="col-sm-6">

			    <input class="eqLogicAttr form-control input-sm" data-l1key="configuration" data-l2key="zoneOn" placeholder="{{Nom Commande On}}" style="margin-bottom : 5px;width : 300px; display : inline-block;">
			    <a class="btn btn-default btn-sm cursor listEquipementAction" data-input="infoName" style="margin-left : 5px;"><i class="fa fa-list-alt "></i> {{Rechercher équipement}}</a>

                    </div>

	      </div>


              <div class="form-group">
                    <label class="col-sm-2 control-label">{{Commande Off}}</label>
                    <div class="col-sm-6">

                            <input class="eqLogicAttr form-control input-sm" data-l1key="configuration" data-l2key="zoneOff" placeholder="{{Nom Commande On}}" style="margin-bottom : 5px;width : 300px; display : inline-block;">
                            <a class="btn btn-default btn-sm cursor listEquipementAction" data-input="infoName" style="margin-left : 5px;"><i class="fa fa-list-alt "></i> {{Rechercher équipement}}</a>

                    </div>

              </div>


             <div class="form-group">
                    <label class="col-sm-2 control-label">{{Commande Status}}</label>
                    <div class="col-sm-6">

                            <input class="eqLogicAttr form-control input-sm" data-l1key="configuration" data-l2key="zoneStatus" placeholder="{{Nom Commande Status }}" style="margin-bottom : 5px;width : 300px; display : inline-block;">
                            <a class="btn btn-default btn-sm cursor listEquipementInfo" data-input="infoName" style="margin-left : 5px;"><i class="fa fa-list-alt "></i> {{Rechercher équipement}}</a>

                    </div>

              </div>





            </div>
        </fieldset>
    </form>
</div>

<legend>{{arrosage}}</legend>
<a class="btn btn-success btn-sm cmdAction" data-action="add"><i class="fa fa-plus-circle"></i> {{Ajouter une sequence d'arrosage}}</a><br/><br/>
<table id="table_cmd" class="table table-bordered table-condensed">
    <thead>
        <tr>
    <th>{{Nom}}</th><th>{{Heure début}}</th><th>{{Durée}}</th><th>{{Jours}}</th><th></th>
        </tr>
    </thead>
    <tbody>

    </tbody>
</table>

<form class="form-horizontal">
    <fieldset>
        <div class="form-actions">
            <a class="btn btn-danger eqLogicAction" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
            <a class="btn btn-success eqLogicAction" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
        </div>
    </fieldset>
</form>

</div>
</div>

<?php
include_file('desktop', 'arrosage', 'js', 'arrosage');
include_file('core', 'plugin.template', 'js');
?>
