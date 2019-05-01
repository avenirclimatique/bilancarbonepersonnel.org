<?php 
//==============================================================================
// Fonction qui permet de gérer des connexions à des bases différentes 
//==============================================================================
function choix_connexion () 
{
	//======================================================
	// Décocher la ligne qui correspond au cas voulu dans la liste ci-dessous
	//======================================================
	//define ("CHOIX_BASE", "local_emmanuel" );
	define ("CHOIX_BASE", "local_nicolas" );
	//define ("CHOIX_BASE", "en_ligne_bilan_carbone_personnel" );
	//define ("CHOIX_BASE", "en_ligne_comptable_carbone" );
	//define ("CHOIX_BASE", "en_ligne_demonstration" );
	//define ("CHOIX_BASE", "en_ligne_bilan_carbone_personnel_bis" );
	//======================================================
	if ( CHOIX_BASE == "local_emmanuel")
		$connexion = connexion ("root", "", "facteur_emission", "localhost") ;
	//
	else if ( CHOIX_BASE == "local_nicolas")
		$connexion = connexion ("root", "", "facteur_emission", "localhost") ;
	//
	else if ( CHOIX_BASE == "en_ligne_comptable_carbone")
		// $connexion = connexion ("risler_basec", "ckRwrAJH", "risler_basec", "mysql5-11") ;
		$connexion = connexion ("rislercomptc", "7trQ46by", "rislercomptc", "mysql5-12") ;
	//
	else if ( CHOIX_BASE == "en_ligne_bilan_carbone_personnel")
		$connexion = connexion ("risler_basc2", "iMvukcUX", "risler_basc2", "mysql5-12") ;
	//
	else if ( CHOIX_BASE == "en_ligne_demonstration")
		$connexion = connexion ("rislerbasdem", "2bCGHHUS", "rislerbasdem", "mysql5-12") ;
	//
	else if ( CHOIX_BASE == "en_ligne_bilan_carbone_personnel_bis")
		$connexion = connexion ("rislerbcpbis", "gUnjFJky", "rislerbcpbis", "mysql5-12") ;
	return $connexion ; 
}
?>
