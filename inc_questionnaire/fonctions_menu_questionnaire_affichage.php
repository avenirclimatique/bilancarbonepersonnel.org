<?php 
//==========================================================================================
// Afficher menu questionnaire
//==========================================================================================
function afficher_menu_questionnaire ( $url_actuelle , $TEXT , $connexion ) 
{
	// ===================
	// initialiser le tableau $liste_page (qui sert ensuite à aller d'une page à la suivante ou à la précédente)
	$liste_page = array () ; 
	// ===================
	// ouverture de la boite
	$url_actuelle = decoder_url ( $url_actuelle ) ; // la fonction se trouve dans inc/fonctions_generales.php
	// ===================
	// ouverture de la boite
	echo "<ul id='menu_questionnaire'> <!-- début de la boite menu_questionnaire -->\n\n" ; 
	// ===================
	// affichage de l'onglet 'Résultats'
	afficher_onglet_resutlat () ; 
	// ===================
	$donnees_rubrique = exec_requete ( "SELECT rub_id , rub_nom , rub_est_repetee FROM t_rubrique ORDER BY rub_ordre" , $connexion ) ;  
	while ( $objet_rubrique = objet_suivant ( $donnees_rubrique ) )
	{
		echo "<li>   <!-- debut item de liste d'une categorie d'emission -->\n" ; 	
		echo "<div class='titre'>" ; 
		//===============================================================================
		// on affiche le lien éventuel vers le fichier d'explication de la rubrique
		$rub_id = $objet_rubrique->rub_id ; // agréable pour aller ensuite chercher les pages de la rubrique
		$rub_nom = $objet_rubrique->rub_nom ; 
		if ( $lien_explication = lien_explication ( $rub_nom , RUBRIQUE , $url_actuelle ) ) 
			echo $lien_explication ;  // la fonction lien_explication se trouve dans le fichier fonctions_affichage_explications.php
		// ==========================================
		// nom de la rubrique
		echo "<strong class='nom'>" ; 
		afficher_texte( $rub_nom , $TEXT ); 
		echo "</strong>" ; 
		echo "</div>\n" ; 
		// ==========================================
		echo "<ul>  <!-- début de liste de pages -->\n" ; 
		if ( $objet_rubrique->rub_est_repetee == 'true' )
		{
			for ( $rub_numero = 1 ; $rub_numero <= $_SESSION[MENU_NOMBRE][$rub_nom] ; $rub_numero++ )
			{
				echo "<li>" ; 
				if ( $rub_numero > 1 )
					echo lien_supprimer ( $rub_nom , $rub_numero , RUBRIQUE ) ; 
				echo "<strong>" ; 
				afficher_texte( $rub_nom , $TEXT ); 
				echo " " . $rub_numero . "</strong></li>\n" ; 
				// =======
				$liste_page = afficher_menu_rubrique ( $rub_id , $rub_numero , $url_actuelle , $liste_page , $TEXT , $connexion ) ; 
			}
		}
		else
		{
			$liste_page = afficher_menu_rubrique ( $rub_id , false , $url_actuelle , $liste_page , $TEXT , $connexion ) ; 
		}
		echo "</ul>  <!-- fin de liste de pages -->\n" ; 
		echo "</li>   <!-- fin d'item de liste -->\n\n" ; 
		// ==========================================
		// onglet "ajouter" pour la rubrique (en l'occurrence le logement)
		if ( $objet_rubrique->rub_est_repetee == 'true' )
		{
			echo "<li>\n"
				. "<a class='ajouter' href='index.php?" . TYPE_PAGE . "=" . QUESTIONNAIRE . "&amp;" . ACTION . "=" . AJOUTER 
				. "&amp;" . RUBRIQUE . "=" . $rub_nom . "'>" ; 
			afficher_texte( AJOUTER . '_' . $rub_nom , $TEXT );
			echo "</a>\n</li>\n\n" ; 
		}	
	}
	// ===================
	// affichage de l'onglet 'Résultats'
	afficher_onglet_resutlat () ; 
	// ===================
	// fermeture de la boite
	echo "</ul>  <!-- fin de la boite menu_questionnaire -->\n\n" ; 
	return $liste_page ; 
}
//==========================================================================================
// Afficher le menu d'une rubrique
//==========================================================================================
function afficher_menu_rubrique ( $rub_id , $rub_numero , $url_actuelle , $liste_page , $TEXT , $connexion )
{
	$donnees_page = exec_requete ( "SELECT page_nom , page_est_repetee FROM t_page WHERE page_rub_id = $rub_id ORDER BY page_ordre" , $connexion ) ; 
	while ( $objet_page = objet_suivant ( $donnees_page ) )
	{
		$page_nom = $objet_page->page_nom ; 
		if ( $objet_page->page_est_repetee == 'true' )
		{
			for ( $page_numero = 1 ; $page_numero <= $_SESSION[MENU_NOMBRE][$page_nom] ; $page_numero++ )
			{
				$liste_page[] = afficher_menu_onglet 
					( $page_nom , $rub_numero , $page_numero , false , $url_actuelle , $TEXT , $connexion ) ;
			}
			// affichage de l'onglet "Ajouter..." 
			afficher_menu_onglet 
				( $page_nom , $rub_numero , false , true , $url_actuelle , $TEXT , $connexion ) ;
		}
		else
			$liste_page[] = afficher_menu_onglet 
				( $page_nom , $rub_numero , false , false , $url_actuelle , $TEXT , $connexion ) ;
	}
	return $liste_page ; 
}
//==========================================================================================
// Afficher un onglet du menu
//==========================================================================================
function afficher_menu_onglet ( $page_nom , $rub_numero , $page_numero , $est_ajouter_onglet , $url_actuelle , $TEXT , $connexion )
{
	echo "<li>\n" ; 
	// ======================
	// si c'est un ajout ou si la page n'est pas numérotée on affiche le lien vers le fichier d'explications si celui-ci existe 
	if ( ( $est_ajouter_onglet || !$page_numero ) && ( !$rub_numero || $rub_numero == 1 ) ) 
	// dans le cas du logement, on n'affiche les liens vers les fichiers d'explications que pour le premier logement
		if ( $lien_explication = lien_explication ( $page_nom , PAGE , $url_actuelle ) ) 
		// la fonction lien_explication se trouve dans le fichier fonctions_affichage_explications.php
			echo $lien_explication . " <!-- lien vers fichier d'explication associé à la page --> \n";
	// ======================
	// si c'est une page numérotée et que ce n'est pas la première page du logement on affiche le lien qui permet de supprimer la page en question
	if ( $page_numero )
		echo lien_supprimer ( $page_nom , $page_numero , PAGE ) ; 
	// ======================
	// si ce n'est pas un ajout on affiche l'icône indiquant si la page est complète ou non
	if ( !$est_ajouter_onglet )
	{
		afficher_completude_page ( $page_nom , $rub_numero , $page_numero ) ; 
		$nom_classe = 'nom' ; 
	}
	else
		$nom_classe = 'ajouter' ; 
	// ======================
	// Si on se trouve sur cette page on affiche une petie flèche devant le lien
	if ( ( $page_nom == $url_actuelle[PAGE] ) && ( ($page_numero == $url_actuelle[NUMERO] ) || ( $rub_numero == $url_actuelle[NUMERO] ) ) )
		afficher_ici_page () ; 
	// ======================
	// et enfin on affiche le lien vers la page !
	echo "<a class='" . $nom_classe . "' href='index.php?" . TYPE_PAGE . "=" . QUESTIONNAIRE ;
	if ( $est_ajouter_onglet ) 
		echo "&amp;" . ACTION . "=" . AJOUTER ; 
	// ======================
	// On détermine l'url 
	$url = $page_nom ; 
	if ( $rub_numero )
		$url = $url . "%" . $rub_numero ; 
	if ( $page_numero )
		$url = $url . "%" . $page_numero ; 
	// ======================
	echo "&amp;" . PAGE . "=" . $url ; 
	echo "' >  <!-- lien vers la page --> \n" ; 
	if ( !$est_ajouter_onglet ) 
		afficher_texte( $page_nom , $TEXT );
	else
		afficher_texte( AJOUTER . "_" . $page_nom , $TEXT ) ;
	echo "</a></li>\n" ; 
	return $url ; // pour $liste_page 
}
// ==============================================================================================================================
// Affichage de l'onglet 'Résultats'
// ==============================================================================================================================
function afficher_onglet_resutlat ()
{
	echo "<li>\n<div class='titre_resultat'>\n" ; 
	if ( in_array ( false , $_SESSION[PAGE_COMPLETE] ) )
	{
		$nom_classe = "incomplet" ; 
		$message = 'saisie incomplète' ; 
	}
	else
	{
		$nom_classe = 'complet' ; 
		$message = 'saisie complète' ; 
	}
	echo "<a class='resultat_" . "$nom_classe" . "' href='index.php?type_page=" . PAGE_RESULTAT . "' title='Page de résultats' >" 
		. "R&eacute;sultats</a> <!-- lien vers la page de résultats --> \n" 
		. "<span class = 'saisie_" . "$nom_classe" . "' > (" . $message . ")</span>\n" 
		. "</div>\n</li>\n\n" ; 
	// on affiche l'icône indiquant si l'accès à la page de résultats est 
	//$nom_classe = afficher_completude_resultat () ; // affiche l'icône et retourne la classe pour feuille de style balise <a>
	//echo "  <!-- icone indiquant si la page est ou non complète -->\n" ; 
	//echo "<li><div class='titre_resultat'><a class='resultat' href='index.php?type_page=" . RESULTAT . "'>" ; 
}
// ==============================================================================================================================
// retourne l'url de la page actuelle si on est sur une des pages du questionnaire, false sinon
// ==============================================================================================================================
function url_page_actuelle ()
{
	$url = false ; 
	if ( isSet ( $_POST['valider_page'] ) )
		$url = $_POST['url'] ; 
	else if ( isSet ( $_GET['type_page'] ) && $_GET['type_page'] == QUESTIONNAIRE )
	{
		if ( !isSet ( $_GET['action'] ) )
			$url = $_GET['page'] ; 
		else
			// la variable $_GET['action'] est définie, elle vaut forcément 'ajouter' autrement dit on vient d'ajouter une page
			$url = $_GET['url'] ; 
	}
	return $url ; 
}
// ==============================================================================================================================
// Affichage des icônes 'page complète' et 'page incomplète'
// ==============================================================================================================================
function afficher_completude_page ( $page_nom , $rub_numero , $page_numero )
{
	if ( $rub_numero )
		$reference_page = $page_nom . '%' . $rub_numero ; 
	else if ( $page_numero )
		$reference_page = $page_nom . '%' . $page_numero ; 
	else
		$reference_page = $page_nom ; 
	if ( $_SESSION[PAGE_COMPLETE][$reference_page] == true ) 
				echo "<span class='page_complet' title='Page complète' >.</span>" ; 
		else 
			echo "<span class='page_incomplet' title='Page incomplète' >.</span>" ; 
	echo "  <!-- icone indiquant si la page est ou non complète -->\n" ; 
}
// ==============================================================================================================================
// Affiche la petite flèche indiquant où on est dans le questionnaire
// ==============================================================================================================================
function afficher_ici_page ()
{
	echo "<span class='ici_menu_questionnaire' title='Vous êtes ici' >&gt;</span>  <!-- flèche de position dans le menu du questionnaire -->\n" ; 
}
// ==============================================================================================================================
// Lien de suppression
// ==============================================================================================================================
function lien_supprimer ( $nom , $numero , $type_suppression )
{
	if ( $type_suppression == PAGE )	
		$title = 'Cliquez sur ce lien pour supprimer cette page' ; 
	else
		$title = 'Cliquez sur ce lien pour supprimer ce logement' ; 
	$lien = "<a href='index.php?action=" . SUPPRIMER ;
	if ( $type_suppression == PAGE )
		$lien = $lien . "&amp;" . PAGE . "=" ; 
	else
		$lien = $lien . "&amp;" . RUBRIQUE . "=" ; 
	$lien = $lien . $nom . '%' . $numero
		. "' class='lien_supprimer' "
		. " title='" . $title . "' >X</a>   <!-- lien permettant de supprimer la "
		. $type_suppression . " --> \n" ; 
	return $lien ; 
}
?>