<?php 
//==========================================================================================
// Affichage d'une page générique
//==========================================================================================
function affiche_page_faq ()
{ 
	if ( isSet ( $_GET['page'] ) )
		{
			$page = $_GET['page'] ; 
			require ("./textes/fr/faq/faq_fr.php") ; 
			$intitule_question = $TEXT[$page] ;
			echo "<div id='texte'> <!-- début de la boite de texte -->\n\n" ; 
			echo "<h2 id='titre_question_faq' >" . $intitule_question . "</h2>\n\n" ; 
			require ( "textes/fr/faq/$page.html" ) ; 
			echo "\n\n</div> <!-- fin de la boite de texte -->\n\n" ; 
			echo "<p class='retour_menu_faq'>\n"
				. "Pour revenir au menu de la F.A.Q., <a href='index.php?type_page=generique&amp;page=menu_faq' >cliquez ici</a>.\n</p>\n\n" ;  
		}
}
?>