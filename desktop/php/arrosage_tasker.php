<div class="col-lg-10 col-md-9 col-sm-8 eqLogic arrosage_tasker " style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
 <a class="btn btn-success eqLogicAction pull-right" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
 <a class="btn btn-danger eqLogicAction pull-right" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
 <a class="btn btn-default eqLogicAction pull-right" data-action="configure"><i class="fa fa-cogs"></i> {{Configuration avanc�e}}</a>
 <ul class="nav nav-tabs" role="tablist">
  <li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
  <li role="presentation"><a href="#taskereqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fa fa-tachometer"></i> {{Equipement}}</a></li>
  <li role="presentation" class="active"><a href="#tasktab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Taches}}</a></li>
</ul>
<div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
  <div role="tabpanel" class="tab-pane" id="taskereqlogictab">
    <br/>
        <div class="col-sm-6">
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

	 <div class="col-sm-6 " id="arrosage_config">
	     <form class="form-horizontal">
	        <fieldset>
	               <legend>{{Configuration}}</legend>
			 <div class="form-group">
                                <label class="col-sm-2 control-label">{{Temps entre zone : }}</label>
                                <div class="col-sm-2">
                                        <input class="eqLogicAttr form-control input-sm" data-l1key="configuration" data-l2key="delayBtwZone"  type="number" min="0" max="5" placeholder="{{0}}" style="margin-bottom : 5px;width : 45px; display : inline-block;">

                                         <label class="control-label" style="display : inline-block;">{{min}}</label>
                                </div>
                        </div>
	        </fieldset>
	    </form>
	</div>
</div>
<div role="tabpanel" class="tab-pane active" id="tasktab">
<div>
  <br/>
<div class="alert alert-danger">{{En raison d'un bug, toujours sauvegarder deux fois lors d'un ajout ou d'une modification de tâche.}} </div>
</div>
</br>
<a class="btn btn-success btn-sm cmdAction" data-action="add"><i class="fa fa-plus-circle"></i> {{Ajouter une commande}}</a><br/><br/>
<table id="table_cmd" class="table table-bordered table-condensed">
    <thead>
        <tr>
    <th>{{Nom}}</th><th>{{Heure début}}</th><th>{{Zone}}</th><th>{{Jours}}</th><th>{{Mois}}</th><th>{{Inactive}}</th><th></th>
        </tr>
    </thead>
    <tbody>

    </tbody>
</table>
</div>

</div>

</div>

</div>

