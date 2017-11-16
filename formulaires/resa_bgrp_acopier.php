<?php
/**
 * Liste des Bases de groupements de réservation pour saisie/sélection.
 *
 * @plugin     Groupement de Reservations
 * @copyright  2016
 * @author     Jean-Gabriel Remond
 * @licence    GNU/GPL
 * @package    SPIP/grouperesa/formulaires/ - Saisies
 * --
 * @todo : 
 * Fait:
 * JR-27/11/2016-Creation.
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/actions');
include_spip('inc/editer');


/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 */
function formulaires_editer_resa_bgrp_acopier_identifier_dist($id_resa_mgrp='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return;
}

/**
 * Chargement du formulaire de copie de resa_bgrp
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @return array
 *     Environnement du formulaire
 */
function formulaires_resa_bgrp_acopier_charger_dist ($id_resa_mgrp='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = array();
	return $valeurs;
}


/**
 * Vérifications du formulaire de copie de resa_bgrp
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @return array
 *     Tableau des erreurs
 */
function formulaires_resa_bgrp_acopier_verifier_dist ($id_resa_mgrp='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$erreurs = array();
	// champs obligatoires
	foreach(array ('id_resa_bgrp') as $obligatoire) {
		if (!_request($obligatoire)) $erreurs[$obligatoire] = 'Ce champ est obligatoire';
	}
	if (count($erreurs)) {
		$erreurs['message_erreur'] = 'Votre saisie contient des erreurs !';
	}
	return $erreurs;
}


/**
 * Traitement du formulaire de copie de resa_bgrp
 *
 * Traiter l'id posté et retrouver les champs de la basee choisie.
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @return array
 *     Retours des traitements
 */
function formulaires_resa_bgrp_acopier_traiter_dist ($id_resa_mgrp='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$id_resa_bgrp	 = _request('id_resa_bgrp');

	// Lecture la Table spip_resa_bgrps pour retrover les donnees de base.
	$resaselect = array(
		'bgrp.titre as titre',
		'bgrp.descriptif as descriptif',
		'bgrp.place_bgrp as place_bgrp');
	$resafrom = array( 
		'spip_resa_bgrps bgrp');
	$resawhere = array(
		'bgrp.id_resa_bgrp = ' . intval($id_resa_bgrp));
	$resagroupby = array();
	$resaorderby = array();
	$resalimit ='';

	$bgrp_donnees = sql_allfetsel( $resaselect, $resafrom, $resawhere, $resagroupby, $resaorderby, $resalimit);

	if ($bgrp_donnees) {
		foreach($bgrp_donnees as $bgrp1_donnees) {
			set_request('titre',$bgrp1_donnees['titre']);
			set_request('descriptif',$bgrp1_donnees['descriptif']);
			set_request('place_mgrp',$bgrp1_donnees['place_bgrp']);
		}
	}

	// Creation du modele F(base)
	$retours = formulaires_editer_objet_traiter('resa_mgrp', $id_resa_mgrp, '', '', $retour, '', $row, $hidden);

	$debug1= "DEBUG grouperesa JR : formulaires/resa_bgrp_acopier.php - formulaires_resa_bgrp_acopier_traiter_dist - Pt48 -";
	spip_log("$debug1.", true);
	foreach($retours as $key => $value) {
		spip_log("retours(key)=$key.", true);
		spip_log("retours(value)=$value.", true);
		}
	$id_resa_mgrp_ret= $retours[id_resa_mgrp];
	spip_log("id_resa_mgrp(ret)= $id_resa_mgrp_ret.", true);
	spip_log("retour(var)= $retour.", true);
	spip_log("row(var)=$row.", true);
	foreach($row as $row_det) {
		spip_log("row(det)=$row_det.", true);
	}
	spip_log("hidden=$hidden.", true);
	spip_log("FIN $debug1.", true);
	
	
	// Le retour doit afficher le modele en modification
	$id_resa_mgrp=$retours[id_resa_mgrp];
	$retour['redirect'] = generer_url_ecrire("resa_mgrp_edit", "id_resa_mgrp=$id_resa_mgrp&retour=".urlencode(self()));
	
	
	/*$debug1= "DEBUG grouperesa JR : formulaires/resa_bgrp_acopier.php - formulaires_resa_bgrp_acopier_traiter_dist - Pt49 -";
	spip_log("$debug1.", true);
	spip_log("retour(var)= $retour.", true);
	spip_log("FIN $debug1.", true);
	*/
	
	
	return $retour;
}
