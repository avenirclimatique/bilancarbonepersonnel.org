<?php 

//==========================================================================================
// Affichage d'une page générique
//==========================================================================================
function affiche_page_generique ()
{ 
	//=============================================
	// page générique
	if ( isSet ( $_GET['page'] ) )
	{
		$page = $_GET['page'] ; 
		if ( $page != MENU_FAQ )
		{
			// ce n'est pas le menu de la faq
			echo "<div id='texte'> <!-- début de la boite de texte -->\n\n" ; 
			require ( "./textes/fr/generique/$page.html" ) ;
			echo "\n\n</div> <!-- fin de la boite de texte -->\n\n" ; 
		}
		else
		{
			// c'est le menu de la faq
			echo "<h2>Questions fréquemment posées (FAQ)</h2>" ; 
			require ("./textes/fr/faq/faq_fr.php") ; 
			$faq = simplexml_load_file( './textes/faq.xml' );
			foreach ( $faq->categorie as $categorie ) 
			{
				$titre = utf8_decode( $categorie->titre ) ; 
				$nom_titre = $TEXT[$titre] ;
				echo "<h3>" . $nom_titre . "</h3>\n " ; 
				echo "<ul>\n " ; 
				foreach ( $categorie->question as $question ) 
				{
					//echo $question ; 
					$nom_question = utf8_decode( $question ) ; 
					$intitule_question = $TEXT[$nom_question] ;
					echo "<li><a href='index.php?type_page=" . FAQ . "&amp;page=" . $nom_question . "'>" . $intitule_question . "</a></li>" ; 
				}
				echo "</ul>\n " ; 
			}
		}
	}
}
?>