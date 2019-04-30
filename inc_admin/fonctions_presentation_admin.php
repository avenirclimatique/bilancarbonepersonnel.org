<?php 
//==========================================================================================
// En-tete
//==========================================================================================
function afficher_en_tete_admin ()
{
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> ' . "\n\n"  
		. '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">' . "\n\n" 
		. '<head>		<!-- début de la balise head --> ' . "\n\n" ; 
	//
	echo "<title>" ; 
	echo "BILAN CARBONE Personnel - Espace Administration" ; 
	echo "</title>\n\n" ; 
	//
	echo '<meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>' . "\n\n" ; 
	//
	echo '<link href="style_admin.css" rel="stylesheet" type="text/css"/>' . "\n" ; 
	//
  echo '<link rel="shortcut icon" href="img/favicon.ico" />' . "\n\n" ; 
	//
	echo "</head>   <!-- fin de la balise head --> \n\n" ; 
	//
	echo "<body>  <!-- début de la balise body --> \n\n" ; 
	//
	echo "<div id='contour'>  <!-- début de la balise contour --> \n\n" ; 
}
//==========================================================================================
// Bandeau titre
//==========================================================================================
function afficher_bandeau_titre_admin ()
{
  echo "<div id='titre'>		<!-- début de la boite 'titre' -->\n\n" ; 
	echo "<h1>" ; 
	echo "BILAN CARBONE Personnel - Espace Administration" ; 
	echo "</h1>\n\n" ;
	echo "<div class='separateur'></div>\n\n" ; 
  echo "</div>   <!-- fin de la boite 'titre' -->\n\n" ; 

}
//==========================================================================================
// Menu
//==========================================================================================
function afficher_menu_admin ()
{
	echo "<ul id='menu_admin' >\n" 
		. "<li><a href='admin.php?page=" . COMPTE . "' >Comptes</a></li>\n" 
		. "<li><a href='admin.php?page=" . NUMEROTER_QUESTIONNAIRE . "' >Numéroter les éléments du questionnaire</a></li>\n" 
		. "<li><a href='admin.php?page=" . 'transfert_fe' . "' >Transférer les facteurs d'émission vers la bdd</a></li>\n" 
		. "<li><a href='admin.php?page=" . 'liste_unites_fe' . "' >Liste des unités des facteurs d'émission</a></li>\n" 
		. "<li><a href='admin.php?page=" . 'backup_bdd' . "' >Gestion des backups</a></li>\n" 
		. "</ul>\n\n" ; 
}
// ==============================================================================================================================
// Pied de page
// ==============================================================================================================================
function afficher_pied_de_page_admin ()
{
	echo "\n</div>  <!-- fin de la boite id='contour' -->\n\n" ; 
	echo "<!-- ================================================================= -->\n\n" ; 
	echo "\n\n</body>\n\n"
		. "</html>" ; 

}
?>
