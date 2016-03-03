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

}

class arrosageCmd extends cmd {

 	public function preSave() {
		$this->setType('action');
		$this->setSubType('other');
		if ($this->getConfiguration('duration') == '') {
			throw new Exception(__('La durée ne peut pas etre null', __FILE__));
		}
                if ($this->getConfiguration('startTime') == '') {
                        throw new Exception(__('L\'heure de début ne peut pas etre null', __FILE__));
                }
	
   		$dayStat = 0;
		for ($i = 0;$i <= 7; $i++)
		{
   			
                	if ($this->getConfiguration('cbDay' .$i) != 0) {
				$dayStat++;
                 /*       	throw new Exception(__('Un jour doit etre selectionné', __FILE__));*/
                	}	
		}
		if ($dayStat == 0) {
			throw new Exception(__('Un jour doit etre selectionné', __FILE__));
		}

/*
		if (filter_var($this->getConfiguration('recipient'), FILTER_VALIDATE_EMAIL) === false) {
			throw new Exception(__('L\'adresse mail n\'est pas valide', __FILE__));
		}a
*/
	}



}
