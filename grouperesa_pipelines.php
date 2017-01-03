<?php
/**
 * Utilisations des pipelines par Groupement de Reservations
 *
 * @plugin     Groupement de Reservations
 * @copyright  2016
 * @author     Jean-Gabriel Remond
 * @licence    GNU/GPL
 * @package    SPIP\grouperesa\ - Pipelines
 * ----
 * @todo : JR-27/12/2016-Comment gerer les id_resa_grp face au tableau des evenements en verification de reservation ?
 * Fait :
 * JR-21/12/2016-Implementation F(grouperesa_formulaire_verifier).
 * JR-18/12/2016-Implementation F(grouperesa_recuperer_fond).
 * JR-17/12/2016-Implementation F(grouperesa_affiche_milieu).
 * JR-16/12/2016-Creation du fihier.
 */


if (!defined('_ECRIRE_INC_VERSION')) return;


/*
 * Un fichier de pipelines permet de regrouper
 * les fonctions de branchement de votre plugin
 * sur des pipelines existants.
 */


/** Ref log fonction -> 010
 * 
 * Optimiser la base de données 
 * 
 * Supprime les liens orphelins de l'objet vers quelqu'un et de quelqu'un vers l'objet.
 * Supprime les liens orphelins de l'objet vers quelqu'un et de quelqu'un vers l'objet.
 * Supprime les objets à la poubelle.
 *
 * @pipeline optimiser_base_disparus
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function grouperesa_optimiser_base_disparus($flux){

	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('resa_mgrp'=>'*'),'*');
	$flux['data'] += objet_optimiser_liens(array('resa_grp'=>'*'),'*');
	
	sql_delete("spip_resa_bgrps", "statut='poubelle' AND maj < " . $flux['args']['date']);
	sql_delete("spip_resa_mgrps", "statut='poubelle' AND maj < " . $flux['args']['date']);
	sql_delete("spip_resa_grps", "statut='poubelle' AND maj < " . $flux['args']['date']);
	
	return $flux;
}

/** Ref log fonction -> 020
 * 
 * Ajouter les objets sur les vues des parents directs
 *
 * @pipeline affiche_enfants
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 **/
function grouperesa_affiche_enfants($flux) {
	if ($e = trouver_objet_exec($flux['args']['exec'])
		AND $e['edition'] == false) {
		$id_objet = $flux['args']['id_objet'];

		// Pour les evenements si regroupables
		if ($e['type'] == 'evenement' AND grouperesa_evenement_regroupe($id_objet, null, null)) {
			// Les modeles lies aux evenements
			$flux['data'] .= recuperer_fond('prive/objets/editer/liens', array(
				'table_source' => 'resa_mgrps',
				'objet' => $e['type'],
				'id_objet' => $id_objet
			));
		}

		if ($e['type'] == 'resa_grp') {
			$flux['data'] .= recuperer_fond(
					'prive/objets/liste/evenement_lie_resa_grp',
					array(
							'titre' => _T('resa_grp:titre_evenement'),
							'id_resa_grp' => $id_objet,
							'objet' => 'evenement',
					)
			);
			$flux['data'] .= recuperer_fond(
					'prive/objets/liste/objets_lies_resa_grp',
					array(
							'titre' => _T('resa_grp:titre_reservations_detail'),
							'id_resa_grp' => $id_objet,
							'objet' => 'reservations_detail',
					)
			);
		
		}
		
	}
	return $flux;
}

/** Ref log fonction -> 030
 * 
 * Afficher le nombre d'éléments dans les parents
 *
 * @pipeline boite_infos
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 **/
function grouperesa_boite_infos($flux) {
	if (isset($flux['args']['type']) and isset($flux['args']['id']) and $id = intval($flux['args']['id'])) {
		$texte = "";

		// Pour les evenements regroupables, cptage des modeles et groupes
		if ($flux['args']['type'] == 'evenement' AND grouperesa_evenement_regroupe($id, null, null)){
			if ($nb = sql_countsel('spip_resa_mgrps', array("id_evenement=" . $id))) {
				$texte .= "<div>" . singulier_ou_pluriel($nb, "resa_mgrp:info_1_resa_mgrp", "resa_mgrp:info_nb_resa_mgrps") . "</div>\n";
			}

			if ($nb = sql_countsel('spip_resa_grps', array("id_evenement=" . $id))) {
				$texte .= "<div>" . singulier_ou_pluriel($nb, "resa_grp:info_1_resa_grp", "resa_grp:info_nb_resa_grps") . "</div>\n";
			}
		
		if ($texte and $p = strpos($flux['data'], "<!--nb_elements-->")) {
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		}
		}
	}
	return $flux;
}

/** Ref log fonction -> 040
 * 
 * Compter les enfants d'un objet
 *
 * @pipeline objets_compte_enfants
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 **/
function grouperesa_objet_compte_enfants($flux) {

	// Pour les evenements regroupables
	if ($flux['args']['objet'] == 'evenement' 
		AND $id_evenement = intval($flux['args']['id_objet']) 
		AND grouperesa_evenement_regroupe($id_evenement, null, null)) {
		// juste les publiés ?
		if (array_key_exists('statut', $flux['args']) and ($flux['args']['statut'] == 'publie')) {
			$flux['data']['resa_mgrps'] = sql_countsel('spip_resa_mgrps', "id_evenement= " . intval($id_evenement) . " AND (statut = 'publie')");
			$flux['data']['resa_grps'] = sql_countsel('spip_resa_grps', "id_evenement= " . intval($id_evenement) . " AND (statut = 'publie')");
		} else {
			$flux['data']['resa_mgrps'] = sql_countsel('spip_resa_mgrps', "id_evenement= " . intval($id_evenement) . " AND (statut <> 'poubelle')");
			$flux['data']['resa_grps'] = sql_countsel('spip_resa_grps', "id_evenement= " . intval($id_evenement) . " AND (statut <> 'poubelle')");
		}
	}

	return $flux;
}

/** Ref log fonction -> 050
 * 
 * Ajout de contenu sur certaines pages,
 * notamment des formulaires de liaisons entre objets
 *
 * @pipeline affiche_milieu
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function grouperesa_affiche_milieu($flux) {
	$texte = "";
	$e = trouver_objet_exec($flux['args']['exec']);

	/*$debug1= "DEBUG grouperesa JR : grouperesa_pipelines.php - grouperesa_affiche_milieu - Pt051 -";
	 spip_log("$debug1.", true);
	 $type_log=$e['type'];
	 spip_log("type= $type_log.", true);
	 if (is_array($e)){
		foreach($e as $key => $value) {
		spip_log("e(key)= $key.", true);
		spip_log("e(value)= $value.", true);
		}
	}else{
		spip_log("e= $e.", true);
	}
	spip_log("FIN $debug1.", true);
	*/

	// resa_grps sur les reservations_details
	if (!$e['edition'] AND in_array($e['type'], array('reservations_detail'))) {
		$id_reservations_detail=$flux['args']['id_reservations_detail'];

		/*$debug1= "DEBUG grouperesa JR : grouperesa_pipelines.php - grouperesa_affiche_milieu - Pt052 -";
		spip_log("$debug1.", true);
		spip_log("flux['args']=.", true);
		foreach($flux['args'] as $key => $value) {
			spip_log("flux['args'](key)=$key.", true);
			spip_log("flux['args'](value)=$value.", true);
		}
		spip_log("id_reservations_detail= $id_reservations_detail.", true);
		spip_log("FIN $debug1.", true);
		*/

		// Tester si evenement regroupable
		if ($mot = grouperesa_evenement_regroupe(null, $id_reservations_detail, null)) {
			$flux['args']['type_reservation']=$mot;
			$texte .= recuperer_fond('prive/objets/editer/liens', array(
					'table_source' => 'resa_grps',
					'objet' => $e['type'],
					'id_objet' => $flux['args'][$e['id_table_objet']]
			));
		}
	}

	if ($texte) {
		if ($p=strpos($flux['data'],"<!--affiche_milieu-->"))
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
			else
				$flux['data'] .= $texte;
	}

	return $flux;
}


/** Ref log fonction -> 060
 * 
 * Intervient sur un squelette
 *
 * @pipeline recuperer_fond
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 **/
function grouperesa_recuperer_fond($flux) {
	$fond = $flux['args']['fond'];
	$contexte = $flux['data']['contexte'];

	/*$debug1= "DEBUG grouperesa JR : grouperesa_pipelines.php - grouperesa_recuperer_fond - Pt061 -";
	 spip_log("$debug1.", true);
	 spip_log("form= $form.", true);
	 spip_log("flux=.", true);
	 foreach($flux as $key => $value) {
		spip_log("flux(key)=$key.", true);
		spip_log("flux(value)=$value.", true);
	}
	spip_log("flux['args']=.", true);
	foreach($flux['args'] as $key => $value) {
		spip_log("flux['args'](key)=$key.", true);
		spip_log("flux['args'](value)=$value.", true);
	}
	spip_log("FIN $debug1.", true);
	*/

	if ($fond == 'formulaires/inc-reservation_evenements_champ'
		or $fond == 'formulaires/inc-reservation_evenements_declinaisons_prix'
		or $fond == 'formulaires/editer_reservations-detail') {

		/*$debug1= "DEBUG grouperesa JR : grouperesa_pipelines.php - grouperesa_recuperer_fond - Pt062 -";
		spip_log("$debug1.", true);
		spip_log("fond= $fond.", true);
		if ($flux_args = $flux['args']){
			foreach($flux_args as $key => $value) {
				spip_log("flux['args'](key)=$key.", true);
				spip_log("flux['args'](value)=$value.", true);
			}
		}else{
			spip_log("flux['args'] vide.", true);
		}
		if ($flux_args_contexte = $flux['args']['contexte']){
			foreach($flux_args_contexte as $key => $value) {
				spip_log("flux['args']['contexte'](key)=$key.", true);
				spip_log("flux['args']['contexte'](value)=$value.", true);
			}
		}else{
			spip_log("flux['args']['contexte'] vide.", true);
		}*/
		/*$flux_args_contexte=$flux['args']['contexte']['id_evenement'];
		spip_log("flux['args']['contexte'](id_evenement)=$flux_args_contexte.", true);
		$flux_args_contexte=$flux['args']['contexte']['prix'];
		spip_log("flux['args']['contexte'](prix)=$flux_args_contexte.", true);
		$flux_args_contexte=$flux['args']['contexte']['evenements'];
		spip_log("flux['args']['contexte'](evenements)=$flux_args_contexte.", true);
		$flux_args_contexte=$flux['args']['contexte']['lang'];
		spip_log("flux['args']['contexte'](lang)=$flux_args_contexte.", true);
		if ($flux_args_options = $flux['args']['options']){
			$flux_args_options = $flux['args']['options'];
			foreach($flux_args_options as $key => $value) {
				spip_log("flux['args']['options'](key)=$key.", true);
				spip_log("flux['args']['options'](value)=$value.", true);
			}
		}else{
			spip_log("flux['args']['options'] vide.", true);
		}
		if ($flux['data']){
			spip_log("flux['data']=.", true);
			foreach($flux['data'] as $key => $value) {
				spip_log("flux['data'](key)=$key.", true);
				spip_log("flux['data'](value)=$value.", true);
			}
		}else{
			spip_log("flux['data'] vide.", true);
		}
		*/
		/*spip_log("FIN $debug1.", true);
		*/
		
		$id_evenement=$flux['args']['contexte']['id_evenement'];
		$id_reservations_detail=$flux['args']['contexte']['id_reservations_detail'];
				
		// Tester si evenement regroupable
		if ($mot = grouperesa_evenement_regroupe($id_evenement, $id_reservations_detail, $contexte)){
			
		/*$debug1= "DEBUG grouperesa JR : grouperesa_pipelines.php - grouperesa_recuperer_fond - Pt063 -";
		spip_log("$debug1.", true);
		spip_log("mot= $mot.", true);
		spip_log("FIN $debug1.", true);
		*/

			$flux['data']['texte'] .= recuperer_fond('formulaires/inc-reservation_evenements-resa_grp', $contexte);
		}

	}

	return $flux;
}


/** Ref log fonction -> 070
 * 
 * Traitement de post edition de la base de données
 *
 * Gerer la relation entre reservations_detail et resa_grp.
 *
 * @pipeline post_edition
 * @param  array $flux Données du pipeline avec id_resa_grp dans data.
 * @return array       Données du pipeline
 */
function grouperesa_post_edition($flux){

	/*$debug1= "DEBUG grouperesa JR : grouperesa_pipelines.php - grouperesa_post_edition - Pt071 -";
	spip_log("$debug1.", true);
	*/
	/*spip_log("flux['args']=.", true);
	foreach($flux['args'] as $key => $value) {
		spip_log("flux['args'](key)=$key.", true);
		spip_log("flux['args'](value)=$value.", true);
	}*/
	/*spip_log("flux['data']=.", true);
	foreach($flux['data'] as $key => $value) {
		spip_log("flux['data'](key)=$key.", true);
		spip_log("flux['data'](value)=$value.", true);
	}*/
	/*spip_log("FIN $debug1.", true);
	*/
	
	
	if ($flux['args']['table'] == 'spip_reservations_details'
		and $flux['args']['action'] == 'instituer') {
		$id_reservations_detail = $flux['args']['id_reservations_detail'];
		$id_resa_grp = $flux['data']['id_resa_grp'];

		/*$debug1= "DEBUG grouperesa JR : grouperesa_pipelines.php - grouperesa_post_edition - Pt073 -";
		spip_log("$debug1.", true);
		spip_log("id_reservations_detail=$id_reservations_detail.", true);
		spip_log("id_resa_grp=$id_resa_grp.", true);
		spip_log("flux['args']=.", true);
		 foreach($flux['args'] as $key => $value) {
		 spip_log("flux['args'](key)=$key.", true);
		 spip_log("flux['args'](value)=$value.", true);
		 }
		spip_log("flux['data']=.", true);
		foreach($flux['data'] as $key => $value) {
			spip_log("flux['data'](key)=$key.", true);
			spip_log("flux['data'](value)=$value.", true);
		}
		$row = sql_fetsel ( '*', 'spip_reservations_details', 'id_reservations_detail=' . intval ( $id_reservations_detail ) );
		spip_log("enr_rd=.", true);
		foreach($row as $key => $value) {
			spip_log("enr_rd(key)=$key.", true);
			spip_log("enr_rd(value)=$value.", true);
		}
		spip_log("FIN $debug1.", true);
		*/
		
		if ($mot=grouperesa_evenement_regroupe(null, $id_reservations_detail, null)){
			include_spip('action/editer_liens');

			/*$debug1= "DEBUG grouperesa JR : grouperesa_pipelines.php - grouperesa_post_edition - Pt076 -";
			spip_log("$debug1.", true);
			*/
			/*spip_log("id_reservations_detail=.$id_reservations_detail", true);
			spip_log("id_resa_grp=.$id_resa_grp", true);
			spip_log("mot=.", true);
			foreach($mot as $key => $value) {
				spip_log("mot(key)=$key.", true);
				spip_log("mot(value)=$value.", true);
			}*/
			/*spip_log("FIN $debug1.", true);
			*/

			objet_associer(
				array("resa_grp"=>$id_resa_grp),
				array("reservations_detail"=> $id_reservations_detail)
			);
		}

	}

	return $flux;
}

/**
 * Vérifie les données d'un formulaire
 *
 * @pipeline formulaire_verifier
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 **/
function grouperesa_formulaire_verifier($flux) {
	$form = $flux['args']['form'];

	//les champs extras
	include_spip('inc/saisies');
	//include_spip('cextras_pipelines');

	if ($form == 'reservation') {

		/*$debug1= "DEBUG grouperesa JR : grouperesa_pipelines.php - grouperesa_formulaire_verifier - Pt021 -";
		spip_log("$debug1.", true);
		foreach($flux['args'] as $key => $value) {
			spip_log("flux['args'](key)=$key.", true);
			spip_log("flux['args'](value)=$value.", true);
		}
		spip_log("form= $form.", true);
		foreach($flux['args']['args'] as $key => $value) {
			spip_log("flux['args']['args'](key)=$key.", true);
			spip_log("flux['args']['args'](value)=$value.", true);
		}
		spip_log("flux['data']=.", true);
		foreach($flux['data'] as $key => $value) {
			spip_log("flux['data'](key)=$key.", true);
			spip_log("flux['data'](value)=$value.", true);
		}
		$id_evenement_log = _request('id_evenement');
		if (is_array($id_evenement_log)){
			foreach($id_evenement_log as $key => $value) {
				spip_log("id_evenement(key)=$key.", true);
				spip_log("id_evenement(value)=$value.", true);
			}
		}else{
			spip_log("id_evenement=$id_evenement_log.", true);
		}
		$id_resa_grp_log = _request('id_resa_grp');
		spip_log("id_resa_grp=$id_resa_grp_log.", true);
		spip_log("FIN $debug1.", true);
		*/

		$erreurs = array();

		// JR-21/12/2016-Verifier si une relation reservations_detail <-> resa_grp est saisie.
		$id_evenements = _request(id_evenement);
		// Dans ce cas, tous les evenements coches pour la reservation sont en tableau.
		// @todo : JR-27/12/2016-Comment gerer les id_resa_grp en consequence ?
		if (is_array($id_evenements)){
			foreach($id_evenements as $key => $id_evenement) {
				if ($mot=grouperesa_evenement_regroupe($id_evenement, null, null)){
					// champs obligatoires
					foreach(array ('id_resa_grp') as $obligatoire) {
						if (!_request($obligatoire)) $erreurs[$obligatoire] =  _T('grouperesa:groupe_obligatoire');
					}
				}
			}
		}else{
			if ($mot=grouperesa_evenement_regroupe($id_evenements, null, null)){
				// champs obligatoires
				foreach(array ('id_resa_grp') as $obligatoire) {
					if (!_request($obligatoire)) $erreurs[$obligatoire] =  _T('grouperesa:groupe_obligatoire');
				}
			}
		}
		
		$flux['data'] = array_merge($flux['data'], $erreurs);
		
		//$flux['data'] = array_merge($flux['data'], $erreurs);

		/*$debug1= "DEBUG grouperesa JR : grouperesa_pipelines.php - grouperesa_formulaire_verifier - Pt025 -";
		 spip_log("$debug1.", true);
		 spip_log("flux['data']=.", true);
		 foreach($flux['data'] as $key => $value) {
			spip_log("flux['data'](key)=$key.", true);
			spip_log("flux['data'](value)=$value.", true);
			}
			spip_log("FIN $debug1.", true);
			*/

	}

	if ($form == 'reservations_detail') {
		$id_reservations_detail=$flux['args']['id_reservations_detail'];

		/*$debug1= "DEBUG grouperesa JR : grouperesa_pipelines.php - grouperesa_formulaire_verifier - Pt022 -";
		spip_log("$debug1.", true);
		spip_log("form= $form.", true);
		foreach($flux['args']['args'] as $key => $value) {
			spip_log("flux['args']['args'](key)=$key.", true);
			spip_log("flux['args']['args'](value)=$value.", true);
		}
		spip_log("flux['data']=.", true);
		foreach($flux['data'] as $key => $value) {
			spip_log("flux['data'](key)=$key.", true);
			spip_log("flux['data'](value)=$value.", true);
		}
		spip_log("FIN $debug1.", true);
		*/

		$erreurs = array();
		
		// JR-13/12/2016-Verifier si une relation reservations_details <-> resa_grp existe.
		if ($mot=grouperesa_evenement_regroupe(null, $id_reservations_detail, null)){
		// champs obligatoires
			foreach(array ('id_resa_grp') as $obligatoire) {
				if (!_request($obligatoire)) $erreurs[$obligatoire] =  _T('grouperesa:groupe_obligatoire');
			}
		}

		$flux['data'] = array_merge($flux['data'], $erreurs);

		/*$debug1= "DEBUG grouperesa JR : grouperesa_pipelines.php - grouperesa_formulaire_verifier - Pt027 -";
		spip_log("$debug1.", true);
		spip_log("flux['data']=.", true);
		foreach($flux['data'] as $key => $value) {
			spip_log("flux['data'](key)=$key.", true);
			spip_log("flux['data'](value)=$value.", true);
		}
		spip_log("FIN $debug1.", true);
		*/

	}

	//remettre 	le message d'erreur
	if (count($flux['data']) > 0)
		$flux['data']['message_erreur'] = _T('grouperesa:message_erreur');

		return $flux;
}
