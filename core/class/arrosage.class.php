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
	log::add('arrosage', 'debug','cron : log start' );

	  //get water adj value and apply startup delay
	log::add('arrosage', 'debug','cron : get value from eqLogic Master' );
        foreach (eqLogic::byType('arrosage_master') as $obMaster) {
                $waterAdjValue=$obMaster->getConfiguration('waterAdj');
                $standbyValue=$obMaster->getConfiguration('masterStop');
                $startDelay=$obMaster->getConfiguration('delayAdj');
		$checkValue=$obMaster->getConfiguration('checkWeather');	
                $rainYD=$obMaster->getConfiguration('rainYD');
             //  $startTime = date('H:i',strtotime($startTime . '+ '.$startDelay .' minute'));
        }

	//get configuration varial for eqlogic: tasker
	 log::add('arrosage', 'debug','cron : get value from eqLogic tasker' );
        foreach (eqLogic::byType('arrosage_tasker') as $obTasker) {
               $zoneDelay=$obTasker->getConfiguration('delayBtwZone');
	}

       log::add('arrosage', 'debug','cron : check if master standby is active' );
       if ( $standbyValue == 1){
              log::add('arrosage', 'info','cron : master standby on' );
              return 1;
       }



	$weatherInt=0;
	$weatherH1Status=0;
	$weatherH0Status=0;
	$weatherId="";
	$weatherH1Id="";

	//check if weather prevision +1 has been  configured 
	log::add('arrosage', 'debug','cron : check if weather is used' );
	$weatherH1Id=config::byKey('weatherH1','arrosage');
	if($weatherH1Id != "" && $checkValue == 1 ){
		 log::add('arrosage', 'debug','weather usage on ');

		//get weather plugin ID
		$weatherId=cmd::byId(trim($weatherH1Id,"#"))->getEqLogic_id();
		log::add('arrosage', 'debug','weather weather_id : '.$weatherId);
		
		//get H+1 rain prevision
		$weatherH1Status=cmd::byEqLogicIdAndLogicalId($weatherId,'condition_id_1')->execCmd();
		log::add('arrosage', 'debug','weather condition_id_1 : '.$weatherH1Status);
		
		//get day rain prevision
		$weatherH0Status=cmd::byEqLogicIdAndLogicalId($weatherId,'condition_id')->execCmd();
		log::add('arrosage', 'debug','weather condition_id_1 : '.$weatherH0Status);
		
		//check if rain is comming
		if (($weatherH0Status >= 500 && $weatherH0Status <= 599) || ($weatherH1Status >= 500 && $weatherH1Status <= 599)){
		        $weatherInt=1;
			log::add('arrosage', 'info','weather : rain is comming');
		}
		
		 if (($weatherH0Status >= 200 && $weatherH0Status <= 299) || ($weatherH1Status >= 200 && $weatherH1Status <= 299)){
		        $weatherInt=1;
			log::add('arrosage', 'info','weather : storm is comming');
		}
	
	        if ($rainYD == 1){
	                log::add('arrosage', 'info','weather : we had rain yesterday');
	
	                $weatherInt=1;
	        }
	
	
		//save the day rain prevision just before midnight
		if (($weatherH0Status >= 200 && $weatherH0Status <= 299) ||($weatherH0Status >= 500 && $weatherH0Status <= 599)){
			$rainTD=1;
		}
		else{
			$rainTD=0;
		}	
		$rainCron="59 23 * * *";
		try {
			$cStop = new Cron\CronExpression($rainCron, new Cron\FieldFactory);
			if ($cStop->isDue()) {
					$obMaster->setConfiguration('rainYD',$rainTD);	
					$obMaster->save();
			                log::add('arrosage', 'info','weather : save day prevision');
			
			
			     }
			} catch (Exception $exc) {
		          log::add('arrosage', 'error', __('Expression cron non valide pour ', __FILE__) . ' rainCron : ' . $rainCron);
		  }

		//************** moved to cron section **************
		//$weatherInt=0;	
		//if rain exit
	       // if ($weatherInt == 1){
	       //         log::add('arrosage', 'info','weather prevision : enough rain, no irrigation needed ');
	       //         return 1;
	       // }
	}
	foreach (cmd::byLogicalId('task') as $cmdTask) {
		
        	$startTime =  $cmdTask->getConfiguration('startTime');
        	$disableTask = $cmdTask->getConfiguration('cbDisable');
        	$startDay = "";
        	$startMonth = "";
		$duration="";
		$cmdName = $cmdTask->getName();


        	 //if the task is disable exit
        	 if ( $disableTask == 1){
			log::add('arrosage', 'info','task : '.$cmdName.':  has been disable' );
        	       continue;
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
	
		//crete start time and add delay	
		$startTime = date('H:i',strtotime($startTime . '+ '.$startDelay .' minute'));
	
		
		foreach (eqLogic::byType('arrosage') as $eqLogic) {
			if( $eqLogic->getConfiguration($cmdTask->getName()) == 1){
				 $duration = $eqLogic->getConfiguration('zoneDuration');
				 $winterStatus = $eqLogic->getConfiguration('winterMode');
       				 $rainStopStatus = $eqLogic->getConfiguration('rainStop');
       				 $windStopStatus = $eqLogic->getConfiguration('windStop');
       				 $moistureStopStatus = $eqLogic->getConfiguration('moistureStop');
       				 $uvStopStatus =  $eqLogic->getConfiguration('uvStop');
				 $zoneName = $eqLogic->getName();
				
				//refresh widget
				$eqLogic->refreshWidget();

       				 $stopTask = 0;

				//apply duration modification from water adjustement value
				$duration = floor(($duration * $waterAdjValue)/100);
				 log::add('arrosage', 'debug','cron : zone : '.$zoneName.' : duration : ' .  $duration);
					


       				 //check if winter mode has been activated
       				 if ( $winterStatus == 1 ){
					log::add('arrosage', 'debug','cron : zone : '.$zoneName.' : winter mode active' );
       				         $stopTask = 1;
       				 }
       				 //check if the rain control has been activated
       				 if ( $rainStopStatus == 1 ){

       				          //$rainSensorID = trim(config::byKey('rainSensor','arrosage'),"#");
       				         $cmd_device=cmd::byId(trim(config::byKey('rainSensor','arrosage'),"#"));

       				        // log::add('arrosage', 'info','rain stop : ' .  $cmd_device->getConfiguration('value'));

       				         if ( $cmd_device->execCmd() == 1){
       				                $stopTask = 1;
						log::add('arrosage', 'debug','cron : zone : '.$zoneName.' : rain control active');
       				         }
       				 }
       				 //check if the wind crontrol has been activated
       				 if ( $windStopStatus == 1 ){
       				         $cmd_device=cmd::byId(trim(config::byKey('windSensor','arrosage'),"#"));
					
					//log::add('arrosage', 'info','wind stop : ' .  $cmd_device->execCmd());
       				         //check if the current wind speed if under the max speed
       				         if($cmd_device->execCmd() > $eqLogic->getConfiguration('windSpeedMax')){
       				                $stopTask = 1;
						log::add('arrosage', 'debug','cron : zone : '.$zoneName.' : wind control active');
       				         }
       				 }

       				 //check if the moisture control has been activated
       				 if ( $moistureStopStatus == 1 ){
       				         $cmd_device=cmd::byId(trim($eqLogic->getConfiguration('moistureSensor'),"#"));

       				         $moistureValue=$cmd_device->execCmd();
       				         $moistureMaxValue=$eqLogic->getConfiguration('moistureMax');
       				         $moistureMinValue=$eqLogic->getConfiguration('moistureMin');

       				         if ( $moistureValue > $moistureMaxValue ){
       				                $stopTask = 1;
						log::add('arrosage', 'debug','cron : zone : '.$zoneName.' : moisture control active');
       				         }
       				 }

       				 //check if uv crontole has been activated
       				 if ( $uvStopStatus == 1 ){
       				         $cmd_device=cmd::byId(trim($eqLogic->getConfiguration('uvSensor'),"#"));

       				         $uvValue=$cmd_device->execCmd();
       				         $uvMaxValue=$eqLogic->getConfiguration('uvMax');
//     				           $moistureMinValue=$eqLogic->getConfiguration('uvMin');

       				         if ( $uvValue > $uvMaxValue ){
       				                $stopTask = 1;
						log::add('arrosage', 'debug','cron : zone : '.$zoneName.' : UV exceed value');
       				         }
       				 }
				
				//check if an interrupt has been detected
			        if ( $stopTask == 1 ){
					log::add('arrosage', 'debug','cron : zone : '.$zoneName.' : interrupt detected');
			                $cmd_device=cmd::byId(trim($eqLogic->getConfiguration('zoneStatus'),"#"));
					
					//close the valve if open
					$eqLogic->manageValve('Off');
					
			                continue;
			        }
				

				 //construction start cron
	        		$pos = strpos($startTime,':');
                		$startHour = substr($startTime,0,$pos);
                		$startMin = substr($startTime,-$pos);
                		$startDayOfMonth = "*";
                		$startCron = $startMin." ".$startHour." ".$startDayOfMonth." ".$startMonth." ".$startDay;

				//create stop stime
				$stopTime = date('H:i',strtotime($startTime . '+ '.$duration .' minute'));
				
				//check if the time get to the next day
				if ( strtotime($startTime) >  strtotime($stopTime)){
				}

	
				//create stop cron
		                 $pos = strpos($stopTime,':');
	        	         $stopHour = substr($stopTime,0,$pos);
                		 $stopMin = substr($stopTime,-$pos);
				 $stopCron = $stopMin." ".$stopHour." * * *";
				
				log::add('arrosage', 'debug','cron : zone : '.$zoneName.' : start cron : '.$startCron);
				log::add('arrosage', 'debug','cron : zone : '.$zoneName.' : stop cron : '.$stopCron);

				
				 //cron to open the valve
   			          try {
   			             $c = new Cron\CronExpression($startCron, new Cron\FieldFactory);
   			             if ($c->isDue()) {

					     
				                //$weatherInt=0;
				                //if rain exit
				                if ($weatherInt == 1){
				                        log::add('arrosage', 'info','weather prevision : enough rain, no irrigation needed ');
				                        //return 1;
				                }else{

   			                    		 try {
   			                    		   log::add('arrosage', 'info','Cron added############################################## ' );
   			                    		   //log::add('arrosage', 'info','Command on '. $eqLogic->getConfiguration('zoneOn')." at ".$startTime);

					    		    log::add('arrosage', 'info','cron : zone : '.$zoneName.' : zone on');

   			                    		    $eqLogic->manageValve('On');
					    		    
					    		    //save start and stop time in the zone configuration
					    		    $eqLogic->setConfiguration('startTime',$startTime);
					    		    $eqLogic->setConfiguration('stopTime',$stopTime);
					    		    $eqLogic->save();

   			                    		 } catch (Exception $exc) {
   			                    		         log::add('arrosage', 'error', __('Erreur pour ', __FILE__) . $eqLogic->getHumanName() . ' : ' . $exc->getMessage());
   			                    		 }
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
	
						log::add('arrosage', 'info','cron : zone : '.$zoneName.' : zone off');

   			                      $eqLogic->manageValve('Off');

   			             }
   			          } catch (Exception $exc) {
   			                  log::add('arrosage', 'error', __('Expression cron non valide pour ', __FILE__) . $eqLogic->getHumanName() . ' : ' . $stopCron);
   			          }


				//save last stop time as new start time + 1min to have a short pause 
				$startTime=date('H:i',strtotime($stopTime. '+ '. $zoneDelay .' minute'));
			}

			
		}


	}	

 }

  // function to open and close the master valve
  public function manageMasterValve($valveCMD) {
	

	 log::add('arrosage', 'debug','manageMasterValve : called ');
	 if ( config::byKey('masterValve','arrosage')  == 1){
		
		 log::add('arrosage', 'info','master valve '.$valveCMD);
	         //action the master valve
	         $cmd_device=cmd::byId(trim(config::byKey('masterValve'.$valveCMD,'arrosage'),"#"));

		
                log::add('arrosage', 'debug','master valve cmdID : '.trim(config::byKey('masterValve'.$valveCMD,'arrosage'),"#") );


	         $cmd_device->execute();

		 foreach (eqLogic::byType('arrosage_master') as $eqLogic) {
		 	$eqLogic->refreshWidget();
		}
         }


  }

  //function to close valve
  public function manageValve($valveCMD){
	
        log::add('arrosage', 'debug','manageValve : called ');

	//$cmd_device=cmd::byId(trim($this->getConfiguration('zoneStatus'),"#"));
	//check if the valve is open
	//if ( $cmd_device->getConfiguration('value') == 1){
	
	        log::add('arrosage', 'info',' manageValve : close '.$valveCMD);
	
	        //close the valve
	        $cmd_device=cmd::byId(trim($this->getConfiguration('zone'.$valveCMD),"#"));
	        $cmd_device->execute();
	
	        //close the master valve
	        $this->manageMasterValve($valveCMD);
		
		
	        $this->refreshWidget();
	//}
  }

  
  // function to create the master controle panel
  public function createMasterControl(){
	if(count(eqLogic::byType('arrosage_master'))){
		 log::add('arrosage', 'debug','createMasterControl : master controle exist');
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
		log::add('arrosage', 'debug','createMasterControl : master controle doesn\'t exist');	
	}

  }


 // function to create the master controle panel
  public function createTasker(){
        if(count(eqLogic::byType('arrosage_tasker'))){
                 log::add('arrosage', 'debug','createTasker : tasker exist');
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
                log::add('arrosage', 'debug','createTasker : tasker doesn\'t exist');
        }

  }


  public function postSave(){
	$this->createMasterControl();
	$this->createTasker();

  }


  public function timeDiff($firstTime,$lastTime)
  {
  
  	// convert to unix timestamps
  	$firstTime=strtotime($firstTime);
  	$lastTime=strtotime($lastTime);
  	
  	// perform subtraction to get the difference (in seconds) between times
  	$timeDiff= round(abs($lastTime-$firstTime)/60,2);
  	
  	// return the difference
  	return $timeDiff;
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
	$zonePB = '';

        $replace['#id#'] = $this->getId();
	$replace['#eqLink#'] = $this->getLinkToConfiguration();
	$replace['#zoneName#'] = $this->getName();
	$zoneDuration = $this->getConfiguration('zoneDuration');
	$replace['#duration#'] = "Durée : ". $zoneDuration;	

	$startTime = $this->getConfiguration('startTime');
	$stopTime = $this->getConfiguration('stopTime');
	$currentTime = date("H:i");

	$cmd_device=cmd::byId(trim($this->getConfiguration('zoneStatus'),"#"));

	foreach (cmd::byEqLogicId($this->getId()) as $cmd_def) {
                        $replace['#'.$cmd_def->getLogicalId().'ID#'] =  $cmd_def->getId();
                }

	//set the right icone for the widget
	if (  $this->getConfiguration('zoneType') == "drip" ) {
		$iconeType="drip";
	}else {	
		$iconeType="sprinkler2";
	}

        //check if the valve status
	if ( $cmd_device->execCmd() == 1){	
	   $replace['#cmd_stat#'] = 'icon_'.$iconeType.'_on';

	   //get the elapsed time
           $elapsTime = $this->timeDiff($startTime, $currentTime);

	   //calculate the percentage of the elapsed time
           $elapsePCT = round((100 * $elapsTime)/$zoneDuration);
	
	   //bluid the progressbar and task info
	   $zonePB = $zonePB . '<p>Départ : '.$startTime.'</p>';
	   $zonePB = $zonePB . '<p>Arret : '.$stopTime.'</p>';

           $zonePB = $zonePB . '<div class="text-xs-center" id="example-caption-1">Arrosage depuis ' .$elapsTime. 'min</div>';
           $zonePB = $zonePB . '<div class="progress"  style="width:300px">';
           $zonePB = $zonePB . '<div class="progress-bar" role="progressbar" aria-valuenow="'.$elapsePCT.'" aria-valuemin="0" aria-valuemax="100" style="width:'.$elapsePCT.'%">';
           $zonePB = $zonePB . '</div> </div>';
	   
	}else {
           $replace['#cmd_stat#'] = 'icon_'.$iconeType.'_off';
	}

        $replace['#zonePB#'] = $zonePB;

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
	

		//check if the duration is greater as 3 	
		$zoneDurationValue=$this->getConfiguration('zoneDuration');
		if ( $zoneDurationValue < 2 && $zoneDurationValue != ''){
			 throw new Exception(__($zoneDurationValue.'La durée doit être superieur à 3min' , __FILE__));
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
                log::add('arrosage', 'debug','doWinter : command winter' );
		$this->changeOptionStatus('winterMode');
        }    

	//change rain option status                                                                                                                          
        public function doRain(){                                                                                                                                                                     
		log::add('arrosage', 'debug','doRain : command rain' );
		$this->changeOptionStatus('rainStop');
        }    

	//change wind option status                                                                                                                             
        public function doWind(){ 
                log::add('arrosage', 'debug','doWind : command wind' );
		$this->changeOptionStatus('windStop');
        }    

	 //change moisture option status                                                                                                                              
        public function doMoisture(){  
                log::add('arrosage', 'debug','doMoisture : command moisture' );
		$this->changeOptionStatus('moistureStop');
        } 

	//activate or desactivate the zone
	public function doZoneAction(){
                log::add('arrosage', 'debug','doZoneAction : command zone action' );

		log::add('arrosage', 'debug','doZoneAction : zoneStatus device ID : '.$this->getConfiguration('zoneStatus') );
		$cmd_device=cmd::byId(trim($this->getConfiguration('zoneStatus'),"#"));
                //check if the valve is open
                

		log::add('arrosage', 'debug','doZoneAction : zoneStatus device value : '.$cmd_device->execCmd() );
		if ( $cmd_device->execCmd() == 1){

			//close the valve
                        $this->manageValve('Off');
                } else{

			//open the valve
                        $this->manageValve('On');
		}
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
