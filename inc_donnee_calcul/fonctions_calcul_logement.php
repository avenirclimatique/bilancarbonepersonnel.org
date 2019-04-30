<?php 
//==========================================================================================
// Fonction principale de calcul des émissions pour in logement
//==========================================================================================
function calcul_un_logement ( $i , $fe ) 
{
	// on fait tous les calculs sans tenir compte du nombre de personnes, on divisera par le nombre de personnes à la fin 
	// ==================================================
	// constantes à initialiser 
	// ==================================================
	// date de construction
	if ( isSet ( $_SESSION[REPONSE][$i . '_logement_general_date_radio'] ) && 
				$_SESSION[REPONSE][$i . '_logement_general_date_radio'] == 'apres75' )
		$date_construction = 'apres75' ; 
	else
		$date_construction = 'avant75' ; // par défaut on lui colle une date avant 75
	// =========
	// surface
	if ( isSet ( $_SESSION[REPONSE][$i . '_logement_general_surface_num'] ) )
		$surface = $_SESSION[REPONSE][$i . '_logement_general_surface_num'] ; // chiffre stricteement positif
	else
		$surface = 0 ; 
	$type_logement = type_logement ( $i ) ; // la fonction type_logement est dans fonctions_calcul_logement_energie.php
	// ==================================================
	// CONSOS D'ENERGIE (c'est la fonction principale)
	// ==================================================
	$resultat = calcul_toutes_energies_logement ( $i , $date_construction , $surface , $type_logement , $fe ) ; 
	// =================================================================================================================================== 
	//Electricite parties communes, cas d'un logement collectif
	// =================================================================================================================================== 		
	if ( $type_logement == 'collectif' )
	{
		$resultat [ENERGIE_PARTIE_COMMUNE] = calcule_emission_champ_numerique_checkbox_je_ne_sais_pas 
			( $i , 
				'logement_conso_energie_electricite_parties_communes' , 
				$fe['fe_electricite_usage_eclairage_tertiaire'] , 
				$fe ['fe_electricite_usage_eclairage_tertiaire_incertitude'] , 
				846 , 
				0.4 , 			
				1 ) ; 
				// 846 kWh / an, étude de l'ADEME, à mettre dans le fichier des facteurs d'émission !!!!!!!!!!!!!!!!!!!
				// 0.4 au hasard... !!!!!!!!!!!!!!
		//
		// echo "<pre>" ; print_r ( $resultat [ENERGIE_PARTIE_COMMUNE] ) ; echo "</pre>" ; 
		// et dans ce cas il faut ré-ajuster les variables de sessions liées à la consommation d'électricité pour tenir compte de l'électricité des parties communes
		// ???? pas si clair en fait !!!
		$liste_index_emissions_a_agreger = array ( ELECTRICITE , ENERGIE_PARTIE_COMMUNE ) ; 			
		$resultat [ELECTRICITE] = agrege_emission ( $resultat , $liste_index_emissions_a_agreger ) ;
		// ========================
	}
	else
	{
		// on est obligé de les définir pour la figure !!!
		$resultat [ENERGIE_PARTIE_COMMUNE][EMISSION] = 0 ; 
		$resultat [ENERGIE_PARTIE_COMMUNE][INCERTITUDE] = 0 ; 
		
	}
	// ==================================================
	// CONSTRUCTION
	// ==================================================
	if ( $date_construction == 'avant75' )
	{
		$resultat[CONSTRUCTION][EMISSION] = 0 ;
	}
	else
	{
		$resultat[CONSTRUCTION][EMISSION] = 
			$_SESSION[REPONSE][$i . '_logement_general_surface_num'] * $fe['fe_logement_construction_beton'] / 30 ;
	}
	$resultat[CONSTRUCTION][INCERTITUDE] = $fe ['fe_logement_construction_beton_incertitude'] ;
	// ==================================================
	// EQUIPEMENT TRAVAUX
	// ==================================================
	//emissions dues a l'equipement du logement en gros électroménager
	//
	$nombre_frigo = reponse_objet_repete ( $i , 'logement_equipement_nb_frigo' ) ; 
	$nombre_congelo = reponse_objet_repete ( $i , 'logement_equipement_nb_congelateur' ) ; 
	$nombre_lave_vaisselle = reponse_objet_repete ( $i , 'logement_equipement_nb_lave_vaisselle' ) ; 
	$nombre_lave_linge = reponse_objet_repete ( $i , 'logement_equipement_nb_lave_linge' ) ; 
	$nombre_seche_linge = reponse_objet_repete ( $i , 'logement_equipement_nb_seche_linge' ) ; 	
	$nombre_cuisiniere = reponse_objet_repete ( $i , 'logement_equipement_nb_cuisiniere_num' ) ; 	
	// 
	// ======================================================
	//pour le frigo et le congelateur, j'ai pris 1,5 fois le facteur d'emissions pour les machines
	$resultat[GROS_ELECTROMENAGER][EMISSION] =
	(
		$nombre_frigo	
				* ($fe['moy_equipement_electromenager_refrigerateur_congelateur'] // poids moyen
					+ $fe['moy_equipement_electromenager_refrigerateur'])/2 // moyenne des facteurs d'émission de réfrigérateur simple et de frio+congélo
		+ $nombre_congelo * $fe['moy_equipement_electromenager_congelateur']
	) 
		* $fe['fe_equipement_poids_machines'] * 1.5 /(1000 * 10) // divisé par dix car amortissement sur 10 ans !!!
	+ 
	(
		$nombre_lave_vaisselle * $fe['moy_equipement_electromenager_lave_vaisselle'] // c'est le poids à chaque fois
		+ $nombre_lave_linge * $fe['moy_equipement_electromenager_lave_linge'] 
		+ $nombre_seche_linge * $fe['moy_equipement_electromenager_seche_linge'] 
		+ $nombre_cuisiniere * $fe['moy_equipement_electromenager_cuisiniere']
	)
		* $fe['fe_equipement_poids_machines'] / (1000 * 10) ; // divisé par dix car amortissement sur 10 ans !!!
	//
	$resultat[GROS_ELECTROMENAGER][INCERTITUDE] = $fe['fe_equipement_poids_machines_incertitude'] ; 	
	// ======================================================
	// émissions dues aux achats de meubles
	//============================================================================
	$resultat[MEUBLE] = calcule_emission_champ_numerique_simple
		( $i , 'logement_equipement_meuble' , $fe['fe_equipement_achat_meuble'] , $fe['fe_equipement_achat_meuble_incertitude'] , 0.001 ) ; 
	//============================================================================
	//emissions dues aux travaux
	//============================================================================
	$resultat[TRAVAUX] = calcule_emission_champ_numerique_simple
		( $i , 'logement_equipement_travaux' , $fe['fe_logement_travaux'] , $fe['fe_logement_travaux_incertitude'] , 0.001 ) ; 
	//echo "<pre>" ; print_r ( $resultat[TRAVAUX] ) ; echo "</pre>" ; 
	//echo $fe['fe_logement_travaux'] ; 
	// ===============================
	// pour terminer il reste à diviser toutes les émissions par le nombre de personnes
	if ( isSet ( $_SESSION[REPONSE][$i . '_logement_general_nb_personne_num'] ) ) 
		$nombre_personne = $_SESSION[REPONSE][$i . '_logement_general_nb_personne_num'] ; // normalement si on est arrivé là c'est que c'est un chiffre non nul
	else
		$nombre_personne = 1 ; 
	if ( $nombre_personne != 1 ) 
	{
		foreach ( $resultat as $cle=>$resultat_particulier )
		
			$resultat[$cle] [EMISSION] = $resultat[$cle] [EMISSION] / $nombre_personne ; 
	}
	//
	// On met les incertitudes à zéro si les résultats sont à zéro
	$resultat = normaliser_incertitude ( $resultat ) ; 
	//
	return $resultat ; 
}
?>