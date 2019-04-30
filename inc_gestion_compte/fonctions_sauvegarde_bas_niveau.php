<?php 
//==========================================================================================
// Compte le nombre de sauvegardes
//==========================================================================================
function nombre_sauvegarde ( $util_id , $connexion )
{
	$donnees_nombre_sauvegarde = exec_requete ( "SELECT COUNT(*) FROM t_sauvegarde WHERE sauv_util_id = $util_id", $connexion ) ; 
	$nombre_sauvegarde = mysql_fetch_row ( $donnees_nombre_sauvegarde ) ;
	return $nombre_sauvegarde[0] ; 
}
//==========================================================================================
// Supprimer une saisie (mais conserver la sauvegarde !)
//==========================================================================================
function supprimer_saisie ( $sauv_id , $connexion )
{
	// les deux tables des saisies
	exec_requete ("DELETE FROM t_saisie_menu_nombre WHERE saisie_menu_nombre_sauv_id = '$sauv_id' ", $connexion ) ; 
	exec_requete ("DELETE FROM t_saisie_numerique WHERE sais_num_sauv_id = '$sauv_id' ", $connexion ) ; 
	exec_requete ("DELETE FROM t_saisie_discrete WHERE sais_disc_sauv_id = '$sauv_id' ", $connexion ) ; 
}
//==========================================================================================
// Supprimer sauvegarde
//==========================================================================================
function supprimer_sauvegarde_bas_niveau ( $sauv_id , $connexion )
{
	// table des sauvegardes
	exec_requete ("DELETE FROM t_sauvegarde WHERE sauv_id='$sauv_id' ", $connexion ) ; 
	supprimer_saisie ( $sauv_id , $connexion ) ; 
}
//==========================================================================================
// Remplacer (bas niveau)
//==========================================================================================
function remplacer_sauvegarde_bas_niveau ( $util_id , $sauv_id , $connexion )
{
		// saisie ou non complète
	if ( in_array ( false , $_SESSION [PAGE_COMPLETE] ) )
		$sauv_est_saisie_complete = 'false' ; 
	else
		$sauv_est_saisie_complete = 'true' ; 
	// date
	$format = 'Y-m-d H:i:s' ; 
	$date = date ( $format ) ; 
	// pays, version, type_bc
	$sauv_pays_id = addslashes ( $_SESSION [PAYS_ID] ) ; 
	$sauv_version_id = addslashes ( $_SESSION [VERSION_ID] ) ; 
	$sauv_type_bc_id = addslashes ( $_SESSION [TYPE_BC_ID] ) ; 
	exec_requete ("UPDATE t_sauvegarde 
	SET sauv_est_saisie_complete = '$sauv_est_saisie_complete' , sauv_date_time = '$date' , sauv_pays_id = '$sauv_pays_id' , 
	sauv_version_id = '$sauv_version_id' , sauv_type_bc_id = '$sauv_type_bc_id'
	WHERE sauv_id = '$sauv_id' " , $connexion ) ; 
	//==========================================
	// ENREGISTREMENT DES REPONSES DE L'UTILISATEUR
	//==========================================
	supprimer_saisie ( $sauv_id , $connexion ) ; 
	sauvegarder_saisie ( $sauv_id , $connexion ) ; 
}
//==========================================================================================
// Nouvelle sauvegarde (bas niveau)
//==========================================================================================
function nouvelle_sauvegarde_bas_niveau_retourner_sauv_id ( $util_id , $nom_sauvegarde , $connexion )
{
	$nom_sauvegarde_bdd = addslashes ( $nom_sauvegarde ) ; 
	// saisie ou non complète
	if ( in_array ( false , $_SESSION [PAGE_COMPLETE] ) )
		$sauv_est_saisie_complete = 'false' ; 
	else
		$sauv_est_saisie_complete = 'true' ; 
	// date
	$format = 'Y-m-d H:i:s' ; 
	$date = date ( $format ) ; 
	// ================================
	// table t_sauvegarde
	// (pays, version, type_bc)
	$sauv_pays_id = addslashes ( $_SESSION [PAYS_ID] ) ; 
	$sauv_version_id = addslashes ( $_SESSION [VERSION_ID] ) ; 
	$sauv_type_bc_id = addslashes ( $_SESSION [TYPE_BC_ID] ) ; 
	exec_requete ("INSERT INTO t_sauvegarde 
		( sauv_util_id , sauv_nom , sauv_est_saisie_complete , sauv_date_time , sauv_pays_id , sauv_version_id , sauv_type_bc_id) 
		VALUES 
		( '$util_id' , '$nom_sauvegarde_bdd' , '$sauv_est_saisie_complete' , '$date' , '$sauv_pays_id' , '$sauv_version_id' , '$sauv_type_bc_id' ) " , $connexion ) ; 
	$sauv_id = mysql_insert_id () ; 
	//==========================================
	// ENREGISTREMENT DES REPONSES DE L'UTILISATEUR
	//==========================================
	sauvegarder_saisie ( $sauv_id , $connexion ) ; 
	return $sauv_id ; 
}
//==========================================================================================
// Sauvegarde des saisies
//==========================================================================================
function sauvegarder_saisie ( $sauv_id , $connexion )
{
	// echo "<pre>" ; print_r ( $_SESSION[REPONSE] ) ; echo "</pre>" ; 
	// =================================================================================
	// On sauvegarde les variables de $_SESSION [MENU_NOMBRE]
	// =========
	// rubriques
	$donnees_rubrique = exec_requete ("SELECT * FROM t_rubrique ", $connexion ) ;
	while ( $objet_rubrique = objet_suivant ( $donnees_rubrique ) )
	{
		$rub_est_repetee = $objet_rubrique->rub_est_repetee ; 
		if ( $rub_est_repetee == 'true' ) 
		{
			$rub_id = $objet_rubrique->rub_id ; 
			$rub_nom = $objet_rubrique->rub_nom ; 
			$nombre = $_SESSION[MENU_NOMBRE][$rub_nom] ; 
			exec_requete ( "INSERT INTO t_saisie_menu_nombre 
			( saisie_menu_nombre_nombre , saisie_menu_nombre_type , saisie_menu_nombre_rub_id , saisie_menu_nombre_sauv_id )
			VALUES
			( '$nombre' , 'rubrique' , '$rub_id' , '$sauv_id' )
			" , $connexion ) ; 
		}
	}
	// =========
	// pages
	$donnees_page = exec_requete ("SELECT * FROM t_page ", $connexion ) ;
	while ( $objet_page = objet_suivant ( $donnees_page ) )
	{
		$page_est_repetee = $objet_page->page_est_repetee ; 
		if ( $page_est_repetee == 'true' ) 
		{
			$page_id = $objet_page->page_id ; 
			$page_nom = $objet_page->page_nom ; 
			$nombre = $_SESSION[MENU_NOMBRE][$page_nom] ; 
			exec_requete ( "INSERT INTO t_saisie_menu_nombre 
			( saisie_menu_nombre_nombre , saisie_menu_nombre_type , saisie_menu_nombre_page_id , saisie_menu_nombre_sauv_id )
			VALUES
			( '$nombre' , 'page' , '$page_id' , '$sauv_id' )
			" , $connexion ) ; 
		}
	}
	// =================================================================================
	// On parcourt toutes les questions
	$donnees_question = exec_requete 
		("SELECT rub_nom , page_nom , quest_id , quest_nom 
			FROM t_rubrique, t_page , t_question 
			WHERE quest_page_id = page_id AND page_rub_id = rub_id ", $connexion ) ;
	while ( $objet_question = objet_suivant ( $donnees_question ) )
	{
		$quest_id = $objet_question->quest_id ; 
		$quest_nom = $objet_question->rub_nom . '_' . $objet_question->page_nom . '_' . $objet_question->quest_nom ; 
		if ( $nombre = est_repetee_question_renvoyer_nombre ( $quest_id , $connexion ) ) 
			// la question est répétée
			for ( $numero=1 ; $numero <= $nombre ; $numero++ )
				insertion_sauvegarde ( $quest_id , $quest_nom , $numero , $sauv_id , $connexion ) ; 
		else
			// la question n'est pas répétée
			insertion_sauvegarde ( $quest_id , $quest_nom , false , $sauv_id , $connexion ) ; 
	}
}
//==========================================================================================
// Requête d'insertion (pour une question particulière)
//==========================================================================================
function insertion_sauvegarde ( $quest_id , $quest_nom , $numero , $sauv_id , $connexion )
{
	// echo "<p>Insertion sauvegarde pour la question d'id : " . $quest_id . "</p>" ; 
	$tableau_type_reponse = array ( 'num' , 'select' , 'radio' , 'checkbox' ) ; 
	foreach ( $tableau_type_reponse as $type_reponse )
	{
		$index = $quest_nom . '_' . $type_reponse ; 
		if ( $numero )
			$index = $numero . '_' . $index ; 
		// echo "<p>" . $index . "</p>" ; 
		if ( isSet ( $_SESSION[REPONSE][$index] ) )
		{
			$reponse = $_SESSION[REPONSE][$index] ; 
			// echo "<p>Question : " . $index . " Réponse : " . $reponse . "</p>" ; 
			if ( $type_reponse == 'num' )
			{
				// réponse numérique, on cherche l'id de la réponse
				$donnees_reponse = exec_requete ( "SELECT rep_id FROM t_reponse WHERE rep_quest_id = '$quest_id' AND rep_type = 'num' ", $connexion ) ; 
				$objet_reponse = objet_suivant ( $donnees_reponse ) ; 
				$rep_id = $objet_reponse->rep_id ; 
				// écriture de la requête
				$requete = "INSERT INTO t_saisie_numerique 
				( sais_num_rep_id , sais_num_valeur , sais_num_numero , sais_num_sauv_id )
				VALUES
				( '$rep_id' , '$reponse' , '$numero' , '$sauv_id' )" ; 
			}
			else
			{
				// réponse discrète, on cherche l'id de la réponse
				// il faudrait tester si la réponse est la chaine vide ou non ????
				$donnees_reponse = exec_requete ( "SELECT rep_id FROM t_reponse 
					WHERE rep_quest_id = '$quest_id' AND rep_type = '$type_reponse' and rep_valeur = '$reponse' 
					" , $connexion ) ; 
				if ( $objet_reponse = objet_suivant ( $donnees_reponse ) )
				{
					$rep_id = $objet_reponse->rep_id ; 
					// écriture de la requête
					$requete = "INSERT INTO t_saisie_discrete
						( sais_disc_rep_id , sais_disc_numero , sais_disc_sauv_id )
						VALUES
						( '$rep_id' , '$numero' , '$sauv_id' )" ; 
				}
				else
					$requete = false ; 
			}
			// exécution de la requête
			if ( $requete ) 
				exec_requete ( $requete , $connexion ) ; 
		}
	}
}
//==========================================================================================
// Teste si une question est répétée, si oui renvoie le nombre d'occurrence, sinon renvoie false
//==========================================================================================
function est_repetee_question_renvoyer_nombre ( $quest_id , $connexion )
{
	$nombre = false ; 
	// on teste si la page est répétée
	$donnees_question = exec_requete ("SELECT * FROM t_question WHERE quest_id = '$quest_id' ", $connexion ) ;
	$objet_question = objet_suivant ( $donnees_question ) ; 
	$quest_page_id = $objet_question->quest_page_id ; 
	//
	$donnees_page = exec_requete ("SELECT * FROM t_page WHERE page_id = '$quest_page_id' ", $connexion ) ;
	$objet_page = objet_suivant ( $donnees_page ) ; 
	$page_est_repetee = $objet_page->page_est_repetee ; 
	if ( $page_est_repetee == 'true' )
	{
		$nom_page = $objet_page->page_nom ; 
		$nombre = $_SESSION[MENU_NOMBRE][$nom_page] ; 
	}
	else
	{
		// la page n'est pas répétée, on va regarder si la rubrique est répétée
		$page_rub_id = $objet_page->page_rub_id ; 
		$donnees_rubrique = exec_requete ("SELECT * FROM t_rubrique WHERE rub_id = '$page_rub_id' ", $connexion ) ;
		$objet_rubrique = objet_suivant ( $donnees_rubrique ) ; 
		$rub_est_repetee = $objet_rubrique->rub_est_repetee ; 
		if ( $rub_est_repetee == 'true' )
		{
			$nom_rubrique = $objet_rubrique->rub_nom ; 
			$nombre = $_SESSION[MENU_NOMBRE][$nom_rubrique] ; 
		}
	}
	return $nombre ; 
}
?>