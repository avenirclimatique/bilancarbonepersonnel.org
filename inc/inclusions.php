<?php 
// ========================================
// Ce sont les inclusions qui sont faites pour toutes les pages
// d'autres inclusions sont faites dans le fichier index.php en fonction des pages appelées 



require_once ('constantes_config.php') ; 
require_once ('constantes_url_navigation.php') ; 
require_once ('constantes_index_variable_session.php') ; 
require_once ('constantes_categories_pages.php') ; 
require_once ('constantes_index_calcul.php') ; 
require_once ('constantes_metier.php') ; 

require_once ('./inc_admin/fonctions_admin.php') ; 

require_once ('fonctions_generales.php');
require_once ('fonctions_presentation.php') ; 

require_once ('./inc_questionnaire/fonctions_menu_questionnaire_creation_ajout_suppression.php') ; 
require_once ('./inc_questionnaire/fonctions_menu_questionnaire_affichage.php') ; 
require_once ('./inc_questionnaire/fonctions_traitement_post_validation.php') ; 
require_once ('./inc_questionnaire/fonctions_questionnaire.php') ; 

require_once ('fonctions_affichage_explications.php') ; 
require_once ('./inc_gestion_compte/inclusions_gestion_compte.php') ; 


?>