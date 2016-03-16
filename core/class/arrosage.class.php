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
     log::add('arrosage', 'info','log start' );

      foreach (eqLogic::byType('arrosage') as $eqLogic) {
//     	log::add('arrosage', 'info','eqLogic : ' . $eqLogic->getHumanName().', id='.$eqLogic->getId());

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

  //        log::add('arrosage', 'info','startday : '.$startDay );
  //        log::add('arrosage', 'info','startHour : '.$startHour );
  //        log::add('arrosage', 'info','startMin : '.$startMin );
  //        log::add('arrosage', 'info','stopHour : '.$stopHour ); 
  //        log::add('arrosage', 'info','stopMin : '.$stopMin );
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
                           /*$eqLogic->refresh();*/
  
                          // log::add('arrosage', 'info','Cron added '.$startCron );
			  log::add('arrosage', 'info','Command on '. $eqLogic->getConfiguration('zoneOn')." at ".$startTime);
			       $cmd_device=cmd::byId(trim($eqLogic->getConfiguration('zoneOn'),"#"));
				$cmd_device->execute();
/*
		           $cmd_device=cmd::byId(trim($eqLogic->getConfiguration('zoneOff'),"#"));
                                $cmd_device->execute();
 			*/
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
                        log::add('arrosage', 'info','Command on '. $eqLogic->getConfiguration('zoneOff')." at ".$stopTime );
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
/*
     	   $replace['#cmd_id#'] = $cmd_def->getId();
           $replace['#cmd_uid#'] = $cmd_def->getId();
*/
           $cmd_name = $cmd_def->getName();
           $cmd_start = $cmd_def->getConfiguration('startTime');
           $cmd_duration = $cmd_def->getConfiguration('duration');
	   $cmd_list .= '<div style="font-weight: bold;font-size : 12px;#hideCmdName#">' . $cmd_name ." : ".$cmd_start." for ".$cmd_duration." min</div>";
	
	}
	$replace['#cmd_list#'] = $cmd_list;

	if ( $cmd_device->getConfiguration('value') == 1){	
	   $replace['#cmd_stat#'] = 'icon_sprinkler2_on';
	}else {
           $replace['#cmd_stat#'] = 'icon_sprinkler2_off';
	}


	$html = template_replace($replace, getTemplate('core', $_version, 'current', 'arrosage'));
       // cache::set('arrosageWidget' . $_version . $this->getId(), $html, 0);
        return $html;
  }
}

class arrosageCmd extends cmd {

 	public function preSave() {
		$this->setType('action');
		$this->setSubType('other');
		if ($this->getConfiguration('duration') == '') {
			throw new Exception(__('La duree ne peut pas etre null', __FILE__));
		}



                if ($this->getConfiguration('startTime') == '') {
                        throw new Exception(__('L\'heure de début ne peut pas etre null', __FILE__));
                }
		$pos = strpos($this->getConfiguration('startTime'),':');

		if ($pos === false) {
                        throw new Exception(__('L\'heure de debut doit etre au format 00:00', __FILE__));
                }

	
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
