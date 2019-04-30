<?php 
//==========================================================================================
// Nouvelle sauvegarde
//==========================================================================================
function traitement_post_nouvelle_sauvegarde ( $util_id , $nom_sauvegarde , $connexion )
{
	if ( $util_id )
	{
		// la session est toujours active
		// la sauvegarde a déjà été réalisée en début du fichier index (de façon à pouvoir récupérer l'indice sauv_id et à afficher le nom de la sauvegarde dans le menu
		//==========================================
		// Affichage du résultat et du nouveau tableau des sauvegardes
		//==========================================
		echo "<p>Vos saisies ont bien été sauvegardées sous le nom " . $nom_sauvegarde . ".</p>\n" ; 
		echo "<p>Liste de vos sauvegardes : </p>\n" ; 
		tableau_sauvegarde ( $util_id , $connexion ) ; 
	}
	else
		annoncer_expiration_session () ; 
}
//==========================================================================================
// Remplacer sauvegarde
//==========================================================================================
function remplacer_sauvegarde ( $util_id , $sauv_id , $connexion )
{
	if ( $util_id )  // normalement c'est le cas sauf expiration de session
	{
		remplacer_sauvegarde_bas_niveau ( $util_id , $sauv_id , $connexion ) ; 
		echo "<p>Votre saisie actuelle a bien été sauvegardée.</p>\n" ; 
	}
	else
		annoncer_expiration_session () ; 
}
//==========================================================================================
// Annonce expiration de session
//==========================================================================================
function annoncer_expiration_session ()
{
	echo "<p>Nous sommes désolé mais votre session a expiré, et par conséquent l'opération n'a pas pu avoir lieu.</p>"
		. "<p>Vous n'êtes plus identifié.</p>" ; 
}
//==========================================================================================
// Supprimer sauvegarde
//==========================================================================================
function supprimer_sauvegarde ( $util_id , $sauv_id , $nom_sauvegarde , $connexion )
{
	supprimer_sauvegarde_bas_niveau ( $sauv_id , $connexion ) ; 
	echo "<p>Suite à votre confirmation, votre sauvegarde &quot;" . $nom_sauvegarde . "&quot; a bien été supprimée.</p>\n" ; 
	$ns = nombre_sauvegarde ( $util_id , $connexion ) ; 
	if ( $ns == 0 )
		echo "<p>Vous ne disposez plus d'aucune sauvegarde sur le calculateur.</p>\n" ; 
	else
	{
		echo "<p>Liste de vos sauvegardes : </p>\n" ; 
		tableau_sauvegarde ( $util_id , $connexion ) ; 
	}
}
//==========================================================================================
// Annuler charger sauvegarde
//==========================================================================================
function annuler_charger_sauvegarde ( $nom_sauvegarde , $connexion )
{
	echo "<p>Suite à cette annulation, votre sauvegarde intitulée &quot;" . $nom_sauvegarde . "&quot; n'a pas été chargée.</p>\n" ; 
}
//==========================================================================================
// Charger sauvegarde
//==========================================================================================
function charger_sauvegarde ( $connexion )
{
	$util_id = $_GET[UTIL_ID] ; 
	$sauv_id = $_GET['sauv_id'] ; 
	$nom_sauvegarde = $_GET['nom_sauvegarde'] ; 
	// ======================================
	// on commence par supprimer toutes les variables de session associées à la saisie actuelle 
	unset ( $_SESSION [SAUV_ID] ) ; 	
	unset ( $_SESSION [PAYS_ID] ) ; 
	unset ( $_SESSION [TYPE_BC_ID] ) ; 
	unset ( $_SESSION [MENU_NOMBRE] ) ; 
	unset ( $_SESSION [MENU] ) ; 
	unset ( $_SESSION [PAGE_COMPLETE] ) ; 
	unset ( $_SESSION [REPONSE] ) ; 
	unset ( $_SESSION [RESULTAT] ) ; 
	// ======================================
	// données issues de la table t_sauvegarde
	$donnees_sauvegarde = exec_requete ( "SELECT * FROM t_sauvegarde WHERE sauv_id = '$sauv_id' " , $connexion ) ; 
	$objet_sauvegarde = objet_suivant ( $donnees_sauvegarde ) ; 
	$_SESSION [SAUV_ID] = $sauv_id ; 
	$_SESSION [TYPE_BC_ID] = $objet_sauvegarde->sauv_type_bc_id ; 
	// ======================================
	// mise en garde si changement de version
	$ancienne_version_id = $objet_sauvegarde->sauv_version_id ; 
	if ( $_SESSION [VERSION_ID] != $ancienne_version_id )
		echo "<p>Attention ! la sauvegarde que vous avez chargée a été réalisée à partir d'une ancienne version de ce calculateur en ligne de BILAN CARBONE Personnel. Par conséquent, il est possible que certaines saisies n'apparaissent plus, que certaines pages complètes au moment de la sauvegarde ne le soient plus, et que les résultats ne soient pas identiques. </p>" ; 
	// ======================================
	// données issues de la table t_saisie_menu_nombre
	$donnees_saisie_menu_nombre = exec_requete ( "
		SELECT * FROM t_saisie_menu_nombre WHERE saisie_menu_nombre_sauv_id = '$sauv_id' " , $connexion ) ; 
	while ( $objet_saisie_menu_nombre = objet_suivant ( $donnees_saisie_menu_nombre ) )
	{
		$type = $objet_saisie_menu_nombre->saisie_menu_nombre_type ; 
		if ( $type == 'rubrique' )
			$nom = nom_rubrique ( $objet_saisie_menu_nombre->saisie_menu_nombre_rub_id , $connexion ) ; 
		else
			$nom = nom_page ( $objet_saisie_menu_nombre->saisie_menu_nombre_page_id , $connexion ) ; 
		$_SESSION [MENU_NOMBRE] [$nom] = $objet_saisie_menu_nombre->saisie_menu_nombre_nombre ; 
	}
	// echo "<pre>" ; print_r ( $_SESSION [MENU_NOMBRE] ) ; echo "</pre>" ; 
	// ======================================
	$donnees_question = exec_requete ("SELECT quest_id FROM t_question" , $connexion ) ; 
	while ( $objet_question = objet_suivant ( $donnees_question ) )
	{
		$quest_id = $objet_question->quest_id ; 
		$nombre = est_repetee_question_renvoyer_nombre ( $quest_id , $connexion ) ; // OK car la varialbe de session $_SESSION [MENU_NOMBRE] a bien été créée
		// ======================================
		// données issues de la table t_saisie_numerique
		$donnees_reponse = exec_requete ( "
		SELECT sais_num_valeur , sais_num_rep_id , sais_num_sauv_id , sais_num_numero , rep_id
		FROM t_saisie_numerique , t_reponse
		WHERE sais_num_rep_id = rep_id AND rep_quest_id = '$quest_id' AND sais_num_sauv_id = '$sauv_id'
		" , $connexion ) ; 
		while ( $objet_reponse = objet_suivant ( $donnees_reponse ) ) 
		{
			if ( $nombre )
				$numero = $objet_reponse->sais_num_numero ; 
			else
				$numero = false ; 
			$index_reponse = index_reponse ( $quest_id , $numero , $connexion ) . '_' . NUMERIQUE ; 
			$_SESSION[REPONSE][$index_reponse] = $objet_reponse->sais_num_valeur ; 
		}
		// ======================================
		// données issues de la table t_saisie_discrete
		$donnees_reponse = exec_requete ( "
		SELECT sais_disc_rep_id , sais_disc_numero , sais_disc_sauv_id , rep_id , rep_valeur , rep_type
		FROM t_saisie_discrete , t_reponse
		WHERE sais_disc_rep_id = rep_id AND rep_quest_id = '$quest_id' AND sais_disc_sauv_id = '$sauv_id'
		" , $connexion ) ; 
		while ( $objet_reponse = objet_suivant ( $donnees_reponse ) ) 
		{
			if ( $nombre )
				$numero = $objet_reponse->sais_disc_numero ; 
			else
				$numero = false ; 
			$index_reponse = index_reponse ( $quest_id , $numero , $connexion ) . '_' . $objet_reponse->rep_type ; 
			$_SESSION[REPONSE][$index_reponse] = $objet_reponse->rep_valeur ; 
		}
	}
	// ======================================
	// affectation des variables $_SESSION [MENU] et $_SESSION [PAGE_COMPLETE] pour l'affichage du menu
	affecter_completude_pages ( false , $connexion ) ; // false veut dire que ça n'est pas une remise à zéro 
	// echo "<pre>" ; print_r ( $_SESSION[REPONSE] ) ; echo "</pre>" ; 
}
//==========================================================================================
function nom_rubrique ( $rub_id , $connexion )
{
	$donnees = exec_requete ( "SELECT rub_nom FROM t_rubrique WHERE rub_id = '$rub_id' " , $connexion ) ; 
	if ( $objet = objet_suivant ( $donnees ) )
		$nom = $objet->rub_nom ; 
	else 
		echo "<p>rub_id non reconnu</p>" ; 
	return $nom ; 
}
//==========================================================================================
function nom_page ( $page_id , $connexion )
{
	$donnees = exec_requete ( "SELECT page_nom FROM t_page WHERE page_id = '$page_id' " , $connexion ) ; 
	if ( $objet = objet_suivant ( $donnees ) )
		$nom = $objet->page_nom ; 
	else 
		echo "<p>page_id non reconnue</p>" ; 
	return $nom ; 
}
//==========================================================================================
function nom_question ( $quest_id , $connexion )
{
	$donnees = exec_requete ( "SELECT quest_nom FROM t_question WHERE quest_id = '$quest_id' " , $connexion ) ; 
	if ( $objet = objet_suivant ( $donnees ) )
		$nom = $objet->quest_nom ; 
	else 
		echo "<p>page_id non reconnue</p>" ; 
	return $nom ; 
}
//==========================================================================================
// retourne l'index pour la variable $_SESSION[REPONSE] à partir de l'id de la question
//==========================================================================================
function index_reponse ( $quest_id , $numero , $connexion )
{
	$donnees = exec_requete ( "SELECT
	quest_id , quest_nom , quest_page_id , page_id , page_nom , page_rub_id , rub_id , rub_nom
	FROM
	t_question , t_page , t_rubrique
	WHERE
	quest_id = '$quest_id' AND quest_page_id = page_id AND page_rub_id = rub_id 
	" , $connexion ) ; 
	$objet_donnees = objet_suivant ( $donnees ) ; 
	$index = $objet_donnees->rub_nom . '_' . $objet_donnees->page_nom . '_' . $objet_donnees->quest_nom ; 
	if ( $numero )
		$index = $numero . '_' . $index ; 
	return $index ; 
}
?>