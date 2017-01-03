<?php
/**
 * Gestion du formulaire d'édition de resa_grp
 *
 * @plugin     Groupement de Reservations
 * @copyright  2016
 * @author     Jean-Gabriel Remond
 * @licence    GNU/GPL
 * @package    SPIP/grouperesa/formulaires/ - Saisies
 * ----
 * @todo : JR-13/11/2016-Charger les choix de groupement en F(nbrplace_grp, dispo pour chaque).
 * @todo : JR-13/11/2016-En creation , utiliser les resa_mgrp affectes a l'evenement. 
 * Fait :
 * JR-17/12/2016-Creation du fihier cd codep21resa.
 * JR-12/12/2016-Reprise de La Fabrique pour grp-resas_liens
 * JR-06/12/2016-Le chgt des variables permet le retour vers la page appelante.
 * JR-03/12/2016-Constituer le lien en ajoutant id_resa_grp dans reservation_detail.
 * JR-20/11/2016-En creation , utiliser les resa_mgrp connus. 
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
 * @param int|string $id_resa_grp
 *     Identifiant du resa_grp. 'new' pour un nouveau resa_grp.
 * @param int $id_evenement
 *     Identifiant de l'objet parent (si connu)
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le resa_grp créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un resa_grp source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du resa_grp, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_resa_grp_identifier_dist($id_resa_grp='new', $id_evenement=0, $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = array(
			'id_resa_grp' => intval($id_resa_grp),
			'id_evenement' => $id_evenement,
			'retour' => $retour,
			'associer_objet' => $associer_objet,
	);
	//return serialize(array(intval($id_resa_grp), $associer_objet));
}

/**
 * Chargement du formulaire d'édition de resa_grp
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_resa_grp
 *     Identifiant du resa_grp. 'new' pour un nouveau resa_grp.
 * @param int $id_evenement
 *     Identifiant de l'objet parent (si connu)
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le resa_grp créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un resa_grp source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du resa_grp, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_resa_grp_charger_dist($id_resa_grp='new', $id_evenement=0, $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('resa_grp', $id_resa_grp, $id_evenement, $lier_trad, $retour, $config_fonc, $row, $hidden); 
	if (!$valeurs['id_evenement']) {
		$valeurs['id_evenement'] = $id_evenement;
	}
	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de resa_grp
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_resa_grp
 *     Identifiant du resa_grp. 'new' pour un nouveau resa_grp.
 * @param int $id_evenement
 *     Identifiant de l'objet parent (si connu)
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le resa_grp créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un resa_grp source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du resa_grp, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_resa_grp_verifier_dist($id_resa_grp='new', $id_evenement=0, $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$erreurs = array();

	$erreurs = formulaires_editer_objet_verifier('resa_grp', $id_resa_grp, array('titre', 'id_resa_mgrp', 'place_grp', 'id_evenement'));

	// * JR-03/12/2016-Ajout regles a verifier : Le nbre de place ne peut depasser celui du modele ni etre a zero.
	$id_resa_mgrp=_request('id_resa_mgrp');
	
	$resaselect = array(
			'mgrp.place_mgrp as place_mgrp');
	$resafrom = array(
			'spip_resa_mgrps mgrp');
	$resawhere = array(
			'mgrp.id_resa_mgrp = ' . intval($id_resa_mgrp));
	$resagroupby = array();
	$resaorderby = array();
	$resalimit ='1';
	
	$place_mgrp_t = sql_allfetsel( $resaselect, $resafrom, $resawhere, $resagroupby, $resaorderby, $resalimit);
	
	$place_mgrp=$place_mgrp_t[0]['place_mgrp'];
	$place_grp=_request('place_grp');

	/*$debug1= "DEBUG grouperesa JR : formulaires/editer_resa_mgrp - formulaires_editer_resa_mgrp_verifier_dist - Pt07 -";
	 spip_log("$debug1.", true);
	 spip_log("id_resa_grp= $id_resa_grp.", true);
	 foreach($place_mgrp_t[0] as $key => $value) {
	 spip_log("place_mgrp_t(key)=$key.", true);
	 spip_log("place_mgrp_t(value)=$value.", true);
	 }
	 spip_log("nbplace_mgrp= $place_mgrp.", true);
	 spip_log("nbplace_grp= $place_grp.", true);
	 spip_log("hidden=$hidden.", true);
	 spip_log("FIN $debug1.", true);
	 */

	if($place_grp == 0 OR ($place_mgrp > 0 AND $place_grp > $place_mgrp)){
		$erreurs['place_grp'] = _T('resa_grp:err_place_grp',array('nbplace' => $place_mgrp));
	}


	if(count($erreurs))
		$erreurs['message_erreur'] = 'Votre saisie contient des erreurs !';
	
	return $erreurs;
}

/**
 * Traitement du formulaire d'édition de resa_grp
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_resa_grp
 *     Identifiant du resa_grp. 'new' pour un nouveau resa_grp.
 * @param int $id_evenement
 *     Identifiant de l'objet parent (si connu)
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le resa_grp créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un resa_grp source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du resa_grp, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_resa_grp_traiter_dist($id_resa_grp='new', $id_evenement=0, $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$retours = formulaires_editer_objet_traiter('resa_grp', $id_resa_grp, $id_evenement, $lier_trad, $retour, $config_fonc, $row, $hidden);
 
	/*$debug1= "DEBUG grouperesa JR : formulaires/editer_resa_grp - formulaires_editer_resa_grp_traiter_dist - Pt40 -";
	spip_log("$debug1.", true);
	spip_log("id_resa_grp= $id_resa_grp.", true);
	$place_grp= _request('place_grp');
	spip_log("place_grp= $place_grp.", true);
	$id_resa_mgrp= _request('id_resa_mgrp');
	spip_log("id_resa_mgrp= $id_resa_mgrp.", true);
	if ($associer_objet){
		foreach($associer_objet as $key => $value) {
	 		spip_log("associer_objet(key)=$key.", true);
	 		spip_log("associer_objet(value)=$value.", true);
	 	}
	}
	if ($retours){
		foreach($retours as $key => $value) {
			spip_log("retours(key)=$key.", true);
			spip_log("retours(value)=$value.", true);
		}
	}
	spip_log("hidden=$hidden.", true);
	spip_log("FIN $debug1.", true);
	*/
	
	// Un lien a prendre en compte ?
	if ($associer_objet AND $id_resa_grp = $retours['id_resa_grp']) {
		list($objet, $id_objet) = explode('|', $associer_objet);

		if ($objet AND $id_objet AND autoriser('modifier', $objet, $id_objet)) {
			include_spip('action/editer_liens');
			
			objet_associer(array('resa_grp' => $id_resa_grp), array($objet => $id_objet));
			
			if (isset($retours['redirect'])) {
				$retours['redirect'] = parametre_url($retours['redirect'], "id_lien_ajoute", $id_resa_grp, '&');
			}
		}
	}

	/*$debug1= "DEBUG grouperesa JR : formulaires/editer_resa_grp - formulaires_editer_resa_grp_traiter_dist - Pt49 -";
	spip_log("$debug1.", true);
	if ($retours){
		foreach($retours as $key => $value) {
			spip_log("retours(key)=$key.", true);
			spip_log("retours(value)=$value.", true);
		}
	}
	spip_log("FIN $debug1.", true);
	*/
	
	return $retours;
}