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
  require_once __DIR__ . '/../../../../core/php/core.inc.php';

class historiqueMeteo extends eqLogic {
  /*     * *************************Attributs****************************** */

  /*
         * Permet de définir les possibilités de personnalisation du widget (en cas d'utilisation de la fonction 'toHtml' par exemple)
         * Tableau multidimensionnel - exemple: array('custom' => true, 'custom::layout' => false)
          public static $_widgetPossibility = array();
         */
  public static $_widgetPossibility = array('custom' => true);
  /*
         * Permet de crypter/décrypter automatiquement des champs de configuration du plugin
         * Exemple : "param1" & "param2" seront cryptés mais pas "param3"
          public static $_encryptConfigKey = array('param1', 'param2');
         */

  /*     * ***********************Methode static*************************** */

  /*
         * Fonction exécutée automatiquement toutes les minutes par Jeedom
          public static function cron() {}
         */

  /*
         * Fonction exécutée automatiquement toutes les 5 minutes par Jeedom
          public static function cron5() {

          }

         */
  /*
         * Fonction exécutée automatiquement toutes les 10 minutes par Jeedom
          public static function cron10() {}
         */

  /*
         * Fonction exécutée automatiquement toutes les 15 minutes par Jeedom
          public static function cron15() {}
         */

  /*
         * Fonction exécutée automatiquement toutes les 30 minutes par Jeedom
          public static function cron30() {}
         */

  // Fonction exécutée automatiquement toutes les heures par Jeedom
  /*public static function cronHourly () {

  }
  */
  /* Fonction exécutée automatiquement tous les jours par Jeedom
          public static function cronDaily() {}
  */
   /*   public function toHtml($_version = 'dashboard') {
                                log::add ( __CLASS__, 'debug',  ' log replace : 1' );
     
 */
  public static function cronDaily() {

    log::add ( __CLASS__, 'debug', ' [CRON][Daily] Début'  );

    if ( $_eqLogic_id == null ) { //La fonction n’a pas d’argument donc on recherche tous les équipements du plugin
      $eqLogics = self::byType ( 'historiqueMeteo', true );
    } else { //La fonction a l’argument id(unique) d’un équipement(eqLogic)
      $eqLogics = array (self::byId ( $_eqLogic_id ));
    }

    foreach ( $eqLogics as $historiqueMeteo ) {
      $cmd = $historiqueMeteo->getCmd ( null, 'refresh' ); //retourne la commande "refresh si elle existe
      if ( ! is_object ( $cmd ) ) { //Si la commande n'existe pas
        continue; //continue la boucle
      }
      $cmd->execCmd (); //la commande existe on la lance
    }
    log::add ( __CLASS__, 'debug', ' [CRON][Daily] Début'  );

  }
  /*


  /*     * *********************Méthodes d'instance************************* */

  // Fonction exécutée automatiquement avant la création de l'équipement
  public function preInsert () {
    $this->setCategory ( 'energy', 1 );
    $this->setIsEnable ( 1 );
    $this->setIsVisible ( 1 );
  }

  // Fonction exécutée automatiquement après la création de l'équipement
  public function postInsert () {

  }

  // Fonction exécutée automatiquement avant la mise à jour de l'équipement
  public function preUpdate () {
    if ( empty ( $this->getConfiguration ( 'geofence' ) ) ) {
      throw new Exception ( __ ( 'Le code géofence doit être renseigné', __FILE__ ) );
    }
  }

  // Fonction exécutée automatiquement après la mise à jour de l'équipement
  public function postUpdate () {
    self::cronDaily ( $this->getId () ); //lance la fonction cronHourly avec l’id de l’eqLogic

  }
  

  // Fonction exécutée automatiquement avant la sauvegarde (création ou mise à jour) de l'équipement
  public function preSave () {

    log::add(__CLASS__, 'debug', $this->getHumanName () .'pre save' );
    $this->setDisplay ( "width", "800px" );
  }

  function getListeDefaultCommandes () {
    return array (
      "date" => array ('Dernier relevé', 'info', 'string', "", 0, 0, "GENERIC_INFO", 'core::default', 'core::default', '', 1, 0, 0, 1, 0),
      //Lieu 1
      "1_lieu" => array ('Lieu', 'info', 'string', "", 0, 0, "GENERIC_INFO", 'core::default', 'core::default', '', 1, 0, 0, 2, 0),
      "1_dailyrain" => array ('Pluie', 'info', 'numeric', "mm", 0, 1, "RAIN_CURRENT", 'historiqueMeteo::rainJour', 'core::tile', '', 1, 0, 0, 3, 1),
     // "1_tempMaxMinD" => array ('T max/min', 'info', 'string', "", 0, 1, "", 'pluiehier::cercle', 'core::tile', '', 0, 0, 0, 4, 0),
      "1_tempmind" => array ('T° min', 'info', 'numeric', "°C", 0, 1, "WEATHER_TEMPERATURE_MIN", 'historiqueMeteo::thermometer', 'core::tile', '', 1, 0, 0, 4, 0),
      "1_tempmaxd" => array ('T° max', 'info', 'numeric', "°C", 0, 1, "WEATHER_TEMPERATURE_MAX", 'historiqueMeteo::thermometer', 'core::tile', '', 1, 0, 0, 5, 0),
      "1_tempmoyd" => array ('T° moyenne', 'info', 'numeric', "°C", 0, 1, "WEATHER_TEMPERATURE", 'historiqueMeteo::thermometer', 'core::tile', '', 1, 0, 0, 6, 0),
      
      "1_monthrain" => array ('Pluie mois', 'info', 'numeric', "mm", 0, 1, "RAIN_CURRENT", 'historiqueMeteo::rainMois', 'core::tile', '', 1, 0, 0, 7, 1),
     // "1_MaxMinM" => array ('T max/min mensuelle', 'info', 'string', "", 0, 1, "", 'historiqueMeteo::cercle', 'core::tile', '', 0, 0, 0, 8, 0),
      "1_minm" => array ('T° min mois', 'info', 'numeric', "°C", 0, 1, "WEATHER_TEMPERATURE_MIN", 'historiqueMeteo::thermometer', 'core::tile', '', 1, 0, 0, 8, 0),
      "1_maxm" => array ('T° max mois', 'info', 'numeric', "°C", 0, 1, "WEATHER_TEMPERATURE_MAX", 'historiqueMeteo::thermometer', 'core::tile', '', 1, 0, 0, 9, 0),
      "1_tempmoym" => array ('T° moyenne mois', 'info', 'numeric', "°C", 0, 1, "WEATHER_TEMPERATURE", 'historiqueMeteo::thermometer', 'core::tile', '', 1, 0, 0, 10, 0),
     //Lieu 2
      "2_lieu" => array ('Lieu 2', 'info', 'string', "", 0, 0, "GENERIC_INFO", 'core::default', 'core::default', '', 1, 0, 0, 11, 0),
      "2_dailyrain" => array ('Pluie 2', 'info', 'numeric', "mm", 0, 1, "RAIN_CURRENT", 'historiqueMeteo::rainJour', 'core::tile', '', 1, 0, 0, 12, 1),
   //   "2_tempMaxMinD" => array ('T min/max 2', 'info', 'string', "", 0, 1, "", 'pluiehier::cercle', 'core::tile', '', 1, 0, 0, 13, 0),
      "2_tempmind" => array ('T° min 2', 'info', 'numeric', "°C", 0, 1, "WEATHER_TEMPERATURE_MIN", 'historiqueMeteo::thermometer', 'core::tile', '', 1, 0, 0, 13, 0),
      "2_tempmaxd" => array ('T° max 2', 'info', 'numeric', "°C", 0, 1, "WEATHER_TEMPERATURE_MAX", 'historiqueMeteo::thermometer', 'core::tile', '',1, 0, 0, 14, 0),
      "2_tempmoyd" => array ('T° moyenne 2', 'info', 'numeric', "°C", 0, 1, "WEATHER_TEMPERATURE", 'historiqueMeteo::thermometer', 'core::tile', '', 1, 0, 0, 15, 0),
      
      "2_monthrain" => array ('Pluie m2', 'info', 'numeric', "mm", 0, 1, "RAIN_CURRENT", 'historiqueMeteo::rainMois', 'core::tile', '', 1, 0, 0, 16, 1),
     // "2_MaxMinM" => array ('T max/min mensuelle 2', 'info', 'string', "", 0, 1, "", 'pluiehier::cercle', 'core::tile', '', 1, 0, 0, 17, 0),
      "2_minm" => array ('T° min m2', 'info', 'numeric', "°C", 0, 1, "WEATHER_TEMPERATURE", 'historiqueMeteo::thermometer', 'core::tile', '', 1, 0, 0, 17, 0),
      "2_maxm" => array ('T° max m2', 'info', 'numeric', "°C", 0, 1, "WEATHER_TEMPERATURE", 'chistoriqueMeteo::thermometer', 'core::tile', '', 1, 0, 0, 18, 0),
      "2_tempmoym" => array ('T° moyenne m2', 'info', 'numeric', "°C", 0, 1, "WEATHER_TEMPERATURE", 'historiqueMeteo::thermometer', 'core::tile', '', 1, 0, 0, 19, 0),
      // Paramètre histo obligatoire Ne pas Supprimer
      "histo" => array ('Historique', 'info', 'string', "", 0, 0, "GENERIC_INFO", 'core::default', 'core::default', '', 0, 0, 0, 20, 0),
    );
  }
  // Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement
  public function postSave () {

    log::add(__CLASS__, 'debug', $this->getHumanName () .'Fonction: ->post save' );

    foreach( $this->getListeDefaultCommandes() as $id => $data) {
      list($name, $type, $subtype, $unit, $invertBinary, $hist, $generic_type, $template_dashboard, $template_mobile, $listValue,$isVisible,$statd,$statm,$order,$force) = $data;
      $cmd = $this->getCmd(null, $id);
      if ( ! is_object($cmd) ) {

        log::add ( __CLASS__, 'debug', $this->getHumanName () . ' Création commande :' . $id . '/' . $name );
        $cmd = new historiqueMeteoCmd();
        $cmd->setName($name);
        $cmd->setEqLogic_id($this->getId());
        $cmd->setType($type);
        $cmd->setSubType($subtype);
        $cmd->setUnite($unit);
        $cmd->setorder($order);
        $cmd->setLogicalId($id);
        if ($listValue != "") {
          $cmd->setConfiguration('listValue', $listValue);
        }
        $cmd->setDisplay('invertBinary',$invertBinary);
        $cmd->setDisplay('generic_type', $generic_type);
        $cmd->setTemplate('dashboard', $template_dashboard);
        $cmd->setDisplay ( 'showStatsOndashboard', $statd );
        $cmd->setDisplay ( 'showStatsOnmobile', $statm );
        $cmd->setDisplay ( 'forceReturnLineBefore', $force);
        $cmd->setIsVisible($isVisible);
        $cmd->setTemplate('mobile', $template_mobile);
        if ((strpos($id, '_nbcy')!== false) or (strpos($id, '_activ')!== false) or (strpos($id, 'gps_pos')!== false)) {
          $cmd->setIsVisible(0);
        }
        if (strpos($id, '_en')!== false) {
          $cmd->setDisplay('parameters', array("type"=>"mode","largeurDesktop"=>"60","largeurMobile"=>"30"));
        }
        // historic
        $cmd->setIsHistorized($hist);

        $cmd->save();
      } 
    }


    $refresh = $this->getCmd ( null, 'refresh' );
    if ( ! is_object ( $refresh ) ) {
      $refresh = new historiqueMeteoCmd();
      $refresh->setName ( __ ( 'Rafraichir', __FILE__ ) );

      log::add ( __CLASS__, 'debug', $this->getHumanName () . ' Création commande :refresh' . '/Rafraichir'  );
    }
    $refresh->setEqLogic_id ( $this->getId () );
    $refresh->setLogicalId ( 'refresh' );
    $refresh->setType ( 'action' );
    $refresh->setSubType ( 'other' );
    $refresh->save ();
  }


  public function getGeofence( $geoloc ) {
    $array = array();
    $res = array();

    log::add(__CLASS__, 'debug', 'Fonction Geofence: Geoloc from Ajax: ' . $geoloc );

    if ($geoloc == 'none') {
      log::add(__CLASS__, 'error', 'Fonction Geofence: Eqlogic geoloc non configuré.');
      return;
    }
    if ($geoloc == "jeedom") {
      $array['zip'] = config::byKey('info::postalCode');
      $array['ville'] = config::byKey('info::city');
    } else {
      $geotrav = eqLogic::byId($geoloc);
      if (is_object($geotrav) && $geotrav->getEqType_name() == 'geotrav') {
        $geotravCmd = geotravCmd::byEqLogicIdAndLogicalId($geoloc,'location:zip');
        if(is_object($geotravCmd))
          $array['zip'] = $geotravCmd->execCmd();
        $geotravCmd = geotravCmd::byEqLogicIdAndLogicalId($geoloc,'location:city');
        if(is_object($geotravCmd))
          $array['ville'] = $geotravCmd->execCmd();
        else {
          log::add(__CLASS__, 'error', 'Fonction Geofence: Eqlogic geotravCmd object not found');
          return;
        }
      }
      else {
        log::add(__CLASS__, 'error', 'Fonction Geofence: Eqlogic geotrav object not found');
        return;
      }
    }

    $url = 'https://www.terre-net.fr/Meteo/SearchByCritere?critere='.str_replace(' ', '-', $array['ville']).'&countryCode=FR&actionName=Detail';

    log::add(__CLASS__, 'debug', 'Fonction Geofence: url: ' . $url );

    $return = self::callURL($url);
    log::add(__CLASS__, 'debug','Fonction Geofence: ' . print_r($return[0]['Id'],true));

    $res['Geofence'] = $geofence = $return[0]['Id'];

    return $res;
  }
 

  function startsWith( $haystack, $needle ) {
    $length = strlen( $needle );

    return substr( $haystack, 0, $length ) === $needle;
  }

  public function getpluiehier () {
    // Récupération des paramètres utilisateurs
    $geofence = $this->getConfiguration ( "geofence" ); //Code de la ville
    log::add ( __CLASS__, 'debug', "Fonction getpluiehier -> Récupération du paramètre Geofence: $geofence" );

    $histocheck = $this->getConfiguration ( "startHisto" );   // Historique
    log::add ( __CLASS__, 'debug', "Fonction getpluiehier -> Récupération du paramètre Historique: $histocheck" );

    // Est-ce que l'on doit récupérer l'historique?
    $cmd = $this->getCmd ( null, 'histo' );
    $histoValue = $cmd->execCmd();
    log::add ( __CLASS__, 'debug', "Fonction getpluiehier -> Valeur historique: $histoValue" );

    $start = $month = strtotime( date("Y-m-d H:i:s"));  
    $end = strtotime("+1 month", $month);

    if ( ! ( $histoValue == 1 ) ){
      if ( $histocheck == 1 ) {
        $start = $month = strtotime('2013-09-01');
        log::add ( __CLASS__, 'debug', "Fonction getpluiehier -> On charge l'historique..." );
        $this->getCmd ( null, 'histo' )->event ( '1' );
      }
    }


    while($month < $end)
    {
      $moisDeRecherche = date ( "m",$month ); //05
      $anneeDeRecherche = date ( "Y",$month ); //2022';

      $url = "https://www.terre-net.fr/Meteo/HistoriqueRadomeData/$geofence?month=$moisDeRecherche&year=$anneeDeRecherche";
      log::add ( __CLASS__, 'debug', "Fonction getpluiehier -> Url: $url" );

      $month = strtotime("+1 month", $month);

      $curl = curl_init ();
      curl_setopt_array ( $curl, array (
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false, // Skip SSL Verification
        //  CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_HTTPHEADER => array (
          'Content-Length: 0',
          'Cookie: ASP.NET_SessionId = 3imcvpmkcdplmx15f55lm0af'
        ),
      ) );
      //Récupération de la réponse
      $response = curl_exec ( $curl );
      curl_close ( $curl );
      $decoded_json = json_decode ( $response, true );

      //Lecture de la station en paramètre
      //Historique des températures journalière
      $historiques = $decoded_json['Historiques'];
      $counter = 1;
      foreach ( $historiques as $historique ) {

        log::add ( __CLASS__, 'debug', 'Fonction getpluiehier -> Lieu : '.$historique['Station']['Name'] );

        $lieuCmd = $this->getCmd ( null, $counter.'_'.'lieu' );
        $lieuCmd->event ( $historique['Station']['Name'] );

        $dailyRainCmd = $this->getCmd ( null, $counter.'_'.'dailyrain' );
        $tempMoyDayCmd = $this->getCmd ( null, $counter.'_'.'tempmoyd' );
        $tempMinDayCmd = $this->getCmd ( null, $counter.'_'.'tempmind' );
        $tempMaxDayCmd = $this->getCmd ( null, $counter.'_'.'tempmaxd' );

        $tempMaxMinDayCmd = $this->getCmd ( null, $counter.'_'.'tempMaxMinD' );

        $dateCmd = $this->getCmd ( null, 'date' );
        $tempMinMois = '100.00';
        $tempMaxMois = '-15.OO';

       // $formatter = new IntlDateFormatter('fr_FR', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
//$theDate=DateTime::createFromFormat(
        foreach ( $historique['Data'] as $data ) {
          //$dateEnCours = $this->convert_date ( $data['Date'] );
          //$dateConvert = date ( 'Y-m-d 00:00:00', $dateEnCours );
           //$dateAffichage = date_fr ( date ( 'D', $dateEnCours ))." ".date ( 'j', $dateEnCours )." ".date_fr ( date ( 'F', $dateEnCours ))." ".date_fr ( date ( 'Y', $dateEnCours ));
          //$dateAffichage = date_fr ( $dateAffichage );
         // $dateAffichage =DateTime::createFromFormat( 'D j F Y', $dateEnCours );
		//  $dateAffichage = $formatter->format($dateEnCours);
          $dateEnCours = $this->convert_date ( $data['Date'] );
          $dateConvert = date ( 'Y-m-d 00:00:00', $dateEnCours );
          //$dateAffichage = date ( 'd-m-Y', $dateEnCours );
          $dateAffichage = date_fr ( date ( 'D', $dateEnCours ))." ".date ( 'j', $dateEnCours )." ".date_fr ( date ( 'F', $dateEnCours ))." ".date_fr ( date ( 'Y', $dateEnCours ));
          $qttPluie = $data['Precipitations'];
          $tmpMoy = number_format( $data['TemperatueMoyenne'],1);
          $tmpMin = number_format( $data['TemperatureMin'],1);
          $tmpMax = number_format( $data['TemperatureMax'],1);
        //  $tmpMaxMin = $tmpMax.','.$tmpMin;
          $this->recordDay ( $dailyRainCmd, $dateConvert, $qttPluie );
          $this->recordDay ( $tempMoyDayCmd, $dateConvert, $tmpMoy );
          $this->recordDay ( $tempMinDayCmd, $dateConvert, $tmpMin );
          $this->recordDay ( $tempMaxDayCmd, $dateConvert, $tmpMax );
          $this->recordDay ( $dateCmd, $dateConvert, $dateAffichage );
        //  $this->recordDay ( $tempMaxMinDayCmd, $dateConvert, $tmpMaxMin );

          if ( $tempMaxMois < $tmpMax ) { $tempMaxMois = $tmpMax; }

          if ( $tempMinMois > $tmpMin ) { $tempMinMois = $tmpMin;}

        }

        //Historique des températures mensuelles
        $HistoriquesOmbroThermiques = $decoded_json['HistoriquesOmbroThermiques'];
        log::add(__CLASS__, 'debug','Fonction getpluiehier -> HistoriquesOmbroThermiques : ' . print_r($HistoriquesOmbroThermiques,true));

        $HistoriquesOmbroThermique = $HistoriquesOmbroThermiques[$counter - 1];

        $consoMonthCmd = $this->getCmd ( null, $counter.'_'.'monthrain' );
        $tempMoyMonthCmd = $this->getCmd ( null,$counter.'_'. 'tempmoym' );

        $anneeEncours = $HistoriquesOmbroThermique['Annee'];
        log::add ( __CLASS__, 'debug', $this->getHumanName () . 'Fonction getpluiehier -> HistoriquesOmbroThermiques->Année encours ' . $anneeEncours );
        log::add(__CLASS__, 'debug','Fonction getpluiehier -> HistoriquesOmbroThermiques : ' . print_r($HistoriquesOmbroThermique,true));

        foreach ( $HistoriquesOmbroThermique['Data'] as $data ) {

          $moisEnCours = $data['Mois'];
          if ( strlen ( $moisEnCours ) == 2 ) {
            $themonth = $moisEnCours;
          } else {
            $themonth = "0" . $moisEnCours;
          }
          $theDate = $anneeEncours . '-' . $themonth . '-01';
          log::add ( __CLASS__, 'debug', $this->getHumanName () . 'Fonction getpluiehier -> HistoriquesOmbroThermiques-> thedate: ' . $theDate );
          $dt = DateTime::createFromFormat ( 'Y-m-d', $theDate );
          $dateToRecord = $dt->format ( 'Y-m-t 00:00:00' );
          log::add ( __CLASS__, 'debug', $this->getHumanName () . 'Fonction getpluiehier -> HistoriquesOmbroThermiques-> dateToRecord ' . $dateToRecord );

          $qttPluie = number_format($data['PluviometrieTotale'],1);
          log::add ( __CLASS__, 'debug', $this->getHumanName () . 'Fonction getpluiehier -> HistoriquesOmbroThermiques-> PluviometrieTotale ' . $qttPluie );

          $tmpMoy= number_format($data['TemperatureMoyenne'],1);
          log::add ( __CLASS__, 'debug', $this->getHumanName () . 'Fonction getpluiehier -> HistoriquesOmbroThermiques-> TemperatureMoyenne ' . $tmpMoy );

          $this->recordMonth ( $consoMonthCmd, $dateToRecord, $qttPluie );
          $this->recordMonth ( $tempMoyMonthCmd, $dateToRecord, $tmpMoy );

        }

        $tempMinMonthCmd = $this->getCmd ( null, $counter.'_'. 'minm' );
        $this->recordMonth ( $tempMinMonthCmd, $dateToRecord, $tempMinMois );

        $tempMaxMonthCmd = $this->getCmd ( null, $counter.'_'.'maxm' );
        $this->recordMonth ( $tempMaxMonthCmd, $dateToRecord, $tempMaxMois );

       /* $tempMaxMinMois = $tempMaxMois.','.$tempMinMois;
        $tempMaxMinMonthCmd = $this->getCmd ( null, $counter.'_'. 'MaxMinM' );
        $this->recordMonth ( $tempMaxMinMonthCmd, $dateToRecord, $tempMaxMinMois );*/

        $counter++;
      }

    }
  }

  public function recordMonth ( $cmd, $theDate, $theValue ) {
    if ( ! is_null ( $theValue ) ) {
      $cmdId = $cmd->getId ();
      $cmdHistory = history::byCmdIdDatetime ( $cmdId, $theDate );
      if ( is_object ( $cmdHistory ) && $cmdHistory->getValue () == $theValue ) {
        log::add ( __CLASS__, 'debug', $this->getHumanName () . ' Mesure (mois ' . $cmd->getUnite () . ') déjà en historique - Aucune action : ' . ' Date = ' . $theDate . ' => Mesure = ' . $theValue );
      } else {
        log::add ( __CLASS__, 'info', $this->getHumanName () . ' Enregistrement mesure (mois ' . $cmd->getUnite () . ') : ' . ' Date = ' . $theDate . ' => Mesure = ' . $theValue );
        $cmd->event ( $theValue, $theDate );
      }
    } else {
      log::add ( __CLASS__, 'warning', $this->getHumanName () . ' Mesure est null pour mois (' . $cmd->getUnite () . '), Date = ' . $theDate );
    }
  }

  public function recordDay ( $cmd, $theDate, $theValue ) {
    if ( ! is_null ( $theValue ) ) {
      $cmdId = $cmd->getId ();
      $cmdHistory = history::byCmdIdDatetime ( $cmdId, $theDate );
      if ( is_object ( $cmdHistory ) && $cmdHistory->getValue () == $theValue ) {
        log::add ( __CLASS__, 'debug', $this->getHumanName () . ' Mesure (jour ' . $cmd->getUnite () . ') déjà en historique - Aucune action : ' . ' Date = ' . $theDate . ' => Mesure = ' . $theValue );
      } else {
        log::add ( __CLASS__, 'info', $this->getHumanName () . ' Enregistrement mesure (jour ' . $cmd->getUnite () . ') : ' . ' Date = ' . $theDate . ' => Mesure = ' . $theValue );
        $cmd->event ( $theValue, $theDate );
      }
    } else {
      log::add ( __CLASS__, 'warning', $this->getHumanName () . ' Mesure est null pour jour (' . $cmd->getUnite () . '), Date = ' . $theDate );
    }
  }

  public function convert_date ( $str ) {
    preg_match ( "#/Date\((\d{10})\d{3}(.*?)\)/#", $str, $match );
    return $match[1];
  }

  public static function callURL($_url) {
    $request_http = new com_http($_url);
    $request_http->setNoSslCheck(true);
    $request_http->setNoReportError(true);
    $return = $request_http->exec(15,2);
    if ($return === false) {
      log::add(__CLASS__, 'debug', 'Fonction callURL -> Unable to fetch ' . $_url);
      return;
    } else {
      log::add(__CLASS__, 'debug', 'Fonction callURL -> Get ' . $_url);
      log::add(__CLASS__, 'debug', 'Fonction callURL -> Result ' . $return);
    }
    return json_decode($return, true);
  }

  // Fonction exécutée automatiquement après la suppression de l'équipement
  public function postRemove () {

  }

  /*
         * Permet de crypter/décrypter automatiquement des champs de configuration des équipements
         * Exemple avec le champ "Mot de passe" (password)
          public function decrypt() {
          
          $this->setConfiguration('password', utils::decrypt($this->getConfiguration('password')));
          }
          public function encrypt() {
          $this->setConfiguration('password', utils::encrypt($this->getConfiguration('password')));
          }
         */

  /*
         * Permet de modifier l'affichage du widget (également utilisable par les commandes)
          public function toHtml($_version = 'dashboard') {}
         */

  /*
         * Permet de déclencher une action avant modification d'une variable de configuration du plugin
         * Exemple avec la variable "param3"
          public static function preConfig_param3( $value ) {
          // do some checks or modify on $value
          return $value;
          }
         */

  /*
         * Permet de déclencher une action après modification d'une variable de configuration du plugin
         * Exemple avec la variable "param3"
          public static function postConfig_param3($value) {
          // no return value
          }
         */

  /*     * **********************Getteur Setteur*************************** */
}

class historiqueMeteoCmd extends cmd {
  /*     * *************************Attributs****************************** */


  //public static $_widgetPossibility = array();

  /* public static function pluginGenericTypes()
      {
        $generics = array(
          'PLUIEHIER_TEPERATURE_MAX' => array( //capitalise without space
            'name' => __('PluieHier Max',__FILE__),
            'familyid' => 'PluieHier', //No space here
            'family' => __('Plugin PluieHier',__FILE__), //Start with 'Plugin ' ...
            'type' => 'Info',
            'subtype' => array('numeric')
          ),
          'PLUIEHIER_TEPERATURE_MIN' => array( //capitalise without space
            'name' => __('MonPlugin min',__FILE__),
            'familyid' => 'PluieHier', //No space here
            'family' => __('Plugin PluieHier',__FILE__), //Start with 'Plugin ' ...
            'type' => 'Info',
            'subtype' => array('numeric')
          )
        );

        return $generics;
      }
      */
  /*     * ***********************Methode static*************************** */


  /*     * *********************Methode d'instance************************* */

  /*
         * Permet d'empêcher la suppression des commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
          public function dontRemoveCmd() {
          return true;
          }
         */

  // Exécution d'une commande
  public function execute ( $_options = array () ) {
    $eqlogic = $this->getEqLogic (); //récupère l'éqlogic de la commande $this
    switch ( $this->getLogicalId () ) { //vérifie le logicalid de la commande
      case 'refresh': // LogicalId de la commande rafraîchir que l’on a créé dans la méthode Postsave de la classe vdm .
        $eqlogic->getpluiehier (); //On lance la fonction randomVdm() pour récupérer une vdm et on la stocke dans la variable $info
        // $eqlogic->checkAndUpdateCmd ( 'story', $info ); //on met à jour la commande avec le LogicalId "story"  de l'eqlogic
        break;
    }
  }

  /*     * **********************Getteur Setteur*************************** */
}
