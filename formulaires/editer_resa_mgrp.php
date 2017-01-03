<?php
/**
 * Gestion du formulaire d'édition de resa_mgrp
 *
 * @plugin     Groupement de Reservations
 * @copyright  2016
 * @author     Jean-Gabriel Remond
 * @licence    GNU/GPL
 * @package    SPIP/grouperesa/formulaires/ - Saisies
 * --
 * @todo : 
 * Fait:
 * JR-16/12/2016-Creation du fihier.
 * JR-26/11/2016-Cree par La Fabrique.
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/actions');
include_spip('inc/editer');


/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_resa_mgrp
 *     Identifiant du resa_mgrp. 'new' pour un nouveau resa_mgrp.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le resa_mgrp créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un resa_mgrp source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du resa_mgrp, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_resa_mgrp_identifier_dist($id_resa_mgrp='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_resa_mgrp), $associer_objet));
}

/**
 * Chargement du formulaire d'édition de resa_mgrp
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_resa_mgrp
 *     Identifiant du resa_mgrp. 'new' pour un nouveau resa_mgrp.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le resa_mgrp créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un resa_mgrp source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du resa_mgrp, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_resa_mgrp_charger_dist($id_resa_mgrp='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('resa_mgrp', $id_resa_mgrp, '', $lier_trad, $retour, $config_fonc, $row, $hidden);
	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de resa_mgrp
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_resa_mgrp
 *     Identifiant du resa_mgrp. 'new' pour un nouveau resa_mgrp.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le resa_mgrp créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un resa_mgrp source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du resa_mgrp, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_resa_mgrp_verifier_dist($id_resa_mgrp='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){

	/*$debug1= "DEBUG grouperesa JR : formulaires/editer_resa_mgrp - formulaires_editer_resa_mgrp_verifier_dist - Pt03 -";
	spip_log("$debug1.", true);
	spip_log("id_resa_mgrp= $id_resa_mgrp.", true);
	$place_mgrp= _request('place_mgrp');
	spip_log("nbplace_mgrp= $place_mgrp.", true);
	$clonable= _request('clonable');
	spip_log("clonable= $clonable.", true);
	spip_log("hidden=$hidden.", true);
	spip_log("FIN $debug1.", true);
	*/
	
	$erreurs = array();

	$erreurs = formulaires_editer_objet_verifier('resa_mgrp', $id_resa_mgrp, array('titre', 'place_mgrp'));

	// * JR-30/11/2016-Ajout regles a verifier.
	$id_resa_bgrp=_request('id_resa_bgrp');
	
	$resaselect = array(
			'bgrp.place_bgrp as place_bgrp');
	$resafrom = array(
			'spip_resa_bgrps bgrp');
	$resawhere = array(
			'bgrp.id_resa_bgrp = ' . intval($id_resa_bgrp));
	$resagroupby = array();
	$resaorderby = array();
	$resalimit ='1';
	
	$place_bgrp_t = sql_allfetsel( $resaselect, $resafrom, $resawhere, $resagroupby, $resaorderby, $resalimit);

	$place_bgrp=$place_bgrp_t[0]['place_bgrp'];
	$place_mgrp=_request('place_mgrp');

	/*$debug1= "DEBUG grouperesa JR : formulaires/editer_resa_mgrp - formulaires_editer_resa_mgrp_verifier_dist - Pt07 -";
	spip_log("$debug1.", true);
	spip_log("id_resa_mgrp= $id_resa_mgrp.", true);
	foreach($place_bgrp_t[0] as $key => $value) {
		spip_log("place_bgrp_t(key)=$key.", true);
		spip_log("place_bgrp_t(value)=$value.", true);
	}
	spip_log("nbplace_bgrp= $place_bgrp.", true);
	spip_log("nbplace_mgrp= $place_mgrp.", true);
	$clonable_req= _request('clonable');
	spip_log("clonable(req)= $clonable_req.", true);
	spip_log("hidden=$hidden.", true);
	spip_log("FIN $debug1.", true);
	*/
	
	if($place_mgrp == 0 OR ($place_bgrp > 0 AND $place_mgrp > $place_bgrp)){
		$erreurs['place_mgrp'] = _T('resa_mgrp:err_place_mgrp',array('nbplace' => $place_bgrp));
	}

	if(count($erreurs))
		$erreurs['message_erreur'] = 'Votre saisie contient des erreurs !';
		
	return $erreurs;
}

/**
 * Traitement du formulaire d'édition de resa_mgrp
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_resa_mgrp
 *     Identifiant du resa_mgrp. 'new' pour un nouveau resa_mgrp.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le resa_mgrp créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un resa_mgrp source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du resa_mgrp, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_resa_mgrp_traiter_dist($id_resa_mgrp='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){

	
	 /*$debug1= "DEBUG grouperesa JR : formulaires/editer_resa_mgrp - formulaires_editer_resa_mgrp_traiter_dist - Pt40 -";
	 spip_log("$debug1.", true);
	 spip_log("id_resa_mgrp= $id_resa_mgrp.", true);
	 $place_mgrp= _request('place_mgrp');
	 spip_log("place_mgrp= $place_mgrp.", true);
	 $clonable= _request('clonable');
	 spip_log("clonable= $clonable.", true);
	 if ($associer_objet){
	 	list($objet, $id_objet) = explode('|', $associer_objet);
		spip_log("objet=$objet.", true);
	 	spip_log("id_objet=$id_objet.", true);
	 }
	 spip_log("hidden=$hidden.", true);
	 spip_log("FIN $debug1.", true);
	 */	
	
	$retours = formulaires_editer_objet_traiter('resa_mgrp', $id_resa_mgrp, '', $lier_trad, $retour, $config_fonc, $row, $hidden);

	// Un lien a prendre en compte ?
	if ($associer_objet AND $id_resa_mgrp = $retours['id_resa_mgrp']) {
		list($objet, $id_objet) = explode('|', $associer_objet);

		if ($objet AND $id_objet AND autoriser('modifier', $objet, $id_objet)) {
			include_spip('action/editer_liens');
				
			objet_associer(array('resa_mgrp' => $id_resa_mgrp), array($objet => $id_objet));
				
			if (isset($retours['redirect'])) {
				$retours['redirect'] = parametre_url($retours['redirect'], "id_lien_ajoute", $id_resa_mgrp, '&');
			}
		}
	}

	return $retours;
}