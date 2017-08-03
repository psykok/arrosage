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


function arrosage_remove() {
	foreach (eqLogic::byType('arrosage') as $obMaster) {
                $obMaster->remove();
        }

	#remove arrosage master eqLogic
      	foreach (eqLogic::byType('arrosage_master') as $obMaster) {
		$obMaster->remove();
	}

        #remove arrosage master eqLogic
        foreach (eqLogic::byType('arrosage_tasker') as $obMaster) {
                $obMaster->remove();
        }
	
}
function arrosage_install() {
	  $eqLogic = new arrosage_master();
          $eqLogic->setEqType_name('arrosage_master');
          $eqLogic->setName('Centrale');
          $eqLogic->setLogicalId($this->getId().'_centrale');
          $eqLogic->setObject_id($this->getObject_id());
          $eqLogic->setConfiguration('waterAdj',100);
          $eqLogic->setConfiguration('delayAdj',0);
          $eqLogic->setConfiguration('masterStop',0);
          $eqLogic->setIsVisible(1);
          $eqLogic->setIsEnable(1);
          $eqLogic->save();
          log::add('arrosage', 'debug','createMasterControl : master controle cereated');




          $eqLogic = new arrosage_tasker();
          $eqLogic->setEqType_name('arrosage_tasker');
          $eqLogic->setName('Tasker');
          $eqLogic->setLogicalId($this->getId().'_tasker');
          $eqLogic->setObject_id($this->getObject_id());
          $eqLogic->setIsVisible(1);
          $eqLogic->setIsEnable(1);
          $eqLogic->save();
          log::add('arrosage', 'debug','createTasker : tasker created');	
}
?>
