<?php 
//==========================================================================================
// Initier les variables de session $_SESSION [MENU_NOMBRE]
//==========================================================================================
function initier_menu_questionnaire ( $connexion ) 
{
	$donnees_rubrique = exec_requete ( "SELECT rub_nom FROM t_rubrique WHERE rub_est_repetee = 'true' " , $connexion ) ; 
	while ( $objet_rubrique = objet_suivant ( $donnees_rubrique ) ) 
	{
		$rub_nom = $objet_rubrique->rub_nom ; 
		$_SESSION[MENU_NOMBRE][$rub_nom] = 1 ; // pour les rubriques, on démarre à 1
	}
	$donnees_page = exec_requete ( "SELECT page_nom FROM t_page WHERE page_est_repetee = 'true' " , $connexion ) ; 
	while ( $objet_page = objet_suivant ( $donnees_page ) ) 
	{
		$page_nom = $objet_page->page_nom ; 
		$_SESSION[MENU_NOMBRE][$page_nom] = 0 ; // pour les pages, on démarre à 0
	}
	affecter_completude_pages ( true , $connexion ) ; 
}
//==========================================================================================
// Affecter les variables de session $_SESSION [PAGE_COMPLETE] (à partir de $_SESSION [MENU_NOMBRE] et éventuellement à partir de la fonction 
//==========================================================================================
function affecter_completude_pages ( $est_remise_a_zero , $connexion ) 
{
	unset ( $_SESSION [PAGE_COMPLETE] ) ; 
	$donnees_page = exec_requete ( "SELECT page_nom , page_est_repetee , rub_nom , rub_est_repetee FROM t_page , t_rubrique WHERE page_rub_id = rub_id" , $connexion ) ; 
	while ( $objet_page = objet_suivant ( $donnees_page ) ) 
	{
		$page_nom = $objet_page->page_nom ; 
		if ( $objet_page->rub_est_repetee == 'true' )
		{
			// la rubrique est répétée
			$rub_nom = $objet_page->rub_nom ; 
			//echo "<p>" . $rub_nom . "</p>" ; 
			for ( $i=1 ; $i <= $_SESSION[MENU_NOMBRE][$rub_nom] ; $i++ )
			{
				if ( $est_remise_a_zero )
					$_SESSION [PAGE_COMPLETE][$page_nom . '%' . $i] = false ; 
				else
				{
					$diagnostic_page = diagnostic_page ( $page_nom , $i , false , $connexion ) ; 
					$_SESSION [PAGE_COMPLETE][$page_nom . '%' . $i] = $diagnostic_page['est_complete_page'] ; 
				}
			}
		}
		else if ( $objet_page->page_est_repetee == 'true' )
		{
			// la page est répétée
			for ( $i=1 ; $i <= $_SESSION[MENU_NOMBRE][$page_nom] ; $i++ )
			{
				if ( $est_remise_a_zero )
					$_SESSION [PAGE_COMPLETE][$page_nom . '%' . $i] = false ; 
				else
				{
					$diagnostic_page = diagnostic_page ( $page_nom , $i , false , $connexion ) ; 
					$_SESSION [PAGE_COMPLETE][$page_nom . '%' . $i] = $diagnostic_page['est_complete_page'] ; 
				}
			}
		}
		else
		{
			// la page n'est pas répétée
			if ( $est_remise_a_zero )
				$_SESSION [PAGE_COMPLETE][$page_nom] = false ; 
			else
			{
				$diagnostic_page = diagnostic_page ( $page_nom , false , false , $connexion ) ; 
				$_SESSION [PAGE_COMPLETE][$page_nom] = $diagnostic_page['est_complete_page'] ; 
			}
		}
	}
	//echo "<pre>" ; print_r ( $_SESSION[PAGE_COMPLETE] ) ; echo "</pre>" ; 
}
//==========================================================================================
// Ajouter un élément au menu du questionnaire
//==========================================================================================
function ajouter_au_menu_questionnaire ( $connexion ) 
{
	if ( isSet ( $_GET[RUBRIQUE] ) )
	{
		//==============================
		// c'est une rubrique qu'il s'agit d'ajouter
		$rubrique = $_GET[RUBRIQUE] ; 
		$_SESSION[MENU_NOMBRE][$rubrique] += 1 ; 
		$donnees_page = exec_requete ( "SELECT page_nom , rub_nom FROM t_page , t_rubrique WHERE page_rub_id = rub_id AND rub_nom='$rubrique' ORDER BY page_ordre" , $connexion ) ; 
		$page_et_numero = false ; 
		while ( $objet_page = objet_suivant ( $donnees_page ) )
		{
			$url = $objet_page->page_nom . '%' . $_SESSION[MENU_NOMBRE][$rubrique] ; 
			$_SESSION[PAGE_COMPLETE][$url] = false ; 
			if ( !$page_et_numero )
				$page_et_numero = $url ; 
		}
	}
	else
	{
	//==============================
		// c'est une page qu'il s'agit d'ajouter
		$page = $_GET[PAGE] ;
		$_SESSION[MENU_NOMBRE][$page] += 1 ; 
		$page_et_numero = $page . '%' . $_SESSION[MENU_NOMBRE][$page] ; 
		$_SESSION[PAGE_COMPLETE][$page_et_numero] = false ; 
	}
	return $page_et_numero ; 
}
//==========================================================================================
// Gestion d'une première demande de suppression d'une page ou d'une catégorie du questionnaire
//==========================================================================================
function demande_confirmation_suppression ( )
{
	if ( isSet ( $_GET[RUBRIQUE] ) )
	{
		$texte_element_a_supprimer = 'ce logement' ; 
		$renvoi = "&amp;" . RUBRIQUE . "=" . $_GET[RUBRIQUE] ; 
	}
	else
	{
		$texte_element_a_supprimer = 'cette page' ; 
		$renvoi = "&amp;" . PAGE . "=" . $_GET[PAGE] ; 
	}
	echo "<p><strong>Etes-vous sûr de vouloir supprimer " . $texte_element_a_supprimer . "&nbsp?</strong> " 
		. "Si vous confirmez cette demande, toutes vos saisies concernant " . $texte_element_a_supprimer . " seront détruites.</p>\n \n " ; 
	$url_confirmation = "./index.php?action=" . CONFIRMER_SUPPRIMER . $renvoi ; 
	$url_annulation = "./index.php?action=" . ANNULER_SUPPRIMER . $renvoi ; 
	demande_confirmation ( 'Confirmer' , $url_confirmation , 'Annuler' , $url_annulation ) ; 
	// la fonction  demande_confirmation se trouve dans fonctions_generales.php
}
//==========================================================================================
// Gestion d'une première demande de suppression d'une page ou d'une catégorie du questionnaire
//==========================================================================================
function confirme_annulation_demande_suppression ()
{
	echo "<p>Suite à cette annulation <strong>votre demande de suppression n'a pas été prise en compte</strong>. 
	Vous pouvez poursuivre le calcul de votre
	Bilan Carbone Personnel en accédant à la page souhaitée à l'aide du menu ci-contre.</p>" ; 
}
//==========================================================================================
// Gestion d'une suppression confirmée d'une page ou d'une catégorie du questionnaire
//==========================================================================================
function suppression_element_menu_questionnaire ( $connexion )
{
	if ( isSet ( $_GET [RUBRIQUE] ) ) 
	{
		// c'est une rubrique (en l'occurrence le logement)
		$str = explode ( '%' , $_GET [RUBRIQUE] ) ; 
		$numero_supprime = $str[1] ; 
		$rubrique = $str[0] ; 
		// =============
		// tests pour tester la validité de la manoeuvre
		$is_suppression_valide = true ; // optimiste
		if ( $_SESSION[MENU_NOMBRE][$rubrique] == 1 )
			$is_suppression_valide = false ; 
		// =============
		// on décale tout de 2
		if ( $is_suppression_valide )
		{
			//================================================================
			// plus difficile : mise à jour des variables de $_SESSION[REPONSE] et $_SESSION [PAGE_COMPLETE]
			mettre_a_jour_reponse_suppression_logement ( $numero_supprime , $connexion ) ; 
			// et on diminue de 1 la variable $_SESSION[MENU_NOMBRE]
			$_SESSION[MENU_NOMBRE][$rubrique] -= 1 ; 
		}
	}
	else
	{
		// c'est une page numérotée...
		$str = explode ( '%' , $_GET [PAGE] ) ; 
		$numero_supprime = $str[1] ; 
		$page = $str[0] ; 
		// =============
		// tests pour tester la validité de la manoeuvre
		$is_suppression_valide = true ; // optimiste
		if ( $_SESSION[MENU_NOMBRE][$page] == 0 )
			$is_suppression_valide = false ;
		if ( $is_suppression_valide )
		{
			// ===================
			// mise à jour de $_SESSION [PAGE_COMPLETE]
			for ( $j = $numero_supprime ; $j <= $_SESSION[MENU_NOMBRE][$page] - 1 ; $j++ )
			{
				$k = $j+1 ; 
				$_SESSION [PAGE_COMPLETE][$page . '%' . $j] = $_SESSION [PAGE_COMPLETE][$page . '%' . $k] ; 
			}
			// on fait les choses proprement : on supprime la variable $_SESSION [PAGE_COMPLETE] obsolète
			unset ( $_SESSION [PAGE_COMPLETE][$page . '%' . $_SESSION[MENU_NOMBRE][$page]] ) ; 
			//================================================================
			// Le plus difficile : mise à jour des variables de $_SESSION[REPONSE]
			mettre_a_jour_reponse_suppression_page ( $page , $numero_supprime , $connexion ) ; 
			// et on met à jour la variable $_SESSION[MENU_NOMBRE] pour cette page
			$_SESSION[MENU_NOMBRE][$page] -= 1 ; 
		}
	}
	// echo "<pre>" ; print_r ( $_SESSION [MENU] ) ; echo  "</pre>" ; 
	if ( $is_suppression_valide )
		$message =  "<p><strong>Votre annulation a bien été prise en compte</strong> 
		(vous pouvez le constater sur le menu ci-contre). Vous pouvez poursuivre 
		le calcul de votre
		Bilan Carbone Personnel en accédant à la page souhaitée à l'aide du menu ci-contre.</p>" ; 
	else
		$message =  "<p><strong>Attention :</strong> 
		suite à une fausse manoeuvre vous avez demandé une suppression incompatible avec la structure du questionnaire. Votre 
		demande n'a pas été prise en compte. Vous pouvez poursuivre 
		le calcul de votre
		Bilan Carbone Personnel en accédant à la page souhaitée à l'aide du menu ci-contre.</p>" ; 
	return ( $message ) ; 
}
//==========================================================================================
// Mise à jour des variables de de $_SESSION[REPONSE] et $_SESSION [PAGE_COMPLETE] suite à la suppression d'une catégorie : on triche : c'est forcément un logement ! 
//==========================================================================================
function mettre_a_jour_reponse_suppression_logement ( $numero_supprime , $connexion ) 
{	
	$nombre_logement = $_SESSION[MENU_NOMBRE]['logement'] ; 
	$donnees_page = exec_requete ( "SELECT * FROM t_page , t_rubrique WHERE page_rub_id = rub_id AND rub_nom = 'logement'" , $connexion ) ; 
	while ( $objet_page = objet_suivant ( $donnees_page ) ) 
	{
		for ( $i = $numero_supprime ; $i <= $nombre_logement - 1 ; $i++ ) 
		{
			$j=$i+1 ; 
			$_SESSION [PAGE_COMPLETE][$objet_page->page_nom . '%' . "$i"] = $_SESSION [PAGE_COMPLETE][$objet_page->page_nom . '%' . "$j"] ; 
		}
		unset ( $_SESSION [PAGE_COMPLETE][$objet_page->page_nom . '%' . "$nombre_logement"] ) ; 
		mettre_a_jour_suppression_page_particuliere ( $objet_page->page_id , $numero_supprime , $nombre_logement , $connexion ) ; 
	}
}
//==========================================================================================
// Mise à jour des variables de de $_SESSION[REPONSE] pour une page particulière (utilisée à la fois pour suppression d'une catégorie et d'une page
//==========================================================================================
function mettre_a_jour_suppression_page_particuliere ( $page_id , $numero_supprime , $ancien_nombre_page , $connexion ) 
{
	$donnees_question = exec_requete ( 
		"SELECT quest_nom , quest_id , page_nom , rub_nom FROM t_question , t_page , t_rubrique 
		WHERE quest_page_id = page_id AND page_id = '$page_id' AND page_rub_id = rub_id " , $connexion ) ; 
	while ( $objet_question = objet_suivant ( $donnees_question ) ) 
	{
		$reference_question = $objet_question->rub_nom . '_' . $objet_question->page_nom . '_' . $objet_question->quest_nom ;
		$quest_id = $objet_question->quest_id ; 
		$donnees_type_reponse = exec_requete ( "SELECT DISTINCT rep_type FROM t_reponse WHERE rep_quest_id = '$quest_id' " , $connexion ) ; 
		while ( $objet_type_reponse = objet_suivant ( $donnees_type_reponse ) )
		{
			$reference_reponse = $reference_question . '_' . $objet_type_reponse->rep_type ; 
			// on décale tout
			for ( $i = $numero_supprime ; $i <= $ancien_nombre_page -1 ; $i++ )
			{
				unset ( $_SESSION[REPONSE][$i . '_' . $reference_reponse] ) ; 
				$j = $i + 1 ; 
				if ( isSet ( $_SESSION[REPONSE][$j . '_' . $reference_reponse] ) )
					$_SESSION[REPONSE][$i . '_' . $reference_reponse] = $_SESSION[REPONSE][$j . '_' . $reference_reponse] ; 
			}
			// on supprime les variables d'indice maximal obsolètes
			unset ( $_SESSION[REPONSE][$ancien_nombre_page . '_' . $reference_reponse] ) ; 
		}
	}
}
//==========================================================================================
// Mise à jour des variables de de $_SESSION[REPONSE] suite à la suppression d'une page
//==========================================================================================
function mettre_a_jour_reponse_suppression_page ( $page , $numero_supprime , $connexion ) 
{
	$donnees_page = exec_requete ( "SELECT page_id FROM t_page WHERE page_nom = '$page' " , $connexion ) ; 
	$objet_page = objet_suivant ( $donnees_page ) ; 
	mettre_a_jour_suppression_page_particuliere ( $objet_page->page_id , $numero_supprime , $_SESSION[MENU_NOMBRE][$page] , $connexion ) ; 
}
?>