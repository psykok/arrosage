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



class arrosage extends eqLogic {

  public static function cron() {
//	log::add('arrosage', 'info','log start' );

      foreach (eqLogic::byType('arrosage') as $eqLogic) {
//     	log::add('arrosage', 'info','eqLogic : ' . $eqLogic->getHumanName().', id='.$eqLogic->getId());
	
	$winterStatus = $eqLogic->getConfiguration('winterMode');
	$rainStopStatus = $eqLogic->getConfiguration('rainStop');
	$windStopStatus = $eqLogic->getConfiguration('windStop');
	$moistureStopStatus = $eqLogic->getConfiguration('moistureStop');
	$uvStopStatus =  $eqLogic->getConfiguration('uvStop');
	
	$stopTask = 0;


        //check if winter mode has been activated                                 
	if ( $winterStatus == 1 ){
                $stopTask = 1;
        }
	//check if the rain control has been activated
	if ( $rainStopStatus == 1 ){
		 //$rainSensorID = trim(config::byKey('rainSensor','arrosage'),"#");

                $cmd_device=cmd::byId(trim(config::byKey('rainSensor','arrosage'),"#"));
                if ( $cmd_device->getConfiguration('value') == 1){
                         $stopTask = 1;
                }
        }                                                                                                                                                                                                                                    
        //check if the wind crontrol has been activated
        if ( $windStopStatus == 1 ){
                $cmd_device=cmd::byId(trim(config::byKey('windSensor','arrosage'),"#"));
		
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
//                $moistureMinValue=$eqLogic->getConfiguration('uvMin');

                if ( $uvValue > $uvMaxValue ){
                        $stopTask = 1;
                }
        }






        //check if an interrption has been detected
        if ( $StopTask == 1 ){
		$cmd_device=cmd::byId(trim($eqLogic->getConfiguration('zoneStatus'),"#"));

                if ( $cmd_device->getConfiguration('value') == 1){
                        $cmd_device=cmd::byId(trim($eqLogic->getConfiguration('zoneOff'),"#"));
                        $cmd_device->execute();
                }
                return 1;
        }


     	foreach (cmd::byEqLogicId($eqLogic->getId()) as $cmd_def) {
//          log::add('arrosage', 'info','cmd : '.$cmd_def->getHumanName() );
     	
	  $duration = $cmd_def->getConfiguration('duration');
	  $startTime =  $cmd_def->getConfiguration('startTime');
          $startDay = "";

          //log::add('arrosage', 'info','real duration : '.$duration );

          //concatenation of the week days we need to start the cron job
          for ($i = 1; $i <= 7 ;$i++)
          {
                 if ($cmd_def->getConfiguration('cbDay'.$i) == 1) {
	           if ($startDay != "")
                   {                                                                                                                                             
                         $startDay = $startDay .",";
                   }      
                   $startDay = $startDay . $i;
                 }
          }

	  //creation of the stopTime
          $stopTime = date('H:i',strtotime($startTime . '+ '.$duration .' minute'));

          $pos = strpos($startTime,':');
          $startHour = substr($startTime,0,$pos);
          $startMin = substr($startTime,-$pos);

          $pos = strpos($stopTime,':');                                                                                                                        
          $stopHour = substr($stopTime,0,$pos);
          $stopMin = substr($stopTime,-$pos);     

  //        $startMin = "*";
  //        $startHour = "*";
 
          $startMonth = "*";
          $startDayOfMonth = "*";
   //      $startDay = "*";
 
          $startCron = $startMin." ".$startHour." ".$startDayOfMonth." ".$startMonth." ".$startDay;
	  $stopCron = $stopMin." ".$stopHour." * * *";  
  
          if ($eqLogic->getIsEnable() == 1 && $startCron != '') {
	     //cron to open the valve
             try {

                $c = new Cron\CronExpression($startCron, new Cron\FieldFactory);
                if ($c->isDue()) {
                        try {
                          // log::add('arrosage', 'info','Cron added '.$startCron );
			  //log::add('arrosage', 'info','Command on '. $eqLogic->getConfiguration('zoneOn')." at ".$startTime);
			       $cmd_device=cmd::byId(trim($eqLogic->getConfiguration('zoneOn'),"#"));
				$cmd_device->execute();

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
                } 
 	     } catch (Exception $exc) {                                                                                                                         
                     log::add('arrosage', 'error', __('Expression cron non valide pour ', __FILE__) . $eqLogic->getHumanName() . ' : ' . $stopCron);
             }            


          }

     	}
     }
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
        $replace['#id#'] = $this->getId();
	 $replace['#eqLink#'] = $this->getLinkToConfiguration();
	$replace['#zoneName#'] = $this->getName();

	$cmd_device=cmd::byId(trim($this->getConfiguration('zoneStatus'),"#"));

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

	$html = template_replace($replace, getTemplate('core', $_version, 'current', 'arrosage'));
       // cache::set('arrosageWidget' . $_version . $this->getId(), $html, 0);
        return $html;
  }

	public function preSave() {

		//check if the moisture control has been activated and if the max and min have been defined
                if ($this->getConfiguration('moistureStop') == 1){
                        $moistureMaxValue = $this->getConfiguration('moistureMax');
                        $moistureMinValue = $this->getConfiguration('moistureMin');

                        if ($moistureMinValue < 0 || $moistureMinValue > $moistureMaxValue){
                                throw new Exception(__('L\'humidité min doit etre superieur a 0% et inférieur à l\'humidité max', __FILE__));
                        }
                        if ($moistureMaxValue > 100 || $moistureMaxValue <  $moistureMinValue){
                                throw new Exception(__('L\'humidité max doit etre inférieur a 100% et superieur à l\'humidité min', __FILE__));
                        }
                }

		//check if the wind control has been activated and if the max speed has been defnied
               if ($this->getConfiguration('windStop') == 1){

                        $windMaxValue = $this->getConfiguration('windSpeedMax');

                        if (  $windMaxValue < 1 ){
                                throw new Exception(__('La vitesse du vent max doit être superieur a 0km/h ' , __FILE__));

                        }

                }


	}
}

class arrosageCmd extends cmd {

 	public function preSave() {
		$this->setType('action');
		$this->setSubType('other');

		//check if the duration of the task is set
		if ($this->getConfiguration('duration') == '') {
			throw new Exception(__('La duree ne peut pas etre null', __FILE__));
		}


		//check if the start time is set
                if ($this->getConfiguration('startTime') == '') {
                        throw new Exception(__('L\'heure de début ne peut pas etre null', __FILE__));
                }

		//check if the time is set in the right format
		$pos = strpos($this->getConfiguration('startTime'),':');

		if ($pos === false) {
                        throw new Exception(__('L\'heure de debut doit etre au format 00:00', __FILE__));
                }

		//check if a starteup day has been selected	
   		$dayStat = 0;
		for ($i = 0;$i <= 7; $i++)
		{
   			
                	if ($this->getConfiguration('cbDay' .$i) != 0) {
				$dayStat++;
                	}	
		}
		if ($dayStat == 0) {
			throw new Exception(__('Un jour doit etre selectionné', __FILE__));
		}

	}



}
