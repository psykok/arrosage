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
§§*/

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

#include_file('core', 'arrosage_master', 'class', 'arrosage');
#include_file('core', 'arrosage_tasker', 'class', 'arrosage');


class arrosage extends eqLogic {

  public static function cron() {
	log::add('arrosage', 'debug','cron : log start' );
	
	//init variable to be sure to not empty ones
	$waterAdj=100;
	$startDelay=0;



        $checkValue=0;
#	  //get water adj value and apply startup delay
#	log::add('arrosage', 'debug','cron : get value from eqLogic Master' );
#        foreach (eqLogic::byType('arrosage_master') as $obMaster) {
#                $waterAdjValue=$obMaster->getConfiguration('waterAdj');
#                $standbyValue=$obMaster->getConfiguration('masterStop');
#                $startDelay=$obMaster->getConfiguration('delayAdj');
#		$checkValue=$obMaster->getConfiguration('checkWeather');	
#                $rainYD=$obMaster->getConfiguration('rainYD');
#             //  $startTime = date('H:i',strtotime($startTime . '+ '.$startDelay .' minute'));
#        }
#
#	//get configuration varial for eqlogic: tasker
#	 log::add('arrosage', 'debug','cron : get value from eqLogic tasker' );
#        foreach (eqLogic::byType('arrosage_tasker') as $obTasker) {
#               $zoneDelay=$obTasker->getConfiguration('delayBtwZone');
#	}

       #log::add('arrosage', 'debug','cron : check if master standby is active' );
       #if ( $standbyValue == 1){
       #       log::add('arrosage', 'info','cron : master standby on' );
       #       
       #       return 1;
       #}



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
	foreach (eqLogic::byType('arrosage') as $zone) {
	        log::add('arrosage', 'debug','cron : ############### start' ); 
	        log::add('arrosage', 'debug','cron : ############### '.$zoneID ); 
                	
                $startTime =  $zone->getConfiguration('startTime');
                $disableTask = $zone->getConfiguration('winterMode');
        	$startDay = "";
        	$startMonth = "";
		$duration="";

		$zoneName = $zone->getName();

                log::add('arrosage', 'debug','cron : zone : '.$zoneName );


        	 //if the task is disable exit
        	 if ( $disableTask == 1){
			log::add('arrosage', 'info','zone : '.$zoneName.':  has been disable' );
        	       continue;
        	}



        	 //concatenation of the week days we need to start the cron job
        	 for ($i = 1; $i <= 7 ;$i++)
        	 {
        	        if ($zone->getConfiguration('cbDay'.$i) == 1) {
        	          if ($startDay != "")
        	          {
        	                $startDay = $startDay .",";
        	          }
        	          $startDay = $startDay . $i;
        	        }
        	 }
	        log::add('arrosage', 'debug','cron : zone : '.$zoneName.' startDay : '.$startDay ); 

        	 //concatenation of the moth we need to start the cron job
        	 for ($i = 1; $i <= 12 ;$i++)
        	 {
        	        if ($zone->getConfiguration('cbMonth'.$i) == 1) {
        	          if ($startMonth != "")
        	          {
        	                $startMonth = $startMonth .",";
        	          }
        	          $startMonth = $startMonth . $i;
        	        }
        	 }
	        log::add('arrosage', 'debug','cron : zone : '.$zoneName.' startMonth : '.$startMonth ); 
	
		//crete start time and add delay
                log::add('arrosage', 'debug','cron : zone : '.$zoneName.' : init start time : ' .  $startTime);	
		$startTime = date('H:i',strtotime($startTime . '+ '.$startDelay .' minute'));
                log::add('arrosage', 'debug','cron : zone : '.$zoneName.' : start time with delay: ' .  $startTime);	
		
		#foreach (eqLogic::byType('arrosage') as $zone) {
			#if( $zone->getConfiguration($zone->getName()) == 1){
		 $duration = $zone->getConfiguration('zoneDuration');
		 $winterStatus = $zone->getConfiguration('winterMode');
       		 $rainStopStatus = $zone->getConfiguration('rainStop');
       		 $windStopStatus = $zone->getConfiguration('windStop');
       		 $moistureStopStatus = $zone->getConfiguration('moistureStop');
       		 $uvStopStatus =  $zone->getConfiguration('uvStop');
                log::add('arrosage', 'debug','cron : zone : '.$zoneName.' : zoneDuration : ' .  $duration);	
                log::add('arrosage', 'debug','cron : zone : '.$zoneName.' : winterMode : ' .  $winterStatus);	
                log::add('arrosage', 'debug','cron : zone : '.$zoneName.' : rainStop : ' .  $rainStopStatus);	
                log::add('arrosage', 'debug','cron : zone : '.$zoneName.' : windStop : ' .  $windStopStatus);	
                log::add('arrosage', 'debug','cron : zone : '.$zoneName.' : moistureStop : ' .  $moistureStopStatus);	
                log::add('arrosage', 'debug','cron : zone : '.$zoneName.' : uvStop : ' .  $uvStopStatus);	

				
		//refresh widget
		$zone->refreshWidget();

       		$stopTask = 0;

		//apply duration modification from water adjustement value
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
       		         if($cmd_device->execCmd() > $zone->getConfiguration('windSpeedMax')){
       		                $stopTask = 1;
				log::add('arrosage', 'debug','cron : zone : '.$zoneName.' : wind control active');
       		         }
       		 }
	        log::add('arrosage', 'debug','cron : ############### stop' ); 

       				 //check if the moisture control has been activated
       				 if ( $moistureStopStatus == 1 ){
       				         $cmd_device=cmd::byId(trim($zone->getConfiguration('moistureSensor'),"#"));

       				         $moistureValue=$cmd_device->execCmd();
       				         $moistureMaxValue=$zone->getConfiguration('moistureMax');
       				         $moistureMinValue=$zone->getConfiguration('moistureMin');

       				         if ( $moistureValue > $moistureMaxValue ){
       				                $stopTask = 1;
						log::add('arrosage', 'debug','cron : zone : '.$zoneName.' : moisture control active');
       				         }
       				 }

       				 //check if uv crontole has been activated
       				 if ( $uvStopStatus == 1 ){
       				         $cmd_device=cmd::byId(trim($zone->getConfiguration('uvSensor'),"#"));

       				         $uvValue=$cmd_device->execCmd();
       				         $uvMaxValue=$zone->getConfiguration('uvMax');
//     				           $moistureMinValue=$zone->getConfiguration('uvMin');

       				         if ( $uvValue > $uvMaxValue ){
       				                $stopTask = 1;
						log::add('arrosage', 'debug','cron : zone : '.$zoneName.' : UV exceed value');
       				         }
       				 }
				
				//check if an interrupt has been detected
			        if ( $stopTask == 1 ){
					log::add('arrosage', 'debug','cron : zone : '.$zoneName.' : interrupt detected');
			                $cmd_device=cmd::byId(trim($zone->getConfiguration('zoneStatus'),"#"));
					
					//close the valve if open
					$zone->manageValve('Off');
					
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
							$zone->doNotification('arrosage : weather prevision','enough rain, no irrigation needed');
				                        //return 1;
				                }else{

   			                    		 try {
   			                    		   log::add('arrosage', 'info','Cron added############################################## ' );
   			                    		   //log::add('arrosage', 'info','Command on '. $zone->getConfiguration('zoneOn')." at ".$startTime);

					    		    log::add('arrosage', 'info','cron : zone : '.$zoneName.' : zone on');
							    $zone->doNotification('arrosage : zone : '.$zoneName,' zone on');

   			                    		    $zone->manageValve('On');
								    		    
					    		    //save start and stop time in the zone configuration
					    		    $zone->setConfiguration('startTime',$startTime);
					    		    $zone->setConfiguration('stopTime',$stopTime);
					    		    $zone->save();

   			                    		 } catch (Exception $exc) {
   			                    		         log::add('arrosage', 'error', __('Erreur pour ', __FILE__) . $zone->getHumanName() . ' : ' . $exc->getMessage());
   			                    		 }
						}
   			             }


   			          } catch (Exception $exc) {
   			                  log::add('arrosage', 'error', __('Expression cron non valide pour ', __FILE__) . $zone->getHumanName() . ' : ' . $startCron);
   			          }
   			          // cron to close the valve
   			          try {

   			             $cStop = new Cron\CronExpression($stopCron, new Cron\FieldFactory);
   			             if ($cStop->isDue()) {
   			                     //log::add('arrosage', 'info','Command on '. $zone->getConfiguration('zoneOff')." at ".$stopTime );
	
						log::add('arrosage', 'info','cron : zone : '.$zoneName.' : zone off');
                                              	$zone->doNotification('arrosage : zone : '.$zoneName,'zone off');
   			                      	$zone->manageValve('Off');

   			             }
   			          } catch (Exception $exc) {
   			                  log::add('arrosage', 'error', __('Expression cron non valide pour ', __FILE__) . $zone->getHumanName() . ' : ' . $stopCron);
   			          }


				//save last stop time as new start time + 1min to have a short pause 
				$startTime=date('H:i',strtotime($stopTime. '+ '. $zoneDelay .' minute'));
			##}

			
		#}

	        log::add('arrosage', 'debug','cron : ############### end loop' ); 

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

		# foreach (eqLogic::byType('arrosage_master') as $zone) {
		# 	$zone->refreshWidget();
		#}
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

  




  public function postSave(){
        log::add('arrosage', 'debug','arrosage : postsSave');

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
	$replace['#time#'] = "Heure début : ". $this->getConfiguration('startTime');	

       //concatenation of the week days we need to start the cron job
        for ($i = 1; $i <= 7 ;$i++)
        {
               if ($this->getConfiguration('cbDay'.$i) == 1) {
                 if ($startDay != "")
                 {
                       $startDay = $startDay .", ";
                 }
                 $startDay = $startDay . jddayofweek($i,2);
               }
        }
         $replace['#days#'] = "Jours : " .$startDay ;

        //concatenation of the moth we need to start the cron job
        for ($i = 1; $i <= 12 ;$i++)
        {
               if ($this->getConfiguration('cbMonth'.$i) == 1) {
                 if ($startMonth != "")
                 {
                       $startMonth = $startMonth .", ";
                 }
                 $startMonth = $startMonth . date('M', mktime(0, 0, 0, $i, 10));;
               }
        }
        $replace['#months#'] = "Mois : ". $startMonth;

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
			if ($moistureMinValue == "" || $moistureMaxValue == ""){
                                throw new Exception(__('L\'humidité min et l\'humidité max ne peuvent pas être vide', __FILE__));
                        }

                        if ($moistureMinValue < 0 || $moistureMinValue > $moistureMaxValue){
                                throw new Exception(__('L\'humidité min doit être superieur à 0% et inférieur à l\'humidité max', __FILE__));
                        }
                        if ($moistureMaxValue > 100 || $moistureMaxValue <  $moistureMinValue){
                                throw new Exception(__('L\'humidité max doit être inférieur à 100% et superieur à l\'humidité min', __FILE__));
                        }
                }

		//check if the wind control has been activated and if the max speed has been defnied
               if ($this->getConfiguration('windStop') == 1){
			$cmd_device=cmd::byId(trim(config::byKey('windSensor','arrosage'),"#"));
                        if( $cmd_device == "" ){
                                throw new Exception(__('Arret si vent ne peut pas etre activ� sans sonde' ,  __FILE__));
                                log::add('arrosage', 'debug','arrosage : preSave : cannot ativate wind check without wind sensore' );
                        }

                        $windMaxValue = $this->getConfiguration('windSpeedMax');
                        if (  $windMaxValue == "" ){
                                throw new Exception(__('La vitesse du vent max ne peut pas être vide' ,  __FILE__));
                        }
                        if (  $windMaxValue < 1 ){
                                throw new Exception(__('La vitesse du vent max doit être superieur à 0km/h ' , __FILE__));
                        }

                }
		//check for uv setup
               if ($this->getConfiguration('uvStop') == 1){

                        $uvMaxValue = $this->getConfiguration('uvMax');
                        if (  $uvMaxValue == "" ){
                                throw new Exception(__('UV max ne peut pas être vide' ,  __FILE__));
                        }
                        if (  $uvMaxValue < 1 ){
                                throw new Exception(__('UV max doit être superieur à 0% ' , __FILE__));
                        }

                }
		
		if ($this->getConfiguration('rainStop') == 1){
			$cmd_device=cmd::byId(trim(config::byKey('rainSensor','arrosage'),"#"));
			if( $cmd_device == "" ){
				throw new Exception(__('Arret si pluie ne peut pas etre activ� sans sonde de pluie' ,  __FILE__));
				log::add('arrosage', 'debug','arrosage : preSave : cannot ativate rain check without rain sensore' );
			}
		}

		//check if the duration is greater as 3 	
		$zoneDurationValue=$this->getConfiguration('zoneDuration');
		if ( $zoneDurationValue < 4 && $zoneDurationValue != ''){
			 throw new Exception(__($zoneDurationValue.'La durée doit être superieur à 3min' , __FILE__));
		}
		

	}
  	public function postInsert(){

		log::add('arrosage', 'debug','arrosage : postInsert : Commad creation' );
        //        if(count(cmd::byLogicalId('winter')) == 0) {
			$this->createCustomCmd('winter');
			$this->createCustomCmd('rain');
			$this->createCustomCmd('wind');
			$this->createCustomCmd('moisture');
			$this->createCustomCmd('zoneAction');			
          //      }
	}
	public function preInsert(){
                log::add('arrosage', 'debug','arrosage : preInsert : Set dureation to 5min by default' );
		$this->setConfiguration("zoneDuration",5);
                $this->setLogicalId("zone");


        }

	public function createCustomCmd($zoneName){
		log::add('arrosage', 'debug','arrosage : createCustomCmd : '.$zoneName );
		$masterCmd = new arrosageCmd();
                $masterCmd->setName($zoneName);
                $masterCmd->setLogicalId($zoneName);
                $masterCmd->setEqLogic_id($this->id);
                $masterCmd->setType('action');
                $masterCmd->setSubType('other');
                $masterCmd->save();
	}


	 //change winter option status                                                                                                                                   
        public function doWinter(){
                log::add('arrosage', 'debug','doWinter : command winter' );
		$this->changeOptionStatus('winterMode');
		$this->doNotification("arrosage: doWinter","zone : ".$this->getName()." : status has change");
        }    

	//change rain option status                                                                                                                          
        public function doRain(){                                                                                                                                                                     
		log::add('arrosage', 'debug','doRain : command rain' );
		$this->changeOptionStatus('rainStop');
                $this->doNotification("arrosage: doRain","zone : ".$this->getName()." : status has change");
        }    

	//change wind option status                                                                                                                             
        public function doWind(){ 
                log::add('arrosage', 'debug','doWind : command wind' );
		$this->changeOptionStatus('windStop');
                $this->doNotification("arrosage: doWind","zone : ".$this->getName()." : status has change");
        }    

	 //change moisture option status                                                                                                                              
        public function doMoisture(){  
                log::add('arrosage', 'debug','doMoisture : command moisture' );
		$this->changeOptionStatus('moistureStop');
                $this->doNotification("arrosage: doMoisture","zone : ".$this->getName()." : status has change");
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
                        $this->doNotification("arrosage: doZoneAction","zone ".$this->getName()." off");
                } else{

			//open the valve
                        $this->manageValve('On');
                        $this->doNotification("arrosage: doZoneAction","zone ".$this->getName()." on");
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
	public function doNotification($title,$message) {


                                     log::add('arrosage', 'debug','notification : master : '.config::byKey('masterNotif','arrosage'));
		if ( config::byKey('masterNotif','arrosage')  == 1){
                	$notifDevice=config::byKey('notifDev','arrosage');

                	             log::add('arrosage', 'debug','notification : device string : '.$notifDevice);

                	if ($notifDevice != ''){
                	        $notifCmd=cmd::byString($notifDevice);
				     log::add('arrosage', 'debug','notification : title : '.$title );
                	        $notifCmd->execCmd($options=array('title'=>$title, 'message'=> $message));
                	}
		}
        }


}

class arrosageCmd extends cmd {

        public function execute($_options = array()) {
                if ($this->getLogicalId() == 'winter') {                                                                                                                                                 
                        $this->getEqLogic()->doWinter();
                      //  $this->getEqLogic()->doNotification('test','test2');                                                                                                        
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
