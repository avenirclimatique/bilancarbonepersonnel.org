<?php 
// pour que le & avant l'envoi d'identifiant de session PHPSESSID soit valide XHTML
ini_set('arg_separator.output', '&amp;'); 
// pour que les input de type hidden ajoutés por php dans les formulaires soient valides XHTML
ini_set("url_rewriter.tags","a=href,area=href,frame=src,iframe=src,input=src"); 

//=============================================
// Création de session
//=============================================
session_start() ;

require_once ('inc_admin/inclusions_admin.php') ; 

//=============================================
// On normalise les entrées HTTP
normalisation();

//=============================================
// Connexion
$connexion = connexion (NOM, PASSE, BASE, SERVEUR);

//=============================================
// Niveau d'erreur
//=============================================
error_reporting(E_ALL);

//=============================================
// Traitement de l'identification en tant qu'administrateur
//=============================================
if( isSet ( $_POST['formulaire_login_admin'] ) )
{
  $login_admin = $_POST['login_admin'] ; 
	$pass_admin = $_POST['pass_admin'] ; 
	if ( est_valide_login_admin ( $login_admin , $pass_admin ) )
	{
		//echo "<p><strong>Vous êtes dorénavant identifié en tant qu'administrateur</strong>.</p>" ; 
    $_SESSION[MODE_ADMIN] = true;
	}
  else
	{
    echo "<p><strong>Mauvais login ou mot de passe administrateur</strong>. Vous n'êtes pas identifié en tant qu'administrateur. </p>"
			. "<p>Pour vous tenter à nouveau de vous identifier en tant qu'administrateur <strong><a href='admin.php'>cliquez ici</a>.</p>" ; 
		$_SESSION[MODE_ADMIN] = false;
	}
}  

//=============================================
// Formulaire
//=============================================
if ( !isSet ( $_SESSION[MODE_ADMIN] ) || $_SESSION[MODE_ADMIN] != true )
{
	formulaire_login_admin () ; 
}
else
//=============================================
// Espace admin
//=============================================
{
	afficher_en_tete_admin () ; 
	afficher_bandeau_titre_admin () ; 
	afficher_menu_admin () ; 
	if ( isSet ( $_GET['page'] ) )
	{
		if ( $_GET['page'] == COMPTE ) 
			afficher_compte ( $connexion ) ; 
		else if ( $_GET['page'] == NUMEROTER_QUESTIONNAIRE ) 
			numeroter_questionnaire ( $connexion ) ; 
		else if ( $_GET['page'] == 'transfert_fe' ) 
			transfert_fe_vers_bdd ( $connexion ) ; 
		else if ( $_GET['page'] == 'liste_unites_fe' ) 
			liste_unites_fe ( $connexion ) ; 
		else if ( $_GET['page'] == 'backup_bdd' ) 
			backup_bdd () ; 
		else if ( $_GET['page'] == 'remove_bckp' ) 
			remove_backup ( $_GET['file']) ; 
		else if ( $_GET['page'] == 'saveBDD' ) 
			add_backup () ; 
	}
	afficher_pied_de_page_admin () ; 
}
?>
