<?php 

define ( "ACTION" , "action" ) ;
define ( "TYPE_PAGE" , "type_page" ) ; 
// define ( "PAGE" , "page" ) ;
define ( "NUMERO" , "numero" ) ; 
// define ( "RUBRIQUE" , "rubrique" ) ;



// types de page
define ( "GENERIQUE" , "generique" ) ;
define ( "FAQ" , "faq" ) ; 
define ( "EXPLICATION" , "explication" ) ; 
define ( "QUESTIONNAIRE" , "questionnaire" ) ; 
define ( "PAGE_RESULTAT" , "resultats" ) ;
define ( "GESTION_COMPTE" , "gestion_compte" ) ; 
define ( "ADMIN" , "admin" ) ; 


// pages génériques
define ( "ACCUEIL" , "accueil" ) ;
define ( "PRESENTATION" , "mode_d_emploi" ) ;
define ( "NOUVEAUTE" , "nouveautes" ) ;
define ( "MENU_FAQ" , "menu_faq" ) ;
define ( "LIEN" , "liens" ) ; 
define ( "REMERCIEMENT" , "remerciements" ) ; 

// pages Administrateur
define ( "VARIABLE" , "variables" ) ; 
define ( "TEST_FORMULE" , "test_formules" ) ; 
define ( "STATISTIQUE" , "statistiques" ) ; 

// actions pour le menu du questionnaire
define ( "AJOUTER" , "ajouter" ) ; 
define ( "SUPPRIMER" , "supprimer" ) ; 
define ( "CONFIRMER_SUPPRIMER" , "confirmer_supprimer" ) ; 
define ( "ANNULER_SUPPRIMER" , "annuler_supprimer" ) ; 

// actions pour le menu du questionnaire
define ( "PREMIERE_PAGE_QUESTIONNAIRE" , "general%1" ) ; // on pourrait calculer cette première page de façon plus dynamique...
define ( "POST_VALIDATION_PAGE" , "post_validation_page" ) ; // on pourrait calculer cette première page de façon plus dynamique...

// pages de gestion du compte
define ( "REMETTRE_A_ZERO" , "remettre_a_zero" ) ; 
define ( "CONFIRMER_REMETTRE_A_ZERO" , "confirmer_remettre_a_zero" ) ; 
define ( "ANNULER_REMETTRE_A_ZERO" , "annuler_remettre_a_zero" ) ; 
//
define ( "SE_CONNECTER" , "se_connecter" ) ; 
define ( "CREER_COMPTE" , "creer_compte" ) ; 
define ( "S_IDENTIFIER" , "s_identifier" ) ; 
//
//define ( "POST_SAISIE_COURRIEL" , "post_saisie_courriel" ) ; 
define ( "POST_SAISIE_COURRIEL_CREATION_COMPTE" , "post_saisie_courriel_creation_compte" ) ; 
define ( "POST_SAISIE_COURRIEL_IDENTIFICATION" , "post_saisie_courriel_identification" ) ; 
//
//define ( "POST_SAISIE_PASS" , "post_saisie_pass" ) ; 
define ( "POST_SAISIE_PASS_CREATION_COMPTE" , "post_saisie_pass_creation_compte" ) ; 
define ( "POST_SAISIE_PASS_IDENTIFICATION" , "post_saisie_pass_identification" ) ; 
//
define ( "DEMANDE_NOUVEAU_PASS" , "demande_nouveau_pass" ) ; 
//
define ( "MENU_SAUVEGARDE" , "menu_sauvegarde" ) ; 
//
define ( "RENOMMER_SAUVEGARDE" , "renommer_sauvegarde" ) ; 
define ( "POST_RENOMMER_SAUVEGARDE" , "post_renommer_sauvegarde" ) ; 
//
define ( "SUPPRIMER_SAUVEGARDE" , "supprimer_sauvegarde" ) ; 
define ( "CONFIRMER_SUPPRIMER_SAUVEGARDE" , "confirmer_supprimer_sauvegarde" ) ; 
define ( "ANNULER_SUPPRIMER_SAUVEGARDE" , "annuler_supprimer_sauvegarde" ) ; 
//
define ( "NOUVELLE_SAUVEGARDE" , "nouvelle_sauvegarde" ) ; 
define ( "POST_NOUVELLE_SAUVEGARDE" , "post_nouvelle_sauvegarde" ) ; 
//
define ( "SAUVEGARDER" , "sauvegarder" ) ; 
define ( "ANNULER_SAUVEGARDER" , "annuler_sauvegarder" ) ; 
define ( "CONFIRMER_SAUVEGARDER" , "confirmer_sauvegarder" ) ; 
// 
define ( "CHARGER_SAUVEGARDE" , "charger_sauvegarde" ) ; 
define ( "CONFIRMER_CHARGER_SAUVEGARDE" , "confirmer_charger_sauvegarde" ) ; 
define ( "ANNULER_CHARGER_SAUVEGARDE" , "annuler_charger_sauvegarde" ) ; 
//
define ( "SE_DECONNECTER" , "se_deconnecter" ) ; 
define ( "CONFIRMER_SE_DECONNECTER" , "confirmer_se_deconnecter" ) ; 
define ( "ANNULER_SE_DECONNECTER" , "annuler_se_deconnecter" ) ; 
//
// la constante suivante sert à aiguiller vers la fonction appel_gestion_compte dans index.php
//define ( "POST_GESTION_COMPTE" , "post_gestion_compte" ) ; 


?>