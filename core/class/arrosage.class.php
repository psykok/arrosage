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
     	log::add('arrosage', 'info','eqLogic : ' . $eqLogic->getHumanName().', id='.$eqLogic->getId());

     	foreach (cmd::byEqLogicId($eqLogic->getId()) as $cmd_def) {
          log::add('arrosage', 'info','cmd : '.$cmd_def->getHumanName() );
     	
	  $duration = $cmd_def->getConfiguration('duration');
	  $startTime =  $cmd_def->getConfiguration('startTime');
          $startDay = "";

          //log::add('arrosage', 'info','real duration : '.$duration );

          /*concatenation des jours de la semaine*/
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
          /*creation de l'heure d'arret*/
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
    //      $startMin = "*";
    //      $startHour = "*";
 
          $startMonth = "*";
          $startDayOfMonth = "*";
    //      $startDay = "*";
 
          $autorefresh = $startMin." ".$startHour." ".$startDayOfMonth." ".$startMonth." ".$startDay;
  
  
          if ($eqLogic->getIsEnable() == 1 && $autorefresh != '') {
             try {

                $c = new Cron\CronExpression($autorefresh, new Cron\FieldFactory);
                if ($c->isDue()) {
                        try {
                           /*$eqLogic->refresh();*/
  
                           log::add('arrosage', 'info','Cron added '.$autorefresh );
			  log::add('arrosage', 'info','Command on '. $eqLogic->getConfiguration('zoneOn') );
                        } catch (Exception $exc) {
                                log::add('arrosage', 'error', __('Erreur pour ', __FILE__) . $eqLogic->getHumanName() . ' : ' . $exc->getMessage());
                        }
                }
             } catch (Exception $exc) {
                     log::add('arrosage', 'error', __('Expression cron non valide pour ', __FILE__) . $eqLogic->getHumanName() . ' : ' . $autorefresh);
             }
          }

     	}
     }
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
