<?php 
if (!isset($_SESSION)){
	session_start();
}
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> ' . "\n\n"  
		. '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">' . "\n\n" ;
require_once("config_connexion.php");
require_once("fonctions.php");
require_once("fonctions_public.php");
require_once("fichier_mot_cle.php");
// On initialise les varibles de session
init_Session ($rubrique_id, $connexion);
echo "
<head>
<meta http-equiv=\"Content-Type\" content=\"texthtml; charset=ISO-8859-1 \"/>
<title>Facteur d'émission</title>
<link rel=\"stylesheet\" type=\"text/css\" href=\"style.css\"/>
</head>
<body>";


$connexion = choix_connexion () ;
// la fonction choix_connexion se trouve au début du fichier fonctions.php
entete_page($connexion);
echo "<div class=\"retour_ligne\" >&nbsp;</div>";
$menu="";
if (isset($_GET["menu"])) {
	$menu = ($_GET["menu"]);
}

recupere_POST($connexion);


// On ouvre le menu des facteurs d'émission
if ($menu=="facteur_emission"){
	require_once("fonctions_facteur_emission_Public.php");
	menu_rubrique ($connexion);
	
	echo "
		<div id=\"contenu_facteur_emission\">";
	
		if (isset($_GET["tableauDef"])){
			resetValue (intval($_GET["tableauDef"]), $connexion);	
		}
		if (isset($_GET["rubrique_id"])) {
			$rubrique_id = intval($_GET["rubrique_id"]);
			afficheTableauRubrique ($rubrique_id, $connexion);
		}
		else {
			echo "
			<div class=\"Bloc\">
				<h2> Consultation Publique </h2>
				<p> Veuillez selectionner dans le menu de gauche la table que vous souhaitez consulter </p>
			</div>";
		}
	echo "
		</div>";
}
// On ouvre le menu des unités
else if ($menu=="unites"){
	require_once("fonctions_unite_Public.php");
	menu_nature_unite ($connexion);
	recupere_post($connexion);
	echo "
		<div id=\"contenu_unite\">";
	if (isset($_GET["nature_unite_id"])) {
		afficheTableConversion (intval($_GET["nature_unite_id"]), $connexion);
	}
	else {
	echo "
		<div class=\"Bloc\">
			<h2> Consultation Publique </h2>
			<p> Veuillez selectionner dans le menu de gauche la table que vous souhaitez consulter </p>
		</div>";
	}
	echo "
		</div>";
}
else if ($menu==""){
	echo "	
			<div class=\"menu\">";
			affiche_choix_menu("public",$connexion);
	echo "
			</div>";
	echo "
			<div id=\"contenu_unite\">
				<div class=\"Bloc\">
					<h2> Bienvenue </h2>
					<p> Choisissez dans le menu de gauche la section que vous souhaitez consulter </p>
				</div>
			</div>";
}
fin_page($connexion);
echo "
</div>
</body>
</html>";

// // On ?crase le tableau de session
// $_SESSION = array();

// // On d?truit la session
// session_destroy();

?>
