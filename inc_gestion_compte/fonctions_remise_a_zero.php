<?php 
//==========================================================================================
//       REMETTRE A ZERO
//==========================================================================================
function demande_confirmation_remise_a_zero () 
{
	echo "<p><strong>Etes-vous sûr de vouloir annuler toutes vos saisies&nbsp;?</strong></p>" 
		. "<p>Cette opération <strong>annule toutes vos saisies</strong> et vous permet de recommencer votre BILAN CARBONE™ Personnel depuis le début."
		. "Elle <strong>ne détruit pas vos sauvegardes</strong> si vous en avez effectuées."
		. "Elle <strong>n'annule pas votre identification</strong> si vous êtes identifié.</p>\n " ; 
	//
	$url_confirmation = "./index.php?type_page=" . GESTION_COMPTE . "&amp;page=" . CONFIRMER_REMETTRE_A_ZERO ; 
	$url_annulation = "./index.php?type_page=" . GESTION_COMPTE . "&amp;page=" . ANNULER_REMETTRE_A_ZERO ; 
	demande_confirmation ( 'Confirmer' , $url_confirmation , 'Annuler' , $url_annulation ) ; 
	// la fonction  demande_confirmation se trouve dans fonctions_generales.php
}
//==========================================================================================
function annuler_remise_a_zero () 
{
	echo "<p>Suite à votre annulation vos saisies sont conservées. Vous pouvez poursuivre le calcul de votre BILAN CARBONE™ Personnel
		en accédant aux pages du questionnaire à l'aide du menu ci-contre.</p>\n " ; 
}
//==========================================================================================
function remise_a_zero () 
{
	echo "<p>Suite à votre confirmation toutes vos saisies ont été annulées.</p>
		<p>Vous pouvez recommencer le calcul de votre BILAN CARBONE™ Personnel.
		Pour accéder à la première page du questionnaire 
		<a href='./index.php?type_page=" . QUESTIONNAIRE . "&amp;page=" . PREMIERE_PAGE_QUESTIONNAIRE . "'><strong>cliquez ici</strong></a>.</p>\n " ; 
}
?>