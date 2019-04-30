<?php 
//==========================================================================================
// fonction d'affichage d'un text à partir de son index dans le fichier de lang
// permet de gérer les chaine type logement%1
// si la l'index n'existe pas alors on affiche l'index, en mode admin l'erreur sera signalée
//==========================================================================================
function afficher_texte ( $chaine , $TEXT )
{ 
	if ( isSet ( $TEXT["$chaine"] ) )
		echo $TEXT["$chaine"] ;
	else
		echo 'erreur : texte "' . $chaine . '" manquant' ; 
}
//==========================================================================================
// 
//==========================================================================================
function retourner_texte ( $chaine , $TEXT )
{
	if ( isSet ( $TEXT["$chaine"] ) )
		$texte = $TEXT["$chaine"] ;
	else
		$texte = false ; 
	return $texte ; 
}
//==========================================================================================
// Décoder l'url (séparer le nom de la page du numéro éventuellement associé)
//==========================================================================================
function decoder_url ( $url )
{
	if ( $url )
		if ( strpos ( $url , '%' ) )
		{
			$str = explode ( '%' , $url ) ; 
			$url_decodee[PAGE] = $str[0] ; 
			$url_decodee[NUMERO] = $str[1] ; 			
		}
		else
		{
			$url_decodee[PAGE] = $url ; 
			$url_decodee[NUMERO] = false ; 
		}
	else
		$url_decodee = false ; 
	return $url_decodee ; 
}
//==========================================================================================
// Demande de confirmation ou non d'une suppression, déconnexion, etc
//==========================================================================================
function demande_confirmation ( $texte_confirmation , $url_confirmation , $texte_annulation , $url_annulation )
{
	echo "<ul class='confirmation_annulation'>\n " ; 
	echo "<li><a href='" . $url_confirmation . "'>" . $texte_confirmation . "</a></li>\n " ; 
	echo "<li><a href='" . $url_annulation . "'>" . $texte_annulation . "</a></li>\n " ; 
	echo "</ul>\n \n " ; 
}
//==============================================================================
// Cette fonction supprime tout échappement automatique
// des données HTTP dans un tableau de dimension quelconque
// Attention: il faudrait aussi "nettoyer" les clés ... ou
// éviter de mettre des clés avec des apostrophes

function NormalisationHTTP($tableau)
{
  //Si on est en échappement automatique, on rectifie...
  foreach ($tableau as $cle => $valeur) 
    {
      if (!is_array($valeur)) // On agit
	$tableau[$cle] = stripSlashes($valeur);
      else  // On appelle récursivement
	$tableau[$cle] = NormalisationHTTP($valeur);
    }
  return $tableau;
}
// 
//==============================================================================
function normalisation()
{
  //Si on est en échappement automatique, on rectifie...
  if (get_magic_quotes_gpc())
    {
      $_POST = normalisationHTTP($_POST);
      $_GET = normalisationHTTP($_GET);
      $_REQUEST = normalisationHTTP($_REQUEST);
      $_COOKIE = normalisationHTTP($_COOKIE);
    }
}
//==============================================================================
// Fonction connexion : connexion à MySQL
//==============================================================================
function connexion ($nom, $mot_de_passe, $base, $serveur)
{
	// Connexion au serveur //changement de mysql_pconnect en mysqli_connect le 05/03/2017
	$connexion = mysqli_connect($serveur, $nom, $mot_de_passe, $base);

	if (!$connexion) 
	{
	echo "Désolé, connexion au serveur $serveur impossible\n";
	exit;
	}
	
	// Connexion à la base //changement de mysql_select_db en mysqli_select_db et mysql_error en mysqli_connect_error le 05/03/2017
	if (!mysqli_select_db($connexion,$base)) {
	echo "Veuillez nous excuser, une erreur est survenue.<br/><br/>L'accès à la base $base impossible\n";
	if ( CONFIG != 'distant' )
		echo "<b>Message de MySQL :</b><br/> " . mysqli_connect_error($connexion);
	exit;
	}

	// On renvoie la variable de connexion
	return $connexion;
}
//==============================================================================
// Fonction d'exécution d'une requête
//==============================================================================
function exec_requete ($requete, $connexion) {
	$resultat = mysqli_query ($connexion, $requete);
	
	if ($resultat) 
		return $resultat;
	else
	{
		$date = date("Y-m-d H:i:s");
		$message_complet = "<h2>Erreur dans l'exécution d'une requête.</h2>"
		. "\n Heure: " . $date
		. "\n <br/> Message MYSQL : <i>" . htmlentities(mysqli_connect_error($connexion)) . "</i>"
		. "\n <br/> Requête SQL : <br/> \n<pre>" . htmlentities($requete)
		. "</pre>\n <br/> Variables d'environnement: <ul> \n"
		. "<li> POST <br/> \n<pre>" . var_export( @$_POST, true ) . "</pre>"
		. "<li> GET  <br/> \n<pre>" . var_export( @$_GET , true ) . "</pre>"
		. "<li> SESSION <br/> \n<pre>" . var_export( @$_SESSION, true ) . "</pre>"
		. "<li> TRACE <br/> \n<pre>" . var_export( debug_backtrace(), true ) . "</pre>"
		. "\n </ul><br/> Cordialement, <br/> Le développeur du site ;-)";
		if ( CONFIG != 'en_ligne' ) // à décocher quand on va mettre vraiment en ligne, pas pour le moment
		//if ( true )
		{
			echo $message_complet;
			// $en_tete = "From: webmestre@avenirclimatique.org\n";
			// $en_tete .= "Content-Type: text/html;" ;
			// $courriel = "adrien.ragot@gmail.com";
			// mail ( $courriel, 'Erreur sur le serveur de l\'enquete en ligne.', $message_complet, $en_tete );
		}
		else {
			echo "<b>Veuillez nous excuser, une erreur est survenue.</b><br />";
			echo "Si l'erreur persiste, veuillez contacter <a href='mailto:webmestre@bilancarbonepersonnel.org'>webmestre@bilancarbonepersonnel.org</a>
			pour signaler le problème. Nous vous remercions.<br />";
			echo "L'équipe d'Avenir Climatique.";
			$en_tete = "From: webmestre@bilancarbonepersonnel.org\n";
			$en_tete .= "Content-Type: text/html;" ;
			$courriel = "webmestre@bilancarbonepersonnel.org";
			mail ( $courriel, 'Erreur sur le serveur de l\'enquete en ligne.', $message_complet, $en_tete );
		}
		exit;
	}
}
//==============================================================================
// Recherche de l'objet suivant
//==============================================================================
function objet_suivant ($resultat)
{
	return mysqli_fetch_object ($resultat);
}
//==============================================================================
// Recheche de la ligne suivante  (retourne un tableau)
//==============================================================================
function ligne_suivante ($resultat)
{
	return mysqli_fetch_array ($resultat);
}
//==============================================================================
// Nom de la version
//==============================================================================
function nom_version ( $connexion )
{
	$version_id = $_SESSION [VERSION_ID] ; 
	$donnees_nom_version = exec_requete ( "SELECT * FROM t_version WHERE version_id = '$version_id'", $connexion ) ; 
	$objet_nom_version = objet_suivant ( $donnees_nom_version ) ; 
	$nom_version = $objet_nom_version->version_nom ; 
	return $nom_version ; 
}
//==============================================================================
// Nom de la saisie
//==============================================================================
function nom_sauvegarde ( $connexion )
{
	$sauv_id = $_SESSION [SAUV_ID] ; 
	$donnees_nom_sauvegarde = exec_requete ( "SELECT * FROM t_sauvegarde WHERE sauv_id = '$sauv_id'", $connexion ) ; 
	$objet_nom_sauvegarde = objet_suivant ( $donnees_nom_sauvegarde ) ; 
	$nom_sauvegarde = $objet_nom_sauvegarde->sauv_nom ; 
	return $nom_sauvegarde ; 
}
//==============================================================================
// Avertissement cookies
//==============================================================================
function avertissement_cookies ()
{
	echo "<p class='mise_en_garde_cookies'><strong>ATTENTION ! Il semble que votre navigateur n'accepte pas les cookies ou que votre session ait expiré.</strong> </p>\n" 
		. "<p class='mise_en_garde_cookies'>Si votre navigateur est configuré pour ne pas accepter les cookies, toute saisie effectuée sur page est perdue lorsque vous passez à une autre page du questionnaire. Vous devez donc configurer votre navigateur afin qu'il accepte les cookies pour pouvoir utiliser le calculateur. Le seul cookie utilisé par le calculateur est votre identifiant de session, mais celui-ci est indispensable. Pour davantage d'explications, voir la <a href='index.php?type_page=faq&amp;page=cookies'>page de la FAQ</a>.</p>\n"
		. "<p class='mise_en_garde_cookies'>Ce message de mise en garde est affiché tant que votre navigateur n'accepte pas les cookies, lorsque vous validez une page du questionnaire, que vous tentez d'accéder à la page de résultats, ou que vous cliquez sur l'onglet &quot;Remettre à zéro&quot; ci-dessus.</p>\n" ; 
}

//==========================================================================================
// 
//==========================================================================================
/*

function afficher($chaine)
{
  $int = round(pow((1/$chaine),0.1),0);
  echo round($chaine,$int+2);
}

*/
//==========================================================================================
// fonction qui parcour le fichier csv pour en tirer les facteurs d'émission
//==========================================================================================
function charger_facteurs()
{
	$file = "./inc_donnee_calcul/facteurs_emissions.csv" ;
  $taille = 1024;
  $delimiteur = ";";
	//
  if ( $fp = fopen( $file , "r" ) )
  {
    while ( $ligne = fgetcsv ( $fp , $taille , $delimiteur ) )
    {
      foreach ( $ligne as $elem )
      {
        $fe["$ligne[0]"] = $ligne[1];
        //echo $ligne[0] , ' -> ' , $ligne[1] , '<br/>';
      }
    }
    fclose ($fp);
  }
  else
    echo "Problème dans la base de données des facteurs";
  return $fe ;
}

/*
//==========================================================================================
//generation d'un password
//==========================================================================================
function new_pass()
{
  $tableau = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","0","1","2","3","4","5","6","7","8","9");
  $valeur_aleatoires = array_rand($tableau,8);
  
  $mdp = "";

  foreach($valeur_aleatoires as $i)
  {
    $mdp = $mdp.$tableau[$i];
  }
  return $mdp;
}
*/
//==========================================================================================
//
//==========================================================================================
?>
