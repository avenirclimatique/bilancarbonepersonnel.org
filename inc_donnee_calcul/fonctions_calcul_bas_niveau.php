<?php 
//==========================================================================================
// Fonction qui fournit 0 ou une réponse numérique par défaut, quand la variable de session de réponse n'est pas fournie
//==========================================================================================
function reponse_objet_repete ( $numero , $index )
{
	$index = $numero . '_' . $index ; 
	if ( isSet ( $_SESSION[REPONSE][$index . '_num'] ) )
		//$reponse = rend_valide_virgule ( $_SESSION[REPONSE][$index . '_num'] ) ; 
		$reponse = $_SESSION[REPONSE][$index . '_num'] ; 
	else 
		$reponse = 0 ; 
	return $reponse ; 
}
//==========================================================================================
// Fonction qui fournit 0 ou une réponse numérique par défaut, quand la variable de session de réponse n'est pas fournie
//==========================================================================================
function reponse ( $index )
{
	if ( isSet ( $_SESSION[REPONSE][$index . '_num'] ) )
		//$reponse = rend_valide_virgule ( $_SESSION[REPONSE][$index . '_num'] ) ; 
		$reponse = $_SESSION[REPONSE][$index . '_num'] ; 
	else 
		$reponse = 0 ; 
	return $reponse ; 
}
//==========================================================================================
// Fonction qui ajouter à un index son numéro au début ... (pas un gros boulot)
//==========================================================================================
function numerote_index_question ( $numero , $index )
{
	if ( $numero == false )
		$index_numerote = $index ; 
	else
		$index_numerote = $numero . '_' . $index ; 
	return $index_numerote ; 
}
//==========================================================================================
// Fonction qui affecte un tableau $resultat[EMISSION] et $resultat[INCERTITUDE] associé à une question à champ numérique simple (sans checkbox )
//==========================================================================================
function calcule_emission_champ_numerique_simple 
	( $numero , $index_question , $fe_emission , $fe_incertitude , $facteur_multiplicatif_emission )
{
	// =================================
	// on ajoute à l'index de la question le numero (du logement, de la voiture...) si nécessaire
	$index_question = numerote_index_question ( $numero , $index_question ) ; 
	//
	// ==================================
	// on va récupérer la réponse à la question (si pas de saisie ça récupère zéro)
	$reponse = reponse ( $index_question ) ; 
	//
	// ==================================
	// on calcule
	$resultat[EMISSION] = $reponse * $fe_emission * $facteur_multiplicatif_emission ; 
	$resultat[INCERTITUDE] = $fe_incertitude ; 
	//
	// ==================================
	// on renvoie
	return $resultat ; 
}
//==========================================================================================
// Fonction qui affecte un tableau $resultat[EMISSION] et $resultat[INCERTITUDE] associé à une question à champ numérique avec une boite checkbox qui permet à l'utilisateur
// de demander à ce que le calcul soit fait avec une valeur par défaut
//==========================================================================================
function calcule_emission_champ_numerique_checkbox_je_ne_sais_pas 
	( $numero , $index_question , $fe_emission , $fe_incertitude , $reponse_moyenne , $reponse_moyenne_incertitude , $facteur_multiplicatif_emission )
{
	// =================================
	// on ajoute à l'index de la question le numero (du logement, de la voiture...) si nécessaire
	$index_question = numerote_index_question ( $numero , $index_question ) ; 
	//
	// =================================
	
	if ( !isSet ( $_SESSION[REPONSE][$index_question . '_checkbox'] ) 
			&& isSet ( $_SESSION [REPONSE][$index_question . '_num'] ) )
	{
		// la checkbox n'est pas cochée, et on a une réponse numérique valide, car normalement vérifiée par le programme
		$reponse =	$_SESSION [REPONSE][$index_question . '_num'] ; 
		$resultat[INCERTITUDE] = $fe_incertitude ;
	}
	else if ( isSet ( $_SESSION[REPONSE][$index_question . '_checkbox'] ) )
	{
		// la checkbox est cochée
		//echo $index_question . "checkbox cochée <br/>" ; 
		$reponse = $reponse_moyenne ; 
		$resultat[INCERTITUDE] = $reponse_moyenne_incertitude ; 
	}
	else
	{
		// la checkbox n'est pas cochée et on n'a pas de réponse numérique
		$reponse = 0 ; 
		$resultat[INCERTITUDE] = 0 ; 
	}
	// ==================================
	// on calcule
	$resultat[EMISSION] = $reponse * $fe_emission * $facteur_multiplicatif_emission ; 
	//
	// ==================================
	// on renvoie
	return $resultat ; 
}
//==========================================================================================
// Fonction d'agrégation d'émissions
//==========================================================================================
function agrege_emission ( $emission_a_agreger , $liste_index_emissions_a_agreger )
{
	$total_emission = 0 ; 
	foreach ( $liste_index_emissions_a_agreger as $index )
	{
		// echo $index . "<br/>" ; 
		$total_emission += $emission_a_agreger [$index][EMISSION] ; 
	}	
	if ( $total_emission != 0 )
	{
		$incertitude_abs = 0 ; 
		foreach ( $liste_index_emissions_a_agreger as $index )
			$incertitude_abs += $emission_a_agreger [$index][EMISSION] * $emission_a_agreger [$index][INCERTITUDE] ; 
		$incertitude = $incertitude_abs / $total_emission ; 
	}
	else
		$incertitude = 0 ; // par convention 
		//$incertitude = $emission_a_agreger [$index][INCERTITUDE] ; // par convention on prend la dernière incertitude... sachant que 
	// ==========
	$resultat = array () ; 
	$resultat [EMISSION] = $total_emission ; 
	$resultat [INCERTITUDE] = $incertitude ; 
	// ==========
	return $resultat ; 
}
//==========================================================================================
// Met les incertitudes à zéro si les résultats sont à zéro
//==========================================================================================
function normaliser_incertitude ( $resultat )
{
	foreach ( $resultat as $cle=>$emission_incertitude ) 
	{
		if ( $resultat[$cle][EMISSION] == 0 ) 
			$resultat[$cle][INCERTITUDE] = 0 ; 
	}
	return $resultat ; 
}
/*
//=====================================================================================================================================
// Agréger deux émissions (variante de la précédente, utilisée si on ne veut pas s'embêter avec un tableau d'indices
//=====================================================================================================================================
function agrege_deux_emissions ( $emissions_1 , $emissions_2 )
// agrège deux émissions et les incertitudes associées
{
	$emissions [EMISSION] = $emissions_1 [EMISSION] + $emissions_2 [EMISSION] ; 
	if ( $emissions [EMISSION] != 0 ) 
		$emissions [INCERTITUDE] = 
			( $emissions_1 [EMISSION] * $emissions_1 [INCERTITUDE] + $emissions_2 [EMISSION] * $emissions_2 [INCERTITUDE] )
			/ $emissions [EMISSION] ; 
	else
		$emissions [INCERTITUDE] = 0 ; 
	return $emissions ; 
}
*/

/*
//==========================================================================================
// Fonction de calcul automatique d'émission
//==========================================================================================
function calcule_emission ( $intitule_reponse_numerique , $index_fe , $facteur_multiplicatif , $fe )
{
	$reponse = reponse ( $intitule_reponse_numerique ) ; 
	$resultat[EMISSION] = $reponse * $fe[ $index_fe ] * $facteur_multiplicatif ; 
	$resultat[INCERTITUDE] = $fe[ $index_fe . '_incertitude' ] ; 
	return $resultat ; 
}
*/

?>