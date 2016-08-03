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



class arrosage_tasker extends eqLogic {

	public function getLinkToConfiguration() {
		return 'index.php?v=d&p=arrosage&m=arrosage&id=' . $this->getId();
	}//End getLinkToConfiguration func


        public function toHtml($_version = 'dashboard') {
                if ($this->getIsEnable() != 1) {
                        return '';
                }
		$cmd_list='';	
		$replace['#id#'] = $this->getId();                                                                                                                                                        
                $replace['#eqLink#'] = $this->getLinkToConfiguration();                                                                                         
                $replace['#zoneName#'] = $this->getName(); 
		$version = jeedom::versionAlias($_version);

		foreach (cmd::byEqLogicId($this->getId()) as $cmd_def) {
	//      	log::add('arrosage', 'info','dashboard cmd : '.$cmd_def->getHumanName() );
	 
	        	$cmd_name = $cmd_def->getName();
	        	$cmd_start = $cmd_def->getConfiguration('startTime');
			$cmd_list .= '<tr><td id="tab">' . $cmd_name .'</td><td  id="tab">'.$cmd_start.'</td>';
			
	                if ($version != 'mobile') {
				$cmd_list .= '<td  id="tab_day">';
				$cmd_list .= '<table id="days"><tr>';
				 for ($i = 1; $i <= 7 ;$i++)
		                 {
					$tdstyle=' style="background-color: none;color:white;"';
		                        if ($cmd_def->getConfiguration('cbDay'.$i) == 1) {
						$tdstyle=' style="background-color: white;color:#19bc9c;"';
		                        }
					$shortDayName=array(1 => "L","M","M","J","V","S","D");
					$cmd_list .=  '<td'.$tdstyle.'>'.$shortDayName[$i].'</td>';
		                 }
			$cmd_list .= " </tr></table></td>";
			}			

			$cmd_list .= "</tr>";
		
		}
		$replace['#cmd_list#'] = $cmd_list;
		
		$html = template_replace($replace, getTemplate('core', $version, 'tasker', 'arrosage'));
               // cache::set('arrosageWidget' . $_version . $this->getId(), $html, 0);
                return $html;
	}
	
	 public function preSave() {

                //check if the delay beteen the zone is >0 and <5min
		$zoneDelay= $this->getConfiguration('delayBtwZone');

                if ($zoneDelay < 0){
                              throw new Exception(__('Le temps doit etre superieur ou égal 0min', __FILE__));
                }
                if ($zoneDelay > 5){
                              throw new Exception(__('Le temps doit etre inférieur ou égal 5min', __FILE__));
                }

        }



}


class arrosage_taskerCmd extends cmd {

	  public function preSave() {

              log::add('arrosage', 'info','type cmd : '. $this->getEqType_name() );


                $this->setType('action');
                $this->setSubType('other');
                $this->setLogicalId('task');

                //check if the duration of the task is set
          //      if ($this->getConfiguration('duration') == '') {
            //            throw new Exception(__('La duree ne peut pas etre null', __FILE__));
              //  }


                //check if the start time is set
                if ($this->getConfiguration('startTime') == '') {
                        throw new Exception(__('L\'heure de début ne peut pas être null', __FILE__));
                }

                //check if the time is set in the right format
		$timeValue=$this->getConfiguration('startTime');
                $pos = strpos($timeValue,':');

                if ($pos === false) {
                        throw new Exception(__('L\'heure de début doit être au format 24h: 00:00', __FILE__));
                }
		if (substr($timeValue,0,$pos) > 24){	
			throw new Exception(__('L\'heure de début doit être inferieur à 24', __FILE__));
		}
		if (substr($timeValue,0,$pos) < 0){
                        throw new Exception(__('L\'heure de début doit être superieur à 0', __FILE__));
                }
		if (substr($timeValue,$pos+1) > 59){
                        throw new Exception(__('Les minutes doivent être inferieur à 59', __FILE__));
                }
                if (substr($timeValue,$pos+1) < 0){
                        throw new Exception(__('Les minutes doivent être superieur à 0', __FILE__));
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
                        throw new Exception(__('Un jour doit être selectionné', __FILE__));
                }

               //check if a starteup month  has been selected
                $monthStat = 0;
                for ($i = 0;$i <= 12; $i++)
                {

                        if ($this->getConfiguration('cbMonth' .$i) != 0) {
                                $monthStat++;
                        }
                }
                if ($monthStat == 0) {
                        throw new Exception(__('Un mois doit être selectionné', __FILE__));
                }


		

        }
	public function isTime($time)
	{
		return preg_match("#([0-1]{1}[0-9]{1}|[2]{1}[0-3]{1}):[0-5]{1}[0-9]{1}#", $time);
	}


}

?>

