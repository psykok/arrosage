<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
$plugin = plugin::byId('arrosage');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">
    <div class="col-lg-2 col-md-3 col-sm-4">
        <div class="bs-sidebar">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
                <a class="btn btn-default eqLogicAction" style="width : 100%;margin-top : 5px;margin-bottom: 5px;" data-action="add"><i class="fa fa-plus-circle"></i> {{Ajouter Zone}}</a>
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
                <?php
			//Arrosage zone
			foreach ($eqLogics as $eqLogic) {
				echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '" data-eqLogic_type="arrosage"><a>' . $eqLogic->getHumanName(true) . '</a></li>';
			}
                        //Arroasge master control
			foreach (eqLogic::byType('arrosage_master') as $eqLogic) {
				echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '" data-eqLogic_type="arrosage_master"><a>' . $eqLogic->getHumanName(true) . '</a></li>';
			}
			//Arrosage taske central
			foreach (eqLogic::byType('arrosage_tasker') as $eqLogic) {
                                echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '" data-eqLogic_type="arrosage_tasker"><a>' . $eqLogic->getHumanName(true) . '</a></li>';
                        }
		?>
           </ul>
       </div>
   </div>

   <div class="col-lg-10 col-md-9 col-sm-8 eqLogicThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">



   <legend><i class="fa fa-cog"></i>  {{Gestion}}</legend>
   <div class="eqLogicThumbnailContainer">

   <!-- Add zone button -->
    <div class="cursor eqLogicAction" data-action="add" style="background-color : #ffffff; height : 140px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
     <center>
      <i class="fa fa-plus-circle" style="font-size : 5em;color:#94ca02;"></i>
    </center>
    <span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#94ca02"><center>Ajouter</center></span>
  </div>
  



  <!-- Plugin configuration button -->
  <div class="cursor eqLogicAction" data-action="gotoPluginConf" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;">
    <center>
      <i class="fa fa-wrench" style="font-size : 5em;color:#767676;"></i>
    </center>
    <span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Configuration}}</center></span>
  </div>

	<?php
		//Arroasge master control
		foreach (eqLogic::byType('arrosage_master') as $eqLogic) {
		        echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >';
		        echo "<center>";
		        echo '<img src="plugins/arrosage/doc/images/master_icon.png" height="105" width="95" />';
		        echo "</center>";
		        echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;"><center>' . $eqLogic->getHumanName(true, true) . '</center></span>';
		        echo '</div>';
		}
                 //Arrosage taske central
                foreach (eqLogic::byType('arrosage_tasker') as $eqLogic) {
                        echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >';
                        echo "<center>";
                        echo '<img src="plugins/arrosage/doc/images/master_icon.png" height="105" width="95" />';
                        echo "</center>";
                        echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;"><center>' . $eqLogic->getHumanName(true, true) . '</center></span>';
                        echo '</div>';
                }
	?>
</div>




    <legend><i class="fa fa-table"></i>{{Mes zones}}</legend>
    <div class="eqLogicThumbnailContainer">
	<?php
                //list all zone
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

<div class="col-lg-10 col-md-9 col-sm-8 arrosage eqLogic" style="border-left: solid 1px #EEE; padding-left: 50px;padding-right: 50px;display: none;">
  <a class="btn btn-success eqLogicAction pull-right" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
  <a class="btn btn-danger eqLogicAction pull-right" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
  <a class="btn btn-default eqLogicAction pull-right" data-action="configure"><i class="fa fa-cogs"></i> {{Configuration avanc�e}}</a>
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
    <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fa fa-tachometer"></i> {{Equipement}}</a></li>
    <li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Commandes}}</a></li>
  </ul>
<div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
  <div role="tabpanel" class="tab-pane active" id="eqlogictab">
		<div class="">
			<form class="form-horizontal">
				<fieldset>
					</br>
                    			<div class="form-group">
                    			    <label class="col-sm-4 control-label">{{Nom de la zone}}</label>
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
					        <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
					        <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
                    			 </div>
                   			</div>
				</fieldset>
	    		</form>
		</div>
<div class="" id="arrosage_config">
    <form class="form-horizontal">
        <fieldset>
                <legend>{{Configuration}}</legend>

                <div class="form-group">
                    <label class="col-sm-2 control-label">{{Type arrosage}}</label>
                    <div class="col-sm-6">
                    <select class="eqLogicAttr form-control" data-l1key='configuration' data-l2key='zoneType' style="width : 200px;" >
			 <option value="sprinkler">{{Standard}}</option>
			 <option value="drip">{{Goute à goute}}</option>
		    </select>	

                    </div>
                </div>
		
                <div class="form-group">
                    <label class="col-sm-2 control-label">{{Hivernage}}</label>
                    <div class="col-sm-6">
                    <input type="checkbox" class="eqLogicAttr " data-l1key='configuration' data-l2key='winterMode' />
                    </div>
                </div>

              <div class="form-group">
                    <label class="col-sm-2 control-label">{{Arret si pluie}}</label>
                    <div class="col-sm-6">
                    <input type="checkbox" class="eqLogicAttr " data-l1key='configuration' data-l2key='rainStop' />
                    </div>
                </div>

                <div style="border:  solid 1px #d9d9d9; padding-top: 20px;margin-bottom: 20px;">
              		<div class="form-group">
              		      <label class="col-sm-2 control-label">{{Arret si vent}}</label>
              		      <div class="col-sm-2">
              		      	<input type="checkbox" class="eqLogicAttr " data-l1key='configuration' data-l2key='windStop' />
              		      </div>
   	      		      <div class="input-group"  style="width : 180px;">	
	      		      	<div class="input-group-addon" >{{Max}}</div>
              		      	<input class="eqLogicAttr form-control" type="number" min="0" data-l1key="configuration" data-l2key="windSpeedMax" >
	      		      	<div class="input-group-addon">km/h</div>
	      		      </div>
              		  </div>
                </div>


                <div style="border:  solid 1px #d9d9d9; padding-top: 20px;margin-bottom: 20px;">
             		<div class="form-group">
             		       <label class="col-sm-2 control-label">{{Arret humidité}}</label>
             		       <div class="col-sm-2">
             		       	<input type="checkbox" class="eqLogicAttr " data-l1key='configuration' data-l2key='moistureStop' />
	     		       </div>

             		       <div class="input-group"  style="width : 340px;">
             		           <div class="input-group-addon" >{{Min}}</div>
             		           <input class="eqLogicAttr form-control" type="number" min="0" max="100" data-l1key="configuration" data-l2key="moistureMin" >
             		           <div class="input-group-addon">%</div>
	     		   	<div style="margin-left : 15px;"></div>
	     		   	 <div class="input-group-addon">{{Max}}</div>
             		           <input class="eqLogicAttr form-control" type="number" min="0" max="100" data-l1key="configuration" data-l2key="moistureMax" >
             		           <div class="input-group-addon">%</div>
             		       </div>
             		 </div>

             		 <div class="form-group">
             		       <label class="col-sm-2 control-label">{{Sonde humidité}}</label>
             		       <div class="col-sm-6">
             		               <input class="eqLogicAttr form-control input-sm" data-l1key="configuration" data-l2key="moistureSensor" placeholder="{{Nom sonde}}" style="margin-bottom : 5px;width : 300px; display : inline-block;">
             		               <a class="btn btn-default btn-sm cursor listEquipementInfo" data-input="infoName" style="margin-left : 5px;"><i class="fa fa-list-alt "></i> {{Rechercher équipement}}</a>
             		       </div>
             		 </div>
              	</div>

                <div style="border:  solid 1px #d9d9d9; padding-top: 20px;margin-bottom: 20px;">
             		<div class="form-group">
             		       <label class="col-sm-2 control-label">{{Arret UV }}</label>
             		       <div class="col-sm-2">
             		       <input type="checkbox" class="eqLogicAttr " data-l1key='configuration' data-l2key='uvStop' />
             		       </div>
<!--
             		        <div class="col-sm-2">
             		       <label class="control-label" style="display : inline-block;">{{Min : }}</label>
             		       <input class="eqLogicAttr form-control input-sm" data-l1key="configuration" data-l2key="uvMin" placeholder="{{10}}" style="margin-bottom : 5px;width : 45px; display : inline-block;">
             		        <label class="control-label" style="display : inline-block;">{{%}}</label>
             		       </div>
-->

             		       <div class="input-group"  style="width : 160px;">
             		           <div class="input-group-addon" >{{Max}}</div>
             		           <input class="eqLogicAttr form-control" type="number" min="0" max="100" data-l1key="configuration" data-l2key="uvMax">
             		           <div class="input-group-addon">%</div>
             		       </div>
             		 </div>

             		 <div class="form-group">
             		       <label class="col-sm-2 control-label">{{Sonde UV}}</label>
             		       <div class="col-sm-6">
             		               <input class="eqLogicAttr form-control input-sm" data-l1key="configuration" data-l2key="uvSensor" placeholder="{{Nom sonde}}" style="margin-bottom : 5px;width : 300px; display : inline-block;">
             		               <a class="btn btn-default btn-sm cursor listEquipementInfo" data-input="infoName" style="margin-left : 5px;"><i class="fa fa-list-alt "></i> {{Rechercher équipement}}</a>
             		       </div>
             		 </div>
              </div>


                <div style="border:  solid 1px #d9d9d9; padding-top: 20px;margin-bottom:20px;">
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

                <div style="border:  solid 1px #d9d9d9; padding-top: 20px;margin-bottom:20px;">
			<div class="form-group">
                	    <label class="col-sm-2 control-label">{{Durée}}</label>
                	    <div class="col-sm-2">
                	    <div class="input-group"  style="width : 150px;">
                	        <input class="eqLogicAttr form-control" type="number" min="5" step="5" data-l1key="configuration" data-l2key="zoneDuration">
                	        <div class="input-group-addon">min</div>
                	    </div>
			    </div>
                	</div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">{{Tache}}</label>
                            <div class="col-sm-2">
					 <?php
                                                foreach (cmd::byLogicalId('task') as $cmdTask) {
						echo '<div>';
						echo '<label><input class="eqLogicAttr"  data-l1key="configuration" data-l2key="'.$cmdTask->getName().'" type="checkbox" name="startDays" id="mon" valu=="' .  $cmdTask->getId() . '">' .  $cmdTask->getName() . '</label>';
						echo '</div>';
						}
                                         ?>
			     </div>
                        </div>
			
                </div>


            </div>
        </fieldset>
    </form>
</div>
<div role="tabpanel" class="tab-pane" id="commandtab">

<!--<a class="btn btn-success btn-sm cmdAction" data-action="add"><i class="fa fa-plus-circle"></i> {{command}}</a><br/><br/>-->
<table id="table_cmd_zone" class="table table-bordered table-condensed">
    <thead>
        <tr>
    <th>{{Nom}}</th><th>{{option}}</th><th></th>
        </tr>
    </thead>
    <tbody>

    </tbody>
</table>
</div>
</div>

</div>
	<?php include_file('desktop', 'arrosage_master', 'php', 'arrosage'); ?>
        <?php include_file('desktop', 'arrosage_tasker', 'php', 'arrosage'); ?>
</div>

<?php
	include_file('desktop', 'arrosage', 'js', 'arrosage');
	include_file('core', 'plugin.template', 'js');
?>
