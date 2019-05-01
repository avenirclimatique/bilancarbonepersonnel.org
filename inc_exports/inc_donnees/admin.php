<?php 
if (!isset($_SESSION)){
	session_start();
	$_SESSION['chiffre_signi'] = 3;	// Par dÃ©faut on a trois chiffres significatifs
	$_SESSION["mode"] = "admin";
}

require_once("config_connexion.php");
require_once("./fonctions.php");

$connexion = choix_connexion () ;
// la fonction choix_connexion se trouve au début du fichier fonctions.php

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> ' . "\n\n"  
		. '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">' . "\n\n" ;
error_reporting (E_ALL);

require_once("./fonctions_facteur_emission.php");
require_once("./fonctions_unite.php");
echo "
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=ISO-8859-1\"/>
<title>Facteur d'émission</title>
<link rel=\"stylesheet\" type=\"text/css\" href=\"style.css\"/>
<script language=\"javascript\" type=\"text/javascript\" src=\"./jscripts/tiny_mce/tiny_mce.js\"></script>
<script language=\"javascript\" type=\"text/javascript\">
tinyMCE.init({
	mode : \"none\"
	theme : \"simple\"

});
</script>

</head>
<body>";

authentification($connexion);
entete_page($connexion);

echo "<div class=\"retour_ligne\" >&nbsp;</div>";
$menu=""; // Cette variable permet de savoir quelle menu doit être afficher (données, unités)
$rubrique_id =-1; // Cette variable permet de savoir quelle rubrique est consultée
recupere_formulaires($connexion);
//////MENU DONNEES/////////
if ($menu=="facteur_emission"){
	
	if (isset($_POST["action_facteur_emission"])) {
		action_facteur_emission ($connexion);
	}
	/*genere_fic_langue($connexion);
	require("./fichier_mot_cle.php");*/
	
	menu_rubrique ($connexion);
	
	echo "	
			<div id=\"contenu_facteur_emission\">\n";
	if (isset($_GET["table_id"])) {
		$tableau->tab_id = intval($_GET["table_id"]);
		entete_table (intval($_GET["table_id"]), $connexion);
		afficheTableauAdmin ($tableau, "edit_valeur", $connexion);
		menu_creer_colonne (intval($_GET["table_id"]), $connexion);
		menu_creer_ligne (intval($_GET["table_id"]), $connexion);
		afficheTableauAdmin ($tableau, "edit_ordre", $connexion);
	} 

	else {
		entete_rubrique ($rubrique_id, $connexion);
		afficheTableauRubrique ($rubrique_id, $connexion);
		if($rubrique_id>0) {
			menu_creer_table($rubrique_id, $connexion);
		}
		menu_creer_rubrique ($rubrique_id, $connexion);
	}
	echo "	
			</div>
			<div class=\"retour\" >&nbsp;</div>";
}

//////MENU UNITES/////////
else if ($menu=="unites"){
	
	if (isset($_POST["action_unite"])) {
		action_unite ($connexion);
	}
	/*genere_fic_langue($connexion);
	require("./fichier_mot_cle.php");*/
	
	menu_nature_unite ($connexion);

	echo "
			<div id=\"contenu_unite\">";
	menu_creer_nature_unite ($connexion);

	if (isset($_GET["nature_unite_id"])) {
		entete_nature_unite (intval($_GET["nature_unite_id"]), $connexion);
		afficheTableConversion (intval($_GET["nature_unite_id"]), $connexion);
		menu_creer_unite (intval($_GET["nature_unite_id"]), $connexion);
		afficheTableModifOrdre (intval($_GET["nature_unite_id"]), $connexion);
	}
	echo "
			</div>
			<div class=\"retour\" >&nbsp;</div>";
}
else if ($menu==""){
	echo "	
			<div class=\"menu\">";
			affiche_choix_menu("admin",$connexion);
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

?>
