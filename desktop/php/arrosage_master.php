<div class="col-lg-10 col-md-9 col-sm-8 eqLogic arrosage_master " style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
    <div class='row'>
        <div class="col-sm-6">
            <form class="form-horizontal">
                <fieldset>
                    <legend><i class="fa fa-arrow-circle-left eqLogicAction cursor" data-action="returnToThumbnailDisplay"></i> {{Général}}<i class='fa fa-cogs eqLogicAction pull-right cursor expertModeVisible' data-action='configure'></i></legend>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">{{Nom}}</label>
                        <div class="col-sm-6">
                            <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                            <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de la centrale}}"/>
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
			<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr " data-label-text="{{Activer}}" data-l1key="isEnable" checked/>{{Activer}}</label>
                        <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr " data-label-text="{{Visible}}" data-l1key="isVisible" checked/>{{Visible}}</label>
                     </div>
                   </div>
                </fieldset>
            </form>
        </div>

	 <div class="col-sm-6 " id="arrosage_config">
	     <form class="form-horizontal">
	        <fieldset>
	               <legend>{{Configuration Centrale}}</legend>
		       <div class="form-group">
		                <label class="col-sm-2 control-label">{{Arrêt général}}</label>
		                <div class="col-sm-6">
		                    <input type="checkbox" class="eqLogicAttr " data-l1key='configuration' data-l2key='masterStop' />
		       		</div>
		       </div>

                       <div class="form-group">
                                <label class="col-sm-2 control-label">{{Check météo}}</label>
                                <div class="col-sm-6">
                                    <input type="checkbox" class="eqLogicAttr " data-l1key='configuration' data-l2key='checkWeather' />
                                </div>
                       </div>


			<div class="form-group">
		                <label class="col-sm-2 control-label">{{Coef arrosage : }}</label>
        		 	<div class="col-sm-2">
		        		<input class="eqLogicAttr form-control input-sm" data-l1key="configuration" data-l2key="waterAdj" style="margin-bottom : 5px;width : 45px; display : inline-block;">
        				 <label class="control-label" style="display : inline-block;">{{%}}</label>
	        		</div>
                        </div>

                        <div class="form-group">
                        <label class="col-sm-2 control-label">{{Retard arrosage : }}</label>
			<div class="col-sm-2">
                                        <input class="eqLogicAttr form-control input-sm" data-l1key="configuration" data-l2key="delayAdj" style="margin-bottom : 5px;width : 45px; display : inline-block;">
                                         <label class="control-label" style="display : inline-block;">{{min}}</label>
                                </div>

              		</div>

			 <div class="form-group">
                                <label class="col-sm-2 control-label">{{Pluie hier}}</label>
                                <div class="col-sm-6">
                                    <input type="checkbox" class="eqLogicAttr " data-l1key='configuration' data-l2key='rainYD' />
                                </div>
                       </div>

	        </fieldset>
	    </form>
	</div>
</div>

<legend>{{Command}}</legend>
<!--<a class="btn btn-success btn-sm cmdAction" data-action="add"><i class="fa fa-plus-circle"></i> {{command}}</a><br/><br/>-->
<table id="table_cmd_master" class="table table-bordered table-condensed">
    <thead>
        <tr>
    <th>{{Nom}}</th><th>{{option}}</th><th></th>
        </tr>
    </thead>
    <tbody>

    </tbody>
</table>



<form class="form-horizontal">
    <fieldset>
        <div class="form-actions">
<!--            <a class="btn btn-danger eqLogicAction" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>-->
            <a class="btn btn-success eqLogicAction" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
        </div>
    </fieldset>
</form>

</div>
