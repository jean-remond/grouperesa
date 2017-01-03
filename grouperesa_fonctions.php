<?php
/**
 * Fonctions utiles au plugin Groupement de Reservations
 *
 * @plugin     Groupement de Reservations
 * @copyright  2016
 * @author     Jean-Gabriel Remond
 * @licence    GNU/GPL
 * @package    SPIP\grouperesa\ - Fonctions
 * ----
 * @todo :
 * Fait :
 * JR-20/12/2016-Revue requete dans F(grouperesa_evenement_regroupe) pour cas ss rd.
 * JR-18/12/2016-Ajout F(grouperesa_evenement_regroupe).
 * JR-16/12/2016-Creation du fihier.
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Un fichier de fonctions permet de définir des éléments
 * systématiquement chargés lors du calcul des squelettes.
 *
 * Il peut par exemple définir des filtres, critères, balises, …
 *
 */

/**
 * filtre de securisation des squelettes
 * utilise avec [(#REM|grouperesa_securise_squelette)]
 * evite divulgation d'info si plugin desactive
 * par erreur fatale
 *
 * @param unknown_type $letexte
 * @return unknown
 */
function grouperesa_securise_squelette($letexte){
	return "";
}


/**
 * logger les contenus suivant importance ou debug
 * GROUPERESA_DEBUG definie dans grouperesa_options.
 *
 * @param string $contenu
 * @param boolean $important
 * @return na
 */
function grouperesa_log($contenu, $important=false) {
	if ($important
		OR (defined('GROUPERESA_DEBUG') and GROUPERESA_DEBUG)) {
		spip_log($contenu,'grouperesa');
	}else{
		spip_log($contenu);
	}
}

/**
 * Valider si l'evenement est regroupable
 *
 * @param string $id_evenement
 * @param string $id_reservations_detail
 * @return string $mot => Mot-cle du groupe 'type_reservation' associe a evenement
 */
function grouperesa_evenement_regroupe($id_evenement, $id_reservations_detail, $contexte) {
	$mot=array();
	
	if (test_plugin_actif('grouperesa')){

		// Rechercher pour le groupe-mot 'type_reservation' le mot associe a evenement
		$resaselect = array(
				'm.titre as titre');
		$resafrom = array(
				'spip_mots_liens ml',
				'spip_mots m',
		);
		$resawhere = array(
				'ml.objet = "evenement"',
				'ml.id_mot = m.id_mot',
				'm.type = "type_reservation"',
		);
		$resalimit = '1';
		if (intval($id_reservations_detail)){
			$resafrom = array_merge($resafrom,
				array('spip_reservations_details rd'));
			$resawhere = array_merge($resawhere,
				array(
					'ml.id_objet = rd.id_evenement',
					'rd.id_reservations_detail = ' . intval($id_reservations_detail)
				)
			);
		}else{
			$resawhere = array_merge($resawhere,
					array('ml.id_objet = ' . intval($id_evenement)));
		}

		/*$debug1= "DEBUG grouperesa JR : grouperesa_fonctions.php - grouperesa_evenement_regroupe - Pt068 -";
		spip_log("$debug1.", true);
		spip_log("id_evenement=$id_evenement.", true);
		spip_log("id_reservations_detail=$id_reservations_detail.", true);
		*/
		/*spip_log("resaselect=.", true);
		foreach($resaselect as $key => $value) {
			spip_log("resaselect(key)=$key.", true);
			spip_log("resaselect(value)=$value.", true);
		}
		spip_log("resafrom=.", true);
		foreach($resafrom as $key => $value) {
			spip_log("resafrom(key)=$key.", true);
			spip_log("resafrom(value)=$value.", true);
		}*/
		/*spip_log("resawhere=.", true);
		foreach($resawhere as $key => $value) {
			spip_log("resawhere(key)=$key.", true);
			spip_log("resawhere(value)=$value.", true);
		}*/
		/*$req = sql_get_select($resaselect, $resafrom, $resawhere, null, null, $resalimit);
		if (is_array($req)){
			spip_log("req=.", true);
			foreach($req as $key => $value) {
				spip_log("req(key)=$key.", true);
				spip_log("req(value)=$value.", true);
			}
		}else{
				spip_log("req=$req.", true);
		}*/
		/*spip_log("FIN $debug1.", true);
		*/
		
		$mot = sql_fetsel($resaselect, $resafrom, $resawhere, null, null, $resalimit);

		/*$debug1= "DEBUG grouperesa JR : grouperesa_fonctions.php - grouperesa_evenement_regroupe - Pt069 -";
		spip_log("$debug1.", true);
		if (is_array($mot)){
			spip_log("mot=.", true);
			foreach($mot as $key => $value) {
				spip_log("mot(key)=$key.", true);
				spip_log("mot(value)=$value.", true);
			}
		}else{
			spip_log("mot=$mot.", true);
				
		}
		spip_log("FIN $debug1.", true);
		*/
		
	}
	return $mot;
}