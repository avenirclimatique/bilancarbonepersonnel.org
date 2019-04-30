<?php 
//==========================================================================================
// Fonction principale de calcul des émissions pour l'alimentation
//==========================================================================================
function calcul_alimentation ( $fe ) 
{
	$resultat = array () ; 
	//================================================================================================================================
	// VIANDE POISSON LAITAGES
	//================================================================================================================================
	$proportion_bio = reponse ( 'alimentation_viande_poisson_laitage_bio' ) / 100 ; 
	// echo $proportion_bio ; 
	$proportion_non_bio = 1 - $proportion_bio ; 
	//
	//========================================================
	//viande rouge
	// non bio
	$facteur = 
				( $fe['fe_alimentation_veau'] 
				+ $fe['fe_alimentation_boeuf'] 
				+ $fe['fe_alimentation_agneau_mouton'] ) / 3 ; 	
	$incertitude = 
				( $fe['fe_alimentation_veau_incertitude'] 
				+ $fe['fe_alimentation_boeuf_incertitude'] 
				+ $fe['fe_alimentation_agneau_mouton_incertitude'] ) / 3 ; 
	//
	$resultat_intermediaire ['non_bio'] = calcule_emission_champ_numerique_checkbox_je_ne_sais_pas 
		( false , 
			'alimentation_viande_poisson_laitage_viande_rouge' , 
			$facteur , $incertitude , 
			$fe['moy_alimentation_viande_rouge'] /12 , 
			$fe['moy_alimentation_viande_rouge_incertitude'] , 
			$proportion_non_bio * 12 * 0.001 ) ; 
	// bio
	$facteur = 
			( 	$fe['fe_alimentation_bio_agneau_a_l_herbe'] 
				+ $fe['fe_alimentation_bio_agneau_de_lait'] 
				+ $fe['fe_alimentation_bio_boeuf'] 
				+ $fe['fe_alimentation_bio_mouton'] 
				+ $fe['fe_alimentation_bio_veau'] ) / 5 ; 
	$incertitude = 
			( 	$fe['fe_alimentation_bio_agneau_a_l_herbe_incertitude'] 
				+ $fe['fe_alimentation_bio_agneau_de_lait_incertitude'] 
				+ $fe['fe_alimentation_bio_boeuf_incertitude'] 
				+ $fe['fe_alimentation_bio_mouton_incertitude'] 
				+ $fe['fe_alimentation_bio_veau_incertitude'] ) / 5 ; 
	//
	$resultat_intermediaire ['bio'] = calcule_emission_champ_numerique_checkbox_je_ne_sais_pas 
		( false , 
			'alimentation_viande_poisson_laitage_viande_rouge' , 
			$facteur , $incertitude , 
			$fe['moy_alimentation_viande_rouge'] /12 , 
			$fe['moy_alimentation_viande_rouge_incertitude'] , 
			$proportion_bio * 12 * 0.001 ) ; 
	// total viande rouge
	$liste_index_emissions_a_agreger = array ( 'non_bio' , 'bio' ) ; 
	$resultat[VIANDE_ROUGE] = agrege_emission ( $resultat_intermediaire , $liste_index_emissions_a_agreger ) ; 
	//echo "<pre>" ; print_r ( $resultat[VIANDE_ROUGE] ) ; echo "</pre>" ; 
	//echo $fe['moy_alimentation_viande_rouge_incertitude'] ; 
	//echo "<br/>" ; echo $fe['moy_alimentation_viande_rouge'] ; 
	//========================================================
	//viande de porc
	// non bio
	$resultat_intermediaire ['non_bio'] = calcule_emission_champ_numerique_checkbox_je_ne_sais_pas 
		( false , 
			'alimentation_viande_poisson_laitage_porc' , 
			$fe['fe_alimentation_cochon']  , 
			$fe['fe_alimentation_cochon_incertitude'] , 
			$fe['moy_alimentation_porc'] /12 , 
			$fe['moy_alimentation_porc_incertitude'] , 
			$proportion_non_bio * 12 * 0.001 ) ; 
	// bio
	$resultat_intermediaire ['bio'] = calcule_emission_champ_numerique_checkbox_je_ne_sais_pas 
		( false , 
			'alimentation_viande_poisson_laitage_porc' , 
			$fe['fe_alimentation_bio_porc'] , 
			$fe['fe_alimentation_bio_porc_incertitude'] , 
			$fe['moy_alimentation_porc'] /12 , 
			$fe['moy_alimentation_porc_incertitude'] , 
			$proportion_bio * 12 * 0.001 ) ; 
	// total viande de porc
	$liste_index_emissions_a_agreger = array ( 'non_bio' , 'bio' ) ; 
	$resultat[VIANDE_PORC] = agrege_emission ( $resultat_intermediaire , $liste_index_emissions_a_agreger ) ; 
	//echo "<pre>" ; print_r ( $resultat[VIANDE_PORC] ) ; echo "</pre>" ; 
	//echo $fe['fe_alimentation_cochon'] ; 
	//echo "<br/>" ; echo $fe['moy_alimentation_porc'] ; 
	//========================================================
	//viande blanche
	// non bio
	$facteur = 
			( 	$fe['fe_alimentation_canard'] 
				+ $fe['fe_alimentation_dinde_fermiere'] 
				+ $fe['fe_alimentation_dinde_industrielle'] 
				+ $fe['fe_alimentation_poulet_fermier'] 
				+ $fe['fe_alimentation_poulet_industriel'] ) / 5 ; 
	//echo "<p>Facteur pour viande blanche non bio :" . $facteur . "</p>" ; 
	$incertitude = 
			( 	$fe['fe_alimentation_canard_incertitude'] 
				+ $fe['fe_alimentation_dinde_fermiere_incertitude'] 
				+ $fe['fe_alimentation_dinde_industrielle_incertitude'] 
				+ $fe['fe_alimentation_poulet_fermier_incertitude'] 
				+ $fe['fe_alimentation_poulet_industriel_incertitude'] ) / 5 ; 
	//
	$resultat_intermediaire ['non_bio'] = calcule_emission_champ_numerique_checkbox_je_ne_sais_pas 
		( false , 
			'alimentation_viande_poisson_laitage_viande_blanche' , 
			$facteur, 
			$incertitude , 
			$fe['moy_alimentation_viande_blanche'] / 12, 
			$fe['moy_alimentation_viande_blanche_incertitude'] , 
			$proportion_non_bio * 12 * 0.001 ) ; 
	// bio
	$facteur = ( 	$fe['fe_alimentation_bio_canard'] 
				+ $fe['fe_alimentation_bio_poulet'] ) / 2 ; 
	$incertitude = (		$fe['fe_alimentation_bio_canard_incertitude'] 
				+ $fe['fe_alimentation_bio_poulet_incertitude'] ) / 2 ; 
	$resultat_intermediaire ['bio'] = calcule_emission_champ_numerique_checkbox_je_ne_sais_pas 
		( false , 
			'alimentation_viande_poisson_laitage_viande_blanche' , 
			$facteur, 
			$incertitude, 
			$fe['moy_alimentation_viande_blanche'] / 12 , 
			$fe['moy_alimentation_viande_blanche_incertitude'] , 
			$proportion_bio  * 12 * 0.001 ) ; 
	// total viande blanche
	// echo "<pre>" ; print_r ( $resultat_intermediaire ) ; echo "</pre>" ; 
	$liste_index_emissions_a_agreger = array ( 'non_bio' , 'bio' ) ; 
	$resultat[VIANDE_BLANCHE] = agrege_emission ( $resultat_intermediaire , $liste_index_emissions_a_agreger ) ; 
	// echo "<pre>" ; print_r ( $resultat[VIANDE_BLANCHE] ) ; echo "</pre>" ; 
	//========================================================
	//poisson
	if ( isSet ( $_SESSION[REPONSE]['alimentation_viande_poisson_laitage_provenance_poisson_select'] ) )
		$provenance = $_SESSION[REPONSE]['alimentation_viande_poisson_laitage_provenance_poisson_select'] ; 
	else
		$provenance = 'thon' ; 
	// =================
	// détermination des facteurs en fonction de la provenance
	if ( $provenance == "thon" ) // poisson de mer en majorité
	{
		// non bio 
		$facteur = ( $fe['fe_alimentation_crevettes_pechees'] + $fe['fe_alimentation_peche_tropicale'] ) / 2 ; 
		$incertitude = ( $fe['fe_alimentation_crevettes_pechees_incertitude'] + $fe['fe_alimentation_peche_tropicale_incertitude'] ) / 2 ; 
		// bio 
		$facteur = $fe['fe_alimentation_bio_crevettes'] ; 
		$incertitude = $fe['fe_alimentation_bio_crevettes_incertitude'] ; 
	}
	else if ( $provenance == "truite" ) // poisson de rivière en majorité
	{
		// non bio 
		$facteur = ( $fe['fe_alimentation_poisson'] + $fe['fe_alimentation_peche_europeenne'] ) / 2  ; 
		$incertitude = ( $fe['fe_alimentation_poisson_incertitude'] + $fe['fe_alimentation_peche_europeenne_incertitude'] ) / 2 ; 
		// bio 
		$facteur = $fe['fe_alimentation_bio_poisson_europeen'] ; 
		$incertitude = $fe['fe_alimentation_bio_poisson_europeen_incertitude'] ; 
	}
	else if ( $provenance == "je_ne_sais_pas" ) //???
	{
		// non bio 
		$facteur = 
			(	$fe['fe_alimentation_crevettes_pechees'] 
			+ $fe['fe_alimentation_poisson'] 
			+ $fe['fe_alimentation_peche_tropicale'] 
			+ $fe['fe_alimentation_peche_europeenne'] ) / 4 ; 
		$incertitude = 
			( $fe['fe_alimentation_crevettes_pechees_incertitude'] 
			+ $fe['fe_alimentation_poisson_incertitude'] 
			+ $fe['fe_alimentation_peche_tropicale_incertitude'] 
			+ $fe['fe_alimentation_peche_europeenne_incertitude']) / 4 ; 
		// bio  
		$facteur = ( $fe['fe_alimentation_bio_poisson_europeen'] + $fe['fe_alimentation_bio_crevettes'] ) / 2 ; 
		$incertitude = ( $fe['fe_alimentation_bio_poisson_europeen_incertitude'] + $fe['fe_alimentation_bio_crevettes_incertitude'] ) / 2 ; 
	}
	//===========================
	// Calcul à partir des facteurs précédemment déterminés
	// non bio 
	$resultat_intermediaire['non_bio'] = calcule_emission_champ_numerique_checkbox_je_ne_sais_pas 
		( false , 
			'alimentation_viande_poisson_laitage_poisson' , 
			$facteur , 
			$incertitude , 
			$fe['moy_alimentation_poisson'] / 12, 
			$fe['moy_alimentation_poisson_incertitude'] , 
			$proportion_non_bio * 12 * 0.001 ) ; 
	// bio 
	$resultat_intermediaire ['bio'] = calcule_emission_champ_numerique_checkbox_je_ne_sais_pas 
		( false , 
			'alimentation_viande_poisson_laitage_poisson' , 
			$facteur , 
			$incertitude , 
			$fe['moy_alimentation_poisson'] / 12 , 
			$fe['moy_alimentation_poisson_incertitude'] , 
			$proportion_bio * 12 * 0.001 ) ; 
	// total 
	$liste_index_emissions_a_agreger = array ( 'non_bio' , 'bio' ) ; 
	$resultat[POISSON] = agrege_emission ( $resultat_intermediaire , $liste_index_emissions_a_agreger ) ; 
	//========================================================
	//fromage
		// non bio
	$facteur = 
				( $fe['fe_alimentation_beurre'] 
				+ $fe['fe_alimentation_fromage_pate_crue'] 
				+ $fe['fe_alimentation_fromage_pate_cuite'] ) / 3 ; 	
	$incertitude = 
			( $fe['fe_alimentation_beurre_incertitude'] 
			+ $fe['fe_alimentation_fromage_pate_crue_incertitude'] 
			+ $fe['fe_alimentation_fromage_pate_cuite_incertitude'] ) / 3 ; 
	//
	$resultat_intermediaire ['non_bio'] = calcule_emission_champ_numerique_checkbox_je_ne_sais_pas 
		( false , 
			'alimentation_viande_poisson_laitage_fromage' , 
			$facteur , $incertitude , 
			$fe['moy_alimentation_fromage'] / 12 , 
			$fe['moy_alimentation_fromage_incertitude'] , 
			$proportion_non_bio * 12 * 0.001 ) ; 
	// bio
	$facteur = 
			( $fe['fe_alimentation_bio_beurre'] 
			+ $fe['fe_alimentation_bio_gruyere'])/2 ; 
	$incertitude = 
				( $fe['fe_alimentation_bio_beurre_incertitude'] 
				+ $fe['fe_alimentation_bio_gruyere_incertitude'] ) / 2 ; 
	//
	$resultat_intermediaire ['bio'] = calcule_emission_champ_numerique_checkbox_je_ne_sais_pas 
		( false , 
			'alimentation_viande_poisson_laitage_fromage' , 
			$facteur , 
			$incertitude , 
			$fe['moy_alimentation_fromage'] / 12 , 
			$fe['moy_alimentation_fromage_incertitude'] , 
			$proportion_bio * 12 * 0.001 ) ; 
	// total 
	$liste_index_emissions_a_agreger = array ( 'non_bio' , 'bio' ) ; 
	$resultat[FROMAGE] = agrege_emission ( $resultat_intermediaire , $liste_index_emissions_a_agreger ) ; 
	//========================================================
	//laitages
	// non bio
	$resultat_intermediaire ['non_bio'] = calcule_emission_champ_numerique_checkbox_je_ne_sais_pas 
		( false , 
			'alimentation_viande_poisson_laitage_yaourt' , 
			$fe['fe_alimentation_yaourts'] , 
			$fe['fe_alimentation_yaourts_incertitude'] , 
			$fe['moy_alimentation_yaourt'] / 12 , 
			$fe['moy_alimentation_yaourt_incertitude'] , 
			$proportion_non_bio * 12 * 0.001 ) ; 
	// bio
	$resultat_intermediaire ['bio'] = calcule_emission_champ_numerique_checkbox_je_ne_sais_pas 
		( false , 
			'alimentation_viande_poisson_laitage_yaourt' , 
			$fe['fe_alimentation_bio_yaourt'] , 
			$fe['fe_alimentation_bio_yaourt_incertitude'] , 
			$fe['moy_alimentation_yaourt'] / 12 , 
			$fe['moy_alimentation_yaourt_incertitude'] , 
			$proportion_bio * 12 * 0.001 ) ; 
	// total 
	$liste_index_emissions_a_agreger = array ( 'non_bio' , 'bio' ) ; 
	$resultat[LAITAGE] = agrege_emission ( $resultat_intermediaire , $liste_index_emissions_a_agreger ) ; 
	//========================================================
	//lait
	// non bio
	$resultat_intermediaire ['non_bio'] = calcule_emission_champ_numerique_checkbox_je_ne_sais_pas 
		( false , 
			'alimentation_viande_poisson_laitage_lait' , 
			$fe['fe_alimentation_lait_de_vache'] , 
			$fe['fe_alimentation_lait_de_vache_incertitude'] , 
			$fe['moy_alimentation_lait'] / 12, 
			$fe['moy_alimentation_lait_incertitude'] , 
			$proportion_non_bio * 12 * 0.001 ) ; 
	// bio
	$resultat_intermediaire ['bio'] = calcule_emission_champ_numerique_checkbox_je_ne_sais_pas 
		( false , 
			'alimentation_viande_poisson_laitage_lait' , 
			$fe['fe_alimentation_bio_lait'] , 
			$fe['fe_alimentation_bio_lait_incertitude'] , 
			$fe['moy_alimentation_lait'] /12 , 
			$fe['moy_alimentation_lait_incertitude'] , 
			$proportion_bio * 12 * 0.001 ) ; 
	// total 
	$liste_index_emissions_a_agreger = array ( 'non_bio' , 'bio' ) ; 
	$resultat[LAIT] = agrege_emission ( $resultat_intermediaire , $liste_index_emissions_a_agreger ) ; 
	//================================================================================================================================
	//   FRUITS ET LEGUMES
	//================================================================================================================================
	$proportion_bio = reponse ( 'alimentation_fruit_legume_bio' ) / 100 ; 
	$proportion_non_bio = 1 - $proportion_bio ; 
	//
	//calculs des emissions fruits et legumes
	//pour les fruits et legumes, on prend la moyenne nationale que l'on separe ainsi : 
	// 1/5 de fruits et legumes importes de loin, 
	// 1/5 de fruits et legume importes de pas loin, 
	// 1/5 cultives sous serre chauffes et 
	// 2 cinquiemes en local
	$pourcentage_fruit_exotique = 0.2;  // importé de loi
	$pourcentage_serre_chauffee = 0.15; // serre chauffée
	$pourcentage_importe = 0.25; // importé de pas loin
	$pourcentage_local = 0.4;
	//========================================================
	// Fruits et légumes hors saison
	//========================================================
	// Sous serre : 
	if ( isSet ( $_SESSION[REPONSE]['alimentation_fruit_legume_tomates_checkbox'] ) )
		$poids_serre = $fe['moy_alimentation_legume'] * $pourcentage_serre_chauffee ;
	else
		$poids_serre = reponse ( 'alimentation_fruit_legume_tomates' ) ; 
	//
	// importé
	if ( isSet ( $_SESSION[REPONSE]['alimentation_fruit_legume_tomates_checkbox'] ) 
			|| isSet ( $_SESSION[REPONSE]['alimentation_fruit_legume_fraises_checkbox'] ) 
			|| isSet ( $_SESSION[REPONSE]['alimentation_fruit_legume_fraises_checkbox'] ) )
		// à l'une des trois questions il n'a pas su répondre : on lui affecte une valeur par défaut, car les trois réponses entrent dans le calcul sinon
		$poids_importe = $fe['moy_alimentation_legume'] * $pourcentage_importe ;
	else
	{
		// à aucun des trois il n'a déclaré demander une valeur par défaut
		$poids_tomate = reponse ( 'alimentation_fruit_legume_tomates' ) ; 
		$poids_fraise = reponse ( 'alimentation_fruit_legume_fraises' ) ; 
		$poids_raisin = reponse ( 'alimentation_fruit_legume_raisins' ) ; 
		$poids_importe = 
				$poids_tomate //tomates : une part pour les serres chauffees, et une autre qui passe en importe : c'est pour les concombres, piments et autres cultures sous serres chauffees
			+ 2 * $poids_fraise //le fois 2, c'est pour les oranges, pamplemousses, olives ....  
			+ $poids_raisin ; 
	}
	//une part pour les serres chauffees, et une autre qui passe en importe : c'est pour les concombres, piments et autres cultures sous serres chauffees
	$resultat[FRUIT_LEGUME_HORS_SAISON][EMISSION] = 
			( $poids_serre * $fe['fe_alimentation_legumes_sous_serres_chauffees'] 
			+ $poids_importe * $fe['fe_alimentation_legumes_et_fruits_importes_par_bateau'] ) * 0.001 ;
	//
	$resultat[FRUIT_LEGUME_HORS_SAISON][INCERTITUDE] = $fe['fe_alimentation_legumes_incertitude'] ; 
	//===============================================
	// Fruits et légumes exotiques
	//========================================================
	$facteur = 0.3 * $fe['fe_alimentation_legumes_et_fruits_importes_par_avion'] 
						+ 0.7 * $fe['fe_alimentation_legumes_et_fruits_importes_par_bateau'] ; 
	//
	$resultat[FRUIT_LEGUME_TROPICAL] = calcule_emission_champ_numerique_checkbox_je_ne_sais_pas 
		( false , 
			'alimentation_fruit_legume_exotiques' , 
			$facteur , 
			$fe['fe_alimentation_legumes_incertitude'] , 
			$fe['moy_alimentation_legume'] * $pourcentage_fruit_exotique , 
			$fe['fe_alimentation_legumes_incertitude'] , 
			0.001 ) ; 
	//===============================================
	// Fruits et légumes de saison
	//========================================================
	// non bio
	$resultat_intermediaire ['non_bio'] = calcule_emission_champ_numerique_checkbox_je_ne_sais_pas 
		( false , 
			'alimentation_fruit_legume_saisons' , 
			$fe['fe_alimentation_legumes'] , 
			$fe['fe_alimentation_legumes_incertitude'] , 
			$fe['moy_alimentation_legume'] / 52 , 
			$fe['fe_alimentation_legumes_incertitude'] , 
			$proportion_non_bio * 52 * 0.001 ) ; 
	// bio
	$resultat_intermediaire ['bio'] = calcule_emission_champ_numerique_checkbox_je_ne_sais_pas 
		( false , 
			'alimentation_fruit_legume_saisons' , 
			$fe['fe_alimentation_bio_legume'] , 
			$fe['fe_alimentation_legumes_incertitude'] , 
			$fe['moy_alimentation_legume'] / 52 , 
			$fe['fe_alimentation_legumes_incertitude'] , 
			$proportion_bio * 52 * 0.001 ) ; 
	// total 
	$liste_index_emissions_a_agreger = array ( 'non_bio' , 'bio' ) ; 
	$resultat_intermediaire['de_saison_hors_pomme_de_terre'] = agrege_emission ( $resultat_intermediaire , $liste_index_emissions_a_agreger ) ; 
	// 
	// Pommes de terre
	// pas de question, on prend une moyenne, mais on ne le fait que si une réponse a été apportée à la question sur les fruits et légumes de saison
	if (  
				( isSet ( $_SESSION[REPONSE]['alimentation_fruit_legume_saisons_num'] ) 
						&& $_SESSION[REPONSE]['alimentation_fruit_legume_saisons_num'] != "" )
		|| isSet ( $_SESSION[REPONSE]['alimentation_fruit_legume_saisons_checkbox'] ) )
	{
		$resultat_intermediaire['pomme_de_terre'][EMISSION] = (
				$fe['fe_alimentation_pommes_de_terre'] * $proportion_non_bio
			+ $fe['fe_alimentation_bio_pommes_terre'] * $proportion_bio ) * $fe['moy_alimentation_pommes_de_terre'] * 0.001 ; // conso annuelle
		//
		$resultat_intermediaire['pomme_de_terre'][INCERTITUDE] = 	$fe['fe_alimentation_legumes_incertitude'] ; 
		// echo "<pre>" ; print_r ( $resultat_intermediaire['pomme_de_terre'] ) ; echo "</pre>" ; 
	}
	else
	{
		$resultat_intermediaire['pomme_de_terre'][EMISSION] = 0 ; 
		$resultat_intermediaire['pomme_de_terre'][INCERTITUDE] = 0 ; 
	}
	// total fruit legume de saison
	$liste_index_emissions_a_agreger = array ( 'de_saison_hors_pomme_de_terre' , 'pomme_de_terre' ) ; 
	$resultat[FRUIT_LEGUME_SAISON] = agrege_emission ( $resultat_intermediaire , $liste_index_emissions_a_agreger ) ; 
	//================================================================================================================================
	//  BOISSON
	//================================================================================================================================
	// eau 
	if ( !isSet ( $_SESSION[REPONSE]['alimentation_boisson_eau_radio'] ) || $_SESSION[REPONSE]['alimentation_boisson_eau_radio'] == 'robinet' )
		$resultat[EAU][EMISSION] = 0 ; 
	else 
		$resultat[EAU][EMISSION] = $fe['fe_alimentation_eau_minerale'] / 1000 * 1.5 * 365 ;
	$resultat[EAU][INCERTITUDE] = $fe['fe_alimentation_eau_minerale_incertitude'] ;
	//
	// ==========
	// alcool 
	$resultat[ALCOOL] = calcule_emission_champ_numerique_checkbox_je_ne_sais_pas 
		( false , 
			'alimentation_boisson_alcool' , 
			$fe['fe_alimentation_alcool'] , 
			$fe['fe_alimentation_alcool_incertitude'] , 
			( $fe['moy_alimentation_vin'] + $fe['moy_alimentation_biere'] ) / 12 , 
			($fe['moy_alimentation_vin_incertitude'] + $fe['moy_alimentation_biere_incertitude'])/2 , 
			 0.001 * 12 ) ; 
	//
	//================================================================================================================================
	//  Autres denrées alimentaires
	//================================================================================================================================
	$proportion_bio = reponse ( 'alimentation_fruit_legume_autre_bio' ) / 100 ; 
	$proportion_non_bio = 1 - $proportion_bio ; 
	//
	if ( isSet ( $_SESSION[REPONSE]['alimentation_fruit_legume_autre_bio_num'] ) )
	{
		$calcul = 
				$fe['moy_alimentation_pain'] * $fe['fe_alimentation_pain']
				
			+ $fe['moy_alimentation_oeuf'] * 
						( $fe['fe_alimentation_oeufs'] * $proportion_non_bio		
						+ $fe['fe_alimentation_bio_oeufs'] * $proportion_bio )
						
			+ $fe['moy_alimentation_huile'] * $fe['fe_alimentation_huile_de_tournesol'] 
			+ $fe['moy_alimentation_sucre'] * $fe['fe_alimentation_sucre']  
			+ $fe['moy_alimentation_epicerie'] * $fe['fe_alimentation_epicerie'] ;
		//
		$resultat[AUTRE_ALIMENTATION][EMISSION] = $calcul * 0.001 ; 
		//
		$resultat[AUTRE_ALIMENTATION][INCERTITUDE] = 	(
					$fe['moy_alimentation_pain_incertitude'] 
				+ $fe['moy_alimentation_oeuf_incertitude'] 
				+ $fe['moy_alimentation_huile_incertitude'] 
				+ $fe['moy_alimentation_sucre_incertitude'] 
				+ $fe['moy_alimentation_epicerie_incertitude']
				) / 5;
	}
	else
	{
		$resultat[AUTRE_ALIMENTATION][EMISSION] = 0 ; 
		$resultat[AUTRE_ALIMENTATION][INCERTITUDE] = 0 ; 
	}
	//
	// On met les incertitudes à zéro si les résultats sont à zéro
	$resultat = normaliser_incertitude ( $resultat ) ; 
	//
	return $resultat ; 
}
?>