<?php 
//=====================================================================================================================================
// FONCTIONS BAS NIVEAU DE NOMENCLATURE ET CORRESPONDANCES DES USAGES, ENERGIES DES USAGES, ET ENERGIES POUR LE LOGEMENT
//=====================================================================================================================================

//=====================================================================================================================================
// Bête liste des énergies du logement
//=====================================================================================================================================
function liste_energie () 
{
	$liste = array ( 'electricite', 'gaz_naturel', 'fioul', 'charbon', 'gpl', 'chauffage_urbain', 'bois', 'solaire' ) ; 
	return $liste ; 
}
//=====================================================================================================================================
// Bête liste des usages d'énergie du logement
//=====================================================================================================================================
function liste_usage () 
{
	$liste = array ( CHAUFFAGE , ECS, CUISSON , ELECTRICITE_AUTRE ) ; 
	return $liste ; 
}
//=====================================================================================================================================
// Fonction qui fait la correspondance entre les réponses de l'utilisateur et les énergies (les réponses sont plus diverses que les énergies, par ex : gaz naturel individuel et collectif correspondent tous deux à gaz naturel)
//=====================================================================================================================================
function energie_usage ( $usage , $energie_de_l_usage )
// fait le lien entre le type de chauffage/ecs/cuisson/autre et le type d'énergie 
// (plusieurs types de chauffage peuvent utiliser une même énergie, ex : gaz naturel individuel ou collectif)
{
	if ( $usage == CHAUFFAGE ) 
		switch ( $energie_de_l_usage )
		{
			case 'gaz_naturel_individuel' : $energie = 'gaz_naturel' ; break ; 
			case 'gaz_naturel_collectif' : $energie = 'gaz_naturel' ; break ; 
			case 'fioul_individuel' : $energie = 'fioul' ; break ; 
			case 'fioul_collectif' : $energie = 'fioul' ; break ; 
			case 'gpl' : $energie = 'gpl' ; break ; 
			case 'charbon' : $energie = 'charbon' ; break ; 
			case 'electricite' : $energie = 'electricite' ; break ; 
			case 'chauffage_urbain' : $energie = 'chauffage_urbain' ; break ; 
			case 'bois' : $energie = 'bois' ; break ; 
			case 'solaire' : $energie = 'solaire' ; break ; 
		}
	else if ( $usage == ECS )
		// fait le lien entre le type d'ecs et le type d'énergie (remarque : on pourrait s'en passer car pareil !)
		switch ( $energie_de_l_usage )
		{
			case 'gaz_naturel' : $energie = 'gaz_naturel' ; break ; 
			case 'fioul' : $energie = 'fioul' ; break ; 
			case 'gpl' : $energie = 'gpl' ; break ; 
			case 'electricite' : $energie = 'electricite' ; break ; 
			case 'chauffage_urbain' : $energie = 'chauffage_urbain' ; break ; 
			case 'bois' : $energie = 'bois' ; break ; 
			case 'solaire' : $energie = 'solaire' ; break ; 
		}	
	else if ( $usage == CUISSON )
		// fait le lien entre le type de cuisson et le type d'énergie (remarque : on pourrait s'en passer car pareil)
		switch ( $energie_de_l_usage )
		{
			case 'gaz_naturel' : $energie = 'gaz_naturel' ; break ; 
			case 'electricite' : $energie = 'electricite' ; break ; 		
		}
	else if ( $usage == ELECTRICITE_AUTRE )
		$energie = 'electricite' ; 
	return $energie ; 
}
//=====================================================================================================================================
// Index pour les facteurs d'émission pour l'énergie du logement
//=====================================================================================================================================
function index ( $energie , $usage )
// calcule les index des facteurs d'émission associé à l'énergie ; dans le cas de l'électricité, ce facteur d'émission dépend de l'usage (dans les autres cas il n'en dépend pas) 
// retourne un tableau à deux entrées : $index [EMISSION] et $index [INCERTITUDE]
{
	// dans certains cas on doit renommer l'énergie pour l'adapter à la nomenclature du tableau des facteurs d'émission
	switch ( $energie ) 
	{
		case 'fioul' : $energie = 'fioul_domestique' ; break ; 
		case 'charbon' : $energie = 'houille' ; break ; 
	}
	// puis on détermine l'index du facteur d'émission
	if ( $energie == 'gaz_naturel' || $energie == 'fioul_domestique' || $energie == 'gpl' || $energie == 'charbon' ) 
		// cas des combustibles fossiles
		$index = 'fe_combustibles_fossiles_' . $energie ; 
	else if ( $energie == 'chauffage_urbain' ) 
		// cas du chauffage urbain
		$index = 'fe_vapeur' ; 
	else if ( $energie == 'bois' )
		// cas du bois
		$index = 'fe_combustibles_organiques_bois' ; 
	else if ( $energie == 'electricite' ) 
	{
		// cas de l'électricité : le facteur d'émission dépend de l'usage
		switch ( $usage ) 
		{
			case CHAUFFAGE : $index = 'fe_electricite_usage_chauffage' ; break ; 
			case ECS : $index = 'fe_electricite_usage_froid_ecs_et_divers_residentiels' ; break ; 
			case CUISSON : $index = 'fe_electricite_usage_cuisson_residentiel' ; break ; 
			case ELECTRICITE_AUTRE : $index = 'fe_electricite_usage_tertiaire_indifferencie' ; break ; 
		}
	}
	else if ( $energie == 'solaire' )
		// énergie solaire, pas de facteurs d'émission, on attribue la valeur false
		$index = false ; 
	return $index ; 
} 
//=====================================================================================================================================
// Détermination d'une variable de correction pour la zone géographique
//=====================================================================================================================================
function facteur_zone ( $departement , $fe )
{
	switch( $departement )
	{
		case "01-ain": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "02-aisne": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "03-allier": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "04-alpes-de-haute-provence": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "05-hautes-alpes": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "06-alpes-maritimes": $facteur_zone = $fe['moy_logement_zone_h3']; break;
		case "07-ardeche": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "08-ardennes": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "09-ariege": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "10-aube": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "11-aude": $facteur_zone = $fe['moy_logement_zone_h3']; break;
		case "12-aveyron": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "13-bouches-du-rhone": $facteur_zone = $fe['moy_logement_zone_h3']; break;
		case "14-calvados": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "15-cantal": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "16-charente": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "17-charente-maritime": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "18-cher": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "19-correze": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "2A-corse-du-sud": $facteur_zone = $fe['moy_logement_zone_h3']; break;
		case "2B-haute-corse": $facteur_zone = $fe['moy_logement_zone_h3']; break;
		case "21-cote-d_or": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "22-cotes-d_armor": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "23-creuse": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "24-dordogne": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "25-doubs": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "26-drome": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "27-eure": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "28-eure-et-loir": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "29-finistere": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "30-gard": $facteur_zone = $fe['moy_logement_zone_h3']; break;
		case "31-haute-garonne": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "32-gers": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "33-gironde": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "34-herault": $facteur_zone = $fe['moy_logement_zone_h3']; break;
		case "35-ille-et-vilaine": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "36-indre": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "37-indre-et-loire": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "38-isere": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "39-jura": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "40-landes": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "41-loir-et-cher": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "42-loire": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "43-haute-loire": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "44-loire-atlantique": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "45-loiret": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "46-lot": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "47-lot-et-garonne": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "48-lozere": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "49-maine-et-loire": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "50-manche": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "51-marne": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "52-haute-marne": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "53-mayenne": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "54-meurthe-et-moselle": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "55-meuse": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "56-morbihan": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "57-moselle": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "58-nievre": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "59-nord": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "60-oise": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "61-orne": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "62-pas-de-calais": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "63-puy-de-Dome": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "64-pyrenees-atlantiques": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "65-hautes-pyrenees": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "66-pyrenees-orientales": $facteur_zone = $fe['moy_logement_zone_h3']; break;
		case "67-bas-rhin": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "68-haut-rhin": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "69-rhone": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "70-haute-saone": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "71-saone-et-loire": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "72-sarthe": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "73-savoie": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "74-haute-savoie": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "75-seine": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "76-seine-maritime": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "77-seine-et-marne": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "78-yvelines": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "79-deux-sevres": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "80-somme": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "81-tarn": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "82-tarn-et-garonne": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "83-var": $facteur_zone = $fe['moy_logement_zone_h3']; break;
		case "84-vaucluse": $facteur_zone = $fe['moy_logement_zone_h3']; break;
		case "85-vendee": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "86-vienne": $facteur_zone = $fe['moy_logement_zone_h2']; break;
		case "87-haute-vienne": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "88-vosges": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "89-yonne": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "90-territoire-de-belfort": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "91-essonne": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "92-hauts-de-seine": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "93-seine-saint-denis": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "94-val-de-marne": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "95-val-d_oise": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "suisse": $facteur_zone = $fe['moy_logement_zone_h1']; break;
		case "belgique": $facteur_zone = $fe['moy_logement_zone_h1']; break;
	}
	return $facteur_zone ; 
}
?>