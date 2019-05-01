<?php 

//==============================================================================
// Fonction  secure :cette fonction va rajouter si nécessaire des slash pour échapper les caractère spéciaux
//==============================================================================
function secure($var){
	return( get_magic_quotes_gpc() ? $var : addslashes($var));
}

///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////FONCTIONS TRAITEMENT DE FORMULAIRE /////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////
//==============================================================================
// Fonction recupere_formulaires : cette fonction recupère puis traite les informations contenues dans le formulaire

//==============================================================================
function recupere_formulaires ($connexion) {
	global $rubrique_id,$menu;
	
	if (isset($_POST["nb_ch_signi"])){
		$_SESSION['chiffre_signi'] = intval($_POST["nb_ch_signi"]);	
	}
	if (isset($_GET["rubrique_id"])) {
		$rubrique_id = intval($_GET["rubrique_id"]);
	}
	if (isset($_GET["menu"])) {
		$menu = ($_GET["menu"]);
	}
}



///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////FONCTISONS AFFICHAGE ////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////

//==============================================================================
// Fonction authentification : affiche un menu demandant l'authentification  de l'utilisateur
//==============================================================================
function authentification ($connexion) {
	// Identification pour passer en mode admin
if (isset($_POST["username"]) && isset($_POST["password"])){
	$_SESSION["usrname"] = mysql_escape_string($_POST["username"]);
	$_SESSION["mtpe"] = mysql_escape_string($_POST["password"]);
}
if (!isset($_SESSION["usrname"]) || !isset($_SESSION["mtpe"])){
echo "
	<div class=\"Bloc_login\">
		<h2> Authentification </h2>
		<div class=\"contenu\">
			<p><strong>La page à laquelle vous souhaitez vous connecter requiert une authentification.</strong></p>
			<!-- form -->
			<form method=\"post\" name=\"login_form\" id=\"login_form\" action=\"admin.php?menu=facteur_emission\">
				<label for=\"idUsername\">Identifiant :</label><br/>
				<input name=\"username\" id=\"idUsername\" type=\"text\" maxlength=\"64\"/><br/>
				<label for=\"idPassword\">Mot de passe :</label><br/>
				<input name=\"password\" id=\"idPassword\" type=\"password\" maxlength=\"64\"/><br/>
				<input class=\"submit\" type=\"submit\" value=\"Identification\"/>
			</form>
		</div>
	</div>";
exit;
}
else{
	$liste_pass = exec_requete ("select passe from usr_tbl where identificateur = '{$_SESSION["usrname"]}' ", $connexion);
	$pass = objet_suivant($liste_pass);
	if ($pass== NULL || $pass->passe!=$_SESSION["mtpe"]){
		unset($_SESSION["usrname"]);
		unset($_SESSION["mtpe"]);
		echo "
	<div class=\"Bloc_login\">
		<h2> Authentification </h2>
		<div class=\"contenu\">
			<p><strong> Identifiant ou mot de passe incorrect veuillez rééssayer </strong></p>
			<!-- form -->
			<form method=\"post\" name=\"login_form\" id=\"login_form\" action=\"admin.php?menu=facteur_emission\">
				<input class=\"submit\" type=\"submit\" value=\"Rééssayer\"/>
			</form>
		</div>
	</div>";
	exit;
	}
	else if ($pass->passe==$_SESSION["mtpe"]){
	$_SESSION["authentifie"] = "oui";
	return "";
	}
}
}

//==============================================================================
// Fonction entete_page : affiche l'entete de la page, ouvre la div Page
//==============================================================================
function entete_page ($connexion) {
	echo "
	<div id=\"Page\">
	<div class=\"BlocHaut\">
		<a href=\"index.php\" title=\"Retour à l'accueil\"><img src=\"./logo.png\" alt=\"Avenir Climatique\" /></a>
		<h1>BASE CARBONE</h1>
	</div>";
}
//==============================================================================
// Fonction fin_page : affiche la fin de page ferme la div Page
//==============================================================================
function fin_page ($connexion) {
	echo "
		<div id=\"BlocBas\" > &nbsp;</div>";
}

//==============================================================================
// Fonction arborescence_rubrique : retourne l'arborescence complï¿½te de la rubrique
//==============================================================================
function arborescence_rubrique ($rubrique_id, $connexion) {
	if ($rubrique_id > 0) {
		$liste_rubrique = exec_requete ("select * from fe_rubrique where rub_id = {$rubrique_id}", $connexion);
		if ($rubrique = objet_suivant ($liste_rubrique)) {
			if ( arborescence_rubrique ($rubrique->rub_prec_id, $connexion) == "&nbsp;"){
				return echo_MC('NOM_RUBRIQUE',$rubrique->rub_nom);
			}
			else {
				return arborescence_rubrique ($rubrique->rub_prec_id, $connexion)." &nbsp;&nbsp;>&nbsp;&nbsp;".echo_MC('NOM_RUBRIQUE',$rubrique->rub_nom);
			}
		}
	}
	return "&nbsp;";
}


//==============================================================================
// Fonction afficher_choix_menu();
//==============================================================================
function affiche_choix_menu($model,$connexion){
	global $_SERVER;
	global $_SESSION;
	$get = str_replace("&","&amp;",$_SERVER['QUERY_STRING']);
	if ($model=="public"){
		$antimode = "admin";	
	}
	else if ($model == "admin"){
		$antimode = "public";
		if (isset($_GET["table_id"])){ // Pour que d'un passage admin vers public, si on se trouve dans le mode edit_valeur, on retombe sur la bonne rubrique de facteurs d'émissions
			$liste_rubrique = exec_requete ("select rub_id from fe_table where tab_id = {$_GET["table_id"]}", $connexion);
			$rubrique_id = objet_suivant($liste_rubrique);
			$get = str_replace("table_id={$_GET["table_id"]}","rubrique_id={$rubrique_id->rub_id}",$get);
		}
	}
	else {
		echo " Mode : {$model} non connu désolé"; 
	}
	if (isset($_SESSION["authentifie"]) && ($_SESSION["authentifie"]=="oui")){
		echo "
			<p class=\"change_mode\"><a href=\"".$antimode.".php?".$get."\"> Passer en mode ".$antimode." </a></p>
			<div class=\"retour_ligne\" >&nbsp;</div>";
	}
	echo "
			<p class=\"choix_menu\"><a href=\"{$model}.php?menu=facteur_emission\">"._('Données')."</a></p>
			<p class=\"choix_menu\"><a href=\"{$model}.php?menu=unites\">"._('Unités')."</a></p>";
}

//==============================================================================
// Fonction choixChiffresSignificatifs : affiche le choix du nombre de chiffres significatifs, le mode étant par défaut admin et sinon "publique"
//==============================================================================
function choixChiffresSignificatifs () {
	global $_SERVER;
	global $_SESSION;

	echo "	
		<div class=\"Bloc_chiffre_signi\">
			<form method=\"post\" action=\"".$_SERVER['PHP_SELF'];if ($_SERVER['QUERY_STRING']!=''){ echo "?".str_replace("&","&amp;",$_SERVER['QUERY_STRING']);}
	echo "\">
				<p>
				<label for=\"chiffre_significatif\">"._('Chiffres Signigicatifs')."</label> 
				<input id=\"chiffre_significatif\" type=\"text\" size=\"1\" maxlength=\"1\" name=\"nb_ch_signi\" value=\"{$_SESSION['chiffre_signi']}\" style=\"width:15px\"/>
				<input type=\"submit\" value=\""._('OK')."\"/>
				</p>
			</form>";
	if ($_SESSION["mode"]=="public"){
		echo "
			<form method=\"post\" action=\"".$_SERVER['PHP_SELF'];if ($_SERVER['QUERY_STRING']!=''){ echo "?".str_replace("&","&amp;",$_SERVER['QUERY_STRING']);}
	echo "	
			\">
			<p><input type=\"hidden\" name=\"RAZ\" value=\"-1\" /></p>
			<p><input type=\"submit\" value=\"Unités par défaut\" /></p>
			</form>";
	}
	echo "
		</div>\n";
}

//==============================================================================
// Fonction echo_MC($mot-clé) affiche la version traduite du mot clé si elle existe 
// affiche en rouge le mot clé sinon, option $v permet d'afficher uniquement le mot clé (dans le cas ou on veut l'afficher dans un select ou input)
// par défaut $v=0 .
//==============================================================================
function echo_MC($type_mot_cle,$mot_cle) {
	global $TEXT;
	/*if ($mot_cle != ""){
		return $TEXT[$mot_cle];
	}
	else return "";*/
	return htmlentities($mot_cle);
}
//==============================================================================
// Fonction to_X_chif_signi : formatte un nombre avec X chiffres significatifs
// note :  le résultat final sera de la forme X.XXXe+P avec 
// X les chiffres du nombre, P la puissance  10 du nombre
//On aura :	-$resultatFinal[$X+2] ==> signe de la puissance
//		-$resultatFinal[$X+3] ==> P (la puissance de 10)
//==============================================================================
function to_X_chif_signi($resultat,$X=17){// par défaut on utilise le nombre maximal de chiffre
	$n='';
	if ($resultat==0){
		return 0;
	}
	if ($resultat< 0){
		$resultat = -$resultat;
		$n='-';
	}
	$len = $X-1;
	
	if (version_compare(PHP_VERSION,'5.2.0')<=0){
		$resultatFormate = sprintf("%.".($X)."e", $resultat);
	}
	else{
		$resultatFormate = sprintf("%.".($X-1)."e", $resultat);	
	}	
	
		$resultatFinal= (string)$resultatFormate;
		$debut = substr($resultatFinal,0 ,1);
		$fin = substr($resultatFinal, 2, $X-1);
		$final = $debut.$fin;
	
		if ($resultatFinal[$X+2] == '+'){
			$debut1 = substr($final,0,$resultatFinal[$X+3]+1);
	
			if ($resultatFinal[$X+3] < ($X-1)){
				/* On trie les chiffres avant la virgule */
				$debut1 = strrev(wordwrap(strrev($debut1), 3, " ", 1));
			
				/* On vérifie que les chiffres après la virgule ne sont pas des 0 */
				$fin1 = substr($final, $resultatFinal[$X+3]+1 , $X-$resultatFinal[$X+3]);
			
				$p = $X-$resultatFinal[$X+3];
	
				while ( ($p > 0) && isset($fin1[$p-2]) && ($fin1[$p-2] == 0)) {
					$fin1 = substr($fin1, 0, $p-2);
					$p--;
				}
				if (!($p-1==0)){
					$fin1 = ".".$fin1;
				}
				$resultatFinal = $debut1.$fin1;
			}
			else{
				$fin2 ="";
				for ($k=0; $k<($resultatFinal[$X+3]-$len); $k++){
					$fin2 = $fin2.'0';
				}
	
				$resultatFinal = $debut1.$fin2;
				/* On trie les chiffres avant la virgule */
				$resultatFinal = strrev(wordwrap(strrev($resultatFinal), 3, " ", 1));
			}
	
		}
		else if ($resultatFinal[$X+2] == '-'){
			$debut2 = "0.";
			$p = $X;
			while ( ($p>0) && ($final[$p-1] == 0)  ) {
				$final = substr($final, 0, $p-1);
				$p--;
			}
			if (!($p==0)){
				for ($l=0; $l<($resultatFinal[$X+3]-1); $l++){
					$debut2 = $debut2.'0';
				}
			}
			$resultatFinal = $debut2.$final;
			if (strlen($resultatFinal) > 8){
				$chiffres=strlen($final);
				if ($chiffres<2){
					$chiffres=0;
				}
				$nombre = substr($resultatFormate,0 ,$chiffres+1);
				$exposant = substr($resultatFormate,$X+1 , $X+3);
				$resultatFinal = $nombre.$exposant;
			}
		}
		return ($n.$resultatFinal);
	
	
}
///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////FONCTIONS FICHIER DE LANGUE/////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////

//==============================================================================
// Fonction ligne_fic_langue($nom_table,$nom_champs,$fichier,$connexion)
// écrit les lignes du fichier de langue de la table et du champs passé en paramètre
//==============================================================================
function ligne_fic_langue($nom_table,$nom_champ,$fichier,$connexion){
	$objet = exec_requete ("select {$nom_champ} from {$nom_table}", $connexion);
	while(	$item = mysql_fetch_array($objet)){
		if ($item[$nom_champ] != ""){
			fputs($fichier,"\$TEXT[\"".$item[$nom_champ]."\"] = _(\"".$item[$nom_champ]."\");\n");
		}
	}
}

//==============================================================================
// Fonction genere_fic_langue () : cette fonction va générer le fichier de langue contenant toutes 
//les chaines de caractères contenues dans la base de données
//==============================================================================
function genere_fic_langue($connexion){
	$fic = fopen("./fichier_mot_cle.php","w");

	// On écrit l'entête du fichier
	fputs($fic, "<?php
\$TEXT=array();\n\n");
	
	// On écrit les chaînes de caractères contenues dans la table fe_rubrique
	fputs($fic,"\n// Nom des rubriques\n");
	ligne_fic_langue("fe_rubrique","rub_nom",$fic,$connexion);
	
	// On écrit les chaînes de caractères contenues dans la table fe_table
	fputs($fic,"\n// Nom des tables\n");
	ligne_fic_langue("fe_table","tab_nom",$fic,$connexion);
	
	fputs($fic,"\n// Titres des lignes d'une table\n");
	ligne_fic_langue("fe_table","tab_titre_ligne",$fic,$connexion);
	
	fputs($fic,"\n// Commentaire d'une table\n");
	ligne_fic_langue("fe_table","tab_commentaire",$fic,$connexion);
	
	// On écrit les chaînes de caractères contenues dans la table fe_colonne
	fputs($fic,"\n// Nom des colonnes\n");
	ligne_fic_langue("fe_colonne","col_commentaire",$fic,$connexion);
	
	// On écrit les chaînes de caractères contenues dans la table fe_ligne
	fputs($fic,"\n// Nom des lignes\n");
	ligne_fic_langue("fe_ligne","lig_nom",$fic,$connexion);
	
	// On écrit les chaînes de caractères contenues dans la table fe_nature_unite
	fputs($fic,"\n// Nom des natures d'unité\n");
	ligne_fic_langue("fe_nature_unite","nature_unite_nom",$fic,$connexion);
	
	// On écrit les chaînes de caractères contenues dans la table fe_unite_fondamentale
	fputs($fic,"\n// Nom des unités\n");
	ligne_fic_langue("fe_unite_fondamentale","unite_fond_symbole",$fic,$connexion);
	
	//On écrit la fin du fichier 
	fputs($fic,"\n?>");
	
	fclose($fic);
}

//==============================================================================
// Fonction copie_fic_langue_to_database()
// prend les fichiers du fichier de mot clé et les mets dans la base de donnée
//==============================================================================
function copie_fic_langue_to_database(){
	$connexion = connexion("root", "", "facteur_emission", "localhost");
	$connexion2 = connexion("root", "", "facteur_emission2", "localhost");
	$table="";
	$champ="";
	
	$fic_mot_cle = fopen("./langue/mots_cle.php","r");
	if($fic_mot_cle){
		while(!feof($fic_mot_cle)){
			$ligne = fgets($fic_mot_cle);
			
			if (substr($ligne,0,6)=="table:"){
				$table=substr($ligne,6);
			}
			if (substr($ligne,0,6)=="champ:"){
				$champ=substr($ligne,6);
			}
			if (substr($ligne,0,7)=="\$texte["){
				$chaine = str_replace("\'","'",substr($ligne,strpos($ligne,"=")+5,-5));
				$mot_cle = substr($ligne,8,strpos($ligne,"=")-8-3);
				exec_requete ("update {$table} set {$champ} = \"".secure($chaine)."\" where {$champ} =\"{$mot_cle}\"", $connexion2);
			}
			
		}
	}
}
?>
