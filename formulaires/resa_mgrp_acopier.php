<?php
/**
 * Gestion du formulaire de choix d'une resa_mgrp
 *
 * @plugin     Groupement de Reservations
 * @copyright  2016
 * @author     Jean-Gabriel Remond
 * @licence    GNU/GPL
 * @package    SPIP/grouperesa/formulaires/ - Saisies
 * --
 * @todo : JR-20161202-Ajouter creation liens avec evenement dans traiter.
 * Fait:
 * JR-17/12/2016-Creation du fihier cf codep21resa.
 * JR-01/12/2016-Creation.
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
function formulaires_editer_resa_mgrp_acopier_identifier_dist($id_resa_grp='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return;
}

/**
 * Chargement du formulaire de copie de resa_mgrp
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @return array
 *     Environnement du formulaire
 */
function formulaires_resa_mgrp_acopier_charger_dist ($id_resa_grp='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = array();
	return $valeurs;
}


/**
 * Vérifications du formulaire de copie de resa_mgrp
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @return array
 *     Tableau des erreurs
 */
function formulaires_resa_mgrp_acopier_verifier_dist ($id_resa_grp='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$erreurs = array();
	// champs obligatoires
	foreach(array ('id_resa_mgrp') as $obligatoire) {
		if (!_request($obligatoire)) $erreurs[$obligatoire] = 'Ce champ est obligatoire';
	}
	if (count($erreurs)) {
		$erreurs['message_erreur'] = 'Votre saisie contient des erreurs !';
	}
	return $erreurs;
}


/**
 * Traitement du formulaire de copie de resa_mgrp
 *
 * Traiter l'id posté et retrouver les champs de la basee choisie.
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @return array
 *     Retours des traitements
 */
function formulaires_resa_mgrp_acopier_traiter_dist ($id_resa_grp='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$id_resa_mgrp	 = _request('id_resa_mgrp');

	// Lecture la Table spip_resa_mgrps pour retrouver les donnees du modele.
	$resaselect = array(
		'mgrp.id_resa_mgrp as id_resa_mgrp',
		'mgrp.titre as titre',
		'mgrp.descriptif as descriptif',
		'mgrp.place_mgrp as place_mgrp',
		'liens.id_objet',
	);
	$resafrom = array( 
		'spip_resa_mgrps mgrp',
		'spip_resa_mgrps_liens liens',
	);
	$resawhere = array(
		'mgrp.id_resa_mgrp = ' . intval($id_resa_mgrp),
		'mgrp.id_resa_mgrp = liens.id_resa_mgrp',
		'liens.objet = "evenement"',
	);
	$resagroupby = array();
	$resaorderby = array();
	$resalimit ='';

	$mgrp_donnees = sql_allfetsel( $resaselect, $resafrom, $resawhere, $resagroupby, $resaorderby, $resalimit);

	if ($mgrp_donnees) {
		foreach($mgrp_donnees as $mgrp1_donnees) {
			set_request('titre',$mgrp1_donnees['titre']);
			set_request('descriptif',$mgrp1_donnees['descriptif']);
			set_request('place_grp',$mgrp1_donnees['place_mgrp']);
			set_request('id_evenement',$mgrp1_donnees['id_objet']);
			$id_evenement=$mgrp1_donnees['id_objet'];
			}
	}

	/*$debug1= "DEBUG grouperesa JR : formulaires/resa_mgrp_acopier.php - formulaires_resa_mgrp_acopier_traiter_dist - Pt46 -";
	spip_log("$debug1.", true);
	spip_log("id_resa_grp(var)=$id_resa_grp.", true);
	spip_log("id_resa_mgrp(var)=$id_resa_mgrp.", true);
	$titre_req= _request('titre');
	spip_log("titre(req)= $titre_req.", true);
	$descriptif_req= _request('descriptif');
	spip_log("descriptif(req)= $descriptif_req.", true);
	$place_grp_req= _request('place_grp');
	spip_log("place_grp(req)= $place_grp_req.", true);
	$id_evenement_req= _request('id_evenement');
	spip_log("id_evenement(req)= $id_evenement_req.", true);
	spip_log("id_evenement(var)= $id_evenement.", true);
	spip_log("retour(var)= $retour.", true);
	spip_log("row(var)=$row.", true);
	spip_log("row(fe)= ", true);
	if ($row){
		foreach ($row as $key => $value){
			spip_log("row(key)=$key.", true);
			spip_log("row(value)=$value.", true);
		}
	}
	spip_log("hidden=$hidden.", true);
	spip_log("FIN $debug1.", true);
	*/
	
	// Creation du groupement F(modele)
	$retours = formulaires_editer_objet_traiter('resa_grp', $id_resa_grp, $id_evenement, '', $retour, '', $row, $hidden);
	
	/*$debug1= "DEBUG grouperesa JR : formulaires/resa_mgrp_acopier.php - formulaires_resa_mgrp_acopier_traiter_dist - Pt48 -";
	spip_log("$debug1.", true);
	foreach($retours as $key => $value) {
		spip_log("retours(key)=$key.", true);
		spip_log("retours(value)=$value.", true);
		}
	$id_resa_grp_ret= $retours[id_resa_grp];
	spip_log("id_resa_grp(ret)= $id_resa_grp_ret.", true);
	spip_log("retour(var)= $retour.", true);
	spip_log("row(var)=$row.", true);
	spip_log("row(fe)= ", true);
	if ($row){
		foreach ($row as $key => $value){
			spip_log("row(key)=$key.", true);
			spip_log("row(value)=$value.", true);
		}
	}
	spip_log("hidden=$hidden.", true);
	spip_log("FIN $debug1.", true);
	*/
	
	// Le retour doit afficher le groupement en modification
	$id_resa_grp=$retours[id_resa_grp];
	$retour['redirect'] = generer_url_ecrire("resa_grp_edit", "id_resa_grp=$id_resa_grp&retour=".urlencode(self()));
	
	
	/*$debug1= "DEBUG grouperesa JR : formulaires/resa_mgrp_acopier.php - formulaires_resa_mgrp_acopier_traiter_dist - Pt49 -";
	spip_log("$debug1.", true);
	spip_log("retour(var)= $retour.", true);
	spip_log("FIN $debug1.", true);
	*/

	return $retour;
}
