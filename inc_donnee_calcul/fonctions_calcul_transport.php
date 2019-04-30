<?php 
//==========================================================================================
// Fonction principale de calcul des émissions pour le transport
//==========================================================================================
function calcul_transport ( $fe ) 
{
	$resultat = array () ; 
	// ===============================================================================================================
	//          VOITURE
	$nombre = $_SESSION[MENU_NOMBRE][VOITURE] ; 
	for( $i=1 ; $i <= $nombre ; $i+=1 )
		$resultat[VOITURE . '_' . $i] = calcul_voiture ( $i , $fe ) ; 
	// ===============================================================================================================
	//          DEUX-ROUES
	$nombre = $_SESSION[MENU_NOMBRE][DEUX_ROUES] ; 
	for( $i=1 ; $i <= $nombre ; $i+=1 )
		$resultat[DEUX_ROUES . '_' . "$i"] = calcul_deux_roues ( $i , $fe ) ; 
	// ===============================================================================================================
	//          VOLS EN AVION
	$nombre = $_SESSION[MENU_NOMBRE][VOL_AVION] ; 
	for( $i=1 ; $i <= $nombre ; $i+=1 )
		$resultat[VOL_AVION . '_' . "$i"] = calcul_vol_avion ( $i , $fe ) ; 
	// ===============================================================================================================
	//          Transports en commun
	$resultat_transport_commun = calcul_transport_commun ( $fe ) ; 
	$resultat = array_merge ( $resultat , $resultat_transport_commun ) ; 
	// ===============================================================================================================
	//
	// On met les incertitudes à zéro si les résultats sont à zéro
	$resultat = normaliser_incertitude ( $resultat ) ; 
	//
	//          et on renvoie le tout
	return $resultat ; 
}
// ===============================================================================================================
//          VOITURE
// ===============================================================================================================
function calcul_voiture ( $i , $fe ) 
{
	// =========
	// initialisation
	if ( isSet ( $_SESSION[REPONSE][$i . '_transport_voiture_motorisation_select'] ) )
		$carburant = $_SESSION[REPONSE][$i . '_transport_voiture_motorisation_select'] ; 
	else 
		$carburant = 'essence' ; 
	if ( isSet ( $_SESSION[REPONSE][$i . '_transport_voiture_puissance_select'] ) )
		$puissance = $_SESSION[REPONSE][$i . '_transport_voiture_puissance_select'] ; 
	else 
		$puissance = '3cv' ; 
	if ( isSet ( $_SESSION[REPONSE][$i . '_transport_voiture_type_trajet_select'] ) )
		$type_trajet = $_SESSION[REPONSE][$i . '_transport_voiture_type_trajet_select'] ; 
	else
		$type_trajet = 'urbain' ; 
	if ( isSet ( $_SESSION[REPONSE][$i . '_transport_voiture_age_voiture_select'] ) )
		$age = $_SESSION[REPONSE][$i . '_transport_voiture_age_voiture_select'] ; 
	else
		$age = 'moins_de_10 ans' ; 
	//
	if ( $puissance != 'je_ne_sais_pas' )
	{
		// la puissance fiscale est connue
		$fe_fabrication_au_km = $fe['fe_transport_voiture_' . $carburant . '_' . $puissance . '_fabrication'] ; 
		$fe_combustible_au_km = $fe['fe_transport_voiture_' . $carburant . '_' . $puissance . '_' . $type_trajet] ; 
		$resultat[INCERTITUDE] = $fe['fe_transport_voiture_' . $carburant . '_' . $puissance . '_incertitude'] ; 
	}
	else
	{
		// la puissance fiscale est inconnue
		$fe_fabrication_au_km = $fe['fe_transport_voiture_' . $carburant . '_moyenne_fabrication']; // Moyenne
		$fe_combustible_au_km = $fe['fe_transport_voiture_diesel_moyenne_usage']; // Moyenne
		$resultat[INCERTITUDE] = $fe['fe_transport_voiture_' . $carburant . '_moyenne_incertitude']; // Moyenne
	}
	// Maintenant si l'utilisateur a donnée la puissance fiscalde de son véhicule, on dispose d'un autre paramètre qui est la conso moyenne pour ce type de carburant, de puissance, et de trajet
	// $fe['moy_transport_voiture_' . $carburant . '_litres_' . $puissance . '_' . $type_trajet] ; 
	// si en plus l'utilisateur a fourni sa conso aux 100 km, on va comparer les deux, et s'il y a un gros écart, moduler $fe_combustible_au_km
	if ( $puissance != 'je_ne_sais_pas'  && isSet ( $_SESSION[REPONSE][$i . '_transport_voiture_consommation_num'] ) )
	{
		$ecart_litre = 1.5; 
		$pourcentage_style_conduite = 0.2;
		$conso_moyenne = $fe['moy_transport_voiture_' . $carburant . '_litres_' . $puissance . '_' . $type_trajet] ; 
		$conso_utilisateur = $_SESSION[REPONSE][$i . '_transport_voiture_consommation_num'] ; 
		if ( $conso_utilisateur < $conso_moyenne - $ecart_litre )
			$fe_combustible_au_km = $fe_combustible_au_km * ( 1 - $pourcentage_style_conduite ) ;
		if ( $conso_utilisateur > $conso_moyenne + $ecart_litre )
			$fe_combustible_au_km = $fe_combustible_au_km * ( 1 + $pourcentage_style_conduite ) ;
	}
	//=============
	// prise en compte des émissions dues à la fabrication si véhicule récent
	$facteur = $fe_combustible_au_km ; 
	if ( $age != 'plus_de_10_ans' )
		// si 'moins de dix ans' ou 'je ne sais pas' on ajoute le facteur d'émission pour la fabrication
		$facteur = $facteur + $fe_fabrication_au_km ; 
	//=============	
	// distance 
	$distance = reponse_objet_repete ( $i , 'transport_voiture_kilometrage' ) ; 
	//echo "distance : " . $distance . "<br/>" ; 
	//=============	
	// calcul
	$resultat[EMISSION] = $distance * $facteur ; 
	//==================
	// division par le nombre d'utilisateurs
	if ( isSet ( $_SESSION[REPONSE][$i . '_transport_voiture_responsabilite_num'] ) )
		$nombre_utilisateur = $_SESSION[REPONSE][$i . '_transport_voiture_responsabilite_num'] ; 
	else
		$nombre_utilisateur = 1 ; 
	$resultat[EMISSION] = $resultat[EMISSION] / $nombre_utilisateur ; 
	//==================
	// et on renvoie 
	//echo "<pre>" ; print_r ( $resultat ) ; echo "</pre>" ; 
	return $resultat ; 
}
// ===============================================================================================================
//          DEUX-ROUES
// ===============================================================================================================
function calcul_deux_roues ( $i , $fe ) 
{
	// =========
	// initialisation
	$distance = reponse_objet_repete ( $i , 'transport_deux_roues_distance' ) ; 
	//echo "distance : " . $distance . "<br/>" ; 
	// 
	if ( isSet ( $_SESSION[REPONSE][$i . '_transport_deux_roues_consommation_num'] ) )
	{
		// on a une consommation
		$consommation = $_SESSION[REPONSE][$i . '_transport_deux_roues_consommation_num'] ; 
		// calcul
		$resultat[EMISSION] = $distance /100 * $consommation * $fe['fe_combustibles_fossiles_supercarburant_par_litre'] ;
		$resultat[INCERTITUDE] = $fe['fe_combustibles_fossiles_supercarburant_incertitude'] ;
	}
	else
	{
		// on n'a pas de consommation
		if ( isSet ( $_SESSION[REPONSE][$i . '_transport_deux_roues_puissance_select'] ) ) 
			$puissance = $_SESSION[REPONSE][$i . '_transport_deux_roues_puissance_select'] ; 
		else 
			$puissance = '50cm3' ; 
		$index = 'fe_transport_moto_' . $puissance ; 
		$facteur = $fe [$index] + $fe [$index . '_fabrication'] ; 
		// calcul
		$resultat[EMISSION] = $distance * $facteur ; 
		$resultat[INCERTITUDE] = $fe [$index . '_incertitude'] ; 
	}
	return $resultat ; 
}
// ===============================================================================================================
//          VOLS EN AVION
// ===============================================================================================================
function calcul_vol_avion ( $i , $fe ) 
{
	// =========
	// initialisation
	$distance = reponse_objet_repete ( $i , 'transport_vol_avion_distance' ) ; 
	// 
	if ( isSet ( $_SESSION[REPONSE][$i . '_transport_vol_avion_nb_vols_num'] ) ) 
		$nombre_vol = $_SESSION[REPONSE][$i . '_transport_vol_avion_nb_vols_num'] ; 
	else
		$nombre_vol = 0 ; 
	//
	if ( isSet ( $_SESSION[REPONSE][$i . '_transport_vol_avion_classe_select'] ) ) 
		$classe = $_SESSION[REPONSE][$i . '_transport_vol_avion_classe_select'] ; 
	else 
		$classe = 'seconde' ; 
	// =========
	// distinction selon court ou long courrier
	if ( $distance < 1500 )
		$index_fe = 'fe_transport_avion_court_courrier' ; 
	else 
		$index_fe = 'fe_transport_avion_long_courrier' ; 
	// =========
	// distinction selon la classe
	if ( $classe == "seconde" )
		$index_fe = $index_fe . '_seconde' ; 
	else if ( $classe == "affaires" )
		$index_fe = $index_fe . '_affaire' ; 
	else if ( $classe == "premiere" )
		$index_fe = $index_fe . '_inconnue' ; 
	// =========
	// calcul
	$resultat[EMISSION] = $nombre_vol * $distance * $fe[$index_fe] ;
	$resultat[INCERTITUDE] = $fe [$index_fe . '_incertitude'] ;
	return $resultat ; 
}
// ===============================================================================================================
//          TRANSPORTS EN COMMUN
// ===============================================================================================================
function calcul_transport_commun ( $fe ) 
{
	//================================
	// train 
	$resultat[TRAIN] = calcule_emission_champ_numerique_simple 
		( false , 'transport_transport_commun_train' , 
			$fe['fe_transport_train_france_moyenne'] , $fe['fe_transport_train_france_moyenne_incertitude'] , 12 ) ; 
	//================================
	// collectif non électrique
	$facteur = (
				( $fe['fe_transport_bus_idf_fabrication'] 
					+ $fe['fe_transport_bus_idf'] ) / $fe['moy_transport_bus_idf_passagers'] 
				+ ($fe['fe_transport_bus_province_fabrication'] 
					+ $fe['fe_transport_bus_province'])/ $fe['moy_transport_bus_province_passagers']
			) / 2;
	//
	$incertitude = ($fe['fe_transport_bus_idf_incertitude'] + $fe['fe_transport_bus_province_incertitude']) / 2 ;
	//
	$resultat[COLLECTIF_NON_ELECTRIQUE] = calcule_emission_champ_numerique_simple 
		( false , 'transport_transport_commun_bus' , $facteur , $incertitude , 10/60 * 52  ) ;
	//================================
	// collectif électrique
	$resultat[COLLECTIF_ELECTRIQUE] = calcule_emission_champ_numerique_simple 
		( false , 'transport_transport_commun_rer' , 
			$fe['fe_transport_train_france_ile_de_france'] , $fe['fe_transport_train_france_ile_de_france_incertitude'] , 45/60 * 52   ) ;	
	//
	return $resultat ; 
}
?>