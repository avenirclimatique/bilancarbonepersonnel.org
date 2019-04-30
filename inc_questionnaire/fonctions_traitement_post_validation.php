<?php 
// ===================================================================================================
// Traitement post-validation
// ===================================================================================================
function traitement_post_validation ( $url , $TEXT , $connexion )
{
	// on récupère gratuitement $page et $numero par POST (on pourrait les retrouver à partir de $url mais on est paresseux !)
	$page = $_POST ['page'] ; 
	$numero = $_POST ['numero'] ; 
	// ======================================
	//
	$diagnostic_page = diagnostic_page ( $page , $numero , true , $connexion ) ; 
	//
	//==================================================
	// Mise à jour de la variable $_SESSION [PAGE_COMPLETE]
	//==================================================
	// on regarde si le questionnaire était complet juste avant
	$etait_incomplet_questionnaire = in_array ( false , $_SESSION [PAGE_COMPLETE] ) ;
	// on met à jour la variable $_SESSION [PAGE_COMPLETE]
	$_SESSION [PAGE_COMPLETE][$url] = $diagnostic_page ['est_complete_page'] ; 
	// on regarde si le questionnaire est complet maintenant 
	$est_incomplet_questionnaire = in_array ( false , $_SESSION [PAGE_COMPLETE] ) ;
	if ( $etait_incomplet_questionnaire && !$est_incomplet_questionnaire )
		$diagnostic_page ['permet_d_acceder_au_resultat'] = true ; 
	// =================================================
	// on met à jour la variable $_SESSION[EST_SAISIE_EFFECTUEE]
	$_SESSION[EST_SAISIE_EFFECTUEE] = true ; 
	//==================================================
	// Réalisation et affichage du message en haut de page
	//==================================================
	// echo "<pre>" ; print_r ( $diagnostic_page ) ; echo "</pre>" ; 
	$message_diagnostic_page = formuler_diagnostic_page ( $diagnostic_page , $TEXT ) ; 
	// c'est le message (on ne fait qu'élaborer le message, on ne l'affiche pas !)
	return $message_diagnostic_page ; 
}
// ===================================================================================================
// Etablissement du diagnostic de validité de la page
// ===================================================================================================
function diagnostic_page ( $page , $numero , $est_traitement_post_validation , $connexion )
{
	$diagnostic_page['est_complete_page'] = true ; // soyons optimistes
	$diagnostic_page['est_valide_page'] = true ; // soyons optimistes
	$diagnostic_page['est_remplie_et_sans_effet_reponse_numerique'] = false ; // soyons optimistes
	$diagnostic_page['est_vide_reponse_numerique'] = false ; // soyons optimistes
	$diagnostic_page['est_invalide_reponse_question_intitule'] = '' ; // soyons optimistes
	$diagnostic_page['est_invalide_reponse_facultative'] = false ; 
	$diagnostic_page['est_autre_page_rendue_incomplete'] = false ; // a priori
	$diagnostic_page['permet_d_acceder_au_resultat'] = false ; // a priori
	$diagnostic_page['sont_incompatibles_reponses'] = array () ; 
	$diagnostic_page['sont_incompatibles_reponses'][] = false ; // a priori
	// ================================
	// s'il s'agit d'un traitement post-validation, le tableau $reponse est construit à partir de la variable $_POST
	// s'il s'agit du chargement d'une sauvegarde, il est construit à partir de $_SESSION[REPONSE]
	$tableau_reponse = tableau_reponse ( $page , $numero , $est_traitement_post_validation , $connexion ) ; 
	// =================================
	$donnees_question = exec_requete ( "
	SELECT quest_id , quest_nom FROM t_question , t_page WHERE page_nom = '$page' AND quest_page_id = page_id " , $connexion ) ; 
	while ( $objet_question = objet_suivant ( $donnees_question ) )
	{
		// on parcourt les questions
		$quest_id = $objet_question->quest_id ; 
		// on regarde déjà si la question a été affichée (sinon pas la peine de faire quoi que ce soit) 
		if ( doit_afficher_question ( $numero , $quest_id , $connexion ) )
		{
			$question_reference = question_reference ( $quest_id , $connexion ) ; // c'est la référence de la question
			$question_intitule = $objet_question->quest_nom . '_intitule' ; // c'est l'intitule
			// ça sert à fournir l'intitulé d'une question à laquelle l'utilisateur a apporté une mauvaise réponse
			//
			// echo "<p>" . $question_reference . "</p>" ; 
			// les types de réponses qu'on peut rencontrer : 
			// - un seul champ de réponses
			// - deux champs de réponses : un numérique, et un checkbox "je ne sais pas"
			// Cas particuliers : 
			// -- le cas des réponses du logement : compatibilité entre le type de logement et le type de chauffage
			// -- le cas des réponses des vêtements : incompatibilité entre le tait d'utiliser l'approche par le prix et l'approche par les quantités
			// pour les réponses de type 'select' : on ne vérifie rien
			$diagnostic_page['existe_bouton_radio'] = false ; // servira à dire si la question a été ou non complétée en présence de bouton radio
			$est_coche_bouton_radio = false ; // servira à dire si la question a été ou non complétée en présence de bouton radio
			//
			$donnees_reponse = exec_requete ( "
				SELECT * FROM t_reponse , t_question WHERE rep_quest_id = quest_id AND quest_id = $quest_id
			" , $connexion ) ; 
			while ( $objet_reponse = objet_suivant ( $donnees_reponse ) )
			{
				// on parcourt les réponses à la question
				// echo $question_reference ; 
				$diagnostic_reponse = appel_diagnostic_reponse 
					( $tableau_reponse , $objet_reponse , $question_reference , $question_intitule , $numero , $est_traitement_post_validation , $connexion ) ; 
				if ( !$diagnostic_reponse ['est_valide_reponse'] ) 
				{
					// la réponse est invalide
					if ( !$diagnostic_reponse ['est_remplie_et_sans_effet_reponse_numerique'] ) 
					{
						// la réponse invalide n'est pas facultative
						$diagnostic_page['est_valide_page'] = false ; 
						$diagnostic_page['est_complete_page'] = false ; 
						$diagnostic_page['est_invalide_reponse_question_intitule'] = $diagnostic_reponse ['est_invalide_reponse_question_intitule'] ; 
						// echo "<p>Réponse invalide : " . $diagnostic_page['est_invalide_reponse_question_intitule'] . "</p>" ; 
					}
					else
					{
						// la réponse invalide est facultative
						$diagnostic_page['est_invalide_reponse_facultative'] = true ; 
					}
				}
				//
				if ( !$diagnostic_reponse ['est_complete_reponse'] ) 
					$diagnostic_page['est_complete_page'] = false ; 
				// 
				if ( $diagnostic_reponse ['est_remplie_et_sans_effet_reponse_numerique'] ) 
					$diagnostic_page['est_remplie_et_sans_effet_reponse_numerique'] = true ; 
				// 
				if ( $diagnostic_reponse ['est_vide_reponse_numerique'] ) 
					$diagnostic_page['est_vide_reponse_numerique'] = true ; 
				// 
				if ( $diagnostic_reponse ['sont_incompatibles_reponses'][0] == true )
				{
					$diagnostic_page['sont_incompatibles_reponses'] = $diagnostic_reponse ['sont_incompatibles_reponses'] ; 
					//print_r ( $diagnostic_page['sont_incompatibles_reponses'] ) ; 
					//echo "<p>$diagnostic_page['sont_incompatibles_reponses'][1]</p>" ; 
					//echo "<p>$diagnostic_page['sont_incompatibles_reponses'][2]</p>" ; 
				}
				//
				// on teste le fait qu'un bouton radio a bien été coché si nécessaire
				$diagnostic_page['existe_bouton_radio'] = $diagnostic_reponse ['existe_bouton_radio'] ; 
				$est_coche_bouton_radio = $diagnostic_reponse ['est_coche_bouton_radio'] ; 
			}	
			if ( $diagnostic_page['existe_bouton_radio'] == true && $est_coche_bouton_radio == false )
			{
				$diagnostic_page['est_complete_page'] = false ; 
				// echo "<p>Il existe un bouton radio non saisi.</p>" ; 
			}
		}
	} // fin de la boucle de parcours des questions
	//==================================================
	// Attribution de la valeur false à la variable $_SESSION [PAGE_COMPLETE] pour les pages qui sont dépendantes de cette page
	//==================================================
	$donnees_page = exec_requete ( "SELECT page_influe_sur_page_id FROM t_page WHERE page_nom = '$page' " , $connexion ) ; 
	$objet_page = objet_suivant ( $donnees_page ) ; 
	if ( $est_traitement_post_validation )
		if ( $page_dependante_id = $objet_page->page_influe_sur_page_id )
		{
			// il y a une page qui dépend de cette page
			$donnees_page_dependante = exec_requete ( "SELECT page_nom FROM t_page WHERE page_id = '$page_dependante_id' " , $connexion ) ; 
			$objet_page_dependante = objet_suivant ( $donnees_page_dependante ) ; 
			$url_dependante = $objet_page_dependante->page_nom ; 
			if ( $numero )
				$url_dependante = $url_dependante . '%' . $numero ; 			
			if ( $_SESSION [PAGE_COMPLETE][$url_dependante] == true )
			{
				// une page dépendante jusqu'ici complète va être rendue incomplète, on prévient l'utilisateur
				$_SESSION [PAGE_COMPLETE][$url_dependante] = false ; 
				$diagnostic_page ['est_autre_page_rendue_incomplete'] = true ; 
			}
		}
	// ================================================
	return $diagnostic_page ; 
}
// ===================================================================================================
// Détermination (et écriture)  du message de diagnostic pour la réponse
// ===================================================================================================
function appel_diagnostic_reponse ( $tableau_reponse , $objet_reponse , $question_reference , $question_intitule , $numero , $est_traitement_post_validation , $connexion )
{
	$rep_id = $objet_reponse->rep_id ; 
	// on détermine la valeur des attributs de la réponse
	$non_zero = false ; 
	$reponse_type = $objet_reponse->rep_type ; 
	$donnees_parametre_reponse = exec_requete ( "SELECT * FROM t_parametre_reponse WHERE param_rep_rep_id = '$rep_id' " , $connexion ) ; 
	if ( mysql_num_rows ( $donnees_parametre_reponse ) > 0 ) 
	{
		while ( $objet_parametre_reponse = objet_suivant ( $donnees_parametre_reponse ) ) 
		{
			if ( $objet_parametre_reponse->param_rep_rep_est_non_zero )
			{
				$non_zero = $objet_parametre_reponse->param_rep_rep_est_non_zero ; 
				// echo "<p>Réponse id " . $rep_id . "non zero" ; 
			}
		}
	}
	// on a déterminé les variables $response_type , $non_zero 
	//echo "coucou" ; 
	//echo $question_reference ; 
	//echo "<p>Réponse de type : " . $reponse_type . "</p>" ; 
	$diagnostic_reponse = diagnostic_reponse ( $tableau_reponse , $objet_reponse , $reponse_type , $non_zero , $question_reference , $question_intitule , $numero , $est_traitement_post_validation , $connexion ) ;
	return $diagnostic_reponse ; 
	// 
}
// ===================================================================================================
// Traitement réponse
// ===================================================================================================
function diagnostic_reponse 
	( $tableau_reponse , $objet_reponse , $reponse_type , $non_zero , $question_reference , $question_intitule , $numero , $est_traitement_post_validation , $connexion )
{
	$diagnostic_reponse['est_valide_reponse'] = true ; // optimiste
	$diagnostic_reponse['est_complete_reponse'] = true ; // optimiste
	$diagnostic_reponse['est_remplie_et_sans_effet_reponse_numerique'] = false ; // optimiste ; sert à tester si une réponse est remplie sans qu'on en tienne compte
	$diagnostic_reponse['est_vide_reponse_numerique'] = false ; // optimiste ; sert à tester si une réponse est remplie sans qu'on en tienne compte
	$diagnostic_reponse['est_invalide_reponse_question_intitule'] = '' ; // optimiste
	$diagnostic_reponse['sont_incompatibles_reponses'] = array () ; 
	$diagnostic_reponse['sont_incompatibles_reponses'] [] = false ; // optimiste
	$diagnostic_reponse['existe_bouton_radio'] = false ; // a priori
	$diagnostic_reponse['est_coche_bouton_radio'] = false ; // a priori
	// 
	$est_sans_effet_reponse_numerique = false ; 
	//elle est différente de : $diagnostic_reponse['est_remplie_et_sans_effet_reponse_numerique']
	//
	if ( $reponse_type == NUMERIQUE )
	{
		// ==================================
		// saisies numériques 
		// c'est le seul cas où la réponse peut etre invalide (en attendant mieux)
		// on commence par regarder si cette réponse a de l'effet ou non, si elle sera prise en compte dans les calculs
		//===========
		// on commence par tester le fait qu'il existe, pour la même question, une checkbox "je ne sais pas"
		$quest_id = $objet_reponse->quest_id ; 
		$donnees_autre_reponse = exec_requete ( "
			SELECT rep_id FROM t_reponse WHERE rep_quest_id = '$quest_id' AND rep_type = '" . CHECKBOX . "' AND rep_valeur = 'je_ne_sais_pas' 
			" , $connexion ) ; 
		if ( mysql_num_rows ( $donnees_autre_reponse ) > 0 ) 
		{
			// il existe bien une checkbox "je ne sais pas"
			if ( isSet ( $tableau_reponse[ $question_reference . '_' . CHECKBOX ] ) )
			{
				// echo "<p>Pour la question " . $question_reference . "une réponse est sans effet car une checkbox est cochée</p>" ; 
				$est_sans_effet_reponse_numerique = true ; 
			}
		}
		//======)
		// on teste ensuite s'il existe une condition dans la table t_lien_reponse qui rend la réponse faclultative
		$donnees_lien_reponse = exec_requete ( "
			SELECT quest_id FROM t_lien_reponse , t_reponse, t_question
			WHERE lien_rep_aval_rep_id = " . $objet_reponse->rep_id . " AND lien_rep_type = 'est_facultatif_si_non' AND lien_rep_amont_rep_id = rep_id AND rep_quest_id = quest_id 
			", $connexion ) ; 
		// remarque : on ne teste pas est_facultatif_si_rep car pas d'exemple pour le moment
		if ( $objet_lien_reponse = objet_suivant ( $donnees_lien_reponse ) )
		{
			// il existe bien une checkbox si, lorsqu'elle est décochée, rend la réponse facultative
			$question_amont_reference = question_reference ( $objet_lien_reponse->quest_id , $connexion ) ; 
			if ( !isSet ( $tableau_reponse[ $question_amont_reference . '_' . CHECKBOX ] ) )
				// sans effet car une checkbox est décochée
				$est_sans_effet_reponse_numerique = true ; 
		}
		// on teste s'il y a eu quelque chose de répondu
		if ( isSet ( $tableau_reponse[ $question_reference . '_' . NUMERIQUE] ) && $tableau_reponse[ $question_reference . '_' . NUMERIQUE] != "" )
		{
			// il y a bien eu quelque chose de répondu
			$reponse = $tableau_reponse[ $question_reference . '_' . NUMERIQUE] ; 
			// echo "<p>La réponse est : " . $reponse . "</p>" ; 
			// $diagnostic_reponse['est_valide_reponse'] = true ; 	// partons de l'hypothèse que la réponse est valide
			// mais peut-être la réponse est-elle invalide ?
			// ===========================
			// on remplace la virgule par un point pour les calculs
			$reponse = str_replace ( ',' , '.' , $reponse ) ; 
			// echo "<p>" . $reponse . "</p>" ; 
			// ===========================
			// on teste si cest un nombre positif 
			//if ( is_numeric ( $reponse ) == true ) 
			// echo "<p>La réponse est un nombre</p>" ; 
			if ( is_numeric ( $reponse ) == true )
			{
				if ( $reponse >= 0 )
				{
					if ( $non_zero != false && $reponse == 0 )
					{
						// on a demandé à ce que la réponse ne soit pas zéro et pourtant elle l'est
						// echo "<p>La réponse est zéro et ne devrait pas l'être</p>" ; 
						$diagnostic_reponse['est_valide_reponse'] = false ;
						$diagnostic_reponse['est_invalide_reponse_question_intitule'] = $question_intitule ; 
					}
				}
				else
				{
					// echo "<p>La réponse est un nombre négatif</p>" ; 
					$diagnostic_reponse['est_valide_reponse'] = false ; // la réponse est un nombre négatif
					$diagnostic_reponse['est_invalide_reponse_question_intitule'] = $question_intitule ; 
				}
			}
			else
			{
				// echo "<p>La réponse n'est pas un nombre</p>" ; 
				$diagnostic_reponse['est_valide_reponse'] = false ; // la réponse n'est pas un nombre
				$diagnostic_reponse['est_invalide_reponse_question_intitule'] = $question_intitule ; 
			}
			if ( $est_sans_effet_reponse_numerique )
				// la réponse numerique est sans effet et pourtant quelque chose a été réponu ; on met donc à true la variable suivante : 
				$diagnostic_reponse['est_remplie_et_sans_effet_reponse_numerique'] = true ; 		
			if ( $est_traitement_post_validation )
			{
				if ( $diagnostic_reponse['est_valide_reponse'] == true )
				{
					// echo "<p>La réponse est valide (ou invalide mais sans effet)</p>" ; 
					// la réponse numérique est valide (ou éventuellement sans effet), on la rentre en variable de session
					affecter_variable_session ( $question_reference , $numero , NUMERIQUE ) ; 
				}
				else
				{
					// la réponse n'est pas sans effet, et n'est pas valide, on l'enregistre (on enregistre la référence de la question)
					// $diagnostic_reponse['est_valide_reponse'] = false ; // ici on pourrait identifier la réponse en question ! 
					// on annule la variable de session associée à l'ancienne saisie
					desaffecter_variable_session ( $question_reference , $numero , NUMERIQUE ) ; 
					// echo "<p>Une réponse à la question " . $question_reference . " est invalide. </p>" ; 
				}
			}
		}
		else
		{
			// il n'y a pas de réponse
			// echo "<p>Champ numérique sans réponse</p>" ; 
			// on annule l'éventuelle variable de session associée à la réponse ! 
			if ( $est_traitement_post_validation ) 
				desaffecter_variable_session ( $question_reference , $numero , NUMERIQUE ) ; 
			if ( $est_sans_effet_reponse_numerique == false ) 
			{
				$diagnostic_reponse['est_complete_reponse'] = false ; 
				if ( $non_zero == false )
				// pour les réponses "non zéro" il est évident qu'il ne faut pas les laisser vides donc inutile de prévenir l'utilisateur pour ça ! 
					$diagnostic_reponse['est_vide_reponse_numerique'] = true ; 
				//echo "<p>La réponse à la question " . $question_reference . " est incomplète</p>" ; 
			}
		}
	}
	else if ( $reponse_type == RADIO )
	{
		// ==================================
		// boutons radio
		$diagnostic_reponse['existe_bouton_radio'] = true ;
		// echo "<p>bouton radio</p>" ; 
		if ( isSet ( $tableau_reponse[ $question_reference . '_' . RADIO] ) && $tableau_reponse[ $question_reference . '_' . RADIO] != '' ) 
		{
			$diagnostic_reponse['est_coche_bouton_radio'] = true ; 
			//echo "<p>bouton radio coché </p>" ; 
			if ( $est_traitement_post_validation ) 
				affecter_variable_session ( $question_reference , $numero , RADIO ) ; 
		}
	}
	else if ( $reponse_type == CHECKBOX )
	{
		// ==================================
		// boutons checkbox : rien à tester ! 
		if ( isSet ( $tableau_reponse[ $question_reference .'_' . CHECKBOX] ) && $tableau_reponse[ $question_reference .'_' . CHECKBOX] != "" ) 
		{
			if ( $est_traitement_post_validation ) 
				affecter_variable_session ( $question_reference , $numero , CHECKBOX ) ; 
		}
		else
		{
			// pas de réponse, on tue l'éventuelle variable
			if ( $est_traitement_post_validation ) 
				desaffecter_variable_session ( $question_reference , $numero , CHECKBOX ) ; 
		}	
	}
	else if ( $reponse_type == SELECT )
	{
		// ==================================
		// boutons select
		// il faut tester si des réponses peuvent être incompatibles
		if ( $est_traitement_post_validation ) // sinon pas besoin de tester ça
		{
			if ( $tableau_reponse[ $question_reference . '_' . SELECT] == $objet_reponse->rep_valeur )
			{
				$donnees_reponse_incompatible = exec_requete ( "
					SELECT quest_id , quest_nom , rep_intitule , rep_valeur FROM t_lien_reponse , t_reponse , t_question WHERE lien_rep_aval_rep_id = " . $objet_reponse->rep_id . " AND lien_rep_type = 'est_incompatible_avec' AND lien_rep_amont_rep_id = rep_id AND rep_quest_id = quest_id 
					" , $connexion ) ; 
				while ( $objet_reponse_incompatible = objet_suivant ( $donnees_reponse_incompatible ) )
				{
					// il y a des conditions de compatibilité avec les réponses à d'autres questions
					$autre_question_reference = question_reference ( $objet_reponse_incompatible->quest_id , $connexion ) ; 
					$autre_reponse_valeur = $objet_reponse_incompatible->rep_valeur ; 
					// pour le moment cette incompatibilité ne concerne que deux select différents donc pas la peine de tester l'existence des variables de POST elles existent toujours
					// remarque : la compatibilité est au sein d'une même page ! 
					if ( $tableau_reponse[ $autre_question_reference . '_' . SELECT ] == $autre_reponse_valeur )
					{
						// il y a incompatibilité entre les réponses
						//echo "<p>Réponses incompatibles : " . $objet_reponse_incompatible->quest_nom . '_intitule' . " et " . $question_intitule . "</p>" ; 
						$diagnostic_reponse['sont_incompatibles_reponses'] [0] = true ; 
						$diagnostic_reponse['sont_incompatibles_reponses'] [1] = $objet_reponse_incompatible->quest_nom . '_intitule' ; 
						// a priori elle est arrivée avant
						$diagnostic_reponse['sont_incompatibles_reponses'] [2] = $question_intitule ; 
					}			
				}
			}
		}
		else
		{
			// on est en train de vérifier la complétude de la page pour chargement ; il se peut que la page ne contienne que des boutons "Select" et qu'elle n'ait pas été validée, il faut tester
			if ( !isSet ( $tableau_reponse[ $question_reference .'_' . SELECT] ) ) 
				$diagnostic_reponse['est_complete_reponse'] = false ; 
		}
		// ===========
		// la suite : affectation des variables de session
		// dans ce cas pas besoin de tester le caractère ou non complet, c'est automatiquement complet car toujours une valeur par défaut
		if ( $est_traitement_post_validation ) 
			if ( !$diagnostic_reponse['sont_incompatibles_reponses'] [0] ) // si incompatible on n'enregistre rien du tout
				if ( isSet ( $tableau_reponse[ $question_reference .'_' . SELECT] ) ) 
					affecter_variable_session ( $question_reference , $numero , SELECT ) ; 
	}
	//
	/*
	if ( $diagnostic_reponse['est_valide_reponse'] == true )
		// echo "<p>Réponse valide" ; 
		echo "<p>"; 
	else
		echo "<p>Réponse invalide" ; 
	if ( $diagnostic_reponse['est_complete_reponse'] == true )
		echo "Réponse complète</p>" ; 
	else
		echo "Réponse incomplète</p>" ; 
	*/
	// echo "<pre>" ; print_r ( $diagnostic_reponse ) ; echo "</pre>" ; 
	
	
	return $diagnostic_reponse ; 
}
// ===================================================================================================
// Fonction qui retourne la référence d'une question
// ===================================================================================================
function question_reference ( $quest_id , $connexion )
{
	$donnees = exec_requete ( "
		SELECT rub_nom , page_nom , quest_nom FROM t_question , t_page , t_rubrique 
		WHERE quest_id = '$quest_id' AND quest_page_id = page_id AND page_rub_id = rub_id " , $connexion ) ; 
	$objet = objet_suivant ( $donnees ) ; 
	$question_reference = $objet->rub_nom . '_' . $objet->page_nom . '_' . $objet->quest_nom ; 
	return $question_reference ; 
}
// ===================================================================================================
// Fonction d'affectation de variable de session
// ===================================================================================================
function affecter_variable_session ( $question_reference , $numero , $type )
{
	$reponse = $_POST[ $question_reference . '_' . $type ] ;
	if ( $type == NUMERIQUE )
		$reponse = rend_valide_virgule ( $reponse ) ; 
	if ( $numero == false )
		$index =  $question_reference . '_' . $type  ; 
	else
		$index = $numero . '_' . $question_reference . '_' . $type ; 
	$_SESSION[REPONSE][$index] = $reponse ; 
}
//==========================================================================================
// Fonction qui fournit 0 ou une réponse numérique par défaut, quand la variable de session de réponse n'est pas fournie
//==========================================================================================
function rend_valide_virgule ( $chaine )
{
	$chaine = str_replace ( "," , "." , $chaine ) ;
	return $chaine ; 
	// echo $chaine . "<br/>" ; 
}
// ===================================================================================================
// Fonction de désaffectation de variable de session
// ===================================================================================================
function desaffecter_variable_session ( $question_reference , $numero , $type )
{
	if ( $numero == false )
		unset ( $_SESSION[REPONSE][ $question_reference . '_' . $type ] ) ;
	else
		unset ( $_SESSION[REPONSE][$numero . '_' . $question_reference . '_' . $type ] );
}
// ==============================================================================================================	
// on détermine le tableau $reponse, qui contient toutes les réponses de la page
// ==============================================================================================================	
function tableau_reponse ( $page_nom , $numero , $est_traitement_post_validation , $connexion )
{
	$tableau_reponse = array () ; 
	$donnees_question = exec_requete ( " 
		SELECT quest_id FROM t_question , t_page WHERE quest_page_id = page_id AND page_nom = '$page_nom' " , $connexion ) ; 
	while ( $objet_question = objet_suivant ( $donnees_question ) )
	{
		$quest_id = $objet_question->quest_id ; 
		if ( doit_afficher_question ( $numero , $quest_id , $connexion ) )
		{
			$question_reference = question_reference ( $quest_id , $connexion ) ; 
			$donnees_reponse = exec_requete ( "
				SELECT * FROM t_reponse WHERE rep_quest_id = '$quest_id' " , $connexion ) ; 
			while ( $objet_reponse = objet_suivant ( $donnees_reponse ) )
			{
				$index = $question_reference . '_' . $objet_reponse->rep_type ; 
				if ( $est_traitement_post_validation )
				{
					if ( isSet ( $_POST[$index] ) )
						$tableau_reponse[$index] = $_POST[$index] ; 
				}
				else
				{
					if ( $numero ) 
						$index_session = $numero . '_' . $index ; 
					else 
						$index_session = $index ; 
					if ( isSet ( $_SESSION[REPONSE][$index_session] ) ) 
						$tableau_reponse[$index] = $_SESSION[REPONSE][$index_session] ; 
				}
			}
		}
	}
	return $tableau_reponse ; 
}
// ===================================================================================================
// Ecriture du message de diagnostic
// ===================================================================================================
function formuler_diagnostic_page ( $diagnostic_page , $TEXT )
{
	// On ouvre la boite
	$message_diagnostic_page =  "<div class='message_post_saisie' >" ; 
	if ( $diagnostic_page['est_valide_page'] == false )
	{
		// au moins une des réponses est invalide
		$message_diagnostic_page = $message_diagnostic_page 
			. "<p><span class='reponse_invalide'>Attention&nbsp;: votre ou vos réponse(s) à la question&nbsp;:</span></p>" 
			. "<p><strong>" . retourner_texte ( $diagnostic_page['est_invalide_reponse_question_intitule'] , $TEXT )
			. "</strong></p><p><span class='reponse_invalide'>est (sont) invalide(s)</span>.
			Seules vos réponses valides ont été enregistrées. Par conséquent vous devrez encore compléter cette page avant d'avoir accès aux 				
			résultats. Les champs ci-dessous affichent celles de vos réponses qui ont été enregistrées.</p>" ; 
		// echo "Réponse à la question " . $diagnostic_page['est_invalide_reponse_question_intitule'] . " invalide" ; 
	}
	else if ( $diagnostic_page['sont_incompatibles_reponses'][0] )
	{
		// print_r ( $diagnostic_page ['sont_incompatibles_reponses'] ) ; 
		//echo "$diagnostic['sont_incompatibles_reponses'][1]" ; 
		//echo "$diagnostic['sont_incompatibles_reponses'][2]" ; 
		// deux réponses sont incompatibles
		$message_diagnostic_page = $message_diagnostic_page 
			. "<p><span class='reponse_invalide'>Attention&nbsp;: vos réponses aux questions&nbsp;:</span></p>" 	
			. "<p><strong>" . retourner_texte ( $diagnostic_page['sont_incompatibles_reponses'][1] , $TEXT )
			. "</strong></p><p><span class='reponse_invalide'>et</span></p>"
			. "<p><strong>" . retourner_texte ( $diagnostic_page['sont_incompatibles_reponses'][2] , $TEXT )
			. "</strong></p><p><span class='reponse_invalide'>sont incompatibles</span>. Par conséquent, votre réponse à la seconde de ces 
			deux questions n'a pas pu être enregistrée. Par conséquent vous devrez encore compléter cette page avant d'avoir accès aux 				
			résultats. Les champs ci-dessous affichent celles de vos réponses qui ont été enregistrées.</p>" ; 
	}
	else if ( $diagnostic_page['est_complete_page'] == false )
	{
		// page incomplète
		$message_diagnostic_page = $message_diagnostic_page 
		. "<p><span class='page_incomplete'>Vos réponses pour cette page sont valides mais incomplètes</span>. 
		Par conséquent vous devrez encore compléter cette page avant d'avoir accès aux 				
		résultats.</p>" ; 
		if ( $diagnostic_page['est_vide_reponse_numerique'] )
			$message_diagnostic_page = $message_diagnostic_page . "<p><strong class='page_incomplete'>Attention</strong> : le calculateur a décelé que vous aviez laissé un champ numérique vide
alors que la saisie d'une réponse était nécessaire (vous n'avez pas par ailleurs rendu la saisie inopérante en demandant par exemple à ce que le calcul soit effectué avec une valeur par défaut). Dans ce cas, la page est considérée comme incomplète. Si la réponse à la question correspondante est &quot;0&quot;, vous devez saisir la valeur &quot;0&quot; pour que la page puisse être considérée comme complète.</p>" ; 
		$message_diagnostic_page = $message_diagnostic_page . "</p><p>Les champs ci-dessous affichent vos réponses telles qu'elles ont été enregistrées par le calculateur.</p>" ; 
	}
	else
		// page complète
		$message_diagnostic_page = $message_diagnostic_page
			. "<p><span class='page_complete'>Vos réponses pour cette page sont valides et complètes</span>. 
			Les champs ci-dessous affichent vos réponses telles qu'elles ont été enregistrées par le calculateur. Vous pouvez accéder aux autres
			pages du questionnaire à l'aide des flèches ci-dessous ou du menu à gauche.</p>" ; 
	// s'il y a une réponse numérique sans effet on prévient l'utilisateur
	if ( $diagnostic_page['est_valide_page'] && $diagnostic_page['est_remplie_et_sans_effet_reponse_numerique'] && !$diagnostic_page['est_invalide_reponse_facultative'] )
		$message_diagnostic_page = $message_diagnostic_page . "<p><span class='page_incomplete'>Attention</span>&nbsp;: vous avez saisi une valeur numérique 
			tout en indiquant par ailleurs au calculateur de ne pas tenir compte de cette valeur (par exemple en lui demandant d'effectuer le calcul
			avec une valeur par défaut et non avec celle que vous avez saisie). 
			Dans une telle situation, et par convention, le calculateur ne va pas tenir compte de la valeur numérique que vous avez saisie 
			(cette valeur est tout de même enregistrée, mais sa validité n'a pas été testée), il va effectuer les calculs comme si aucune saisie
			numérique n'avait été effectuée dans le champ correspondant.</p>" ; 
	// enfin si on a rendu une autre page incomplète on le dit
	if ( $diagnostic_page['est_autre_page_rendue_incomplete'] )
		$message_diagnostic_page = $message_diagnostic_page . "<p><span class='page_incomplete'>Attention&nbsp;!</span> Du fait des éventuelles 
			modifications effectuées sur cette page il est possible que vos saisies sur la page Logement->consommations d'énergie pour ce logement
			ne soient plus valides ou complètes. Par conséquent, nous vous invitons à visiter de nouveau 
			la page Logement->consommations d'énergie pour ce logement (elle devra être à nouveau validée pour que vous puissiez accéder à la 
			page de résultats). </p>" ; 
	if ( $diagnostic_page['permet_d_acceder_au_resultat'] )
		$message_diagnostic_page = $message_diagnostic_page . "<p><span class='page_complete'>Félicitations&nbsp;!</span> 
			Vous avez complété toutes les pages du questionnaire. Vous pouvez accéder à la page de résultats en cliquant 
			<strong><a href='index.php?type_page=" . PAGE_RESULTAT . "'>ici</a></strong> (ou sur les onglets 'Résultats' du menu ci-contre)</p>" ; 
	if ( $diagnostic_page['est_invalide_reponse_facultative'] && $diagnostic_page['est_valide_page'] )
		$message_diagnostic_page = $message_diagnostic_page . "<p><span class='page_incomplete'>Attention</span>&nbsp;: vous avez saisi une valeur invalide dans un champ numérique. Cette saisie n'a pas été enregistrée. Vous avez par ailleurs indiqué au calculateur de ne pas tenir compte de cette saisie (par exemple en lui demandant d'effectuer le calcul avec une valeur par défaut et non avec celle que vous avez saisie). </p>" ; 
	// on ferme la boite
	$message_diagnostic_page = $message_diagnostic_page . "</div>" ; 
	return $message_diagnostic_page ; 
}
?>