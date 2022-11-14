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

try {
    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');

    if (!isConnect('admin')) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }

  /* Fonction permettant l'envoi de l'entête 'Content-Type: application/json'
    En V3 : indiquer l'argument 'true' pour contrôler le token d'accès Jeedom
    En V4 : autoriser l'exécution d'une méthode 'action' en GET en indiquant le(s) nom(s) de(s) action(s) dans un tableau en argument
  */
    ajax::init();

	// ajoute la class //
	$_plugin = 'historiqueMeteo';
	include_file('core', $_plugin, 'class', $_plugin);
  	log::add('historiqueMeteo getGeofence', 'debug', '[AJAX] getGeofence() starting 1...');
  	// ------------------------------- ID Obligatoire //
	if (init('id') > 0) {
		// récupère les information de l'équipement //
		$DOC = eqLogic::byId(init('id'));
		$DOC->_whatLog = 'AJAX';
		if (!is_object($DOC)) {
			throw new Exception(__('Objet inconnu verifié l\'id', __FILE__).': '.init('id'));
		}
  		// méthode = getAllRequest : récupère les valeurs de toutes les trames du module wifi connu //
		if (init('action') == 'getGeofence') {
			log::add('historiqueMeteo getGeofence', 'debug', '[AJAX] getGeofence() starting 2...');
			if (($_aRes = $DOC->getGeofence( init('geoloc')) ) !==false) {
				ajax::success(json_encode($_aRes,JSON_UNESCAPED_UNICODE));
			} else {
				ajax::error(__('La requête "getGeofence()" n\'a pas put être exécutée.',__FILE__));
			}
		}
    }
    throw new Exception(__('Aucune méthode correspondante à', __FILE__) . ' : ' . init('action'));
    /*     * *********Catch exeption*************** */
}
catch (Exception $e) {
    ajax::error(displayException($e), $e->getCode());
}