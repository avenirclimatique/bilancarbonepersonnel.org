<?php 
//==========================================================================================
// En-tete
//==========================================================================================
function afficher_en_tete ( $TEXT_EN_TETE , $TEXT_MENU , $page_generique , $connexion ) 
{
	
	/*
	// ===============================================
	// si on est sur la page d'accueil, renvoi éventuel sur la même page pour tester l'activation des cookies
	if ( $page_generique == ACCUEIL && !isSet ( $_GET['second_appel_test_cookies'] ) ) 
		// on est sur la page d'accueil, du coup on teste si c'est le second appel ou non
		// ce n'est pas le second appel, du coup on renvoie vers la page d'accueil de manière à tester si cookie ou non
		header( "Location: 
			http://" . $_SERVER['HTTP_HOST']
									 . rtrim ( dirname ( $_SERVER['PHP_SELF'] ) , '/\\' )
									 . "/index.php?second_appel_test_cookies=oui" )	;
	// ===============================================
	*/
	
	
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> ' . "\n\n"  
		. '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="fr">' . "\n\n" 
		. '<head>		<!-- début de la balise head --> ' . "\n\n" ; 
	//
	echo "<title>" ; 
	afficher_titre_page_html ( $TEXT_EN_TETE , $TEXT_MENU , $connexion ) ; 
	echo "</title>\n\n" ; 
	//
	echo '<meta name="google-site-verification" content="wM2rhZOAWqgmmwMY9qDPW6e_V-hrZrCpvfZh-scHyBo" />' . "\n\n" ; 
	//
	echo '<meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>' . "\n\n" ; 
	//
	echo '<link href="style.css" rel="stylesheet" type="text/css"/>' . "\n"
		. "<link href='style_resultat.css' rel='stylesheet' type='text/css' title='style page resultats'/>\n"
		. "<link href='style_textes.css' rel='stylesheet' type='text/css' title='style page resultats'/>\n\n" ; 
	//
	echo "<link rel='shortcut icon' href='img/logo_ac.ico' />   <!-- pour navigateurs type IE -->\n\n" ; 
  echo "<link rel='icon' type='image/jpg' href='./img/favicon.jpg' />  <!-- pour navigateurs modernes -->\n" ; 
	//
	echo "</head>   <!-- fin de la balise head --> \n\n" ; 
	//
	echo "<body>  <!-- début de la balise body --> \n\n" ; 
	//
	echo "<div id='contour'>  <!-- début de la balise contour --> \n\n" ; 
	//
	lien_acces_menu_contenu_non_voyants () ; 
	//
	//echo "<div id='contenu'>  <!-- début de la balise contenu --> \n\n" ; 	
}
//==========================================================================================
// Afficher le titre de la page
//==========================================================================================
function afficher_titre_page_html ( $TEXT_EN_TETE , $TEXT_MENU , $connexion )
{
	afficher_texte ( 'titre' , $TEXT_EN_TETE ) ;
	if ( isSet ( $_GET['page'] ) ) 
		$page = $_GET['page'] ; 	
	if ( isSet ( $_GET['type_page'] ) ) 
	{
		echo " - " ; 
		$type_page = $_GET['type_page'] ; 
		if ( $type_page == GENERIQUE )
			afficher_texte ( $page , $TEXT_EN_TETE ) ; 
		else if ( $type_page != QUESTIONNAIRE )
			afficher_texte ( $type_page , $TEXT_EN_TETE ) ; 
		if ( $type_page == FAQ )
		{
			echo " - " ; 
			require ( "textes/" . $_SESSION[LANGUE] . "/faq/faq_" . $_SESSION[LANGUE] . ".php" ) ; 
			$longueur_max = 60 ; 
			if ( strlen ( $TEXT[$page] ) <= $longueur_max )
				echo $TEXT[$page] ; 
			else
				echo substr ( $TEXT[$page] , 0 , $longueur_max ) . " ..." ; 
			//echo $_SESSION[LANGUE] ; 
		}
		else if ( $type_page == QUESTIONNAIRE )
		{
			$url_decodee = decoder_url ( $_GET[PAGE] ) ; // la fonction se trouve dans inc/fonctions_generales.php
			$donnees_page = exec_requete ( "SELECT rub_nom , rub_est_repetee , page_nom FROM t_page , t_rubrique WHERE page_nom = '" . $url_decodee[PAGE] . "' AND page_rub_id = rub_id" , $connexion ) ; 
			$objet_page = objet_suivant ( $donnees_page ) ; 
			echo titre_page_questionnaire ( $url_decodee , $objet_page , $TEXT_MENU ) ; // la fonction se trouve dans inc_questionnaire/fonctions_questionnaire.php
		}
	}
}
//==========================================================================================
// Liens pour accès rapide aux menus et au contenu pour non voyants
//==========================================================================================
function lien_acces_menu_contenu_non_voyants () 
{
	echo "<ul id='menu_evitement_non_voyants'>  <!-- pour lecteur d'écran destiné aux non voyants -->\n" 
		. "<li><a href='#principal' >Aller directement au contenu de la page sans parcourir les menus</a></li>"
		. "<li><a href='#menu_questionnaire' class='lien_evitement' >Aller directement au menu d'accès aux pages du questionnaire</a></li>"
		. "<li><a href='#menu_hors_questionnaire' class='lien_evitement' >Aller directement au menu général sans afficher le bandeau titre</a></li>"
		. "</ul>\n\n" ; 
}
//==========================================================================================
// Lien pour l'accès aux menus après message sans action
//==========================================================================================
function lien_retour_menu_page_sans_action_non_voyants () 
{
	echo "<ul class='acces_menu_non_voyants'>  <!-- pour lecteur d'écran destiné aux non voyants -->\n" 
		. "<li><a href='#menu_questionnaire' class='lien_evitement' >Aller au menu d'accès aux pages du questionnaire</a></li>"
		. "<li><a href='#menu_hors_questionnaire' class='lien_evitement' >Aller au menu général</a></li>"
		. "</ul>\n\n" ; 	
}
//==========================================================================================
// Bandeau titre
//==========================================================================================
function afficher_bandeau_titre ( $TEXT , $connexion ) 
{
  echo "<div id='titre'>		<!-- début de la boite 'titre' -->\n\n" ; 
	// 
  echo 
	// ================================= VERSION AVEC LOGO INSA ===================================
    "<a href='http://www.manicore.com/' title='Cabinet de conseil Manicore (Jean-Marc Jancovici)' >\n"
    . "<img alt='Manicore' src='./img/logo_manicore_44.jpg' width='43' height='44' "
		. "/>\n"
    . "<span class='nom'>Manicore</span>\n</a>\n\n" 
		//
    . "<a href='http://www.insa-lyon.fr/' title='INSA de Lyon'>\n"
    . "<img alt='INSA de Lyon' src='./img/logo_insa_lyon_44.png' width='75' height='44' "
		. "/>\n"
    . "<span class='nom'>INSA de Lyon</span>\n</a>\n\n"  
		//
    . "<a href='http://www.avenirclimatique.org' title='Association Avenir Climatique'>\n"
    . "<img alt='Avenir Climatique' src='./img/logo_ac_44.jpg' width='66' height='44' "
		. "/>\n"
    . "<span class='nom'>Avenir Climatique</span>\n</a>\n\n"  
		//
		. "<a href='http://www.ademe.fr' title='Agence de l’Environement et de la Ma&icirc;trise de l’Energie' >\n"
    . "<img alt='ADEME' src='./img/logo_ademe_44.jpg' width='40' height='44' "
		. "/>\n"
    . "<span class='nom'>ADEME</span>\n</a>\n\n" ;
	// ================================= VERSION SANS LOGO INSA ===================================
	/*
		"<a href='http://www.ademe.fr' title='Agence de l’Environement et de la Ma&icirc;trise de l’Energie' >\n"
    . "<img alt='ADEME' src='./img/logo_ademe.jpg' "//width='40' height='40' 
		. "/>\n"
    . "<span class='nom'>ADEME</span>\n</a>\n\n"
		//
    . "<a href='http://www.manicore.com/' title='Cabinet de conseil Manicore (Jean-Marc Jancovici)' >\n"
    . "<img alt='Manicore' src='./img/logo_manicore.jpg' " //width='40' height='40' 
		. "/>\n"
    . "<span class='nom'>Manicore</span>\n</a>\n\n" 
		//		//
    . "<a href='http://www.avenirclimatique.org' title='Association Avenir Climatique'>\n"
    . "<img alt='Avenir Climatique' src='./img/logo_ac.jpg'" // width='60' height='40' "
		. "/>\n"
    . "<span class='nom'>Avenir Climatique</span>\n</a>\n\n" ;
	*/
	// =================================  ===================================
	echo "<h1>" ; 
	afficher_texte('titre',$TEXT);
	echo "</h1>\n\n" ;
	// 
	echo "<p>Version B&ecirc;ta " . nom_version ( $connexion ) . ". Cet outil est en cours de finalisation, merci de votre compr&eacute;hension.</p>\n\n" ; 
	echo "<div class='separateur'></div>\n\n" ; 
	//
  echo "</div>   <!-- fin de la boite 'titre' -->\n\n" ; 
}
//==========================================================================================
// Menu général
//==========================================================================================
function afficher_menu_general ( $util_id , $TEXT , $page_generique , $connexion ) 
{
	// echo "<div id='contour_menu_hors_questionnaire'> <!-- début de la boite 'contour_menu_hors_questionnaire' -->\n\n" ; 
	echo "<div id='menu_hors_questionnaire'> <!-- début de la boite 'menu_hors_questionnaire' -->\n\n" ; 
	//
	$liste_page_generique = array ( ACCUEIL , PRESENTATION , MENU_FAQ , LIEN , REMERCIEMENT, NOUVEAUTE  ) ; 
	//============
	$TEXT[ACCUEIL] = "Accueil" ; 
	$TEXT[PRESENTATION] = "Mode d'emploi" ; 
	$TEXT[NOUVEAUTE] = "Nouveaut&eacute;s" ;
	$TEXT[MENU_FAQ] = "FAQ" ; 
	$TEXT[LIEN] = "Liens" ; 
	$TEXT[REMERCIEMENT] = "Remerciements" ; 
	//============
	echo "<ul id='menu_general'> <!-- début de la boite 'menu_general' -->\n" ; 
	foreach ( $liste_page_generique as $nom_page_generique )
	{
		echo "<li>\n" ; 
		if ( $page_generique == $nom_page_generique )
			//echo "<a href='./index.php?type_page=" . GENERIQUE . "&amp;page=" . $page_generique . "'>" . $TEXT[$page_generique] . "</a>\n" ; 
			echo "<span class='ici_page_generique' >" . $TEXT[$nom_page_generique] . "</span>\n" ; 
		else
		{
			echo "<a href='./index.php?type_page=" . GENERIQUE . "&amp;page=" . $nom_page_generique ;
			if ( $nom_page_generique == MENU_FAQ )
				echo "' title='Questions fréquemment posées" ; 
			echo "' >" . $TEXT[$nom_page_generique] . "</a>\n" ; 
		}
		echo "</li>\n" ; 
	}
	echo "</ul> <!-- fin de la boite 'menu_general' -->\n\n" ; 
	//=================================================
	// menu gestion compte
	echo "<ul id='gestion_compte'> <!-- début de la boite 'gestion_compte' -->\n" ; 
	//
	// remise à zéro
	if ( isSEt ( $_GET['type_page'] ) && $_GET['type_page'] == GESTION_COMPTE && isSEt ( $_GET['page'] ) && 
		( $_GET['page'] == REMETTRE_A_ZERO || $_GET['page'] == CONFIRMER_REMETTRE_A_ZERO ) )
		// si on a annulé la remise à zéro on affiche le lien ! 
		echo "<li>\n<span class='ici_page_generique' >Remettre &agrave; z&eacute;ro</span></li>\n" ; 
	else
		echo "<li>\n<a href='./index.php?type_page=" . GESTION_COMPTE . "&amp;page=" . REMETTRE_A_ZERO . "' "
		//. "title='Recommencer &agrave; z&eacute;ro le calcul de votre Bilan Carbone Personnel'
		. ">Remettre &agrave; z&eacute;ro</a>\n</li>\n" ; 
	
	if ( AFFICHE_MENU_SAUVEGARDE == 'oui' )
	{
		if ( !$util_id ) 
		{
			// l'utilisateur n'est pas identifié : Onglet Se connecter
			if 
			( 
				( isSEt ( $_GET['type_page'] ) && $_GET['type_page'] == GESTION_COMPTE && isSEt ( $_GET['page'] ) && 
				( $_GET['page'] == SE_CONNECTER || $_GET['page'] == CREER_COMPTE || $_GET['page'] == S_IDENTIFIER 
				|| $_GET['page'] == DEMANDE_NOUVEAU_PASS ) )
				|| isSet ( $_POST [POST_SAISIE_COURRIEL_CREATION_COMPTE] ) || isSet ( $_POST [POST_SAISIE_COURRIEL_IDENTIFICATION] )
				|| isSet ( $_POST [POST_SAISIE_PASS_CREATION_COMPTE] ) || isSet ( $_POST [POST_SAISIE_PASS_IDENTIFICATION] )
			)
				echo "<li>\n<span class='ici_page_generique' >Se connecter</span></li>\n" ; 
			else
				echo "<li>\n<a href='./index.php?type_page=" . GESTION_COMPTE . "&amp;page=" . SE_CONNECTER . "' "
					//. "title='Créer un compte ou se connecter à un compte existant' "
					. ">Se connecter</a>\n</li>\n" ; 
		}
		else 
		{
			// l'utilisateur est identifié 
			//
			//Onglet se déconnecter
			if ( isSEt ( $_GET['type_page'] ) && $_GET['type_page'] == GESTION_COMPTE && isSEt ( $_GET['page'] ) && 
				( $_GET['page'] == SE_DECONNECTER 
				//|| $_GET['page'] == CONFIRMER_SE_DECONNECTER 
				//|| $_GET['page'] == ANNULER_SE_DECONNECTER
				)	)
				echo "<li>\n<span class='ici_page_generique' >Me déconnecter</span></li>\n" ; 
			else
				echo "<li>\n<a href='./index.php?type_page=" . GESTION_COMPTE . "&amp;page=" .  SE_DECONNECTER . "' "
				//. "title='Se déconnecter' "
				. ">Me déconnecter</a>\n</li>\n" ; 
			// Onglet Mes sauvegardes
			if ( ( isSEt ( $_GET['type_page'] ) && $_GET['type_page'] == GESTION_COMPTE && isSEt ( $_GET['page'] ) && 
					( $_GET['page'] == MENU_SAUVEGARDE 
					|| $_GET['page'] == RENOMMER_SAUVEGARDE 
					//
					|| $_GET['page'] == CHARGER_SAUVEGARDE 
					//|| $_GET['page'] == CONFIRMER_CHARGER_SAUVEGARDE 
					//|| $_GET['page'] == ANNULER_CHARGER_SAUVEGARDE 
					//
					|| $_GET['page'] == SUPPRIMER_SAUVEGARDE 
					//|| $_GET['page'] == CONFIRMER_SUPPRIMER_SAUVEGARDE 
					//|| $_GET['page'] == ANNULER_SUPPRIMER_SAUVEGARDE 
					
					) ) || isSet ( $_POST [POST_RENOMMER_SAUVEGARDE] ) )
				echo "<li>\n<span class='ici_page_generique' >Mes sauvegardes</span></li>\n" ; 			
			else
				echo "<li>\n<a href='./index.php?type_page=" . GESTION_COMPTE . "&amp;page=" .  MENU_SAUVEGARDE . "&amp;util_id=" . $util_id. "' "
					//. "title='Accéder à mes sauvegardes' "
					. ">Mes sauvegardes</a>\n</li>\n" ; 
			// Onglet Sauvegarder
			if ( ( isSEt ( $_GET['type_page'] ) && $_GET['type_page'] == GESTION_COMPTE && isSEt ( $_GET['page'] ) && 
					( $_GET['page'] == NOUVELLE_SAUVEGARDE 
					|| $_GET['page'] == SAUVEGARDER 
					// || $_GET['page'] == ANNULER_SAUVEGARDER 
					|| $_GET['page'] == CONFIRMER_SAUVEGARDER 
					) ) 
					|| isSet ( $_POST [POST_NOUVELLE_SAUVEGARDE] ) 
					)
				echo "<li>\n<span class='ici_page_generique' >Sauvegarder</span></li>\n" ; 
			else if ( isSet ( $_SESSION[EST_SAISIE_EFFECTUEE] ) )
				echo "<li>\n<a href='./index.php?type_page=" . GESTION_COMPTE . "&amp;page=" .  SAUVEGARDER . "&amp;util_id=" . $util_id. "' " 
					//. "title='Sauvegarder mes saisies'"
					. ">Sauvegarder</a>\n</li>\n" ; 
			else 
				// pas de saisie effectuée
				echo "<li>\n<span class='sauvegarde_inutile'>Sauvegarder</span>\n</li>\n" ; 
		}
	} // fin de la boucle de conditionnalité d'affichage du menu de sauvegarde
	echo "</ul>  <!-- fin de la boite 'gestion_compte' --> \n\n" ; 
		
	// ===========================================================
	// Menu admin si on est identifié comme admin
	// ===========================================================	
	if( isSet ( $_SESSION[MODE_ADMIN] ) && $_SESSION[MODE_ADMIN] == true)
	{
		echo "<div class='separateur'></div> \n\n" ; 
		echo "<ul id='admin'> <!-- début de la boite 'admin' -->\n"
			. "<li>\n<a href='./index.php?type_page=" . ADMIN . "&amp;page=" . VARIABLE . "' >Variables et facteurs d'&eacute;mission</a>\n</li>\n" 
			//. "<li><a href='./index.php?type_page=" . ADMIN . "&amp;page=" . TEST_FORMULE . "' >Test formules</a></li>\n" 
			//. "<li><a href='./index.php?type_page=" . ADMIN . "&amp;page=" . STATISTIQUE . "' >Statistiques</a></li>\n" 
			. "<li><a href='./index.php?action=deconnexion_admin' >Quitter le mode admin</a></li>\n" 
			. "</ul> <!-- fin de la boite 'admin' -->\n\n" ; 
		//
	}
	// ===========================================================
	// Informations : est-on connecté ou pas, etc
	// ===========================================================	
	
	if ( AFFICHE_MENU_SAUVEGARDE == 'oui' )
	{
		if ( isSet ( $_SESSION[UTIL_ID] ) )
		{
			// l'utilisateur est identifié
			echo "<div class='separateur'></div>\n\n" ; 
			echo "<p id='information'>\n"
				. "Vous êtes identifié sous l'adresse courriel &quot;" . $_SESSION[UTIL_COURRIEL] . "&quot;." ; 
			if ( isSet ( $_SESSION[SAUV_ID] ) )
				echo " Votre saisie actuelle est intitulée : &quot;" . nom_sauvegarde ( $connexion ) . "&quot;." ; 
			echo "</p>\n\n" ; 
		}
		else
		{
			// l'utilisateur n'est pas identifié
			echo "<div class='separateur_droit'></div>\n\n" ; 
			echo "<p id='information'>\n"
				. "Vous n'êtes pas identifié.\n"
				. "</p>\n\n" ; 
		}
	}	
	// ===========================================================
	echo "<div class='separateur_droit'></div> \n\n" ; 
	//
	echo "</div>  <!-- fin de la boite 'menu_hors_questionnaire' -->\n\n" ; // menu_hors_questionnaire
	// echo "</div>  <!-- fin de la boite 'contour_menu_hors_questionnaire' -->\n\n" ; 
}
// ==============================================================================================================================
// Pied de page
// ==============================================================================================================================
function afficher_pied_de_page ( $TEXT )
{
	echo "\n</div>  <!-- fin de la boite id='principal' -->\n\n" ; 
	echo "<!-- ================================================================= -->\n\n" ; 
	//
	lien_retour_menu_page_sans_action_non_voyants ();
	//
	echo "<div id ='pied'> 		<!-- début de la boite pied de page --> \n\n"
		. "<ul>\n\n"	
		.	"<li>\n"
		. "Page valide <a href='http://validator.w3.org/check?uri=referer'>XHTML 1.0 strict</a> et "
		. "<a href='http://jigsaw.w3.org/css-validator/check/referer'>CSS 2</a>\n"
		. "</li>\n\n" ; 
	//
	echo "<li>\n"
		. "<strong>contact</strong> : contact <span class='italic'>at</span> bilancarbonepersonnel.org\n"
		. "</li>\n\n" 
		//.	"<li>\n"
		//. "<strong>webmestre</strong> : webmestre <span class='italic'>at</span> bilancarbonepersonnel.org\n"
		//. "</li>\n\n"
		. "</ul>\n\n" ; 
	//
	echo "</div> <!-- fin de la boite id='pied' -->\n\n"
		. "<!-- ======================================================================== -->\n\n"
		// . "</div> <!-- fin de la boite 'contenu' -->\n\n" 
		. "</div> <!-- fin de la boite 'contour' -->\n\n" ; 

	?>
	<!-- google analytics -->

	<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
	</script>
	<script type="text/javascript">
	_uacct = "UA-2867678-1";
	urchinTracker();
	</script>

	<!-- google analytics -->
	<?php
	echo "\n\n</body>\n\n"
		. "</html>" ; 
}
?>