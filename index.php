<?php 
//=============================================
// Pour que le & avant l'envoi d'identifiant de session PHPSESSID soit valide XHTML
//=============================================
ini_set('arg_separator.output', '&amp;'); 
// pour que les input de type hidden ajout�s por php dans les formulaires soient valides XHTML
ini_set("url_rewriter.tags","a=href,area=href,frame=src,iframe=src,input=src"); 

//=============================================
// Cr�ation de session
//=============================================
session_start();
//ini_set('display_errors',1);
//=============================================
// inclusions (hors langue)
//=============================================
require_once ('inc/inclusions.php') ; 

//=============================================
// On normalise les entr�es HTTP
normalisation();

//=============================================
// Connexion
$connexion = connexion (NOM, PASSE, BASE, SERVEUR);

//=============================================
// affectation de la variable de session de version
//=============================================
/*
if ( !isSet ( $_SESSION[VERSION_ID] ) || $_SESSION[VERSION_ID] != '1' )
{
	session_destroy() ; 
	session_start();
}
*/
$_SESSION[VERSION_ID] = '1' ; 
//=============================================
// TEST D'ACTIVATION DES COOKIES
//=============================================
$accepte_cookies_utilisateur = true ; // par d�faut
//
if ( isSet ( $_POST[POST_VALIDATION_PAGE] ) 
	|| ( isSet ( $_GET[TYPE_PAGE] ) && 
				(
					$_GET[TYPE_PAGE] == PAGE_RESULTAT 
					|| $_GET[TYPE_PAGE] == GESTION_COMPTE  
				)
			)
		)
{
	//echo "<p>Page o� l'on teste l'acceptation des cookies</p>" ; 
	// on vient d'une page POST_VALIDATION page, ou on est en train de vouloir acc�der au r�sultat, on teste
	if ( !isSet ( $_SESSION [TEST_COOKIES] ) )
		// l'utilisateur n'accepte pas les cookies
		$accepte_cookies_utilisateur = false ; 
}
else
	// si on ne vient pas d'une page POST, et si on n'est pas en train d'acc�der � la page de r�sultat ni � une page de gestion du compte
	// on affecte � true la variable $_SESSION [TEST_COOKIES]
	$_SESSION [TEST_COOKIES] = true ; 
//=============================================
// affectations de certaines valeurs par d�faut de la variable de session
//=============================================
$_SESSION[PAYS_ID] = '1' ; // pays
$_SESSION[TYPE_BC_ID] = '1' ; // type de bc

//=============================================
// Niveau d'erreur : on le met au niveau maximal si on est en mode admin
//=============================================
// le niveau d'erreur est d�ini dans le fichier ./inc/constantes_config.php
if ( isSet ( $_SESSION[MODE_ADMIN] ) && $_SESSION[MODE_ADMIN] == true )
	error_reporting(E_ALL);

//=============================================
// choix de langue
//=============================================

/*
// Chargement du module de langue
  if(($_SESSION[LANGUE]=='en' || $_GET['langue']=='en') && !($_GET['langue']=='fr'))
  {
    require("lang/langsettings_en.php");
    $_SESSION[LANGUE]= 'en';
  }
  else
  {
    require("lang/langsettings_fr.php");
    $_SESSION[LANGUE]= 'fr';
  }
  require("donnees/fonction.php");
*/

//===============
// � retirer lorsqu'on mettra les options de langue
//===============
require("lang/fr/langsettings_fr.php");
$_SESSION[LANGUE]= 'fr';

//=============================================
// D�connexion du mode administrateur si �a a �t� demand�
//=============================================
if ( isSet ( $_GET[ACTION] ) && $_GET[ACTION] == 'deconnexion_admin' )
{
	echo "<p><strong>Vous n'�tes plus identifi� en tant qu'administrateur</strong>.</p>" ; 
	$_SESSION[MODE_ADMIN] = false;
}
//=============================================
// Initialisation de la variable de positionnement sur le menu du questionnaire 
//=============================================
$url_ici_menu_questionnaire = false ; 
// si �a reste � false on n'indiquera pas de petite fl�che

//=============================================
// Remise � z�ro de l'ensemble des saisies ou d�connexion
//=============================================
if ( isSet ( $_GET[TYPE_PAGE] ) && $_GET[TYPE_PAGE]  == GESTION_COMPTE )
{
	if ( isSet ( $_GET[PAGE] ) )
	{
		$page = $_GET[PAGE] ; 
		if ( $page == CONFIRMER_REMETTRE_A_ZERO )
			{
				// Remise � z�ro
				//unset ( $_SESSION[MENU] ) ; 				
				unset ( $_SESSION[MENU_NOMBRE] ) ; 
				unset ( $_SESSION[PAGE_COMPLETE] ) ; 
				unset ( $_SESSION[REPONSE] ) ; 
				unset ( $_SESSION[RESULTAT] ) ; 
				unset ( $_SESSION[SAUV_ID] ) ; 
				unset ( $_SESSION[EST_SAISIE_EFFECTUEE] ) ; 
			}
		if ( $page == CONFIRMER_SE_DECONNECTER )
		{
			unset ( $_SESSION[UTIL_ID] ) ; 
			unset ( $_SESSION[UTIL_COURRIEL] ) ; 
			unset ( $_SESSION[SAUV_ID] ) ; 
		}
	}
}
//=============================================
// D�finition variable $util_id
//=============================================
if ( isSet ( $_SESSION[UTIL_ID] ) ) 
	$util_id = $_SESSION[UTIL_ID] ; 
else
	$util_id = false ; 
//=============================================
// Mise � jour de la variable de session d'identifiant de l'utilisateur (si l'utilisateur est connect� $_SESSION[UTIL_ID] vaut cet identifiant)
//=============================================
if ( isSet ( $_POST[POST_SAISIE_PASS_IDENTIFICATION] ) || isSet ( $_POST[POST_SAISIE_PASS_CREATION_COMPTE] ) ) 
{
	//echo "<p>Traitement saisie d'un mot de passe pour l'identifiant " . $_POST ['util_id'] . "</p>" ; 
	$util_id_post_saisie_courriel = $_POST ['util_id'] ; 
	$pass = $_POST ['pass'] ; 
	if ( $est_valide_pass = est_valide_pass ( $util_id_post_saisie_courriel , $pass , $connexion ) )
	{
		// echo "<p>Mot de passe valide</p>" ; 
		$_SESSION[UTIL_ID] = $util_id_post_saisie_courriel ; 
		$_SESSION[UTIL_COURRIEL] = $_POST ['courriel'] ; 
		$util_id = $util_id_post_saisie_courriel ; // pour �viter tout probl�me...
	}
}
//=============================================
// Traitement d'une sauvegarde (uniquement la variable $_SESSION[EST_SAISIE_EFFECTUEE], pour le reste ce sera fait plus tard)
//=============================================
if ( isSet ( $_GET[TYPE_PAGE] ) && $_GET[TYPE_PAGE] == GESTION_COMPTE &&
	isSet ( $_GET[PAGE] ) && $_GET[PAGE] == CONFIRMER_SAUVEGARDER )
	unset ( $_SESSION[EST_SAISIE_EFFECTUEE] ) ; 
//=============================================
// Traitement d'une nouvelle sauvegarde
//=============================================
if ( isSet ( $_POST[POST_NOUVELLE_SAUVEGARDE] ) )
{
	$nom_sauvegarde = $_POST ['nom_sauvegarde'] ; 
	if ( $util_id )
	{
		//session active ( si session expir�e on pr�vient l'utilisateur plus loin)
		$sauv_id = nouvelle_sauvegarde_bas_niveau_retourner_sauv_id ( $util_id , $nom_sauvegarde , $connexion ) ; 
		$_SESSION [SAUV_ID] = $sauv_id ; 
		unset ( $_SESSION[EST_SAISIE_EFFECTUEE] ) ; 
	}
}
//=============================================
// Traitement du chargement d'une ancienne sauvegarde
//=============================================
if ( isSet ( $_GET[PAGE] ) && $_GET[PAGE] == CONFIRMER_CHARGER_SAUVEGARDE )
{
	charger_sauvegarde ( $connexion ) ; 
	unset ( $_SESSION[EST_SAISIE_EFFECTUEE] ) ; 
}
//=============================================
// Traitement du changement de nom d'une sauvegarde (on le fait maintenant car si c'est la sauvegarde affich�e en ligne de navigation il faut le faire tout de suite)
//=============================================
if ( isSet ( $_POST[POST_RENOMMER_SAUVEGARDE] ) ) 
	traitement_post_renommer_sauvegarde ( $util_id , $connexion ) ; 
//=============================================
// Traitement de la suppression de la sauvegarde dont la saisie actuelle porte le nom
//=============================================
if ( isSet ( $_GET [TYPE_PAGE] ) && $_GET [TYPE_PAGE] == GESTION_COMPTE && isSet ( $_GET[PAGE] ) && 
		$_GET[PAGE] == CONFIRMER_SUPPRIMER_SAUVEGARDE && isSet ( $_GET[SAUV_ID] ) && isSet ( $_SESSION[SAUV_ID] )
		&& $_GET[SAUV_ID] == $_SESSION[SAUV_ID] )
{
	unset ( $_SESSION [SAUV_ID] ) ; 
	$_SESSION [EST_SAISIE_EFFECTUEE] = true ; 
}
//=============================================
// Cr�ation du menu si n�cessaire
//============================================= 
if ( !isSet ( $_SESSION[MENU_NOMBRE] ) )
	initier_menu_questionnaire ( $connexion ) ; 
//=============================================
// Ajout d'un �l�ment au menu si n�cessaire
//=============================================
if ( isSet ( $_GET[ACTION] ) && $_GET[ACTION]== AJOUTER )
{
	// �a parse le xml
	$url_ici_menu_questionnaire = ajouter_au_menu_questionnaire ( $connexion ) ; // la fonction renvoie l'url de la page o� aller quand on a fait un ajout
	// echo "<p>URL o� se rendre : " . $url_ici_menu_questionnaire . "</p>" ; 
	$_SESSION[EST_SAISIE_EFFECTUEE] = true ; 
}
//=============================================
// Gestion d'une demande de suppression confirm�e d'une page ou d'une cag�gorie du menu
//=============================================
if ( isSet ( $_GET[ACTION] ) && $_GET[ACTION]== CONFIRMER_SUPPRIMER  )
{
	// c'est confirm�, donc on supprime
	$message_suppression_element_menu_questionnaire = suppression_element_menu_questionnaire ( $connexion ) ; 
	// le message sera affich� plus bas
	$suppression_element_menu_questionnaire = true ; 
	$_SESSION[EST_SAISIE_EFFECTUEE] = true ; 
}

//=============================================
// Traitement des donn�es POST d'une page du questionnaire
//=============================================
if ( isSet ( $_POST[POST_VALIDATION_PAGE] ) )
{
	require_once ('inc_questionnaire/fonctions_traitement_post_validation.php') ; 
	require_once ('inc_questionnaire/fonctions_questionnaire.php') ; // on aura besoin de la fonction qui teste si une question a �t� pos�e 
	$url_ici_menu_questionnaire = $_POST ['url'] ; 
	$page = $_POST [PAGE] ; 
	// �a ne parse pas le xml car l'objet page-> est transmis par $_POST 
	$diagnostic_page = traitement_post_validation ( $url_ici_menu_questionnaire , $TEXT[$page] , $connexion ) ; 
	// $est_page_questionnaire = true ; 
	$_SESSION[EST_SAISIE_EFFECTUEE] = true ; 
}
//=============================================
// D�termination de la variable $url_ici_menu_questionnaire lorsque cela est n�cessaire
// cette variable sert � indiquer "o� on se trouve dans le menu" 
// si elle est � false, c'est qu'on n'a pas � indiquer de position pr�cise dans le menu
//=============================================
if ( isSet ( $_GET[TYPE_PAGE] ) && $_GET[TYPE_PAGE] == QUESTIONNAIRE && !isSet ( $_GET[ACTION] ) ) // c'est pas tout � fait optimal...un peu bricol� mais bon
	$url_ici_menu_questionnaire = $_GET[PAGE] ; 
if ( isSet ( $_GET [ACTION] ) && $_GET [ACTION] == SUPPRIMER )
	// demande de confirmation de suppression : c'est justement l� que c'est utile d'indiquer o� on se trouve dans le menu ! 
	if ( isSet ( $_GET[PAGE] ) )
		$url_ici_menu_questionnaire = $_GET[PAGE] ; 

//=============================================
// On d�termine si on est sur une page g�n�rique (variable $page_generique ) dont la page d'accueil
//=============================================
$page_generique = false ; // par d�faut
// on reprend les conditions plus loin sur l'affichage de la boite principale
if ( isSet ( $_GET[TYPE_PAGE] ) )
{
	if ( $_GET[TYPE_PAGE] == GENERIQUE )
		$page_generique = $_GET[PAGE] ; 
}
else if ( count ( $_POST ) == 0 && count ( $_GET ) == 0 )
{
	// on ne vient pas d'une page du questionnaire
	//echo "<p>Page d'accueil</p>" ; 
	$page_generique = ACCUEIL ;
}
//echo "<p>Nombre d'�l�ments dans GET : " . count ( $_GET ) . "</p>" ; 
//echo "<p>Nombre d'�l�ments dans POST : " . count ( $_POST ) . "</p>" ; 
// =========================================================================================================================
// En-tete
// =========================================================================================================================
afficher_en_tete ( $TEXT['en_tete'] , $TEXT['menu_questionnaire'] , $page_generique , $connexion ) ; 
// =========================================================================================================================
// Bandeau titre 
// =========================================================================================================================
afficher_bandeau_titre ( $TEXT['bandeau_titre'] , $connexion ) ; 
// =========================================================================================================================
// Boite de menu g�n�ral 
// =========================================================================================================================
afficher_menu_general ( $util_id , $TEXT['menu_general'] , $page_generique , $connexion ) ; 
// =========================================================================================================================
// MENU DU QUESTIONNAIRE : retourne le tableau liste page, qui est un b�te tableau index� par les diff�rentes pages
// =========================================================================================================================
	$liste_page = afficher_menu_questionnaire ( $url_ici_menu_questionnaire , $TEXT['menu_questionnaire'], $connexion ) ; 
//=======================================================================================================================================
// BOITE PRINCIPALE 
//=======================================================================================================================================
echo "<!-- ================================================================= -->\n " ; 
echo "<div id='principal'>  <!-- debut de la boite 'principal' --> \n \n " ; 
// echo "<pre>" ; print_r ( $liste_page ) ; echo "</pre>" ; 
// =============================
// =============================
// GET TYPE_PAGE
// =============================
if ( isSet ( $_GET[TYPE_PAGE] ) )
{
	$type_page = $_GET[TYPE_PAGE] ; 
	if ( $type_page == GENERIQUE ) 
	{
		// page g�n�rique
		require_once ('inc/fonctions_page_generique.php') ; 
		affiche_page_generique () ; 
	}
	else if ( $type_page == FAQ ) 
	{
		// page de la FAQ
		require_once ('inc/fonctions_page_faq.php') ; 
		affiche_page_faq () ; 
	}
	else if ( $type_page == EXPLICATION ) 
	{
		affiche_explication ( $_GET[PAGE] ) ; 
	}
	else if ( $type_page == PAGE_RESULTAT ) 
	{
		if ( $accepte_cookies_utilisateur )
		{
			require_once ('./inc_affichage_resultat/fonctions_affichage_resultat.php') ; 
			$afficher_resultat = true ; // par d�faut
			if ( in_array ( false , $_SESSION[PAGE_COMPLETE] ) )
				// les saisies ne sont pas compl�tes, s'il n'y a pas eu confirmation on renvoie d'abord sur une demande de confirmation
				if ( !isSet ( $_GET[ACTION] ) )
					// on ne vient pas de la confirmation
					$afficher_resultat = false ; 
			if ( $afficher_resultat )
			{
				// On effectue tous les calculs 
				require_once ('./inc_donnee_calcul/fonction_appel_des_calculs.php') ; 
				$resultat = appelle_calculs_et_retourne_resultat () ; 
				// et on affiche la page de r�sultats
				require_once ('./inc_affichage_resultat/fonction_appel_affichage_resultat.php') ; 		
				affiche_resultat ( $resultat ) ; 
			}
			else
				resultat_mise_en_garde_saisies_non_completes () ; 
		}
		else
			avertissement_cookies () ; 
	}
	else if ( $type_page == QUESTIONNAIRE ) 
	{
		// page du questionnaire ; ceci prend en compte soit l'acc�s direct, soit l'acc�s via un bouton "ajouter"
		if ( !isSet ( $_GET[ACTION] ) )
			$url_ici_menu_questionnaire = $_GET[PAGE] ; // si $_GET[ACTION] alors c'est faux ! dans ce cas $url a �t� d�finie plus haut
		// on affichera la page du questionnaire plus bas
	}
	else if ( $type_page == GESTION_COMPTE ) 
	{
		if ( $accepte_cookies_utilisateur )
			// page de gestion du compte
			afficher_page_gestion_compte ( $TEXT ['gestion_compte'] , $util_id , $connexion ) ; 
		else
			avertissement_cookies () ; 
	}
		else if ( $type_page == ADMIN ) 
	{
		// page Administrateur
		require_once ('inc_admin/fonctions_admin_espace_public.php') ; 
		affiche_page_admin_espace_public () ; 
	}
	
}
// =============================
// POST
// =============================
// on vient d'une validation, on affiche le message de diagnostic �labor� plus haut
else if ( isSet ( $_POST[POST_VALIDATION_PAGE] ) ) 
{
	// echo "<p>Post validation page</p>" ; 
	if ( $accepte_cookies_utilisateur )
		echo $diagnostic_page ; 
	else
		avertissement_cookies () ; 
}
// CREATION COMPTE ET IDENTIFICATION
// COURRIEL
else if ( isSet ( $_POST[POST_SAISIE_COURRIEL_CREATION_COMPTE] ) ) 
	traitement_post_saisie_courriel_creation_compte ( $connexion ) ; 
else if ( isSet ( $_POST[POST_SAISIE_COURRIEL_IDENTIFICATION] ) ) 
	traitement_post_saisie_courriel_identification ( $connexion ) ; 
// PASS
else if ( isSet ( $_POST[POST_SAISIE_PASS_IDENTIFICATION] ) || isSet ( $_POST[POST_SAISIE_PASS_CREATION_COMPTE] ) ) 
	traitement_post_saisie_pass ( $util_id_post_saisie_courriel , $est_valide_pass , $connexion ) ; 
//  SAUVEGARDE
else if ( isSet ( $_POST[POST_NOUVELLE_SAUVEGARDE] ) ) 
	traitement_post_nouvelle_sauvegarde ( $util_id , $nom_sauvegarde , $connexion ) ; 
else if ( isSet ( $_POST[POST_RENOMMER_SAUVEGARDE] ) ) 
	afficher_post_renommer_sauvegarde ( $util_id , $connexion ) ; 

// =============================
// GET ACTION
// =============================
else if ( isSet ( $_GET [ACTION] ) && $_GET [ACTION] == SUPPRIMER )	
{
// On demande confirmation de la suppression
demande_confirmation_suppression () ; 
$url_ici_menu_questionnaire = false ; // pour ne pas afficher la page du questionnaire, voir plus bas

}
else if ( isSet ( $_GET [ACTION] ) && $_GET [ACTION] == ANNULER_SUPPRIMER )	
{
	// L'utilisateur vient d'annuler sa demande de suppression, on lui confirme que �a a bien �t� annul�
	confirme_annulation_demande_suppression () ; 
}
// =============================
// SUPPRESSION ELEMENT MENU
// =============================
else if ( isSet ( $suppression_element_menu_questionnaire ) ) 
	// on vient de supprimer un �l�ment du questionnaire, on affiche le message de confirmation de cette supresssion
	echo $message_suppression_element_menu_questionnaire ; 
// =============================
// ACCUEIL
// =============================
else
{
	// on n'a plus qu'� affiche la page d'accueil
	echo "<div id='texte'> <!-- d�but de la boite de texte -->\n\n" ; 
	require ( "textes/fr/generique/accueil.html" ) ; 
	echo "\n\n</div> <!-- fin de la boite de texte -->\n\n" ; 
}

// affichage d'une page du questionnaire
if ( $url_ici_menu_questionnaire )
{
	// �a se produit dans trois cas : 
	// - lien direct dans le menu ou les fl�ches de navigation
	// - ajout d'un �l�ment ou usage
	// - validation d'une page par $_POST
	// 
	// on r�cup�re les fonctions n�cessaires
	//require_once ('inc_questionnaire/fonctions_questionnaire.php') ; 
	// on doit d�terminer la page pour transmettre le bon fichier texte
	//echo $url_ici_menu_questionnaire ; 
	$url_ici_menu_questionnaire_decodee = decoder_url ( $url_ici_menu_questionnaire ) ; 
	// la fonction se trouve dans inc_questionnaire/fonctions_menu_questionnaire_affichage
	//echo "<pre>" ; print_r ( $url_ici_menu_questionnaire_decodee ) ; echo "</pre>" ; 
	afficher_page_questionnaire ( $url_ici_menu_questionnaire , $url_ici_menu_questionnaire_decodee , $liste_page , $TEXT['menu_questionnaire'] , $TEXT['questionnaire'] , $TEXT[$url_ici_menu_questionnaire_decodee[PAGE]] , $connexion ) ; 
}
//=======================================================================================================================================
// Pied de page
//=======================================================================================================================================
afficher_pied_de_page ( $TEXT['pied_de_page'] ) ; 
?>

