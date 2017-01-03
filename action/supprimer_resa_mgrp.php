<?php
/**
 * Utilisation de l'action supprimer pour l'objet resa_mgrp
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
 * Action pour supprimer un·e resa_mgrp
 *
 * Vérifier l'autorisation avant d'appeler l'action.
 *
 * @example
 *     ```
 *     [(#AUTORISER{supprimer, resa_mgrp, #ID_RESA_MGRP}|oui)
 *         [(#BOUTON_ACTION{<:resa_mgrp:supprimer_resa_mgrp:>,
 *             #URL_ACTION_AUTEUR{supprimer_resa_mgrp, #ID_RESA_MGRP, #URL_ECRIRE{resa_mgrps}},
 *             danger, <:resa_mgrp:confirmer_supprimer_resa_mgrp:>})]
 *     ]
 *     ```
 *
 * @example
 *     ```
 *     [(#AUTORISER{supprimer, resa_mgrp, #ID_RESA_MGRP}|oui)
 *         [(#BOUTON_ACTION{
 *             [(#CHEMIN_IMAGE{resa_mgrp-del-24.png}|balise_img{<:resa_mgrp:supprimer_resa_mgrp:>}|concat{' ',#VAL{<:resa_mgrp:supprimer_resa_mgrp:>}|wrap{<b>}}|trim)],
 *             #URL_ACTION_AUTEUR{supprimer_resa_mgrp, #ID_RESA_MGRP, #URL_ECRIRE{resa_mgrps}},
 *             icone s24 horizontale danger resa_mgrp-del-24, <:resa_mgrp:confirmer_supprimer_resa_mgrp:>})]
 *     ]
 *     ```
 *
 * @example
 *     ```
 *     if (autoriser('supprimer', 'resa_mgrp', $id_resa_mgrp)) {
 *          $supprimer_resa_mgrp = charger_fonction('supprimer_resa_mgrp', 'action');
 *          $supprimer_resa_mgrp($id_resa_mgrp);
 *     }
 *     ```
 *
 * @param null|int $arg
 *     Identifiant à supprimer.
 *     En absence de id utilise l'argument de l'action sécurisée.
 **/
function action_supprimer_resa_mgrp_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = intval($arg);

	// cas suppression
	if ($arg) {
		sql_delete('spip_resa_mgrps',  'id_resa_mgrp=' . sql_quote($arg));
	}
	else {
		spip_log("action_supprimer_resa_mgrp_dist $arg pas compris");
	}
}