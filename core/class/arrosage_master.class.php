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

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';



class arrosage_master extends eqLogic {

	public function getLinkToConfiguration() {
		return 'index.php?v=d&p=arrosage&m=arrosage&id=' . $this->getId();
	}//End getLinkToConfiguration func

	public function toHtml($_version = 'dashboard') {
      		if ($this->getIsEnable() != 1) {
            		return '';
		}
		

		$standbySatus=$this->getConfiguration('masterStop');                                                                                                                                      
                                                                                                                                                                
                if($standbySatus == 1){                                                                                                                                                                   
			$replace['#standbyIcone#'] = 'standby_on';                                                                                         
                }                                                                                                                                                                                         
                else if($standbySatus == 0){                                                                                                                    
			$replace['#standbyIcone#'] = 'standby_off';
                }                                                      

	        $html_forecast = '';
	        $replace['#id#'] = $this->getId();
        	$replace['#eqLink#'] = $this->getLinkToConfiguration();
	        $replace['#zoneName#'] = $this->getName();
		
		 $replace['#delay_value#'] =  $this->getConfiguration('delayAdj');
                 $replace['#water_value#'] =  $this->getConfiguration('waterAdj');

		foreach (cmd::byEqLogicId($this->getId()) as $cmd_def) {
			$replace['#'.$cmd_def->getLogicalId().'ID#'] =  $cmd_def->getId();
		}



		  $html = template_replace($replace, getTemplate('core', $_version, 'master', 'arrosage'));
	       // cache::set('arrosageWidget' . $_version . $this->getId(), $html, 0);
        	return $html;

	}
	public function preSave() {

                //check if the water adjustement is not negative or greater as 100
		$waterAdjValue = $this->getConfiguration('waterAdj');

                if ($waterAdjValue < 0){
                              throw new Exception(__('Le coef d\'arrosage doit etre superieur ou égal 0%', __FILE__));
                }
                if ($waterAdjValue > 200){
                              throw new Exception(__('Le coef d\'arrosage doit etre inférieur ou égal 200%', __FILE__));
                }

		//check if the delay time is not negative
                 $delayAdjValue = $this->getConfiguration('delayAdj');

                 if (  $delayAdjValue < 0 ){
                         throw new Exception(__('Le reatard doit être superieur ou égal à 0 min ' , __FILE__));

                 }



        }
	public function postUpdate(){	
		$this->postInsert();
	}

	public function postInsert(){
		if(count(cmd::byLogicalId('standby')) == 0) {

		//create cmd standby
		$masterCmd = new arrosage_masterCmd();
		$masterCmd->setName('Standby');
		$masterCmd->setLogicalId('standby');
		$masterCmd->setEqLogic_id($this->id);
		$masterCmd->setType('action');
		$masterCmd->setSubType('other');
		$masterCmd->save();


                //create cmd delay
                $masterCmd = new arrosage_masterCmd();
                $masterCmd->setName('Delay');
                $masterCmd->setLogicalId('delay');
                $masterCmd->setEqLogic_id($this->id);
                $masterCmd->setType('action');
                $masterCmd->setSubType('other');
                $masterCmd->save();

                //create cmd water
                $masterCmd = new arrosage_masterCmd();
                $masterCmd->setName('Water');
                $masterCmd->setLogicalId('water');
                $masterCmd->setEqLogic_id($this->id);
                $masterCmd->setType('action');
                $masterCmd->setSubType('other');
                $masterCmd->save();
		}


	}

	//activate or desactivate the standy mode
	public function doStandby(){
		log::add('arrosage', 'info','command: standby' );
		$standbySatus=$this->getConfiguration('masterStop');

		if($standbySatus == 1){
			$this->setConfiguration('masterStop',0);
		}
		else if($standbySatus == 0){
			$this->setConfiguration('masterStop',1);
		}
		$this->save();
		$this->refreshWidget();
	}

	//adjust delay factor
	public function	doDelayAdj(){
		log::add('arrosage', 'info','command: delay' );		
	}

	//adjust water factor
	public function doWaterAdj(){
		log::add('arrosage', 'info','command: water' );
	}


}
class arrosage_masterCmd extends cmd {


	public function execute($_options = array()) {
		if ($this->getLogicalId() == 'standby') {
			$this->getEqLogic()->doStandby();
		}
		
		if ($this->getLogicalId() == 'delay') {
			$this->getEqLogic()->doDelayAdj();
                }
		if ($this->getLogicalId() == 'water') {
			$this->getEqLogic()->doWaterAdj();
                }

		return false;
	}


}

?>

