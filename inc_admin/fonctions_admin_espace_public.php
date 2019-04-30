<?php 
//==========================================================================================
// Affichage d'une page administrateur
//==========================================================================================
function affiche_page_admin_espace_public ( ) 
{
	if ( isSet ( $_GET['page'] ) && isSet ( $_SESSION[MODE_ADMIN] ) && $_SESSION[MODE_ADMIN] == true)
	{
		$page = $_GET['page'] ; 
		if ( $page == VARIABLE )
		{
			// Affichage des variables et facteurs d'émission
			$facteurs_emissions = charger_facteurs() ;
			if( isSet ( $_SESSION['id_ad'] ) || 0==0) // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
			{
				echo '<pre>';
				echo '<p><strong>$_SESSION</strong></p>';
				print_r($_SESSION);
				echo '<p><strong>fe</strong> (facteurs d\'émisssion)</p>';
				// print_r($facteurs_emissions);
				echo '</pre>';
			}
		}
		/*
		else if ( $page == TEST_FORMULE )
			// Test des formules
			test_formule () ; 
		*/
	}
}
//==========================================================================================
// Test des formules
//==========================================================================================

?>