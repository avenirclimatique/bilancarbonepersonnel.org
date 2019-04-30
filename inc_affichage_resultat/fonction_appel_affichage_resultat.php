<?php 
// ==============================================
// AFFICHAGE DE LA PAGE DE RESULTAT
// ==============================================
function affiche_resultat ( $resultat ) {
	// 
	require_once ( 'fonctions_affichage_resultat.php' );
	require_once ( 'fonctions_bas_niveau_affichage_resultats.php' );
	require_once ( 'interface.php' );


	$resultat = nouvelleForme($resultat);
	//
	//=================
	//echo "<pre>" ; print_r ( $resultat ) ; echo "</pre>" ; 
	//echo "<pre>" ; print_r ( $_SESSION[REPONSE] ) ; echo "</pre>" ; 
	
	// ================ PAR VINCENT POUR PDFISATION
	if ( PROPOSE_SORTIE_PDF == 'oui' )
	{
		echo "<p><strong><a href='./pdf/pdfisation.php'>Téléchargez la synthèse de vos Résultats en PDF</a></strong> (n'imprimer que si nécessaire svp)</p>\n\n" ; 
	}
	//=================
	affiche_titre_et_mise_en_garde () ; 
	//=================
	affiche_figure ( $resultat ) ; 
	//=================
	affiche_total_et_commentaire ( $resultat ) ; 
	//=================
	affiche_repartition_grossiere ( $resultat ) ; 
	// ================
	affiche_repartition_detaillee ( $resultat ) ; 
	// ================
	affiche_notes_bas_de_page () ; 


	echo "<p><strong><a href='./exportXML/exportXML.php'>Exportez vos r&eacute;sultats en XML</a></strong></p>\n\n" ; 

}
?>
