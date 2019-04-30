<?php 
//==============================================================================
// CREATION DU COMPTE : TEST DU COURRIEL ET ENVOI DU MOT DE PASSE
//==============================================================================
// validité sémantique du courriel
function est_valide_courriel ($courriel)
{
	return preg_match("/^(\w|-|\.)+@((\w|-)+\.)+[a-z]{2,6}$/i", $courriel) ;
}
//==============================================================================
// le serveur accepte-t-il les courriels ? 
function accepte_courriels_serveur ( $courriel )
// vérifie que le serveur accepte les emails
{
	$accepte_courriels_serveur = TRUE ; // par défaut
	list ( $compte , $domaine ) = split ("@", $courriel, 2) ; 
	if ( EXISTE_FONCTION_CHECKDNSRR )// pas disponible sous windows
	{
	if ( !checkdnsrr ( $domaine, "MX" ) && !checkdnsrr ( $domaine , "A" ) )
		$accepte_courriels_serveur = FALSE ; 
	}
	return $accepte_courriels_serveur ;
}
//==============================================================================
// création d'un mot de passe
function creer_pass ( )
{
	// generer automatiquement un mot de passe 
	$pass = mt_rand ();
	return $pass ; 
}
//==============================================================================
// envoi d'un mot de passe
function envoyer_pass ( $courriel, $pass )
{
	$message = "Bonjour, \n\n" 
	//. "Vous vous avez indiqué cette adresse de courriel pour créer un compte sur le site http://www.bilancarbonepersonnel.org.\n" 
	. "Votre mot de passe pour accéder à votre compte BILAN CARBONE Personnel est : \n" 
	. $pass 
	. "\n\n"
	. "Merci de votre intérêt et à tout de suite sur le site du BILAN CARBONE Personnel !\n\n" 
	. "Le webmestre du site." ;
	$en_tete = "From: webmestre@bilancarbonepersonnel.org\n" ;
	//$en_tete .= "Reply-to: contact@avenirclimatique.org\n" ; 
	mail ( $courriel, 'BILAN CARBONE Personnel : votre mot de passe', $message, $en_tete );
}
//==========================================================================================
// teste si un courriel est enregistré, si oui retourne l'identifiant associé, sinon retourne false
function est_enregistre_courriel_retourner_util_id ( $courriel , $connexion )
{
	$util_id = false ; 
	$courriel = addslashes ( md5 ( $courriel ) )  ; 
	$donnees_utilisateur = exec_requete ( "SELECT util_id FROM t_utilisateur WHERE util_courriel='$courriel' ", $connexion ) ;
	if ( $objet_utilisateur = objet_suivant ( $donnees_utilisateur ) ) 
		$util_id = $objet_utilisateur->util_id ; 
	else
		$util_id = false ; 
	return $util_id ; 
}
//==============================================================================
// enregistrement d'un courriel et d'un mot de passe et renvoi de l'identifiant de l'utilisateur
function enregistrer_courriel_pass_retourner_util_id ( $courriel, $pass, $connexion )
{
	$courriel = addSlashes ( md5 ( $courriel ) ) ; 
	$pass = addSlashes ( md5 ($pass) ) ; 
	exec_requete ("INSERT INTO t_utilisateur ( util_courriel , util_pass_prov ) VALUES 
		('$courriel' , '$pass' ) ", $connexion ) ; 
	// il y a un mot de passe provisoire pour éviter que quelqu'un qui n'est pas propriétaire du courriel puisse changer le mot de passe de celui qui l'est
	$util_id = mysql_insert_id() ; 
	return $util_id ; 
}
//==============================================================================
function enregistrer_nouveau_pass ( $util_id , $pass , $connexion ) 
{
	$pass = addSlashes ( md5 ($pass) ) ; 
	exec_requete ("UPDATE t_utilisateur SET util_pass_prov = '$pass' WHERE util_id = '$util_id' ", $connexion ) ; 
}
//==============================================================================
// teste la validité d'un mot de passe, renvoie true ou false
function est_valide_pass ( $util_id , $pass , $connexion )
{
	// la fonction convient à la fois dans le cas d'une identification ou de la création d'un compte
	$est_valide_pass = false ; // pessimiste
	//
	// préparation des variables $courriel et $pass
	$pass = addSlashes ( md5 ($pass) ) ; 
	//
	// chargement 
	// echo "util_id : " . $util_id ; 
	$donnees_utilisateur = exec_requete ("SELECT * FROM t_utilisateur WHERE 
		util_id = $util_id " , $connexion ) ; 
	$objet_utilisateur = objet_suivant ( $donnees_utilisateur ) ; 
	$vrai_pass = $objet_utilisateur->util_pass ; 
	$vrai_pass_prov = $objet_utilisateur->util_pass_prov ; 
	//
	if ( $pass == $vrai_pass ) 
		// rien de spécial, le mot de passe est valide
		$est_valide_pass = true ; 
	else if ( $pass == $vrai_pass_prov ) 
	{
		$est_valide_pass = true ; 
		// on met à jour le mot de passe "non provisoire" dans la bdd
		$vrai_pass_prov = addSlashes ( $vrai_pass_prov ) ; // ??????????
		exec_requete ("UPDATE t_utilisateur SET util_pass = '$vrai_pass_prov' 
			WHERE util_id = $util_id ", $connexion ) ; 
		$est_valide_courriel = $objet_utilisateur->util_est_valide_courriel ; 
		if ( $est_valide_courriel != 'true' )
		{
			// le courriel n'était pas valide jusque-là, autrement dit c'est la première fois qu'il est validé par mot de passe
			$format = 'Y-m-d H:i:s' ; 
			$date_time = date ( $format ); 
			exec_requete ("UPDATE t_utilisateur SET util_est_valide_courriel = 'true', util_date_time_premiere_validation_courriel ='$date_time' 
				WHERE util_id = $util_id ", $connexion ) ; 
		}
	}
	//
	// si on est connecté en tant qu'administrateur alors le pass est considéré comme bon
	if ( isSet ( $_SESSION[MODE_ADMIN] ) ) 
		$est_valide_pass = true ; 
	//
	return $est_valide_pass ; 
}
/*
//==========================================================================================
// Old
//==========================================================================================
function gestion_compte_old ( $TEXT )
{
		$page = $_GET ['page'] ; 
		//echo "<p>La page est bien définie</p>" ; 
		if ( $page == CONNEXION )
		{
			echo "<h2>";
			afficher_texte('titre_connexion',$TEXT);
			echo "</h2>\n ";

			echo "<p>Actions possibles&nbsp;:</p>\n" ;
			echo "\n <ul>\n <li>" ;
			
			if ($_SESSION['connecte'])
			{
				// l'utilisateur est connecté
				echo "<a href='index.php?type_page=" . GESTION_COMPTE . "&amp;page=" . REDIRECTION_DECONNEXION . "'>Se déconnecter</a>";
				echo "</li>\n<li>";
				echo "<a href='index.php?type_page=" . GESTION_COMPTE . "&amp;page=" . CONNEXION . "&amp;action=" . SAUVEGARDER . "'>
					Sauvegarder vos réponses actuelles</a> 
					(si vous aviez déjà effectué une sauvegarde, l'ancienne sauvegarde sera écrasée par la nouvelle).";
				echo "</li>\n<li>";
				echo "<a href='index.php?type_page=" . GESTION_COMPTE . "&amp;page=" . CONNEXION . "&amp;action=" . CHARGER . "'>
					Charger vos résultats contenus dans la base</a> (cette opération remplace vos données
					de sessions actuelles par celle de votre sauvegarde). " ;
			}
			else
			{
				// l'utilisateur n'est pas connecté
				echo "<a href='index.php?type_page=" . GESTION_COMPTE . "&amp;page=" . CONNEXION . "&amp;action=" . CREER_COMPTE . "'>
					Créer un compte</a>" ;
				echo "</li>\n<li>";
				echo "<a href='index.php?type_page=" . GESTION_COMPTE . "&amp;page=" . CONNEXION . "&amp;action=" . SE_CONNECTER . "'>
					Se connecter à un compte</a>" ;
				echo "</li>\n<li>";
				echo "<a href='index.php?type_page=" . GESTION_COMPTE . "&amp;page=" . CONNEXION . "&amp;action=" . ENVOYER_PASS . "'>
					J'ai oublié mon mot de passe et je voudrais en recevoir un nouveau par mail</a>" ;
			}
			echo "</li>\n<li>";
			echo "<a href='index.php?type_page=" . GESTION_COMPTE . "&page=" . CONNEXION . "&action=" . REMETTRE_A_ZERO . "'>
				Remettre les valeurs par défaut à l'ensemble des questions</a>" ;
			echo "</li>\n</ul>\n\n";
			//echo 'Par défaut en vous connectant vos réponses actuelles sont conservées et sont complétées avec les réponses de votre compte. Une fois connecté vous pourrez choisir';

			if( $_GET['action']== SE_CONNECTER )
			{
				echo '<h3>Se connecter</h3>';
				echo '<form name="mon_formulaire">';
					echo '<table>';
						echo '<tr><td><center>Votre adresse courriel :</center></td><td><input type="text" name="email"/></td></tr>';
						echo '<tr><td><center>Votre mot de passe :</center></td><td><input type="password" name="mp"/></td></tr>';
						echo '<input type="hidden" name="choice" value="se_connecter">';
					echo '</center></table>';
					echo '<input type="button" value="';
					afficher_texte('se_connecter',$TEXT);
					echo '" onClick="javascript:valider_envoie()"/>';
				echo '</form>';
			}

			if($_GET['action']== CREER_COMPTE )
			{
				echo '<h3>creer un compte</h3>';
				echo '<form name="mon_formulaire">';
					echo '<table>';
						echo '<tr><td><center>Votre adresse courriel actuelle :</center></td><td><input type="text" name="email"/></td></tr>';
						echo '<input type="hidden" name="mp"/>';
						echo '<input type="hidden" name="mp2"/>';
						echo '<input type="hidden" name="choice" value="creer_compte">';
					echo '</center></table>';
					echo '<input type="button" value="';
					afficher_texte('creer_compte',$TEXT);
					echo '" onClick ="javascript:valider_creation()"/>';
				echo '</form>';
			}

			if($_GET['action']== ENVOYER_PASS )
			{
				echo "<h3>Envoyer un nouveau mot de passe</h3>\n ";
				echo "<p>NB : l'ancien mot de passe restera valide jusqu'à ce que vous vous connectiez avec votre nouveau mot de passe.</p>" ;
				echo '<form name="mon_formulaire">';
					echo '<table>';
						echo '<tr><td><center>Votre adresse courriel actuelle :</center></td><td><input type="text" name="email"/></td></tr>';
						echo '<input type="hidden" name="choice" value="envoyer_pass">';
					echo '</center></table>';
					echo '<input type="button" value="';
					afficher_texte('envoyer_pass',$TEXT);
					echo '" onClick ="javascript:valider_envoie()"/>';
				echo "</form>\n \n ";
			}

			if($_GET['action']== REMETTRE_A_ZERO )
			{
				echo '<h3>Restauration des réponses par défaut</h3>';
				echo '<form name="mon_formulaire" method="POST" action="redirection_connexion.php">';
					echo '<input type="hidden" name="choice" value="restaurer_valeurs_initiales">';
					echo "&Ecirc;tes vous s&ucirc;r de vouloir restaurer l'&eacute;tat initial du questionaire ?<br/>";
					echo '<input type="submit" value="';
					afficher_texte('supprimer_valeurs',$TEXT);
					echo '"/>';
				echo "</form>\n \n ";
			}

			if($_GET['action']== SAUVEGARDER )
			{
				echo '<h3>Sauvegarder vos réponses</h3>';
				echo '<form name="mon_formulaire" method="POST" action="redirection_connexion.php">';
					echo '<input type="hidden" name="choice" value="sauvegarder">';
					echo 'Cette action écrasera vos réponses potentiellement précédemment sauvegardées<br/>';
					echo '<input type="submit" value="';
					afficher_texte( 'sauvegarder' , $TEXT );
					echo '"/>';
				echo "</form>\n \n ";
			}

			if($_GET['action']== CHARGER )
			{
				echo '<h3>Charger vos réponses</h3>';
				echo '<form name="mon_formulaire" method="POST" action="redirection_connexion.php">';
					echo '<input type="hidden" name="choice" value="charger">';
					echo "<p>Cette action écrasera vos réponses potentiellement précédemment saisies dans le questionnaire pour les remplacer par vos réponses sauvegardées dans la base</p>\n " ;
					echo '<input type="submit" value="';
					afficher_texte('charger',$TEXT);
					echo '"/>';
				echo "</form>\n " ;
				echo "</form>\n \n " ;
			}
		} // fin du cas où $page == CONNEXION
		else if ( $page == REDIRECTION_DECONNEXION )
		{
			// attention BUG possible !!!
			session_start();
			session_destroy();
			header("Status: 301 Moved Permanently", false, 301);
			header("Location: index.php");
			exit();
		}
}
*/
?>