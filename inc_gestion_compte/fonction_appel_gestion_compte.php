<?php 

//==========================================================================================
// Afficher gestion compte
//==========================================================================================
function afficher_page_gestion_compte ( $TEXT , $util_id , $connexion )
{
	// =========================================================
	// on récupère ici les variables GET pour pas à avoir à le faire à chaque fois par la suite
	// ça permet de garantir la transmission de l'identifiant de l'utilisateur même si la session a expiré
	if ( isSet ( $_GET[UTIL_ID] ) )
		$util_id = $_GET[UTIL_ID] ; 
	if ( isSet ( $_GET['sauv_id'] ) )
		$sauv_id = $_GET['sauv_id'] ; 
	if ( isSet ( $_GET['nom_sauvegarde'] ) )
		$nom_sauvegarde = $_GET['nom_sauvegarde'] ; 
	// =========================================================
	if ( isSet ( $_GET ['page'] ) )
	{
		// =========================================================
		// remise à zéro 
		$page = $_GET ['page'] ; 
		if ( $page == REMETTRE_A_ZERO ) 
			demande_confirmation_remise_a_zero () ; 
		if ( $page == CONFIRMER_REMETTRE_A_ZERO ) 
			remise_a_zero () ; 
		if ( $page == ANNULER_REMETTRE_A_ZERO )
			annuler_remise_a_zero () ; 
		// =========================================================
		// CONNEXION
		// se connecter
		if ( $page == SE_CONNECTER )
			se_connecter () ; 
		// créer compte
		if ( $page == CREER_COMPTE )
			creer_compte () ; 
		// se connecter à un compte existant
		if ( $page == S_IDENTIFIER )
			s_identifier () ; 
		// demander un nouveau mot de passe
		if ( $page == DEMANDE_NOUVEAU_PASS )
		{
			$util_id = $_GET['util_id'] ; 
			$courriel = $_GET['courriel'] ; 
			traitement_demande_nouveau_mot_de_passe ( $util_id , $courriel , $connexion ) ; 
		}
		// =========================================================
		// se déconnecter
		if ( $page == SE_DECONNECTER )
			demande_confirmation_se_deconnecter () ; 
		if ( $page == CONFIRMER_SE_DECONNECTER )
			se_deconnecter () ; 
		if ( $page == ANNULER_SE_DECONNECTER )
			annuler_se_deconnecter () ; 
		// =========================================================
		// SAUVEGARDES
		// sauvegarder
		if ( $page == SAUVEGARDER )
			sauvegarder ( $util_id , $connexion ) ; 
		if ( $page == CONFIRMER_SAUVEGARDER )
			remplacer_sauvegarde ( $util_id , $sauv_id, $connexion ) ; 
		if ( $page == ANNULER_SAUVEGARDER )
			annuler_sauvegarder ( $util_id , $nom_sauvegarde , $connexion ) ; 
		//
		// nouvelle sauvegarde
		if ( $page == NOUVELLE_SAUVEGARDE )
			nouvelle_sauvegarde ( $util_id , $connexion ) ; 
		// menu sauvegarde
		if ( $page == MENU_SAUVEGARDE )
			menu_sauvegarde ( $util_id , $connexion ) ; 
		// renommer_sauvegarde
		if ( $page == RENOMMER_SAUVEGARDE )
			renommer_sauvegarde ( $util_id , $sauv_id , $nom_sauvegarde , $connexion ) ; 
		//
		// supprimer sauvegarde
		if ( $page == SUPPRIMER_SAUVEGARDE )
			demande_confirmation_supprimer_sauvegarde ( $util_id , $sauv_id , $nom_sauvegarde , $connexion ) ; 
		if ( $page == ANNULER_SUPPRIMER_SAUVEGARDE )
			annuler_supprimer_sauvegarde ( $util_id , $nom_sauvegarde , $connexion ) ; 
		if ( $page == CONFIRMER_SUPPRIMER_SAUVEGARDE )
			supprimer_sauvegarde ( $util_id , $sauv_id , $nom_sauvegarde , $connexion ) ; 
		//
		// charger sauvegarde
		if ( $page == CHARGER_SAUVEGARDE )
			demande_confirmation_charger_sauvegarde ( $util_id , $sauv_id , $nom_sauvegarde , $connexion ) ; 
		if ( $page == CONFIRMER_CHARGER_SAUVEGARDE )
			afficher_charger_sauvegarde ( $nom_sauvegarde ) ; 
		if ( $page == ANNULER_CHARGER_SAUVEGARDE )
			annuler_charger_sauvegarde ( $nom_sauvegarde , $connexion ) ; 

	}
}


?>