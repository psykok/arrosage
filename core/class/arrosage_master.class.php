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
		
		//check standby status
		$standbySatus=$this->getConfiguration('masterStop');                                                                                                                                      
                                                                                                                                                                
                if($standbySatus == 1){                                                                                                                                                                   
			$replace['#standbyIcone#'] = 'standby_on';                                                                                         
                }                                                                                                                                                                                         
                else if($standbySatus == 0){                                                                                                                    
			$replace['#standbyIcone#'] = 'standby_off';
                }                                                      


                //check if weather check is activated
                $checkSatus=$this->getConfiguration('checkWeather');

                if($checkSatus == 1){
                        $replace['#checkIcone#'] = 'check_on';
                }
                else if($checkSatus == 0){
                        $replace['#checkIcone#'] = 'check_off';
                }

	        $html = '';
	        $replace['#id#'] = $this->getId();
        	$replace['#eqLink#'] = $this->getLinkToConfiguration();
	        $replace['#zoneName#'] = $this->getName();
		
		 $replace['#delay_value#'] =  $this->getConfiguration('delayAdj');
                 $replace['#water_value#'] =  $this->getConfiguration('waterAdj');

		foreach (cmd::byEqLogicId($this->getId()) as $cmd_def) {
			$replace['#'.$cmd_def->getLogicalId().'ID#'] =  $cmd_def->getId();
		}

		//check if master is used
	         if ( config::byKey('masterValve','arrosage')  == 1){

			//check master status
			 $cmd_device=cmd::byId(trim(config::byKey('masterValveStatus','arrosage'),"#"));
                        if($cmd_device->getConfiguration('value') == 1){
				$masterStatus='on';
			}else{
				$masterStatus='off';
			}		
			

			//display master icone with status
			$replace['#master_status#'] = '<img  class="master" style="height:90px;width:90px; margin:10px;" src="plugins/arrosage/core/template/images/master_'.$masterStatus.'.png"/>';
	         }else{
		$replace['#master_status#'] = '';
		}
	



		  $html = template_replace($replace, getTemplate('core', $_version, 'master', 'arrosage'));
	       // cache::set('arrosageWidget' . $_version . $this->getId(), $html, 0);
        	return $html;

	}
	public function preSave() {

                //check if the water adjustement is not negative or greater as 100
		$waterAdjValue = $this->getConfiguration('waterAdj');
		

		if ($waterAdjValue == ""){
                              throw new Exception(__('Le coef d\'arrosage ne peut pas être vide', __FILE__));
                }
                if ($waterAdjValue < 0){
                              throw new Exception(__('Le coef d\'arrosage doit etre superieur ou égal 0%', __FILE__));
                }
                if ($waterAdjValue > 200){
                              throw new Exception(__('Le coef d\'arrosage doit etre inférieur ou égal 200%', __FILE__));
                }

		//check if the delay time is not negative
                 $delayAdjValue = $this->getConfiguration('delayAdj');

                 if (  ($delayAdjValue < 0) || ($delayAdjValue == "" )){
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
			$masterCmd->setOrder(0);
			$masterCmd->save();
		}

                if(count(cmd::byLogicalId('delay')) == 0) {
                	//create cmd delay
                	$masterCmd = new arrosage_masterCmd();
                	$masterCmd->setName('Delay');
                	$masterCmd->setLogicalId('delay');
                	$masterCmd->setEqLogic_id($this->id);
                	$masterCmd->setType('action');
                	$masterCmd->setSubType('other');
			$masterCmd->setOrder(1);
                	$masterCmd->save();
		}

		if(count(cmd::byLogicalId('water')) == 0) {
                	//create cmd water
                	$masterCmd = new arrosage_masterCmd();
                	$masterCmd->setName('Water');
                	$masterCmd->setLogicalId('water');
                	$masterCmd->setEqLogic_id($this->id);
                	$masterCmd->setType('action');
                	$masterCmd->setSubType('other');
			$masterCmd->setOrder(2);
                	$masterCmd->save();
		}

		if(count(cmd::byLogicalId('master')) == 0) {	
			//create cmd water
                	$masterCmd = new arrosage_masterCmd();
                	$masterCmd->setName('Master');
                	$masterCmd->setLogicalId('master');
                	$masterCmd->setEqLogic_id($this->id);
                	$masterCmd->setType('action');
                	$masterCmd->setSubType('other');
			$masterCmd->setOrder(3);
                	$masterCmd->save();
		}

                if(count(cmd::byLogicalId('master')) == 0) {
			 //create cmd water
                	$masterCmd = new arrosage_masterCmd();
                	$masterCmd->setName('checkWeather');
                	$masterCmd->setLogicalId('checkWeather');
                	$masterCmd->setEqLogic_id($this->id);
                	$masterCmd->setType('action');
                	$masterCmd->setSubType('other');
			$masterCmd->setOrder(4);
                	$masterCmd->save();
		}


	}

	//activate or desactivate the standy mode
	public function doStandby(){
		log::add('arrosage', 'info','command: standby' );
		$standbySatus=$this->getConfiguration('masterStop');

                log::add('arrosage', 'debug','masterStop='. $checkStatus );

		if($standbySatus == 1){
			$this->setConfiguration('masterStop',0);
		}
		else if($standbySatus == 0){
			$this->setConfiguration('masterStop',1);
		}
		$this->save();
		$this->refreshWidget();
		
		//close all valve
		foreach (eqLogic::byType('arrosage') as $eqLogic) {
                	$eqLogic->manageValve('Off');
        	}


	}


	//activate or desactivate the check of the weather status
	public function doCheckWeather(){
		 log::add('arrosage', 'info','command: checkWeather' );
		$checkStatus=$this->getConfiguration('checkWeather');


                log::add('arrosage', 'debug','checkWeather='. $checkStatus );

		 if($checkStatus == 1){
                        $this->setConfiguration('checkWeather',0);
                }
                else if($checkStatus == 0){
                        $this->setConfiguration('checkWeather',1);
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
	public function doMaster(){
                log::add('arrosage', 'info','command: master valve' );
	
		 foreach (eqLogic::byType('arrosage') as $eqLogic) {

	
               //check master status
                $cmd_device=cmd::byId(trim(config::byKey('masterValveStatus','arrosage'),"#"));
               if($cmd_device->getConfiguration('value') == 1){
			log::add('arrosage', 'debug','command: master valve off' );
                        $eqLogic->manageMasterValve('Off');

               }else{
                        log::add('arrosage', 'debug','command: master valve on' );
                        $eqLogic->manageMasterValve('On');

               }
                $this->refreshWidget();
	
		return 1;

		}
		
        }



}
class arrosage_masterCmd extends cmd {

	public function execute($_options = array()) {
                log::add('arrosage', 'debug','command ID: ' . $this->getLogicalId() );

		if ($this->getLogicalId() == 'standby') {
			$this->getEqLogic()->doStandby();
		}
		if ($this->getLogicalId() == 'check') {
                        $this->getEqLogic()->doCheckWeather();
                }
		if ($this->getLogicalId() == 'delay') {
			$this->getEqLogic()->doDelayAdj();
                }
		if ($this->getLogicalId() == 'water') {
			$this->getEqLogic()->doWaterAdj();
                }
		
		 if ($this->getLogicalId() == 'master') {
                        $this->getEqLogic()->doMaster();
                }

		return false;
	}


}

?>

