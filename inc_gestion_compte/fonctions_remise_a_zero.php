<?php 
//==========================================================================================
//       REMETTRE A ZERO
//==========================================================================================
function demande_confirmation_remise_a_zero () 
{
	echo "<p><strong>Etes-vous s�r de vouloir annuler toutes vos saisies&nbsp;?</strong></p>" 
		. "<p>Cette op�ration <strong>annule toutes vos saisies</strong> et vous permet de recommencer votre BILAN CARBONE� Personnel depuis le d�but."
		. "Elle <strong>ne d�truit pas vos sauvegardes</strong> si vous en avez effectu�es."
		. "Elle <strong>n'annule pas votre identification</strong> si vous �tes identifi�.</p>\n " ; 
	//
	$url_confirmation = "./index.php?type_page=" . GESTION_COMPTE . "&amp;page=" . CONFIRMER_REMETTRE_A_ZERO ; 
	$url_annulation = "./index.php?type_page=" . GESTION_COMPTE . "&amp;page=" . ANNULER_REMETTRE_A_ZERO ; 
	demande_confirmation ( 'Confirmer' , $url_confirmation , 'Annuler' , $url_annulation ) ; 
	// la fonction  demande_confirmation se trouve dans fonctions_generales.php
}
//==========================================================================================
function annuler_remise_a_zero () 
{
	echo "<p>Suite � votre annulation vos saisies sont conserv�es. Vous pouvez poursuivre le calcul de votre BILAN CARBONE� Personnel
		en acc�dant aux pages du questionnaire � l'aide du menu ci-contre.</p>\n " ; 
}
//==========================================================================================
function remise_a_zero () 
{
	echo "<p>Suite � votre confirmation toutes vos saisies ont �t� annul�es.</p>
		<p>Vous pouvez recommencer le calcul de votre BILAN CARBONE� Personnel.
		Pour acc�der � la premi�re page du questionnaire 
		<a href='./index.php?type_page=" . QUESTIONNAIRE . "&amp;page=" . PREMIERE_PAGE_QUESTIONNAIRE . "'><strong>cliquez ici</strong></a>.</p>\n " ; 
}
?>