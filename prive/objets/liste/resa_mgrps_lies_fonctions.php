<?php
/**
 * Fonctions du squelette associe pour structurer la liste par Initial qd trop importante
 *
 * @plugin     Groupement de Reservations
 * @copyright  2016
 * @author     Jean-Gabriel Remond
 * @licence    GNU/GPL
 * @package    SPIP\grouperesa\prive/objets/liste/ - Fonctions
 * --
 * @todo : 
 * Fait:
 * JR-26/11/2016-Cree par La Fabrique.
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// pour initiale et afficher_initiale
include_spip('prive/objets/liste/auteurs_fonctions');

/**
 * Liste des Groupements de resa
 * pour selection champs extras id_resa_grp de reservation_details.
 *
 *
 * @param int $id_evenement
 * @param array $id_resa_grps
 */
/*function resa_mgrpslies($id_evenement){
	include_spip('base/abstract_sql');

	if (intval($id_evenement)){
		// liste des resa_mgrp  pour 1 evenement.
		// Liste de tous les grp resa existants.
		$codselect = array("grp.id_resa_grp as id_resa_grp, grp.titre as titre");
		$codfrom = array(
				"spip_resa_grps grp",
				"spip_resa_tgrp_liens lien_tgrp",
		);
		$codwhere = array(
				"lien_tgrp.id_evenement=$id_evenement",
				"grp.id_resa_tgrp = lien_tgrp.id_resa_tgrp");
		$codgroupby = array();
		$codorderby = array();
		$codlimit ="";
		$liste_resa_grps = sql_allfetsel($codselect, $codfrom, $codwhere, $codgroupby, $codorderby, $codlimit);
	}else{
		// Liste de tous les grp resa existants.
		$codselect = array("grp.id_resa_grp as id_resa_grp, grp.titre as titre");
		$codfrom = array(
				"spip_resa_grps grp",
				"spip_resa_tgrp_liens lien_tgrp");
		$codwhere = array(
				"grp.id_resa_tgrp = lien_tgrp.id_resa_tgrp");
		$codgroupby = array();
		$codorderby = array();
		$codlimit ="";
		$liste_resa_grps = sql_allfetsel($codselect, $codfrom, $codwhere, $codgroupby, $codorderby, $codlimit);
	}

	if (is_array($liste_resa_grps)) {
		$liste_resa_grps = array_map('reset',$liste_resa_grps);
		return $liste_resa_grps;
	}
	return array();

}*/
