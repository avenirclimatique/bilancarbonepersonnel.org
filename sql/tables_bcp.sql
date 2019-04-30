CREATE TABLE t_pays (
	pays_id INTEGER AUTO_INCREMENT NOT NULL PRIMARY KEY, 
	pays_nom VARCHAR (30) NOT NULL
);

CREATE TABLE t_version (
	version_id INTEGER AUTO_INCREMENT NOT NULL PRIMARY KEY, 
	version_nom VARCHAR (20) NOT NULL
);

CREATE TABLE t_type_bc (
	type_bc_id INTEGER AUTO_INCREMENT NOT NULL PRIMARY KEY, 
	type_bc_nom VARCHAR (30) NOT NULL
);

CREATE TABLE t_rubrique (
	rub_id INTEGER AUTO_INCREMENT NOT NULL PRIMARY KEY, 
	rub_prec_id INTEGER,
	rub_numero INTEGER,
	rub_nom VARCHAR (100), 
	rub_est_repetee ENUM ( 'true' , 'false' )
);

CREATE TABLE t_page (
	page_id INTEGER AUTO_INCREMENT NOT NULL PRIMARY KEY, 
	page_prec_id INTEGER, 	
	page_numero INTEGER,
	page_rub_id INTEGER, 
	page_nom VARCHAR (100),
	page_est_repetee ENUM ( 'true' , 'false' ),
	page_influe_sur_page_id INTEGER, 
	page_est_influencee_par_page_id INTEGER,
	FOREIGN KEY (page_rub_id) REFERENCES t_rubrique
);

CREATE TABLE t_question (
	quest_id INTEGER AUTO_INCREMENT NOT NULL PRIMARY KEY, 
	quest_prec_id INTEGER, 	
	quest_numero INTEGER,
	quest_page_id INTEGER, 
	quest_nom VARCHAR (100),
	quest_est_affichee_seulement_si_reponse ENUM ( 'true' , 'false' ), 
	FOREIGN KEY (quest_page_id) REFERENCES t_page
);

CREATE TABLE t_affiche_question_si_reponse (
	aff_quest_si_rep_quest_id INTEGER,
	aff_quest_si_rep_rep_id INTEGER,
	FOREIGN KEY (aff_quest_si_rep_quest_id) REFERENCES t_question,
	FOREIGN KEY (aff_quest_si_rep_rep_id) REFERENCES t_reponse,
	PRIMARY KEY (aff_quest_si_rep_quest_id, aff_quest_si_rep_rep_id)
);

CREATE TABLE t_reponse (
	rep_id INTEGER AUTO_INCREMENT NOT NULL PRIMARY KEY, 
	rep_prec_id INTEGER, 
	rep_ordre INTEGER,
	rep_quest_id INTEGER, 
	rep_type ENUM ( 'num' , 'select' , 'radio' , 'checkbox' ),
	rep_valeur VARCHAR (100),	
	rep_intitule VARCHAR (100),
	FOREIGN KEY (rep_quest_id) REFERENCES t_question
);

CREATE TABLE t_parametre_reponse (
	param_rep_id INTEGER AUTO_INCREMENT NOT NULL PRIMARY KEY, 
	param_rep_rep_id INTEGER, 
	param_rep_rep_defaut VARCHAR (100),
	param_rep_rep_est_non_zero ENUM ( 'true' , 'false' ),
	param_rep_rep_valeur_num_max INTEGER, 
	FOREIGN KEY (param_rep_rep_id) REFERENCES t_reponse
);

CREATE TABLE t_lien_reponse (
	lien_rep_amont_rep_id INTEGER, 
	lien_rep_aval_rep_id INTEGER, 
	lien_rep_type ENUM ( 'est_incompatible_avec' , 'est_facultatif_si' , 'est_facultatif_si_non' ),
	FOREIGN KEY (lien_rep_amont_rep_id) REFERENCES t_reponse, 
	FOREIGN KEY (lien_rep_aval_rep_id) REFERENCES t_reponse, 
	PRIMARY KEY (lien_rep_amont_rep_id, lien_rep_aval_rep_id)
);

CREATE TABLE t_utilisateur (
	util_id INTEGER AUTO_INCREMENT NOT NULL PRIMARY KEY, 
	util_courriel VARCHAR (50) NOT NULL, 
	util_est_valide_courriel ENUM ( 'true' , 'false' ), 
	util_pass VARCHAR (50) NOT NULL, 
	util_pass_prov VARCHAR (50) NOT NULL, 
	util_date_time_premiere_validation_courriel DATETIME
);

CREATE TABLE t_sauvegarde (
	sauv_id INTEGER AUTO_INCREMENT NOT NULL PRIMARY KEY, 
	sauv_util_id INTEGER, 
	sauv_nom VARCHAR (50),
	sauv_est_saisie_complete ENUM ( 'true' , 'false' ), 
	sauv_date_time DATETIME,
	sauv_pays_id INTEGER, 
	sauv_version_id INTEGER, 
	sauv_type_bc_id INTEGER, 
	FOREIGN KEY (sauv_util_id) REFERENCES t_utilisateur,
	FOREIGN KEY (sauv_pays_id) REFERENCES t_pays,
	FOREIGN KEY (sauv_version_id) REFERENCES t_version,
	FOREIGN KEY (sauv_type_bc_id) REFERENCES t_type_bc
);

CREATE TABLE t_saisie_menu_nombre (
	saisie_menu_nombre_id INTEGER AUTO_INCREMENT NOT NULL PRIMARY KEY, 
	saisie_menu_nombre_nombre INTEGER, 
	saisie_menu_nombre_type ENUM ( 'rubrique' , 'page' ), 
	saisie_menu_nombre_rub_id INTEGER, 
	saisie_menu_nombre_page_id INTEGER, 
	saisie_menu_nombre_sauv_id INTEGER, 
	FOREIGN KEY (saisie_menu_nombre_sauv_id) REFERENCES t_sauvegarde,
	FOREIGN KEY (saisie_menu_nombre_rub_id) REFERENCES t_rubrique,
	FOREIGN KEY (saisie_menu_nombre_page_id) REFERENCES t_page
);

CREATE TABLE t_saisie_numerique (
	sais_num_id INTEGER AUTO_INCREMENT NOT NULL PRIMARY KEY, 
	sais_num_rep_id INTEGER, 
	sais_num_valeur FLOAT,
	sais_num_numero INTEGER, 
	sais_num_sauv_id INTEGER, 
	FOREIGN KEY (sais_num_sauv_id) REFERENCES t_sauvegarde,
	FOREIGN KEY (sais_num_rep_id) REFERENCES t_reponse
);

CREATE TABLE t_saisie_discrete (
	sais_disc_id INTEGER AUTO_INCREMENT NOT NULL PRIMARY KEY, 
	sais_disc_rep_id INTEGER, 
	sais_disc_numero INTEGER, 
	sais_disc_sauv_id INTEGER, 
	FOREIGN KEY (sais_disc_sauv_id) REFERENCES t_sauvegarde,
	FOREIGN KEY (sais_disc_rep_id) REFERENCES t_reponse
);

