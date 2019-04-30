<?php 
//==========================================================================================
// Affichage de la page du questionnaire
//==========================================================================================
function afficher_page_questionnaire ( $url , $url_decodee , $liste_page , $TEXT_MENU , $TEXT_QUESTIONNAIRE , $TEXT_PAGE , $connexion )
{
	// echo "<pre>" ; print_r ( $url_decodee ) ; echo "</pre>" ; 
	// echo $url_decodee[PAGE] ; 
	$donnees_page = exec_requete ( "SELECT * FROM t_page , t_rubrique WHERE page_nom = '" . $url_decodee[PAGE] . "' AND page_rub_id = rub_id" , $connexion ) ; 
	$objet_page = objet_suivant ( $donnees_page ) ; 
	$page_id = $objet_page->page_id ; 
	// ======================
	// on met en garde l'utilisateur si jamais c'est nécessaire
	mise_en_garde_dependance_page ( $url_decodee , $objet_page->page_est_influencee_par_page_id , $connexion ) ; 
	// ======================
	// on affiche les flèches de navigation en haut
	afficher_fleches_navigation ( 'haut' , $url , $liste_page ) ; 
	// ======================
	// on affiche le titre
	afficher_titre ( $url_decodee , $objet_page , $TEXT_MENU ) ; 				
	echo "<div class='separateur_droit'></div>\n\n" ; // séparateur
	// ===============================================
	// début du formulaire de question
	echo "<form action='index.php' method='post'>   <!-- debut boite formulaire --> \n\n" ;
	$donnees_question = exec_requete ( "SELECT * FROM t_question , t_page , t_rubrique 
	WHERE quest_page_id = page_id AND page_rub_id = rub_id AND page_id = $objet_page->page_id 
	ORDER BY quest_ordre" , $connexion ) ; 
	while ( $objet_question = objet_suivant ( $donnees_question ) )
	{
		if ( doit_afficher_question ( $url_decodee[NUMERO] , $objet_question->quest_id , $connexion ) )
		{
			// les conditions pour que la question soit affichée sont remplies
			afficher_question ( $url_decodee , $objet_question , $TEXT_QUESTIONNAIRE , $TEXT_PAGE , $connexion ) ; 
		}
	}
	// ======================
	// on affiche les flèches de navigation en bas
	afficher_fleches_navigation ( 'bas' , $url , $liste_page ) ; 
	// ======================
	// bouton valider et données envoyées en 'hidden'
	echo "\n\n<p class='bouton_valider'>\n" ; 
	// on passe tout les renseignements nécessaire en POST
	echo "<input type='hidden' name='page' value='" . $url_decodee[PAGE] . "' />\n" ; 
	echo "<input type='hidden' name='url' value='" . $url . "' />\n" ; 
	echo "<input type='hidden' name='numero' value='" . $url_decodee[NUMERO] . "' />\n" ; 
	// echo "<input type='hidden' name ='objet_page' value='" . $objet_page . "' />\n" ;  // ça ne marche pas !!!
	//
	echo "<input type='submit' name = '" . POST_VALIDATION_PAGE . "' value='";
	afficher_texte( 'valider' , $TEXT_QUESTIONNAIRE  );
	echo "' />\n" ; 
	// 
	echo "</p>\n" ; // fin des balises qui entourent le bouton valider 
	// ===============================================
	// fin du formulaire de question
	echo "</form>    <!-- fin de la boite formulaire --> \n\n" ; 
}
// ===================================================================================================
//Met en garde si jamais la page dépend des réponses d'une autre page qui n'a pas été validée
// ===================================================================================================
function mise_en_garde_dependance_page ( $url , $page_est_influencee_par_page_id , $connexion )
{
	if ( $page_est_influencee_par_page_id != null )
	{
		// la page est influencée par une autre page 
		if ( !isSet ( $_POST ['validation_page'] ) )
		{
			// on ne vient pas de valider la même page (sinon inutile de mettre en garde on l'a déjà fait auparavant!)
			$donnees_page_influente = exec_requete ( "SELECT page_nom FROM t_page WHERE page_id = $page_est_influencee_par_page_id" , $connexion ) ; 
			$objet_page_influente = objet_suivant ( $donnees_page_influente ) ; 
			if ( $url[NUMERO] )
				$url_page_influente = $objet_page_influente->page_nom . '%' . $url[NUMERO] ; 
			else
				$url_page_influente = $objet_page_influente->page_nom ; 
			if ( $_SESSION[PAGE_COMPLETE][$url_page_influente] == false )
			{
				// on triche : on sait que c'est la page logement->conso_energie
				echo "<div class='message_post_saisie' ><p><span class='page_incomplete'>Attention&nbsp;!</span>
				La page Logement->Général n'est pas complète pour ce logement, or les questions qui sont posées ci-dessous
				dépendent de vos réponses à la page Logement->Général. Par 
				conséquent, nous vous invitons à complèter la page Logement->Général pour ce logement 
				avant de répondre aux questions ci-dessous.</p></div>" ; 
			}
		}
	}
}
// ===================================================================================================
//Affiche une question (avec les aides qui vont avec et les champs des réponses)
// ===================================================================================================
function afficher_question ( $url_decodee , $objet_question , $TEXT_QUESTIONNAIRE , $TEXT_PAGE , $connexion )
{
	echo "\n<div class = 'question' >  <!-- début boite de question --> \n\n" ; 
	$question_reference = $objet_question->rub_nom . '_' . $objet_question->page_nom . '_' . $objet_question->quest_nom ;
	// ===================
	// afficher le lien vers le fichier d'explication si celui-ci existe
	if ( $lien_explication = lien_explication ( $question_reference , QUESTION , $url_decodee ) )
		echo "<div class='explication_question'>" . $lien_explication . "</div>  <!-- lien vers fichier d'explication -->" ; 
		// la fonction lien_explication se trouve dans le fichier inc/fonctions_affichage_explications.php
	// ===================
	// affichage de l'intitulé de la question
	$question_reference_intitule = $objet_question->quest_nom . '_intitule' ; 
	$question_reference_aide = $objet_question->quest_nom . '_aide' ; 
	if ( array_key_exists ( $question_reference_intitule , $TEXT_PAGE ) )
		echo "<p class='intitule'> \n" . $TEXT_PAGE[$question_reference_intitule] . "\n</p> \n\n" ;
	// ===================
	// affichage de l'aide
	if ( array_key_exists ( $question_reference_aide , $TEXT_PAGE ) )
		echo "<div class='question_aide'>\n" . $TEXT_PAGE[$question_reference_aide] . "\n</div>\n\n" ;
	// ===================
	// affichage des réponses à la question
	$donnees_reponse = exec_requete ( 
		"SELECT * FROM t_reponse WHERE rep_quest_id = '$objet_question->quest_id' ORDER BY rep_ordre" , $connexion ) ; 
	$est_precedente_reponse_select = false ; 
	while ( $objet_reponse = objet_suivant ( $donnees_reponse ) )
	{
		if ( $objet_reponse->rep_type == 'select' )
		{	
			if ( !$est_precedente_reponse_select )
			{
				// c'est la première réponse d'une liste de select, on ouvre la balise select
				echo "<p class='reponse'>\n" ; 
				echo "<select name='" . $question_reference . "_select' >\n" ;
				$est_precedente_reponse_select = true ; 
			}
		}
		else
		{
			if ( $est_precedente_reponse_select )
			{
				// c'est la première réponse "non select" après une liste de "select", on ferme la balise select
				echo "</select>\n\n" ;
				echo "\n</p> <!-- fin réponse -->\n\n" ;
			}
		}
		afficher_reponse ( $objet_reponse , $question_reference , $url_decodee[NUMERO] , $TEXT_QUESTIONNAIRE , $TEXT_PAGE , $connexion ) ; 
	}
	if ( $est_precedente_reponse_select )
	{
		// la dernière réponse était une réponse select, on ferme la balise select
		echo "</select>\n\n" ;
		echo "\n</p> <!-- fin réponse -->\n\n" ;
	}
	echo "</div>   <!-- Fin de la boite de question --> \n\n" ;

}
// ===================================================================================================
//Affiche une réponse
// ===================================================================================================
function afficher_reponse ( $objet_reponse , $question_reference , $numero , $TEXT_QUESTIONNAIRE , $TEXT_PAGE , $connexion )
{
	$rep_type = $objet_reponse->rep_type ; 
	$rep_valeur = $objet_reponse->rep_valeur ; 
	$rep_intitule = $objet_reponse->rep_intitule ;
	$rep_defaut = '' ; 
	// on va chercher la réponse par défaut si elle existe 
	$donnees_rep_defaut = exec_requete (" 
		SELECT param_rep_rep_defaut FROM t_parametre_reponse WHERE param_rep_rep_id = " . $objet_reponse->rep_id . " AND param_rep_rep_defaut != ''
		" , $connexion ) ; 
	if ( $objet_rep_defaut = objet_suivant ( $donnees_rep_defaut ) )
	{
		if ( $rep_type == SELECT )
			$rep_defaut = $rep_valeur ; 
		else
			$rep_defaut = $objet_rep_defaut->param_rep_rep_defaut ; 
	}
	//
	if ( isSet ( $_SESSION[REPONSE][ $question_reference . '_' . $rep_type ] ) )
			$rep_defaut = $_SESSION[REPONSE][ $question_reference . '_' . $rep_type ] ; 
	if ( isSet ( $_SESSION[REPONSE][$numero . '_' . $question_reference . '_' . $rep_type ] ) )
		$rep_defaut = $_SESSION[REPONSE][$numero . '_' . $question_reference . '_' . $rep_type ] ; 
	//
	//
	if ( $rep_type != SELECT )
	{
		echo "<p class='reponse'>\n" ; 
		// on traite ensemble le cas d'un champ texte (numérique) radio ou checkbox 
		// on définit le champ 
		if ( $rep_type == NUMERIQUE )
			$type_champ = 'text' ; 
		else 
			$type_champ = $rep_type ; // radio ou checkbox
		// on commence à écrire l'input
		echo "<input type='" . $type_champ . "' name='" . $question_reference . "_" . $rep_type . "' " ;   
		if ( $rep_type == NUMERIQUE )
			// c'est un champ text on affiche la valeur par défaut, on précise la taille, on donne l'id
			echo "value='" . $rep_defaut . "' size='7' id='" . $question_reference . "' " ;
		else 
		{
			// c'est un champ radio ou checkbox 
			echo "value='" . $rep_valeur . "' id='" . $question_reference . "_" . $rep_type . "_" . $rep_valeur . "' " ; 
			if ( $rep_defaut == $rep_valeur )
				echo "checked='checked' ";
		}	
		echo "/>" ; // on ferme la balise input
		//
		if ( $rep_intitule ) 
		{
			if ( $rep_type != NUMERIQUE )
				echo "<label for='" . $question_reference . "_" . $rep_type . "_" . $rep_valeur . "' >" ; 
			afficher_texte ( $rep_intitule , $TEXT_QUESTIONNAIRE ) ;
			if ( $rep_type != NUMERIQUE )
				echo "</label>" ; 
		}
		echo "\n</p> <!-- fin réponse -->\n\n" ;
	}
	else
	{
		// il s'agit d'un champ select, on le traite à part
		echo "<option value='" . $rep_valeur . "'" ;
		if ( $rep_defaut == $rep_valeur )
			echo " selected='selected'";
		echo " >";
		afficher_texte( $rep_intitule , $TEXT_QUESTIONNAIRE ) ;
		echo "</option>\n" ; 
	}			
}
// ===================================================================================================
//Test pour savoir si une question doit ou non être affichée en fonction des conditions stipulées dans le fichier xml et des réponses aux questions d'autres pages
// ===================================================================================================
function doit_afficher_question ( $numero , $quest_id , $connexion )
{
	$doit_afficher_question = true ; // optimiste (si pas de condition)
	$donnees_affiche_question_si_reponse = exec_requete ( 
		"SELECT * FROM t_affiche_question_si_reponse WHERE aff_quest_si_rep_quest_id = $quest_id" , $connexion ) ; 
	
	if ( mysql_num_rows ( $donnees_affiche_question_si_reponse ) > 0 )
	{
		// echo "<p>quest_id : " . $quest_id . "</p>" ; 
		$doit_afficher_question = false ; // les conditions sont inclusives OU , donc on démarre pessimiste
		while ( $objet_affiche_question_si_reponse = objet_suivant ( $donnees_affiche_question_si_reponse ) )
		{
			$rep_voulue_id = $objet_affiche_question_si_reponse->aff_quest_si_rep_rep_id ; 
			// à présent il s'agit de déterminer l'index qui a été utilisé dans le tableau $_SESSION[REPONSE]
			$donnees_reponse = exec_requete ( 
				"SELECT rep_type , rep_valeur , quest_nom , page_nom , rub_nom FROM t_reponse , t_question , t_page , t_rubrique 
				WHERE rep_id = $rep_voulue_id AND rep_quest_id = quest_id AND quest_page_id = page_id AND page_rub_id = rub_id" 
				, $connexion ) ; 
			$objet_reponse = objet_suivant ( $donnees_reponse ) ; 
			$index_reponse = $numero . '_' 
				. $objet_reponse->rub_nom . '_' . $objet_reponse->page_nom . '_' . $objet_reponse->quest_nom . '_' . $objet_reponse->rep_type ; 
			// echo "<p>" . $index_reponse . "</p>" ; 
			if ( isSet ( $_SESSION[REPONSE][$index_reponse] ) && $_SESSION[REPONSE][$index_reponse] == $objet_reponse->rep_valeur ) 
				// remarque : il y a toujours un numéro car ça ne concerne que la rubrique logement ! 
				$doit_afficher_question = true ; 
		}
	}
	return $doit_afficher_question ; 
}
// ===================================================================================================
//Affichage du titre de la page de questions
// ===================================================================================================
function afficher_titre ( $url_decodee , $objet_page , $TEXT )
{
	echo '<h2>';
	echo titre_page_questionnaire ( $url_decodee , $objet_page , $TEXT ) ; 
	echo "</h2>  <!-- titre de la page --> \n\n";
}
// ===================================================================================================
//Titre d'une page du questionnaire (fonction utilisée à la fois pour afficher le titre de la page à la fois dans l'en-tête et dans la page
// ===================================================================================================
function titre_page_questionnaire ( $url_decodee , $objet_page , $TEXT )
{
	$titre = $TEXT[$objet_page->rub_nom] ; 
	if ( $objet_page->est_repetee_rubrique = 'true' )
		// en fait on est dans "logement" ! 
		$titre = $titre . " " . $url_decodee[NUMERO] . ' 	-&rsaquo; ' . $TEXT[$objet_page->page_nom] ;
	else
	{
		// pas logement : c'est éventuellement la page qui porte un numéro
		$titre = $titre . ' -&rsaquo; ' . $TEXT[$objet_page->page_nom] ;
		if ( $url_decodee[NUMERO] != false )
			$titre = $titre . " " . $numero ; 
	}
	return $titre ; 
}
// ===================================
// Fonction qui affiche la boite de parcours (sans sauvegarde) entre les pages
// ===================================
function afficher_fleches_navigation ( $position , $url_actuelle , $liste_page )
{
	$url_connexe = url_connexe ( $url_actuelle , $liste_page ) ; 
	// print_r ( $url_connexe ) ; 
	echo "<div class='boite_nav_quest'>\n" ; 
	if ( $url_connexe ['suivant'] != false )
		echo "<a href='./index.php?type_page=" . QUESTIONNAIRE . "&amp;page=" . $url_connexe ['suivant'] . "' title='Page suivante' >&gt;</a>" ; 
	if ( $url_connexe ['precedent'] != false )
		echo "<a href='./index.php?type_page=" . QUESTIONNAIRE . "&amp;page=" . $url_connexe ['precedent'] . "' title='Page précédente' >&lt;</a>" ; 
	echo "<p>La ou les flèche(s) ci-contre permet(tent) de naviguer d'une page à l'autre sans effectuer de modification. Si vous avez saisi des 
	modifications sur cette page vous devez d'abord les valider (en utilisant le bouton 'Valider' " ; 
	if ( $position == 'haut' ) 
		echo "en bas de page" ; 
	else
		echo "ci-contre" ; 
	echo	") avant de quitter la page. </p>\n" ; 
	echo "</div>    <!-- fin de boite_nav_quest --> \n\n" ; 
}
// ===================================
// Fonction qui détermine l'adresse de la page suivante et de la page précédente
// ===================================
function url_connexe ( $url_actuelle , $liste_page )
{	
	$index_page_actuelle = array_search ( $url_actuelle , $liste_page ) ; 
	if ( $index_page_actuelle > 0 )
		$url_connexe ['precedent'] = $liste_page [$index_page_actuelle-1] ; 
	else
		$url_connexe ['precedent'] = false ; 
	$nombre_liste_page = count ( $liste_page ) ; 
	if ( $index_page_actuelle < $nombre_liste_page -1 ) // le tableau commence à zéro ! 
		$url_connexe ['suivant'] = $liste_page [$index_page_actuelle + 1] ; 
	else
		$url_connexe ['suivant'] = false ; 
	return $url_connexe ; 
}
// ===================================
// 
// ===================================

?>