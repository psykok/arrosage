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

include_file('core', 'arrosage_master', 'class', 'arrosage');
include_file('core', 'arrosage_tasker', 'class', 'arrosage');


class arrosage extends eqLogic {

  public static function cron() {
	log::add('arrosage', 'info','log start' );



	foreach (cmd::byLogicalId('task') as $cmdTask) {
		
        	$startTime =  $cmdTask->getConfiguration('startTime');
        	$disableTask = $cmdTask->getConfiguration('cbDisable');
        	$startDay = "";
        	$startMonth = "";
		$duration="";
		
        	 //if the task is disable exit
        	 if ( $disableTask == 1){
        	       return 1;
        	}



        	 //concatenation of the week days we need to start the cron job
        	 for ($i = 1; $i <= 7 ;$i++)
        	 {
        	        if ($cmdTask->getConfiguration('cbDay'.$i) == 1) {
        	          if ($startDay != "")
        	          {
        	                $startDay = $startDay .",";
        	          }
        	          $startDay = $startDay . $i;
        	        }
        	 }

        	 //concatenation of the moth we need to start the cron job
        	 for ($i = 1; $i <= 12 ;$i++)
        	 {
        	        if ($cmdTask->getConfiguration('cbMonth'.$i) == 1) {
        	          if ($startMonth != "")
        	          {
        	                $startMonth = $startMonth .",";
        	          }
        	          $startMonth = $startMonth . $i;
        	        }
        	 }
	
		 //get water adj value and apply startup delay
                 foreach (eqLogic::byType('arrosage_master') as $obMaster) {
                         $waterAdjValue=$obMaster->getConfiguration('waterAdj');

                         $startDelay=$obMaster->getConfiguration('delayAdj');
			$startTime = date('H:i',strtotime($startTime . '+ '.$startDelay .' minute'));
                 }






	
		
		foreach (eqLogic::byType('arrosage') as $eqLogic) {
			if( $eqLogic->getConfiguration($cmdTask->getName()) == 1){
				 $duration = $eqLogic->getConfiguration('zoneDuration');
				 $winterStatus = $eqLogic->getConfiguration('winterMode');
       				 $rainStopStatus = $eqLogic->getConfiguration('rainStop');
       				 $windStopStatus = $eqLogic->getConfiguration('windStop');
       				 $moistureStopStatus = $eqLogic->getConfiguration('moistureStop');
       				 $uvStopStatus =  $eqLogic->getConfiguration('uvStop');


       				 $stopTask = 0;

				//apply duration modification from water adjustement value
				$duration = floor(($duration * $waterAdjValue)/100);
				 log::add('arrosage', 'info','master water : ' .  $duration);
					


       				 //check if winter mode has been activated
       				 if ( $winterStatus == 1 ){
       				         $stopTask = 1;
       				 }
       				 //check if the rain control has been activated
       				 if ( $rainStopStatus == 1 ){
       				          //$rainSensorID = trim(config::byKey('rainSensor','arrosage'),"#");
       				         $cmd_device=cmd::byId(trim(config::byKey('rainSensor','arrosage'),"#"));

       				        // log::add('arrosage', 'info','rain stop : ' .  $cmd_device->getConfiguration('value'));

       				         if ( $cmd_device->getConfiguration('value') == 1){
       				                  $stopTask = 1;
       				         }
       				 }
       				 //check if the wind crontrol has been activated
       				 if ( $windStopStatus == 1 ){
       				         $cmd_device=cmd::byId(trim(config::byKey('windSensor','arrosage'),"#"));
					
					//log::add('arrosage', 'info','wind stop : ' .  $cmd_device->execCmd());
       				         //check if the current wind speed if under the max speed
       				         if($cmd_device->execCmd() > $eqLogic->getConfiguration('windSpeedMax')){
       				                 $stopTask = 1;
       				         }
       				 }

       				 //check if the moisture control has been activated
       				 if ( $moistureStopStatus == 1 ){
       				         $cmd_device=cmd::byId(trim($eqLogic->getConfiguration('moistureSensor'),"#"));

       				         $moistureValue=$cmd_device->getConfiguration('value');
       				         $moistureMaxValue=$eqLogic->getConfiguration('moistureMax');
       				         $moistureMinValue=$eqLogic->getConfiguration('moistureMin');

       				         if ( $moistureValue > $moistureMaxValue ){
       				                 $stopTask = 1;
       				         }
       				 }

       				 //check if uv crontole has been activated
       				 if ( $uvStopStatus == 1 ){
       				         $cmd_device=cmd::byId(trim($eqLogic->getConfiguration('uvSensor'),"#"));

       				         $uvValue=$cmd_device->getConfiguration('value');
       				         $uvMaxValue=$eqLogic->getConfiguration('uvMax');
//     				           $moistureMinValue=$eqLogic->getConfiguration('uvMin');

       				         if ( $uvValue > $uvMaxValue ){
       				                 $stopTask = 1;
       				         }
       				 }
				
				//check if an interrption has been detected
			        if ( $stopTask == 1 ){
			
			                $cmd_device=cmd::byId(trim($eqLogic->getConfiguration('zoneStatus'),"#"));
			                //check if the valve is open
			                if ( $cmd_device->getConfiguration('value') == 1){
			                        //close the valve
			                        $cmd_device=cmd::byId(trim($eqLogic->getConfiguration('zoneOff'),"#"));
			                        $cmd_device->execute();
			
			                        //close the master valve
			                        $eqLogic->manageMasterValve('Off');
			
			                }
			
			                return 1;
			        }
				



	

				 //construction start cron
	        		$pos = strpos($startTime,':');
                		$startHour = substr($startTime,0,$pos);
                		$startMin = substr($startTime,-$pos);
                		$startDayOfMonth = "*";
                		$startCron = $startMin." ".$startHour." ".$startDayOfMonth." ".$startMonth." ".$startDay;




				//create stop stime
				$stopTime = date('H:i',strtotime($startTime . '+ '.$duration .' minute'));
	
				//create stop cron
		                 $pos = strpos($stopTime,':');
	        	         $stopHour = substr($stopTime,0,$pos);
                		 $stopMin = substr($stopTime,-$pos);
				 $stopCron = $stopMin." ".$stopHour." * * *";


		                //log::add('arrosage', 'info','cmd taker start:'.$startCron );
				//log::add('arrosage', 'info','cmd taker stop:'.$stopCron );

				
				 //cron to open the valve
   			          try {
   			             $c = new Cron\CronExpression($startCron, new Cron\FieldFactory);
   			             if ($c->isDue()) {
   			                     try {
   			                       //log::add('arrosage', 'info','Cron added '.$startCron );
   			                       //log::add('arrosage', 'info','Command on '. $eqLogic->getConfiguration('zoneOn')." at ".$startTime);
   			                            $cmd_device=cmd::byId(trim($eqLogic->getConfiguration('zoneOn'),"#"));
   			                             $cmd_device->execute();

   			                             manageMasterValve('On');

   			                     } catch (Exception $exc) {
   			                             log::add('arrosage', 'error', __('Erreur pour ', __FILE__) . $eqLogic->getHumanName() . ' : ' . $exc->getMessage());
   			                     }
   			             }


   			          } catch (Exception $exc) {
   			                  log::add('arrosage', 'error', __('Expression cron non valide pour ', __FILE__) . $eqLogic->getHumanName() . ' : ' . $startCron);
   			          }
   			          // cron to close the valve
   			          try {

   			             $cStop = new Cron\CronExpression($stopCron, new Cron\FieldFactory);
   			             if ($cStop->isDue()) {
   			                     //log::add('arrosage', 'info','Command on '. $eqLogic->getConfiguration('zoneOff')." at ".$stopTime );
   			                     $cmd_device=cmd::byId(trim($eqLogic->getConfiguration('zoneOff'),"#"));
   			                     $cmd_device->execute();

   			                      manageMasterValve('Off');

   			             }
   			          } catch (Exception $exc) {
   			                  log::add('arrosage', 'error', __('Expression cron non valide pour ', __FILE__) . $eqLogic->getHumanName() . ' : ' . $stopCron);
   			          }


				//save last stop time as new start time + 1min to have a short pause 
				$startTime=date('H:i',strtotime($stopTime. '+ 1 minute'));
			}

			
		}


	}	

 }

  // function to open and close the master valve
  protected function manageMasterValve($valveCMD) {

	 log::add('arrosage', 'info','mastervalve : in');
	 if ( config::byKey('masterValve','arrosage')  == 1){
		
		 log::add('arrosage', 'info','mastervalve :'.$valveCMD);
	         //close the master valve
	         $cmd_device=cmd::byId(trim(config::byKey('masterValve'.valveCMD,'arrosage'),"#"));
	         $cmd_device->execute();
         }


  }
  
  // function to create the master controle panel
  public function createMasterControl(){
	if(count(eqLogic::byType('arrosage_master'))){
		 log::add('arrosage', 'info','master controle exist');
	}
	else{
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
		log::add('arrosage', 'info','master controle doesn\'t exist');	
	}

  }


 // function to create the master controle panel
  public function createTasker(){
        if(count(eqLogic::byType('arrosage_tasker'))){
                 log::add('arrosage', 'info','tasker exist');
        }
        else{   
                $eqLogic = new arrosage_tasker();
                $eqLogic->setEqType_name('arrosage_tasker');
                $eqLogic->setName('Tasker');
                $eqLogic->setLogicalId($this->getId().'_tasker');
                $eqLogic->setObject_id($this->getObject_id());
                $eqLogic->setIsVisible(1);
                $eqLogic->setIsEnable(1);
                $eqLogic->save();
                log::add('arrosage', 'info','tasker doesn\'t exist');
        }

  }


  public function postSave(){
	$this->createMasterControl();
	$this->createTasker();

  }





  public function toHtml($_version = 'dashboard') {
      if ($this->getIsEnable() != 1) {
            return '';
       }

       $_version = jeedom::versionAlias($_version);
/*       
      $mc = cache::byKey('arrosageWidget' . $_version . $this->getId());
        if ($mc->getValue() != '') {
            return $mc->getValue();
        }
*/
        $html_forecast = '';
	$cmd_list = '';
        $replace['#id#'] = $this->getId();
	$replace['#eqLink#'] = $this->getLinkToConfiguration();
	$replace['#zoneName#'] = $this->getName();

	$cmd_device=cmd::byId(trim($this->getConfiguration('zoneStatus'),"#"));

	foreach (cmd::byEqLogicId($this->getId()) as $cmd_def) {
                        $replace['#'.$cmd_def->getLogicalId().'ID#'] =  $cmd_def->getId();
                }


        foreach (cmd::byEqLogicId($this->getId()) as $cmd_def) {
//          log::add('arrosage', 'info','dashboard cmd : '.$cmd_def->getHumanName() );
 
           $cmd_name = $cmd_def->getName();
           $cmd_start = $cmd_def->getConfiguration('startTime');
           $cmd_duration = $cmd_def->getConfiguration('duration');
	   $cmd_list .= '<div style="font-weight: bold;font-size : 12px;#hideCmdName#">' . $cmd_name ." : ".$cmd_start." for ".$cmd_duration." min</div>";
	
	}
	$replace['#cmd_list#'] = $cmd_list;

	//check if the valve status
	if ( $cmd_device->getConfiguration('value') == 1){	
	   $replace['#cmd_stat#'] = 'icon_sprinkler2_on';
	}else {
           $replace['#cmd_stat#'] = 'icon_sprinkler2_off';
	}

        //check if the rain dectection is activated
        if ( $this->getConfiguration('winterMode') == 0 ){
           $replace['#winter_stat#'] = 'off';
        }else{
           $replace['#winter_stat#'] = 'on_red';
        }


	//check if the rain dectection is activated
	if ( $this->getConfiguration('rainStop') == 0 ){	
	   $replace['#rain_stat#'] = 'off';
	}else{	
	   $replace['#rain_stat#'] = 'on';
	}

	//check if the wind detection is activated
        if ( $this->getConfiguration('windStop') == 0 ){
           $replace['#wind_stat#'] = 'off';
        }else{
           $replace['#wind_stat#'] = 'on';
        }

	//check if the moisture detection is activated
        if ( $this->getConfiguration('moistureStop') == 0 ){
           $replace['#moisture_stat#'] = 'off';
        }else{
           $replace['#moisture_stat#'] = 'on';
        }

	$html = template_replace($replace, getTemplate('core', $_version, 'zone', 'arrosage'));
       // cache::set('arrosageWidget' . $_version . $this->getId(), $html, 0);
        return $html;
  }

	public function preSave() {

		//check if the moisture control has been activated and if the max and min have been defined
                if ($this->getConfiguration('moistureStop') == 1){
                        $moistureMaxValue = $this->getConfiguration('moistureMax');
                        $moistureMinValue = $this->getConfiguration('moistureMin');

                        if ($moistureMinValue < 0 || $moistureMinValue > $moistureMaxValue){
                                throw new Exception(__('L\'humidité min doit être superieur à 0% et inférieur à l\'humidité max', __FILE__));
                        }
                        if ($moistureMaxValue > 100 || $moistureMaxValue <  $moistureMinValue){
                                throw new Exception(__('L\'humidité max doit être inférieur à 100% et superieur à l\'humidité min', __FILE__));
                        }
                }

		//check if the wind control has been activated and if the max speed has been defnied
               if ($this->getConfiguration('windStop') == 1){

                        $windMaxValue = $this->getConfiguration('windSpeedMax');

                        if (  $windMaxValue < 1 ){
                                throw new Exception(__('La vitesse du vent max doit être superieur à 0km/h ' , __FILE__));

                        }

                }
	

		//check if the duration is greater as 0	
		$zoneDurationValue=$this->getConfiguration('zoneDuration');
		if ( $zoneDurationValue < 0){
			 throw new Exception(__('La durée doit être superieur à 0' , __FILE__));
		}
		

	}
/*
   public function postUpdate(){                                                                                                                                                                     
                $this->postInsert();                                                                                                                            
        } 
*/
  	public function postInsert(){
        //        if(count(cmd::byLogicalId('winter')) == 0) {
			$this->createCustomCmd('winter');
			$this->createCustomCmd('rain');
			$this->createCustomCmd('wind');
			$this->createCustomCmd('moisture');
			$this->createCustomCmd('zoneAction');			
          //      }


        }

	public function createCustomCmd($cmdName){
		$masterCmd = new arrosageCmd();
                $masterCmd->setName($cmdName);
                $masterCmd->setLogicalId($cmdName);
                $masterCmd->setEqLogic_id($this->id);
                $masterCmd->setType('action');
                $masterCmd->setSubType('other');
                $masterCmd->save();
	}


	 //change winter option status                                                                                                                                   
        public function doWinter(){                                                                                                                                                                     
                //log::add('arrosage', 'info','command: winter' );
		$this->changeOptionStatus('winterMode');
        }    

	//change rain option status                                                                                                                          
        public function doRain(){                                                                                                                                                                     
                //log::add('arrosage', 'info','command: rain' );                                                                                                 
		$this->changeOptionStatus('rainStop');
        }    

	//change wind option status                                                                                                                             
        public function doWind(){                                                                                                                                                                     
                //log::add('arrosage', 'info','command: wind' );                                                                                                 
		$this->changeOptionStatus('windStop');
        }    

	 //change moisture option status                                                                                                                              
        public function doMoisture(){                                                                                                                                                                     
                //log::add('arrosage', 'info','command: moisture' );                                                                                                 
		$this->changeOptionStatus('moistureStop');
        } 
	public function doZoneAction(){
                log::add('arrosage', 'info','command: zoneAction' );
                //$this->changeOptionStatus('moistureStop');
        }
   
	
	//function to change the option status
	public function changeOptionStatus($optionName){
                $optionSatus=$this->getConfiguration($optionName);
                
		if($optionSatus == 1){
                        $this->setConfiguration($optionName,0);
                }
                else if($optionSatus == 0){
                        $this->setConfiguration($optionName,1);
                }
                $this->save();
                $this->refreshWidget();
        }


}

class arrosageCmd extends cmd {

        public function execute($_options = array()) {
                if ($this->getLogicalId() == 'winter') {                                                                                                                                                 
                        $this->getEqLogic()->doWinter();                                                                                                       
                }                                                                                                                                                                                         
                                                                                                                                                                
                if ($this->getLogicalId() == 'rain') {                                                                                                                                                   
                        $this->getEqLogic()->doRain();                                                                                                      
                }                                                                                                                                                                                         
                if ($this->getLogicalId() == 'wind') {                                                                                                         
                        $this->getEqLogic()->doWind();                                                                                                                                                
                }                                 
                if ($this->getLogicalId() == 'moisture') {                                                                                                                                                    
                        $this->getEqLogic()->doMoisture();                                                                                                      
                }                                                                                                              
                if ($this->getLogicalId() == 'zoneAction') {
                        $this->getEqLogic()->doZoneAction();
                }
                                                                                                                                                                                          
                return false;                                                                                                                                   
        }   
}
