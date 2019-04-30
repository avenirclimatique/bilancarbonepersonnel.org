<?php 
// ===================================================================================================
// Traitement post-validation
// ===================================================================================================
function traitement_post_validation ( $url , $TEXT , $connexion )
{
	// on r�cup�re gratuitement $page et $numero par POST (on pourrait les retrouver � partir de $url mais on est paresseux !)
	$page = $_POST ['page'] ; 
	$numero = $_POST ['numero'] ; 
	// ======================================
	//
	$diagnostic_page = diagnostic_page ( $page , $numero , true , $connexion ) ; 
	//
	//==================================================
	// Mise � jour de la variable $_SESSION [PAGE_COMPLETE]
	//==================================================
	// on regarde si le questionnaire �tait complet juste avant
	$etait_incomplet_questionnaire = in_array ( false , $_SESSION [PAGE_COMPLETE] ) ;
	// on met � jour la variable $_SESSION [PAGE_COMPLETE]
	$_SESSION [PAGE_COMPLETE][$url] = $diagnostic_page ['est_complete_page'] ; 
	// on regarde si le questionnaire est complet maintenant 
	$est_incomplet_questionnaire = in_array ( false , $_SESSION [PAGE_COMPLETE] ) ;
	if ( $etait_incomplet_questionnaire && !$est_incomplet_questionnaire )
		$diagnostic_page ['permet_d_acceder_au_resultat'] = true ; 
	// =================================================
	// on met � jour la variable $_SESSION[EST_SAISIE_EFFECTUEE]
	$_SESSION[EST_SAISIE_EFFECTUEE] = true ; 
	//==================================================
	// R�alisation et affichage du message en haut de page
	//==================================================
	// echo "<pre>" ; print_r ( $diagnostic_page ) ; echo "</pre>" ; 
	$message_diagnostic_page = formuler_diagnostic_page ( $diagnostic_page , $TEXT ) ; 
	// c'est le message (on ne fait qu'�laborer le message, on ne l'affiche pas !)
	return $message_diagnostic_page ; 
}
// ===================================================================================================
// Etablissement du diagnostic de validit� de la page
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
	// s'il s'agit d'un traitement post-validation, le tableau $reponse est construit � partir de la variable $_POST
	// s'il s'agit du chargement d'une sauvegarde, il est construit � partir de $_SESSION[REPONSE]
	$tableau_reponse = tableau_reponse ( $page , $numero , $est_traitement_post_validation , $connexion ) ; 
	// =================================
	$donnees_question = exec_requete ( "
	SELECT quest_id , quest_nom FROM t_question , t_page WHERE page_nom = '$page' AND quest_page_id = page_id " , $connexion ) ; 
	while ( $objet_question = objet_suivant ( $donnees_question ) )
	{
		// on parcourt les questions
		$quest_id = $objet_question->quest_id ; 
		// on regarde d�j� si la question a �t� affich�e (sinon pas la peine de faire quoi que ce soit) 
		if ( doit_afficher_question ( $numero , $quest_id , $connexion ) )
		{
			$question_reference = question_reference ( $quest_id , $connexion ) ; // c'est la r�f�rence de la question
			$question_intitule = $objet_question->quest_nom . '_intitule' ; // c'est l'intitule
			// �a sert � fournir l'intitul� d'une question � laquelle l'utilisateur a apport� une mauvaise r�ponse
			//
			// echo "<p>" . $question_reference . "</p>" ; 
			// les types de r�ponses qu'on peut rencontrer : 
			// - un seul champ de r�ponses
			// - deux champs de r�ponses : un num�rique, et un checkbox "je ne sais pas"
			// Cas particuliers : 
			// -- le cas des r�ponses du logement : compatibilit� entre le type de logement et le type de chauffage
			// -- le cas des r�ponses des v�tements : incompatibilit� entre le tait d'utiliser l'approche par le prix et l'approche par les quantit�s
			// pour les r�ponses de type 'select' : on ne v�rifie rien
			$diagnostic_page['existe_bouton_radio'] = false ; // servira � dire si la question a �t� ou non compl�t�e en pr�sence de bouton radio
			$est_coche_bouton_radio = false ; // servira � dire si la question a �t� ou non compl�t�e en pr�sence de bouton radio
			//
			$donnees_reponse = exec_requete ( "
				SELECT * FROM t_reponse , t_question WHERE rep_quest_id = quest_id AND quest_id = $quest_id
			" , $connexion ) ; 
			while ( $objet_reponse = objet_suivant ( $donnees_reponse ) )
			{
				// on parcourt les r�ponses � la question
				// echo $question_reference ; 
				$diagnostic_reponse = appel_diagnostic_reponse 
					( $tableau_reponse , $objet_reponse , $question_reference , $question_intitule , $numero , $est_traitement_post_validation , $connexion ) ; 
				if ( !$diagnostic_reponse ['est_valide_reponse'] ) 
				{
					// la r�ponse est invalide
					if ( !$diagnostic_reponse ['est_remplie_et_sans_effet_reponse_numerique'] ) 
					{
						// la r�ponse invalide n'est pas facultative
						$diagnostic_page['est_valide_page'] = false ; 
						$diagnostic_page['est_complete_page'] = false ; 
						$diagnostic_page['est_invalide_reponse_question_intitule'] = $diagnostic_reponse ['est_invalide_reponse_question_intitule'] ; 
						// echo "<p>R�ponse invalide : " . $diagnostic_page['est_invalide_reponse_question_intitule'] . "</p>" ; 
					}
					else
					{
						// la r�ponse invalide est facultative
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
				// on teste le fait qu'un bouton radio a bien �t� coch� si n�cessaire
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
	// Attribution de la valeur false � la variable $_SESSION [PAGE_COMPLETE] pour les pages qui sont d�pendantes de cette page
	//==================================================
	$donnees_page = exec_requete ( "SELECT page_influe_sur_page_id FROM t_page WHERE page_nom = '$page' " , $connexion ) ; 
	$objet_page = objet_suivant ( $donnees_page ) ; 
	if ( $est_traitement_post_validation )
		if ( $page_dependante_id = $objet_page->page_influe_sur_page_id )
		{
			// il y a une page qui d�pend de cette page
			$donnees_page_dependante = exec_requete ( "SELECT page_nom FROM t_page WHERE page_id = '$page_dependante_id' " , $connexion ) ; 
			$objet_page_dependante = objet_suivant ( $donnees_page_dependante ) ; 
			$url_dependante = $objet_page_dependante->page_nom ; 
			if ( $numero )
				$url_dependante = $url_dependante . '%' . $numero ; 			
			if ( $_SESSION [PAGE_COMPLETE][$url_dependante] == true )
			{
				// une page d�pendante jusqu'ici compl�te va �tre rendue incompl�te, on pr�vient l'utilisateur
				$_SESSION [PAGE_COMPLETE][$url_dependante] = false ; 
				$diagnostic_page ['est_autre_page_rendue_incomplete'] = true ; 
			}
		}
	// ================================================
	return $diagnostic_page ; 
}
// ===================================================================================================
// D�termination (et �criture)  du message de diagnostic pour la r�ponse
// ===================================================================================================
function appel_diagnostic_reponse ( $tableau_reponse , $objet_reponse , $question_reference , $question_intitule , $numero , $est_traitement_post_validation , $connexion )
{
	$rep_id = $objet_reponse->rep_id ; 
	// on d�termine la valeur des attributs de la r�ponse
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
				// echo "<p>R�ponse id " . $rep_id . "non zero" ; 
			}
		}
	}
	// on a d�termin� les variables $response_type , $non_zero 
	//echo "coucou" ; 
	//echo $question_reference ; 
	//echo "<p>R�ponse de type : " . $reponse_type . "</p>" ; 
	$diagnostic_reponse = diagnostic_reponse ( $tableau_reponse , $objet_reponse , $reponse_type , $non_zero , $question_reference , $question_intitule , $numero , $est_traitement_post_validation , $connexion ) ;
	return $diagnostic_reponse ; 
	// 
}
// ===================================================================================================
// Traitement r�ponse
// ===================================================================================================
function diagnostic_reponse 
	( $tableau_reponse , $objet_reponse , $reponse_type , $non_zero , $question_reference , $question_intitule , $numero , $est_traitement_post_validation , $connexion )
{
	$diagnostic_reponse['est_valide_reponse'] = true ; // optimiste
	$diagnostic_reponse['est_complete_reponse'] = true ; // optimiste
	$diagnostic_reponse['est_remplie_et_sans_effet_reponse_numerique'] = false ; // optimiste ; sert � tester si une r�ponse est remplie sans qu'on en tienne compte
	$diagnostic_reponse['est_vide_reponse_numerique'] = false ; // optimiste ; sert � tester si une r�ponse est remplie sans qu'on en tienne compte
	$diagnostic_reponse['est_invalide_reponse_question_intitule'] = '' ; // optimiste
	$diagnostic_reponse['sont_incompatibles_reponses'] = array () ; 
	$diagnostic_reponse['sont_incompatibles_reponses'] [] = false ; // optimiste
	$diagnostic_reponse['existe_bouton_radio'] = false ; // a priori
	$diagnostic_reponse['est_coche_bouton_radio'] = false ; // a priori
	// 
	$est_sans_effet_reponse_numerique = false ; 
	//elle est diff�rente de : $diagnostic_reponse['est_remplie_et_sans_effet_reponse_numerique']
	//
	if ( $reponse_type == NUMERIQUE )
	{
		// ==================================
		// saisies num�riques 
		// c'est le seul cas o� la r�ponse peut etre invalide (en attendant mieux)
		// on commence par regarder si cette r�ponse a de l'effet ou non, si elle sera prise en compte dans les calculs
		//===========
		// on commence par tester le fait qu'il existe, pour la m�me question, une checkbox "je ne sais pas"
		$quest_id = $objet_reponse->quest_id ; 
		$donnees_autre_reponse = exec_requete ( "
			SELECT rep_id FROM t_reponse WHERE rep_quest_id = '$quest_id' AND rep_type = '" . CHECKBOX . "' AND rep_valeur = 'je_ne_sais_pas' 
			" , $connexion ) ; 
		if ( mysql_num_rows ( $donnees_autre_reponse ) > 0 ) 
		{
			// il existe bien une checkbox "je ne sais pas"
			if ( isSet ( $tableau_reponse[ $question_reference . '_' . CHECKBOX ] ) )
			{
				// echo "<p>Pour la question " . $question_reference . "une r�ponse est sans effet car une checkbox est coch�e</p>" ; 
				$est_sans_effet_reponse_numerique = true ; 
			}
		}
		//======)
		// on teste ensuite s'il existe une condition dans la table t_lien_reponse qui rend la r�ponse faclultative
		$donnees_lien_reponse = exec_requete ( "
			SELECT quest_id FROM t_lien_reponse , t_reponse, t_question
			WHERE lien_rep_aval_rep_id = " . $objet_reponse->rep_id . " AND lien_rep_type = 'est_facultatif_si_non' AND lien_rep_amont_rep_id = rep_id AND rep_quest_id = quest_id 
			", $connexion ) ; 
		// remarque : on ne teste pas est_facultatif_si_rep car pas d'exemple pour le moment
		if ( $objet_lien_reponse = objet_suivant ( $donnees_lien_reponse ) )
		{
			// il existe bien une checkbox si, lorsqu'elle est d�coch�e, rend la r�ponse facultative
			$question_amont_reference = question_reference ( $objet_lien_reponse->quest_id , $connexion ) ; 
			if ( !isSet ( $tableau_reponse[ $question_amont_reference . '_' . CHECKBOX ] ) )
				// sans effet car une checkbox est d�coch�e
				$est_sans_effet_reponse_numerique = true ; 
		}
		// on teste s'il y a eu quelque chose de r�pondu
		if ( isSet ( $tableau_reponse[ $question_reference . '_' . NUMERIQUE] ) && $tableau_reponse[ $question_reference . '_' . NUMERIQUE] != "" )
		{
			// il y a bien eu quelque chose de r�pondu
			$reponse = $tableau_reponse[ $question_reference . '_' . NUMERIQUE] ; 
			// echo "<p>La r�ponse est : " . $reponse . "</p>" ; 
			// $diagnostic_reponse['est_valide_reponse'] = true ; 	// partons de l'hypoth�se que la r�ponse est valide
			// mais peut-�tre la r�ponse est-elle invalide ?
			// ===========================
			// on remplace la virgule par un point pour les calculs
			$reponse = str_replace ( ',' , '.' , $reponse ) ; 
			// echo "<p>" . $reponse . "</p>" ; 
			// ===========================
			// on teste si cest un nombre positif 
			//if ( is_numeric ( $reponse ) == true ) 
			// echo "<p>La r�ponse est un nombre</p>" ; 
			if ( is_numeric ( $reponse ) == true )
			{
				if ( $reponse >= 0 )
				{
					if ( $non_zero != false && $reponse == 0 )
					{
						// on a demand� � ce que la r�ponse ne soit pas z�ro et pourtant elle l'est
						// echo "<p>La r�ponse est z�ro et ne devrait pas l'�tre</p>" ; 
						$diagnostic_reponse['est_valide_reponse'] = false ;
						$diagnostic_reponse['est_invalide_reponse_question_intitule'] = $question_intitule ; 
					}
				}
				else
				{
					// echo "<p>La r�ponse est un nombre n�gatif</p>" ; 
					$diagnostic_reponse['est_valide_reponse'] = false ; // la r�ponse est un nombre n�gatif
					$diagnostic_reponse['est_invalide_reponse_question_intitule'] = $question_intitule ; 
				}
			}
			else
			{
				// echo "<p>La r�ponse n'est pas un nombre</p>" ; 
				$diagnostic_reponse['est_valide_reponse'] = false ; // la r�ponse n'est pas un nombre
				$diagnostic_reponse['est_invalide_reponse_question_intitule'] = $question_intitule ; 
			}
			if ( $est_sans_effet_reponse_numerique )
				// la r�ponse numerique est sans effet et pourtant quelque chose a �t� r�ponu ; on met donc � true la variable suivante : 
				$diagnostic_reponse['est_remplie_et_sans_effet_reponse_numerique'] = true ; 		
			if ( $est_traitement_post_validation )
			{
				if ( $diagnostic_reponse['est_valide_reponse'] == true )
				{
					// echo "<p>La r�ponse est valide (ou invalide mais sans effet)</p>" ; 
					// la r�ponse num�rique est valide (ou �ventuellement sans effet), on la rentre en variable de session
					affecter_variable_session ( $question_reference , $numero , NUMERIQUE ) ; 
				}
				else
				{
					// la r�ponse n'est pas sans effet, et n'est pas valide, on l'enregistre (on enregistre la r�f�rence de la question)
					// $diagnostic_reponse['est_valide_reponse'] = false ; // ici on pourrait identifier la r�ponse en question ! 
					// on annule la variable de session associ�e � l'ancienne saisie
					desaffecter_variable_session ( $question_reference , $numero , NUMERIQUE ) ; 
					// echo "<p>Une r�ponse � la question " . $question_reference . " est invalide. </p>" ; 
				}
			}
		}
		else
		{
			// il n'y a pas de r�ponse
			// echo "<p>Champ num�rique sans r�ponse</p>" ; 
			// on annule l'�ventuelle variable de session associ�e � la r�ponse ! 
			if ( $est_traitement_post_validation ) 
				desaffecter_variable_session ( $question_reference , $numero , NUMERIQUE ) ; 
			if ( $est_sans_effet_reponse_numerique == false ) 
			{
				$diagnostic_reponse['est_complete_reponse'] = false ; 
				if ( $non_zero == false )
				// pour les r�ponses "non z�ro" il est �vident qu'il ne faut pas les laisser vides donc inutile de pr�venir l'utilisateur pour �a ! 
					$diagnostic_reponse['est_vide_reponse_numerique'] = true ; 
				//echo "<p>La r�ponse � la question " . $question_reference . " est incompl�te</p>" ; 
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
			//echo "<p>bouton radio coch� </p>" ; 
			if ( $est_traitement_post_validation ) 
				affecter_variable_session ( $question_reference , $numero , RADIO ) ; 
		}
	}
	else if ( $reponse_type == CHECKBOX )
	{
		// ==================================
		// boutons checkbox : rien � tester ! 
		if ( isSet ( $tableau_reponse[ $question_reference .'_' . CHECKBOX] ) && $tableau_reponse[ $question_reference .'_' . CHECKBOX] != "" ) 
		{
			if ( $est_traitement_post_validation ) 
				affecter_variable_session ( $question_reference , $numero , CHECKBOX ) ; 
		}
		else
		{
			// pas de r�ponse, on tue l'�ventuelle variable
			if ( $est_traitement_post_validation ) 
				desaffecter_variable_session ( $question_reference , $numero , CHECKBOX ) ; 
		}	
	}
	else if ( $reponse_type == SELECT )
	{
		// ==================================
		// boutons select
		// il faut tester si des r�ponses peuvent �tre incompatibles
		if ( $est_traitement_post_validation ) // sinon pas besoin de tester �a
		{
			if ( $tableau_reponse[ $question_reference . '_' . SELECT] == $objet_reponse->rep_valeur )
			{
				$donnees_reponse_incompatible = exec_requete ( "
					SELECT quest_id , quest_nom , rep_intitule , rep_valeur FROM t_lien_reponse , t_reponse , t_question WHERE lien_rep_aval_rep_id = " . $objet_reponse->rep_id . " AND lien_rep_type = 'est_incompatible_avec' AND lien_rep_amont_rep_id = rep_id AND rep_quest_id = quest_id 
					" , $connexion ) ; 
				while ( $objet_reponse_incompatible = objet_suivant ( $donnees_reponse_incompatible ) )
				{
					// il y a des conditions de compatibilit� avec les r�ponses � d'autres questions
					$autre_question_reference = question_reference ( $objet_reponse_incompatible->quest_id , $connexion ) ; 
					$autre_reponse_valeur = $objet_reponse_incompatible->rep_valeur ; 
					// pour le moment cette incompatibilit� ne concerne que deux select diff�rents donc pas la peine de tester l'existence des variables de POST elles existent toujours
					// remarque : la compatibilit� est au sein d'une m�me page ! 
					if ( $tableau_reponse[ $autre_question_reference . '_' . SELECT ] == $autre_reponse_valeur )
					{
						// il y a incompatibilit� entre les r�ponses
						//echo "<p>R�ponses incompatibles : " . $objet_reponse_incompatible->quest_nom . '_intitule' . " et " . $question_intitule . "</p>" ; 
						$diagnostic_reponse['sont_incompatibles_reponses'] [0] = true ; 
						$diagnostic_reponse['sont_incompatibles_reponses'] [1] = $objet_reponse_incompatible->quest_nom . '_intitule' ; 
						// a priori elle est arriv�e avant
						$diagnostic_reponse['sont_incompatibles_reponses'] [2] = $question_intitule ; 
					}			
				}
			}
		}
		else
		{
			// on est en train de v�rifier la compl�tude de la page pour chargement ; il se peut que la page ne contienne que des boutons "Select" et qu'elle n'ait pas �t� valid�e, il faut tester
			if ( !isSet ( $tableau_reponse[ $question_reference .'_' . SELECT] ) ) 
				$diagnostic_reponse['est_complete_reponse'] = false ; 
		}
		// ===========
		// la suite : affectation des variables de session
		// dans ce cas pas besoin de tester le caract�re ou non complet, c'est automatiquement complet car toujours une valeur par d�faut
		if ( $est_traitement_post_validation ) 
			if ( !$diagnostic_reponse['sont_incompatibles_reponses'] [0] ) // si incompatible on n'enregistre rien du tout
				if ( isSet ( $tableau_reponse[ $question_reference .'_' . SELECT] ) ) 
					affecter_variable_session ( $question_reference , $numero , SELECT ) ; 
	}
	//
	/*
	if ( $diagnostic_reponse['est_valide_reponse'] == true )
		// echo "<p>R�ponse valide" ; 
		echo "<p>"; 
	else
		echo "<p>R�ponse invalide" ; 
	if ( $diagnostic_reponse['est_complete_reponse'] == true )
		echo "R�ponse compl�te</p>" ; 
	else
		echo "R�ponse incompl�te</p>" ; 
	*/
	// echo "<pre>" ; print_r ( $diagnostic_reponse ) ; echo "</pre>" ; 
	
	
	return $diagnostic_reponse ; 
}
// ===================================================================================================
// Fonction qui retourne la r�f�rence d'une question
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
// Fonction qui fournit 0 ou une r�ponse num�rique par d�faut, quand la variable de session de r�ponse n'est pas fournie
//==========================================================================================
function rend_valide_virgule ( $chaine )
{
	$chaine = str_replace ( "," , "." , $chaine ) ;
	return $chaine ; 
	// echo $chaine . "<br/>" ; 
}
// ===================================================================================================
// Fonction de d�saffectation de variable de session
// ===================================================================================================
function desaffecter_variable_session ( $question_reference , $numero , $type )
{
	if ( $numero == false )
		unset ( $_SESSION[REPONSE][ $question_reference . '_' . $type ] ) ;
	else
		unset ( $_SESSION[REPONSE][$numero . '_' . $question_reference . '_' . $type ] );
}
// ==============================================================================================================	
// on d�termine le tableau $reponse, qui contient toutes les r�ponses de la page
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
		// au moins une des r�ponses est invalide
		$message_diagnostic_page = $message_diagnostic_page 
			. "<p><span class='reponse_invalide'>Attention&nbsp;: votre ou vos r�ponse(s) � la question&nbsp;:</span></p>" 
			. "<p><strong>" . retourner_texte ( $diagnostic_page['est_invalide_reponse_question_intitule'] , $TEXT )
			. "</strong></p><p><span class='reponse_invalide'>est (sont) invalide(s)</span>.
			Seules vos r�ponses valides ont �t� enregistr�es. Par cons�quent vous devrez encore compl�ter cette page avant d'avoir acc�s aux 				
			r�sultats. Les champs ci-dessous affichent celles de vos r�ponses qui ont �t� enregistr�es.</p>" ; 
		// echo "R�ponse � la question " . $diagnostic_page['est_invalide_reponse_question_intitule'] . " invalide" ; 
	}
	else if ( $diagnostic_page['sont_incompatibles_reponses'][0] )
	{
		// print_r ( $diagnostic_page ['sont_incompatibles_reponses'] ) ; 
		//echo "$diagnostic['sont_incompatibles_reponses'][1]" ; 
		//echo "$diagnostic['sont_incompatibles_reponses'][2]" ; 
		// deux r�ponses sont incompatibles
		$message_diagnostic_page = $message_diagnostic_page 
			. "<p><span class='reponse_invalide'>Attention&nbsp;: vos r�ponses aux questions&nbsp;:</span></p>" 	
			. "<p><strong>" . retourner_texte ( $diagnostic_page['sont_incompatibles_reponses'][1] , $TEXT )
			. "</strong></p><p><span class='reponse_invalide'>et</span></p>"
			. "<p><strong>" . retourner_texte ( $diagnostic_page['sont_incompatibles_reponses'][2] , $TEXT )
			. "</strong></p><p><span class='reponse_invalide'>sont incompatibles</span>. Par cons�quent, votre r�ponse � la seconde de ces 
			deux questions n'a pas pu �tre enregistr�e. Par cons�quent vous devrez encore compl�ter cette page avant d'avoir acc�s aux 				
			r�sultats. Les champs ci-dessous affichent celles de vos r�ponses qui ont �t� enregistr�es.</p>" ; 
	}
	else if ( $diagnostic_page['est_complete_page'] == false )
	{
		// page incompl�te
		$message_diagnostic_page = $message_diagnostic_page 
		. "<p><span class='page_incomplete'>Vos r�ponses pour cette page sont valides mais incompl�tes</span>. 
		Par cons�quent vous devrez encore compl�ter cette page avant d'avoir acc�s aux 				
		r�sultats.</p>" ; 
		if ( $diagnostic_page['est_vide_reponse_numerique'] )
			$message_diagnostic_page = $message_diagnostic_page . "<p><strong class='page_incomplete'>Attention</strong> : le calculateur a d�cel� que vous aviez laiss� un champ num�rique vide
alors que la saisie d'une r�ponse �tait n�cessaire (vous n'avez pas par ailleurs rendu la saisie inop�rante en demandant par exemple � ce que le calcul soit effectu� avec une valeur par d�faut). Dans ce cas, la page est consid�r�e comme incompl�te. Si la r�ponse � la question correspondante est &quot;0&quot;, vous devez saisir la valeur &quot;0&quot; pour que la page puisse �tre consid�r�e comme compl�te.</p>" ; 
		$message_diagnostic_page = $message_diagnostic_page . "</p><p>Les champs ci-dessous affichent vos r�ponses telles qu'elles ont �t� enregistr�es par le calculateur.</p>" ; 
	}
	else
		// page compl�te
		$message_diagnostic_page = $message_diagnostic_page
			. "<p><span class='page_complete'>Vos r�ponses pour cette page sont valides et compl�tes</span>. 
			Les champs ci-dessous affichent vos r�ponses telles qu'elles ont �t� enregistr�es par le calculateur. Vous pouvez acc�der aux autres
			pages du questionnaire � l'aide des fl�ches ci-dessous ou du menu � gauche.</p>" ; 
	// s'il y a une r�ponse num�rique sans effet on pr�vient l'utilisateur
	if ( $diagnostic_page['est_valide_page'] && $diagnostic_page['est_remplie_et_sans_effet_reponse_numerique'] && !$diagnostic_page['est_invalide_reponse_facultative'] )
		$message_diagnostic_page = $message_diagnostic_page . "<p><span class='page_incomplete'>Attention</span>&nbsp;: vous avez saisi une valeur num�rique 
			tout en indiquant par ailleurs au calculateur de ne pas tenir compte de cette valeur (par exemple en lui demandant d'effectuer le calcul
			avec une valeur par d�faut et non avec celle que vous avez saisie). 
			Dans une telle situation, et par convention, le calculateur ne va pas tenir compte de la valeur num�rique que vous avez saisie 
			(cette valeur est tout de m�me enregistr�e, mais sa validit� n'a pas �t� test�e), il va effectuer les calculs comme si aucune saisie
			num�rique n'avait �t� effectu�e dans le champ correspondant.</p>" ; 
	// enfin si on a rendu une autre page incompl�te on le dit
	if ( $diagnostic_page['est_autre_page_rendue_incomplete'] )
		$message_diagnostic_page = $message_diagnostic_page . "<p><span class='page_incomplete'>Attention&nbsp;!</span> Du fait des �ventuelles 
			modifications effectu�es sur cette page il est possible que vos saisies sur la page Logement->consommations d'�nergie pour ce logement
			ne soient plus valides ou compl�tes. Par cons�quent, nous vous invitons � visiter de nouveau 
			la page Logement->consommations d'�nergie pour ce logement (elle devra �tre � nouveau valid�e pour que vous puissiez acc�der � la 
			page de r�sultats). </p>" ; 
	if ( $diagnostic_page['permet_d_acceder_au_resultat'] )
		$message_diagnostic_page = $message_diagnostic_page . "<p><span class='page_complete'>F�licitations&nbsp;!</span> 
			Vous avez compl�t� toutes les pages du questionnaire. Vous pouvez acc�der � la page de r�sultats en cliquant 
			<strong><a href='index.php?type_page=" . PAGE_RESULTAT . "'>ici</a></strong> (ou sur les onglets 'R�sultats' du menu ci-contre)</p>" ; 
	if ( $diagnostic_page['est_invalide_reponse_facultative'] && $diagnostic_page['est_valide_page'] )
		$message_diagnostic_page = $message_diagnostic_page . "<p><span class='page_incomplete'>Attention</span>&nbsp;: vous avez saisi une valeur invalide dans un champ num�rique. Cette saisie n'a pas �t� enregistr�e. Vous avez par ailleurs indiqu� au calculateur de ne pas tenir compte de cette saisie (par exemple en lui demandant d'effectuer le calcul avec une valeur par d�faut et non avec celle que vous avez saisie). </p>" ; 
	// on ferme la boite
	$message_diagnostic_page = $message_diagnostic_page . "</div>" ; 
	return $message_diagnostic_page ; 
}
?>