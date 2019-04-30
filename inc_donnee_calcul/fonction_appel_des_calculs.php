<?php 
//==========================================================================================
// FONCTION QUI APPELLE TOUS LES CALCULS
//==========================================================================================
function appelle_calculs_et_retourne_resultat () 
{
	// ==============================================
	// Chargement des facteurs d'émission
	$fe = charger_facteurs () ;
	$resultat = array() ; 	
	// ======================================================	
	require_once ("fonctions_calcul_bas_niveau.php");
	//
	// logement
	require_once ( 'fonctions_calcul_logement.php' );
	require_once ( 'fonctions_calcul_logement_nomenclature.php' ) ; 
	require_once ( 'fonctions_calcul_logement_energie.php' ) ; 
	for( $i=1 ; $i <= $_SESSION[MENU_NOMBRE][LOGEMENT] ; $i+=1 )
		$resultat[LOGEMENT . '_' . $i] = calcul_un_logement ( $i , $fe ) ; //echo "ok_logement" ; 
	//
	// transport
	require_once ("fonctions_calcul_transport.php");
	$resultat[TRANSPORT] = calcul_transport ( $fe ) ; //echo "ok_transports" ; 
	//
	// alimentation
	require_once ("fonctions_calcul_alimentation.php");
	$resultat[ALIMENTATION] = calcul_alimentation ( $fe ) ; //echo "ok_alimentation" ; 
	//
	// consommation
	require_once ("fonctions_calcul_consommation.php");
	require_once ("fonctions_calcul_dechets.php");
	$resultat[CONSOMMATION] = calcul_consommation ( $fe ) ; //echo "ok_consommation" ; 
	// ==============	
	//echo "<pre>" ; print_r ( $resultat ) ; echo "</pre>" ; 
	// echo "<p>Fin des calculs des quatre fichiers de calcul</p>" ; 
	// agregation
	require ("fonction_agrege_resultat.php" ) ; // ça sert à calculer toutes les sommes intermédiaires, agrégations d'émissions, et même la somme totale
	$resultat = agrege_resultat ( $resultat ) ; 
	//
	return $resultat ; 
}
?>