<?php
/**
 * Utilisation de l'action supprimer pour l'objet resa_grp
 *
 * @plugin     Groupement de Reservations
 * @copyright  2016
 * @author     Jean-Gabriel Remond
 * @licence    GNU/GPL
 * @package    SPIP\grouperesa\action - Action
 * ----
 * @todo :
 * Fait :
 * JR-26/11/2016-Cree par La Fabrique.
*/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}



/**
 * Action pour supprimer une resa_grp
 *
 * Vérifier l'autorisation avant d'appeler l'action.
 *
 * @example
 *     ```
 *     [(#AUTORISER{supprimer, resa_grp, #ID_RESA_GRP}|oui)
 *         [(#BOUTON_ACTION{<:resa_grp:supprimer_resa_grp:>,
 *             #URL_ACTION_AUTEUR{supprimer_resa_grp, #ID_RESA_GRP, #URL_ECRIRE{resa_grps}},
 *             danger, <:resa_grp:confirmer_supprimer_resa_grp:>})]
 *     ]
 *     ```
 *
 * @example
 *     ```
 *     [(#AUTORISER{supprimer, resa_grp, #ID_RESA_GRP}|oui)
 *         [(#BOUTON_ACTION{
 *             [(#CHEMIN_IMAGE{resa_grp-del-24.png}|balise_img{<:resa_grp:supprimer_resa_grp:>}|concat{' ',#VAL{<:resa_grp:supprimer_resa_grp:>}|wrap{<b>}}|trim)],
 *             #URL_ACTION_AUTEUR{supprimer_resa_grp, #ID_RESA_GRP, #URL_ECRIRE{resa_grps}},
 *             icone s24 horizontale danger resa_grp-del-24, <:resa_grp:confirmer_supprimer_resa_grp:>})]
 *     ]
 *     ```
 *
 * @example
 *     ```
 *     if (autoriser('supprimer', 'resa_grp', $id_resa_grp)) {
 *          $supprimer_resa_grp = charger_fonction('supprimer_resa_grp', 'action');
 *          $supprimer_resa_grp($id_resa_grp);
 *     }
 *     ```
 *
 * @param null|int $arg
 *     Identifiant à supprimer.
 *     En absence de id utilise l'argument de l'action sécurisée.
 **/
function action_supprimer_resa_grp_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = intval($arg);

	// cas suppression
	if ($arg) {
		sql_delete('spip_resa_grps',  'id_resa_grp=' . sql_quote($arg));
	}
	else {
		spip_log("action_supprimer_resa_grp_dist $arg pas compris");
	}
}