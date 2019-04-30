<?php 
//==========================================================================================
// Fonction principale de calcul des émissions pour la consommation
//==========================================================================================
function calcul_consommation ( $fe ) 
{
	//================================================================================================================================
	// Calcul des émissions liees aux chaussures
	$resultat[CHAUSSURE] = calcule_emission_champ_numerique_simple 
		( false , 'consommation_habillement_achat_chaussure' , $fe['fe_equipement_achat_chaussure'] , 
			$fe['fe_equipement_achat_chaussure_incertitude'] , 0.001 ) ; 
	//================================================================================================================================
	// Calcul des émissions liees aux vêtements
	//================================================================================================================================
	if ( !isSet ( $_SESSION[REPONSE]['consommation_habillement_budget_checkbox'] ) ) 
	{
		// bouton "je ne sais pas" non coché, dans ce cas on ne tient pas compte des éventuels nombres de vêtements saisis, on procède avec l'approche par les prix
		// 
		$resultat[HABILLEMENT_HORS_CHAUSSURE] = calcule_emission_champ_numerique_simple 
			( false , 'consommation_habillement_budget' , $fe['fe_equipement_achat_vetement'] , 
				$fe['fe_equipement_achat_vetement_incertitude'] , 0.001 ) ; 
	}
	else 
	{
		// pas de budget fourni donc approche par le nombre
		//
		//si ils n'ont pas donnes leur budget de vêtements, on approche par le nombre de vêtements achetes en rajoutant 20 % pour tout le reste ...
		// raison ????
		//
		// manteaux
		$resultat_int['manteaux'] = calcule_emission_champ_numerique_simple 
			( false , 'consommation_habillement_nb_manteaux' , $fe['fe_equipement_vetements_manteaux'] , 
				$fe['fe_equipement_vetements_manteaux_incertitude'] , 1.2 ) ; 
		// 
		// pantalons 
		$resultat_int['pantalons'] = calcule_emission_champ_numerique_simple 
			( false , 'consommation_habillement_nb_pantalons' , $fe['fe_equipement_vetements_pantalons'] , 
				$fe['fe_equipement_vetements_pantalons_incertitude'] , 1.2 ) ; 
		// 
		// pulls
		$resultat_int['pulls'] = calcule_emission_champ_numerique_simple 
			( false , 'consommation_habillement_nb_pulls' , $fe['fe_equipement_vetements_pulls'] , 
				$fe['fe_equipement_vetements_pulls_incertitude'] , 1.2 ) ; 
				// 
		// tshirts
		$resultat_int['tshirts'] = calcule_emission_champ_numerique_simple 
			( false , 'consommation_habillement_nb_tshirts' , $fe['fe_equipement_vetements_tshirts_chemises'] , 
				$fe['fe_equipement_vetements_tshirts_chemises_incertitude'] , 1.2 ) ; 
		//
		// on agrège
		$liste_index_emissions_a_agreger = array ( 'manteaux' , 'pantalons' , 'pulls' , 'tshirts' ) ; 
		$resultat[HABILLEMENT_HORS_CHAUSSURE] = agrege_emission ( $resultat_int , $liste_index_emissions_a_agreger ) ; 
	}
	//================================================================================================================================
	// VIE QUOTIDIENNE
	//================================================================================================================================
	//calcul des emissions liees aux ordis
	$resultat[TELE_ORDI] = calcule_emission_champ_numerique_simple 
			( false , 'consommation_vie_quotidienne_budget_ordis_teles' , $fe['fe_equipement_achat_ordi'] , 
				$fe['fe_equipement_achat_ordi_incertitude'] , 0.001 ) ; 
	//
	//calcul des emissions liees au matos informatique autre
	$resultat[PETIT_INFO] = calcule_emission_champ_numerique_simple 
			( false , 'consommation_vie_quotidienne_budget_petit_informatique' , $fe['fe_equipement_achat_informatique'] , 
				$fe['fe_equipement_achat_informatique_incertitude'] , 0.001 ) ; 
	//
	//calcul des emissions liees des achats de consommables
	$resultat[PETIT_CONSO] = calcule_emission_champ_numerique_simple 
			( false , 'consommation_vie_quotidienne_budget_petits_achats' , $fe['fe_equipement_achat_petit_materiel'] , 
				$fe['fe_equipement_achat_petit_materiel_incertitude'] , 0.001 * 12 ) ; 
	//
	//calcul des emissions liees aux services (assurances ...)
	$resultat[ASSU_MUT] = calcule_emission_champ_numerique_simple 
			( false , 'consommation_vie_quotidienne_assurance' , $fe['fe_equipement_service'] , 
				$fe['fe_equipement_service_incertitude'] , 0.001 ) ; 
	//
	//calcul des emissions liees aux telecom
	$resultat[TELEPHONIE] = calcule_emission_champ_numerique_simple 
			( false , 'consommation_vie_quotidienne_facture_telecom' , $fe['fe_equipement_telecom'] , 
				$fe['fe_equipement_telecom_incertitude'] , 0.001 ) ; 
	//
	//calcul des emissions liees au personnel de maison
	$facteur = ($fe['fe_transport_voiture_diesel_moyenne_usage'] 
		+ $fe['fe_transport_bus_province']/ $fe['moy_transport_bus_province_passagers'])/2 ;
	$resultat[EMPLOYE] = calcule_emission_champ_numerique_simple 
			( false , 'consommation_vie_quotidienne_km_personnels' , $facteur , 
				0.5 , 46 ) ; 
	// on compte 46 semaines dans l'année
	// j'ai mis un facteur d'émission de 50 % car on ne sait pas si transport en commun ou voiture
	//
	// Calcul emissions liees aux animaux
	$resultat_int['viande'] = calcule_emission_champ_numerique_simple 
			( false , 'consommation_vie_quotidienne_nourriture_chien1' , $fe['fe_alimentation_boeuf'] , 
				$fe['fe_alimentation_boeuf_incertitude'] , 12 * 0.001 ) ; 
	// 
	$resultat_int['croquettes'] = calcule_emission_champ_numerique_simple 
			( false , 'consommation_vie_quotidienne_nourriture_chien2' , $fe['fe_alimentation_nourriture_chien'] , 
				$fe['fe_alimentation_nourriture_chien_incertitude'] , 12 * 0.001 ) ; 
	//
	$liste_index_emissions_a_agreger = array ( 'viande' , 'croquettes' ) ; 
	$resultat[ANIMAUX] = agrege_emission ( $resultat_int , $liste_index_emissions_a_agreger ) ; 
	//
	//calcul des emissions liees au dechets
	$resultat[DECHET] = calcul_dechets ( $fe ) ; 
	// ========================================
	//               LOISIRS
	// ========================================
	// ski
	$resultat[SPORTS_HIVER] = calcule_emission_champ_numerique_simple 
			( false , 'consommation_loisir_ski' , $fe['fe_equipement_vacances_ski'] , 
				$fe['fe_equipement_vacances_ski_incertitude'] , 1 ) ; 
	//
	// location hors ski
		$resultat[LOCATION] = calcule_emission_champ_numerique_simple 
			( false , 'consommation_loisir_location' , $fe['fe_equipement_vacances_location'] , 
				$fe['fe_equipement_vacances_location_incertitude'] , 1 ) ; 
	//
	// bateaux etc
		$resultat[BATEAU_ETC] = calcule_emission_champ_numerique_simple 
			( false , 'consommation_loisir_voilier' , $fe['fe_equipement_poids_vehicules'] , 
				$fe['fe_equipement_poids_vehicules_incertitude'] , 0.1 ) ; 
	//
	// On met les incertitudes à zéro si les résultats sont à zéro
	$resultat = normaliser_incertitude ( $resultat ) ; 
	//
	return $resultat ; 
}
?>