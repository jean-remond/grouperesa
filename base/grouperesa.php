<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Groupement de Reservations
 * @copyright  2016
 * @author     Jean-Gabriel Remond
 * @licence    GNU/GPL
 * @package    SPIP\grouperesa\base
 * ----
 * @todo :
 * Fait :
 * JR-17/12/2016-V1.3.1-201612171840-Creation des groupes (resa_grp).
 * JR-16/12/2016-V1.2.2-201612170030-Creation des modeles (resa_mgrp).
 * JR-16/12/2016-V1.1.1-201612161730-Forcer installation base.
 * JR-16/12/2016-V1.1.0-201612161700-Création du plugin pour valider la fonction.
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function grouperesa_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['resa_bgrps'] = 'resa_bgrps';
	$interfaces['table_des_tables']['resa_mgrps'] = 'resa_mgrps';
	$interfaces['table_des_tables']['resa_grps'] = 'resa_grps';
	
	$interface['tables_jointures']['spip_resa_mgrps']['id_resa_bgrp'] = 'resa_bgrps';
	$interface['tables_jointures']['spip_resa_mgrps']['id_evenement'] = 'evenements';
	$interface['tables_jointures']['spip_resa_grps']['id_resa_mgrp'] = 'resa_mgrps';
	$interface['tables_jointures']['spip_resa_grps']['id_evenement'] = 'evenements';
	
	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function grouperesa_declarer_tables_objets_sql($tables) {

	// Table ds Bases de groupements
	$tables['spip_resa_bgrps'] = array(
			'type' => 'resa_bgrp',
			'principale' => "oui",
			'table_objet_surnoms' => array('resabgrp'), // table_objet('resa_bgrp') => 'resa_bgrps'
			'field'=> array(
					'id_resa_bgrp'       => 'bigint(21)',
					'titre'              => 'varchar(35) NOT NULL DEFAULT ""',
					'descriptif'         => 'text NOT NULL DEFAULT ""',
					'place_bgrp'  	     => 'bigint(21) default 0',
					'date_publication'   => 'datetime',
					'statut'             => 'varchar(20)  DEFAULT "prepa" NOT NULL',
					'maj'                => 'TIMESTAMP'
			),
			'key' => array(
					'PRIMARY KEY'        => 'id_resa_bgrp',
					'KEY statut'         => 'statut',
			),
			'titre' => 'titre AS titre, "" AS lang',
			'date' => 'date_publication',
			'champs_editables'  => array('titre', 'descriptif', 'place_bgrp'),
			'champs_versionnes' => array('titre', 'place_bgrp'),
			'rechercher_champs' => array("titre" => 1),
			'tables_jointures'  => array(),
			'statut_textes_instituer' => array(
					'prepa'    => 'texte_statut_en_cours_redaction',
					'prop'     => 'texte_statut_propose_evaluation',
					'publie'   => 'texte_statut_publie',
					'refuse'   => 'texte_statut_refuse',
					'poubelle' => 'texte_statut_poubelle',
			),
			'statut'=> array(
					array(
							'champ'     => 'statut',
							'publie'    => 'publie',
							'previsu'   => 'publie,prop,prepa',
							'post_date' => 'date',
							'exception' => array('statut','tout')
					)
			),
			'texte_changer_statut' => 'resa_bgrp:texte_changer_statut_resa_bgrp'
	);

	// Table des modeles de groupements
	$tables['spip_resa_mgrps'] = array(
			'type' => 'resa_mgrp',
			'principale' => "oui",
			'table_objet_surnoms' => array('resamgrp'), // table_objet('resa_mgrp') => 'resa_mgrps'
			'field'=> array(
					'id_resa_mgrp'       => 'bigint(21) NOT NULL',
					'titre'              => 'varchar(35) DEFAULT " " NOT NULL',
					'descriptif'         => 'text NOT NULL DEFAULT ""',
					'id_resa_bgrp'		 => 'bigint(21)',
					'place_mgrp'  	     => 'bigint(21) default 1',
					"clonable" 			 => "ENUM('non', 'oui') DEFAULT 'non' NOT NULL",
					'date_publication'   => 'datetime',
					'statut'             => 'varchar(20)  DEFAULT "prepa" NOT NULL',
					'maj'                => 'TIMESTAMP'
			),
			'key' => array(
					'PRIMARY KEY'        => 'id_resa_mgrp',
					'KEY statut'         => 'statut',
			),
			'titre' => 'titre AS titre, "" AS lang',
			'date' => 'date_publication',
			'champs_editables'  => array('titre', 'descriptif', 'id_resa_bgrp', 'place_mgrp', 'clonable'),
			'champs_versionnes' => array('titre', 'place_mgrp'),
			'rechercher_champs' => array("titre" => 1),
			'tables_jointures'  => array('spip_resa_mgrps_liens'),
			'statut_textes_instituer' => array(
					'prepa'    => 'texte_statut_en_cours_redaction',
					'prop'     => 'texte_statut_propose_evaluation',
					'publie'   => 'texte_statut_publie',
					'refuse'   => 'texte_statut_refuse',
					'poubelle' => 'texte_statut_poubelle',
			),
			'statut'=> array(
					array(
							'champ'     => 'statut',
							'publie'    => 'publie',
							'previsu'   => 'publie,prop,prepa',
							'post_date' => 'date',
							'exception' => array('statut','tout')
					)
			),
			'texte_changer_statut' => 'resa_mgrp:texte_changer_statut_resa_mgrp',
	);

	// Table des groupements
	$tables['spip_resa_grps'] = array(
			'type' => 'resa_grp',
			'principale' => "oui",
			'table_objet_surnoms' => array('resagrp'), // table_objet('resa_grp') => 'resa_grps'
			'field'=> array(
					'id_resa_grp'        => 'bigint(21) NOT NULL',
					'id_evenement'       => 'bigint(21) NOT NULL DEFAULT 0',
					'titre'              => 'varchar(35) DEFAULT " " NOT NULL',
					'descriptif'         => 'text NOT NULL DEFAULT ""',
					'id_resa_mgrp'       => 'bigint(21) NOT NULL DEFAULT 0',
					'place_grp'        => 'BIGINT(21)',
					'date_publication'   => 'datetime',
					'statut'             => 'varchar(20)  DEFAULT "prepa" NOT NULL',
					'maj'                => 'TIMESTAMP'
			),
			'key' => array(
					'PRIMARY KEY'        => 'id_resa_grp',
					'KEY id_evenement'   => 'id_evenement',
					'KEY statut'         => 'statut',
					'KEY id_resa_mgrp'   => 'id_resa_mgrp',
			),
			'titre' => 'titre AS titre, "" AS lang',
			'date' => 'date_publication',
			'champs_editables'  => array('titre', 'descriptif', 'id_resa_mgrp', 'place_grp', 'id_evenement'),
			'champs_versionnes' => array('titre', 'id_resa_mgrp', 'place_grp', 'id_evenement'),
			'rechercher_champs' => array("place_grp" => 2),
			'tables_jointures'  => array('spip_resa_grps_liens'),
			'statut_textes_instituer' => array(
					'prepa'    => 'texte_statut_en_cours_redaction',
					'prop'     => 'texte_statut_propose_evaluation',
					'publie'   => 'texte_statut_publie',
					'refuse'   => 'texte_statut_refuse',
					'poubelle' => 'texte_statut_poubelle',
			),
			'statut'=> array(
					array(
							'champ'     => 'statut',
							'publie'    => 'publie,prop,prepa',
							'previsu'   => 'publie,prop,prepa',
							'post_date' => 'date',
							'exception' => array('statut','tout')
					)
			),
			'texte_changer_statut' => 'resa_grp:texte_changer_statut_resa_grp',
	);
	
	return $tables;
}

/**
 * Déclaration des tables secondaires (liaisons)
 *
 * @pipeline declarer_tables_auxiliaires
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function grouperesa_declarer_tables_auxiliaires($tables) {

	// Association des modeles (principalement vers les evenements)
	$tables['spip_resa_mgrps_liens'] = array(
			'field' => array(
					'id_resa_mgrp'       => 'bigint(21) DEFAULT "0" NOT NULL',
					'id_objet'           => 'bigint(21) DEFAULT "0" NOT NULL',
					'objet'              => 'VARCHAR(25) DEFAULT "" NOT NULL',
					'vu'                 => 'VARCHAR(6) DEFAULT "non" NOT NULL',
			),
			'key' => array(
					'PRIMARY KEY'        => 'id_resa_mgrp,id_objet,objet',
					'KEY id_resa_mgrp'   => 'id_resa_mgrp',
			)
	);

	// Association des groupes (principalement vers les reservations_details)
	$tables['spip_resa_grps_liens'] = array(
			'field' => array(
					'id_resa_grp'        => 'bigint(21) DEFAULT "0" NOT NULL',
					'id_objet'           => 'bigint(21) DEFAULT "0" NOT NULL',
					'objet'              => 'VARCHAR(25) DEFAULT "" NOT NULL',
					'vu'                 => 'VARCHAR(6) DEFAULT "non" NOT NULL',
			),
			'key' => array(
					'PRIMARY KEY'        => 'id_resa_grp,id_objet,objet',
					'KEY id_resa_grp'    => 'id_resa_grp',
			)
	);
	
	return $tables;
}
