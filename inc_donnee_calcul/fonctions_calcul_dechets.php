<?php 
function calcul_dechets ( $fe ) 
{
	//=============================================
	//calcul des emissions liees au dechets
	// ==================================================
	if ( isSet ( $_SESSION[REPONSE]['consommation_vie_quotidienne_poubelle_select'] ) )
		$gestion_poubelle = $_SESSION[REPONSE]['consommation_vie_quotidienne_poubelle_select'] ; 
	else
		$gestion_poubelle = 'pas_de_reponse' ; 
	//=====================================================
	if ( $gestion_poubelle == 'decharge_avec_val')
	{
		// Décharge avec valorisation énergétique
			$emissions_poubelle = 
				$fe['moy_dechet_putrescible'] 
					* $fe['fe_dechets_decharge_dechets_alimentaires_avec_valorisation']/1000 
				+ $fe['moy_dechet_papier_carton'] 
					* ($fe['fe_dechets_decharge_papier_avec_valorisation'] 
						+ $fe['fe_dechets_decharge_carton_avec_valorisation'])/(2*1000) 
				+ 
					(
						$fe['moy_dechet_plastique'] 
						+ $fe['moy_dechet_verre'] 
						+ $fe['moy_dechet_metal'] 
						+ $fe['moy_dechet_autre']
					) 
					* $fe['fe_dechets_decharge_non_fermentescibles_avec_valorisation']/1000;
			
			$emissions_poubelle_incertitude =  
			(
				$fe['moy_dechet_putrescible'] 
					* $fe['fe_dechets_decharge_dechets_alimentaires_avec_valorisation']/1000 
					* $fe['fe_dechets_decharge_dechets_alimentaires_incertitude'] 
				+ $fe['moy_dechet_papier_carton'] 
					* ($fe['fe_dechets_decharge_papier_avec_valorisation'] 
				+ $fe['fe_dechets_decharge_carton_avec_valorisation'])/(2*1000) 
					* ($fe['fe_dechets_decharge_papier_incertitude'] 
						+ $fe['fe_dechets_decharge_carton_incertitude'])/2 
				+ ($fe['moy_dechet_plastique'] 
						+ $fe['moy_dechet_verre'] 
						+ $fe['moy_dechet_metal'] 
						+ $fe['moy_dechet_autre']
					) 
					* $fe['fe_dechets_decharge_non_fermentescibles_avec_valorisation']/1000 
					*$fe['fe_dechets_decharge_non_fermentescibles_incertitude']
			)/$emissions_poubelle;
	}
	else if ( $gestion_poubelle == 'decharge_sans_val' )
	{
		// Décharge sans valorisation énergétique	
			$emissions_poubelle = 
				$fe['moy_dechet_putrescible'] 
					* $fe['fe_dechets_decharge_dechets_alimentaires_sans_valorisation']/1000 
				+ $fe['moy_dechet_papier_carton'] 
					* ($fe['fe_dechets_decharge_papier_sans_valorisation'] 
						+ $fe['fe_dechets_decharge_carton_sans_valorisation'])/(2*1000) 
				+ 
					(
						$fe['moy_dechet_plastique'] 
						+ $fe['moy_dechet_verre'] 
						+ $fe['moy_dechet_metal'] 
						+ $fe['moy_dechet_autre']
					) 
					* $fe['fe_dechets_decharge_non_fermentescibles_sans_valorisation']/1000;
			
			$emissions_poubelle_incertitude =  ($fe['fe_dechets_decharge_dechets_alimentaires_incertitude'] * $fe['moy_dechet_putrescible'] * $fe['fe_dechets_decharge_dechets_alimentaires_sans_valorisation']/1000 + $fe['moy_dechet_papier_carton'] * ($fe['fe_dechets_decharge_papier_sans_valorisation'] + $fe['fe_dechets_decharge_carton_sans_valorisation'])/(2*1000) * ($fe['fe_dechets_decharge_papier_incertitude'] + $fe['fe_dechets_decharge_carton_incertitude'])/2 + $fe['fe_dechets_decharge_non_fermentescibles_incertitude'] * ($fe['moy_dechet_plastique'] + $fe['moy_dechet_verre'] + $fe['moy_dechet_metal'] + $fe['moy_dechet_autre']) * $fe['fe_dechets_decharge_non_fermentescibles_sans_valorisation']/1000)/$emissions_poubelle;
	}
	else if ( $gestion_poubelle == 'incinerateur_avec_val' )
	{
		// Incinérateur avec valorisation énergétique
			$emissions_poubelle = 
				$fe['moy_dechet_putrescible'] 
					* $fe['fe_dechets_incineration_dechets_alimentaires_avec_valorisation']/1000 
				+ $fe['moy_dechet_papier_carton'] 
					* $fe['fe_dechets_incineration_papier_et_carton_avec_valorisation']/1000 
				+ $fe['moy_dechet_plastique'] * $fe['fe_dechets_incineration_plastiques_avec_valorisation']/1000 
				+ 
					(
						$fe['moy_dechet_verre'] 
						+ $fe['moy_dechet_metal'] 
						+ $fe['moy_dechet_autre']
					) 
					* $fe['fe_dechets_incineration_non_combustibles_avec_valorisation']/1000;
					
			$emissions_poubelle_incertitude = ($fe['fe_dechets_incineration_dechets_alimentaires_incertitude'] * $fe['moy_dechet_putrescible'] * $fe['fe_dechets_incineration_dechets_alimentaires_avec_valorisation']/1000 + $fe['fe_dechets_incineration_papier_et_carton_incertitude'] * $fe['moy_dechet_papier_carton'] * $fe['fe_dechets_incineration_papier_et_carton_avec_valorisation']/1000 + $fe['moy_dechet_plastique'] * $fe['fe_dechets_incineration_plastiques_avec_valorisation']/1000 * $fe['fe_dechets_incineration_plastiques_incertitude'] + $fe['fe_dechets_incineration_non_combustibles_incertitude'] * ($fe['moy_dechet_verre'] + $fe['moy_dechet_metal'] + $fe['moy_dechet_autre']) * $fe['fe_dechets_incineration_non_combustibles_avec_valorisation']/1000)/$emissions_poubelle;
	}
	else if ( $gestion_poubelle == 'incinerateur_sans_val' )
	{
		// Incinérateur sans valorisation énergétique
			$emissions_poubelle = 
				$fe['moy_dechet_putrescible'] 
					* $fe['fe_dechets_incineration_dechets_alimentaires_sans_valorisation']/1000 
				+ $fe['moy_dechet_papier_carton'] 
					* $fe['fe_dechets_incineration_papier_et_carton_sans_valorisation']/1000 
				+ $fe['moy_dechet_plastique'] * $fe['fe_dechets_incineration_plastiques_sans_valorisation']/1000 
				+ 
					(
						$fe['moy_dechet_verre'] 
						+ $fe['moy_dechet_metal'] 
						+ $fe['moy_dechet_autre']
					) 
					* $fe['fe_dechets_incineration_non_combustibles_sans_valorisation']/1000;
					
			$emissions_poubelle_incertitude = ($fe['moy_dechet_putrescible'] * $fe['fe_dechets_incineration_dechets_alimentaires_sans_valorisation']/1000 * $fe['fe_dechets_incineration_dechets_alimentaires_incertitude'] + $fe['fe_dechets_incineration_papier_et_carton_incertitude'] * $fe['moy_dechet_papier_carton'] * $fe['fe_dechets_incineration_papier_et_carton_sans_valorisation']/1000 + $fe['moy_dechet_plastique'] * $fe['fe_dechets_incineration_plastiques_sans_valorisation']/1000 * $fe['fe_dechets_incineration_plastiques_incertitude'] + $fe['fe_dechets_incineration_non_combustibles_incertitude'] * ($fe['moy_dechet_verre'] + $fe['moy_dechet_metal'] + $fe['moy_dechet_autre']) * $fe['fe_dechets_incineration_non_combustibles_sans_valorisation']/1000)/$emissions_poubelle;
	}
	else if ( $gestion_poubelle == 'pas_de_reponse' )
	{
		$emissions_poubelle = 0 ; 
		$emissions_poubelle_incertitude = 0 ; 
	}
	else
	{
		// Je ne sais pas ou "autre" c'est le même calcul...
			$emissions_poubelle = 
				$fe['moy_dechet_putrescible'] 
					* $fe['fe_dechets_defaut_dechets_alimentaires']/1000 	
				+ $fe['moy_dechet_papier_carton'] 
					* ($fe['fe_dechets_defaut_carton'] 
							+ $fe['fe_dechets_defaut_papier'])/(2*1000) 
				+ $fe['moy_dechet_plastique'] * $fe['fe_dechets_defaut_plastiques']/1000 
				+ 
					(
						$fe['moy_dechet_verre'] 
						+ $fe['moy_dechet_metal'] 
						+ $fe['moy_dechet_autre']
					) 
					* $fe['fe_dechets_defaut_non_combustibles_ni_fermentescibles']/1000;
								
			$emissions_poubelle_incertitude =  ($fe['fe_dechets_defaut_dechets_alimentaires_incertitude'] * $fe['moy_dechet_putrescible'] * $fe['fe_dechets_defaut_dechets_alimentaires']/1000 + $fe['fe_dechets_defaut_carton_incertitude'] * $fe['moy_dechet_papier_carton'] * ($fe['fe_dechets_defaut_carton'] + $fe['fe_dechets_defaut_papier'])/(2*1000) + $fe['fe_dechets_defaut_papier_incertitude'] + $fe['moy_dechet_plastique'] * $fe['fe_dechets_defaut_plastiques']/1000 * $fe['fe_dechets_defaut_plastiques_incertitude'] + $fe['fe_dechets_defaut_non_combustibles_ni_fermentescibles_incertitude'] * ($fe['moy_dechet_verre'] + $fe['moy_dechet_metal'] + $fe['moy_dechet_autre']) * $fe['fe_dechets_defaut_non_combustibles_ni_fermentescibles']/1000)/ $emissions_poubelle;
		
	}
	//=======================================================
	$resultat[EMISSION] = $emissions_poubelle ; 
	$resultat[INCERTITUDE] = $emissions_poubelle_incertitude ; 
	
	// echo "<pre>" ; print_r ( $resultat ) ; echo "</pre>" ; 
	//
	return $resultat ; 
}

?>