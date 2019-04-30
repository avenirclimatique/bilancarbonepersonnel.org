<?php 
function agrege_resultat ( $resultat )
{
	// ====================================================================================
	// LOGEMENT
	// ====================================================================================
	for( $i=1 ; $i <= $_SESSION[MENU_NOMBRE][LOGEMENT] ; $i+=1 )
	{
		// =================
		// agrégation des résutats pour les différentes énergies
		$liste_energie = liste_energie () ; // la bête liste des énergies
		$resultat[LOGEMENT . '_' . $i][TOTAL_ENERGIE_LOGEMENT] = 
			agrege_emission ( $resultat[LOGEMENT . '_' . $i] , $liste_energie ) ; 
		// ===============================
		// agrégation pour équipement et travaux
		$liste_index_emissions_a_agreger = array ( GROS_ELECTROMENAGER , MEUBLE , TRAVAUX ) ; 
		$resultat[LOGEMENT . '_' . $i][TOTAL_EQUIPEMENT_TRAVAUX] = 
			agrege_emission ( $resultat[LOGEMENT . '_' . $i] , $liste_index_emissions_a_agreger ) ; 
		// ===============================
		// total pour un logement
		$liste_index_emissions_a_agreger = array ( TOTAL_ENERGIE_LOGEMENT , CONSTRUCTION , TOTAL_EQUIPEMENT_TRAVAUX ) ; 
		$resultat[LOGEMENT . '_' . $i][TOTAL_LOGEMENT] = 
			agrege_emission ( $resultat[LOGEMENT . '_' . $i] , $liste_index_emissions_a_agreger ) ; 
	}
	// ====================================================================================
	// TRANSPORT
	// ====================================================================================
	// agréger les émissions des voitures
	$liste_index_emissions_a_agreger = array () ; 
	for ( $i = 1 ; $i <= $_SESSION[MENU_NOMBRE][VOITURE] ; $i++ )
		$liste_index_emissions_a_agreger[] = VOITURE . '_' . $i ; 
	$resultat[TRANSPORT][TOTAL_VOITURE] = agrege_emission ( $resultat[TRANSPORT] , $liste_index_emissions_a_agreger ) ; 
	// agréger les émissions  des deux-roues
	$liste_index_emissions_a_agreger = array () ; 
	for ( $i = 1 ; $i <= $_SESSION[MENU_NOMBRE][DEUX_ROUES] ; $i++ )
		$liste_index_emissions_a_agreger[] = DEUX_ROUES . '_' . $i ; 
	$resultat[TRANSPORT][TOTAL_DEUX_ROUES] = agrege_emission ( $resultat[TRANSPORT] , $liste_index_emissions_a_agreger ) ; 
	// agréger les émissions des avions
	$liste_index_emissions_a_agreger = array () ; 
	for ( $i = 1 ; $i <= $_SESSION[MENU_NOMBRE][VOL_AVION] ; $i++ )
		$liste_index_emissions_a_agreger[] = VOL_AVION . '_' . $i ; 
	$resultat[TRANSPORT][TOTAL_VOL_AVION] = agrege_emission ( $resultat[TRANSPORT] , $liste_index_emissions_a_agreger ) ; 
	//echo "ok_agregation_avion</br" ; 
	// agreger les émissions des transports en commun 
	$liste_index_emissions_a_agreger = array ( TRAIN , COLLECTIF_NON_ELECTRIQUE , COLLECTIF_ELECTRIQUE ) ; 
	$resultat[TRANSPORT][TOTAL_TRANSPORT_COMMUN] = agrege_emission ( $resultat[TRANSPORT] , $liste_index_emissions_a_agreger ) ; 
	// ===============================
	// agréger toutes les émissions
	$liste_index_emissions_a_agreger = array ( TOTAL_VOITURE , TOTAL_DEUX_ROUES , TOTAL_VOL_AVION , TOTAL_TRANSPORT_COMMUN ) ; 
	$resultat[TRANSPORT][TOTAL_TRANSPORT] = agrege_emission ( $resultat[TRANSPORT] , $liste_index_emissions_a_agreger ) ; 
	// ====================================================================================
	// ALIMENTATION
	// ====================================================================================
	// agreger viande poisson
	$liste_index_emissions_a_agreger = array ( VIANDE_ROUGE , VIANDE_PORC , VIANDE_BLANCHE , POISSON ) ; 
	$resultat[ALIMENTATION][TOTAL_VIANDE_POISSON] = agrege_emission ( $resultat[ALIMENTATION] , $liste_index_emissions_a_agreger ) ; 
	// agreger laitage
	$liste_index_emissions_a_agreger = array ( FROMAGE, LAITAGE , LAIT ) ; 
	$resultat[ALIMENTATION][TOTAL_LAITAGE] = agrege_emission ( $resultat[ALIMENTATION] , $liste_index_emissions_a_agreger ) ; 
	// Total fruits et légumes
	$liste_index_emissions_a_agreger = array ( FRUIT_LEGUME_HORS_SAISON , FRUIT_LEGUME_TROPICAL , FRUIT_LEGUME_SAISON ) ; 
	$resultat[ALIMENTATION][TOTAL_FRUIT_LEGUME] = agrege_emission ( $resultat[ALIMENTATION] , $liste_index_emissions_a_agreger ) ; 
	// total boissons
	$liste_index_emissions_a_agreger = array ( EAU , ALCOOL ) ; 
	$resultat[ALIMENTATION][TOTAL_BOISSON]= agrege_emission ( $resultat[ALIMENTATION] , $liste_index_emissions_a_agreger ) ; 
	//  Total alimentation
	$liste_index_emissions_a_agreger = array ( TOTAL_VIANDE_POISSON , TOTAL_LAITAGE , TOTAL_FRUIT_LEGUME , TOTAL_BOISSON , AUTRE_ALIMENTATION ) ; 
	$resultat[ALIMENTATION][TOTAL_ALIMENTATION]= agrege_emission ( $resultat[ALIMENTATION] , $liste_index_emissions_a_agreger ) ; 
	// ====================================================================================
	// CONSOMMATION
	// ====================================================================================
	// on agrège chaussures et habillement hors chaussures
	$liste_index_emissions_a_agreger = array ( CHAUSSURE , HABILLEMENT_HORS_CHAUSSURE ) ; 
	$resultat[CONSOMMATION][HABILLEMENT] = agrege_emission ( $resultat[CONSOMMATION] , $liste_index_emissions_a_agreger ) ; 
	// agregation des résultats pour la vie quotidienne
	$liste_index_emissions_a_agreger = 
		array ( TELE_ORDI , PETIT_INFO , PETIT_CONSO , ASSU_MUT , TELEPHONIE , EMPLOYE , ANIMAUX , DECHET ) ; 
	$resultat[CONSOMMATION][TOTAL_VIE_QUOTIDIENNE] = agrege_emission ( $resultat[CONSOMMATION] , $liste_index_emissions_a_agreger ) ; 
	// agregation des résultats pour les loisirs
	$liste_index_emissions_a_agreger = array ( SPORTS_HIVER , LOCATION , BATEAU_ETC ) ; 
	$resultat[CONSOMMATION][TOTAL_LOISIR] = agrege_emission ( $resultat[CONSOMMATION] , $liste_index_emissions_a_agreger ) ; 
	// agregation des résultats pour la consommation
	$liste_index_emissions_a_agreger = array ( TOTAL_HABILLEMENT , TOTAL_VIE_QUOTIDIENNE , TOTAL_LOISIR ) ; 
	$resultat[CONSOMMATION][TOTAL_CONSOMMATION] = agrege_emission ( $resultat[CONSOMMATION] , $liste_index_emissions_a_agreger ) ;
	// ====================================================================================
	// TOTAL
	// ====================================================================================
	// Total de tous les logements (on renomme les totaux pour pouvoir utiliser la fonction agrege_emission )
	$liste_index_emissions_a_agreger = array () ; 
	for ( $i = 1 ; $i <= $_SESSION[MENU_NOMBRE][LOGEMENT] ; $i++ )
	{
		$resultat[TOUTES_CATEGORIES][TOTAL_LOGEMENT . '_' . $i] = $resultat[LOGEMENT . '_' . $i][TOTAL_LOGEMENT] ;
		$liste_index_emissions_a_agreger[] = TOTAL_LOGEMENT . '_' . $i ; 
	}
	$resultat[TOUTES_CATEGORIES][TOTAL_LOGEMENT] = agrege_emission ( $resultat[TOUTES_CATEGORIES] , $liste_index_emissions_a_agreger ) ; 
	// on renomme les totaux pour pouvoir utiliser la fonction agrege_emission 
	$resultat[TOUTES_CATEGORIES][TOTAL_TRANSPORT] = $resultat[TRANSPORT][TOTAL_TRANSPORT] ; 
	$resultat[TOUTES_CATEGORIES][TOTAL_ALIMENTATION] = $resultat[ALIMENTATION][TOTAL_ALIMENTATION] ; 
	$resultat[TOUTES_CATEGORIES][TOTAL_CONSOMMATION] = $resultat[CONSOMMATION][TOTAL_CONSOMMATION] ;
	// et on somme ! 
	$liste_index_emissions_a_agreger = array ( TOTAL_LOGEMENT , TOTAL_TRANSPORT , TOTAL_ALIMENTATION , TOTAL_CONSOMMATION ) ; 
		$resultat[TOUTES_CATEGORIES][TOTAL] = agrege_emission ( $resultat[TOUTES_CATEGORIES] , $liste_index_emissions_a_agreger ) ; 
	// ====================================================================================
	return $resultat ; 
}
?>