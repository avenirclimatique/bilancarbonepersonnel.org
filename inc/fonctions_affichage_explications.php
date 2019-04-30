<?php 
// ===================================================================================================
//  Affiche l'explication
// ===================================================================================================
function affiche_explication ( $nom_fichier )
{
	echo "<div class='explication' >  <!-- début de fichier d'explication -->\n\n" ; 
	// 
	$adresse_fichier = 'textes/' . $_SESSION[LANGUE] . '/explications/' . $nom_fichier . '.html' ; 
	// affichage du fichier d'aide à proprement parler
	require ( $adresse_fichier ) ; 
	// 
	// si on vient d'une page d'une questionnaire on affiche le lien de retour vers la page
	$page_retour = $_GET ['page_retour'] ; 
	if ( $page_retour != 'aucune' )
		// ça veut dire qu'on venait d'une page du questionnaire, on propose d'y retourner
		echo "\n<p class='retour_depuis_explication'>\n"
			. "<a href='index.php?type_page=" . QUESTIONNAIRE . "&amp;page=" . $page_retour . "'>Retour à la page précédente du questionnaire</a>\n"
			. "</p>\n" ; 
	//
	echo "\n\n</div>  <!-- fin de fichier d'explication -->\n\n" ; 
}
// ===================================================================================================
//  Affiche le lien vers le fichier d'explications si celui-ci existe
// ===================================================================================================
function lien_explication ( $nom_fichier , $type_explication , $page_retour_url )
{
	$adresse_fichier = 'textes/' . $_SESSION[LANGUE] . '/explications/' . $nom_fichier . '.html' ; 
	if ( file_exists ( $adresse_fichier ) )
		$existe_explication = $adresse_fichier ; 
	else
		$existe_explication = false ; 
	if ( $existe_explication )
	{
		$lien = "<a href='index.php?type_page=" . EXPLICATION . "&amp;page=" . $nom_fichier ;
		// =========
		if ( $page_retour_url )
		{
			$lien = $lien . "&amp;page_retour=" . $page_retour_url[PAGE] ;
			if ( $page_retour_url[NUMERO] )
				$lien = $lien . "%" . $page_retour_url[NUMERO] ;
		}
		// ==========
		$lien = $lien . "' class='lien_explication' title =" . '"' ;
		if ( $type_explication == RUBRIQUE )
			$title = 
				'Lien vers des explications sur les facteurs d\'émission et les calculs pour cette catégorie' ; 
		else if ( $type_explication == PAGE )
			$title = 
				'Lien vers des explications sur les facteurs d\'émission et les calculs pour cette page' ; 		
		else if ( $type_explication == QUESTION )
			$title = 
				'Lien vers des explications sur les facteurs d\'émission et les calculs pour cette question' ; 
		$lien = $lien . $title . '" >?</a>' ;
	}
	else
		$lien = false ; 
	return $lien ; 
}
?>