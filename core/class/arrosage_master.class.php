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

	        $html_forecast = '';
	        $replace['#id#'] = $this->getId();
        	$replace['#eqLink#'] = $this->getLinkToConfiguration();
	        $replace['#zoneName#'] = $this->getName();
		
		 $replace['#delay_value#'] =  $this->getConfiguration('delayAdj');
                 $replace['#water_value#'] =  $this->getConfiguration('waterAdj');

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





}
class arrosage_mastercmd extends cmd {



}

?>

