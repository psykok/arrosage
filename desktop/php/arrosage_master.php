<div class="col-lg-10 col-md-9 col-sm-8 eqLogic arrosage_master " style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
 <a class="btn btn-success eqLogicAction pull-right" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
<!-- <a class="btn btn-danger eqLogicAction pull-right" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>-->
 <a class="btn btn-default eqLogicAction pull-right" data-action="configure"><i class="fa fa-cogs"></i> {{Configuration avanc�e}}</a>
 <ul class="nav nav-tabs" role="tablist">
  <li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
  <li role="presentation" class="active"><a href="#mastereqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fa fa-tachometer"></i> {{Equipement}}</a></li>
  <li role="presentation"><a href="#mastercmdtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Commandes}}</a></li>
</ul>
<div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;" >
  <div role="tabpanel" class="tab-pane active" id="mastereqlogictab">
        <div class="">
            <form class="form-horizontal">
                <fieldset>
		    </br>
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

	 <div class="" id="arrosage_config">
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

<div role="tabpanel" class="tab-pane" id="mastercmdtab">
  <br/>
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
</div>
</div>

</div>
