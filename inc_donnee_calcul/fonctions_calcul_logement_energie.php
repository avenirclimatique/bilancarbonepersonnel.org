<?php 
//==========================================================================================
// Fonction de calcul des consommations d'énergie du logement
//==========================================================================================
function calcul_toutes_energies_logement ( $i , $date_construction , $surface , $type_logement , $fe ) 
{
	// variables de saisie nécessaires pour effectuer les calcul
	//
	if ( isSet ( $_SESSION[REPONSE][$i . '_logement_general_departement_select'] ) )
		$departement = $_SESSION[REPONSE][$i . '_logement_general_departement_select'] ; 
	else
		$departement = "01-ain" ; 
	$facteur_zone = facteur_zone ( $departement , $fe ) ; 
	//
	$tableau_energies_des_usages = tableau_energies_des_usages ( $i ) ; // fct ci -dessous
	// ==================================================================
	// On appelle, énergie par énergie, la fonction calcul_energie , qui calcule les émissions liées à cette énergie 
	// en fonction des caractéristiques des différents usages (chauffage, ecs, cuisson, autres usages de l'électricité)
	// Attention, ceci ne prend pas en compte la conso d'électricité des parties communes d'un logement collectif
	// La fonction est juste en-dessous
	// l'appel de la fonction est juste après
	// 
	// Appels de cette fonction
	//
	$liste_energie = liste_energie () ; // la bête liste des énergies
	$resultat = array () ; 
	foreach ( $liste_energie as $energie )
	{
		if ( $energie != BOIS && $energie != SOLAIRE ) // car sinon comme on n'a pas de facteur d'émission ça crée une erreur
		{
			$resultat_une_energie = calcul_une_energie_logement 
				( $energie , $i , $surface , $date_construction , $type_logement , $tableau_energies_des_usages , $fe ) ; 
			$resultat = array_merge ( $resultat , $resultat_une_energie ) ; 
		}
	}
	//
	// si jamais la variable .. n'est pas définie, on la définit (pour éviter les bugs en page de résultat) 
	$liste_usage = liste_usage () ; 
	foreach ( $liste_usage as $usage ) 
		if ( !isSet ( $resultat[$usage] ) )
		{
			$resultat[$usage][EMISSION] = 0 ; 
			$resultat[$usage][INCERTITUDE] = 0 ; 
		}
	// idem avec les énergies
	foreach ( $liste_energie as $energie ) 
		if ( !isSet ( $resultat[$energie] ) )
		{
			$resultat[$energie][EMISSION] = 0 ; 
			$resultat[$energie][INCERTITUDE] = 0 ; 
		}
	return $resultat ; 
}
//=====================================================================================================================================
// Fonction de détermination du tableau des énergies des usages ; utilisée ci-dessus ET en page de présentation
//=====================================================================================================================================
function tableau_energies_des_usages ( $i ) 
{
	if ( isSet ( $_SESSION[REPONSE][$i . '_logement_general_type_chauffage_select'] ) )
		$tableau_energies_des_usages [CHAUFFAGE] = $_SESSION[REPONSE][$i . '_logement_general_type_chauffage_select'] ; 
	else
		$tableau_energies_des_usages [CHAUFFAGE] = 'gaz_naturel_individuel' ; 
	//
	if ( isSet ( $_SESSION[REPONSE][$i . '_logement_general_type_ecs_select'] ) )
		$tableau_energies_des_usages [ECS] = $_SESSION[REPONSE][$i . '_logement_general_type_ecs_select'] ; 
	else
		$tableau_energies_des_usages [ECS] = 'gaz_naturel' ; 
	//
	if ( isSet ( $_SESSION[REPONSE][$i . '_logement_general_type_cuisson_select'] ) )
		$tableau_energies_des_usages [CUISSON] = $_SESSION[REPONSE][$i . '_logement_general_type_cuisson_select'] ;  
	else
		$tableau_energies_des_usages [CUISSON] = 'gaz_naturel' ; 
	$tableau_energies_des_usages [ELECTRICITE_AUTRE] = 'electricite' ; 
	//
	return $tableau_energies_des_usages ; 
}
//=====================================================================================================================================
// Fonction de détermination du type de logement
//=====================================================================================================================================
function type_logement ( $i ) 
{
	if ( isSet ( $_SESSION[REPONSE][$i . '_logement_general_individuel_collectif_select'] ) )
		$type_logement = $_SESSION[REPONSE][$i . '_logement_general_individuel_collectif_select'] ; // existe toujours	// 
	else 
		$type_logement = 'individuel' ; 
	return $type_logement ; 
}
//=====================================================================================================================================
// Fonction de calcul des contributions des différentes énergies
//=====================================================================================================================================
function calcul_une_energie_logement 
			( $energie , $i , $surface , $date_construction , $type_logement , $tableau_energies_des_usages , $fe )
{	
	$liste_usage = liste_usage () ; // liste des quatre usages : chauffage, ecs, cuisson, autre
	$utilise_cette_energie_logement = utilise_cette_energie_logement ( $energie , $tableau_energies_des_usages ) ; 
	// initialisation (pour pouvoir renvoyer quelque chose)
	$resultat[$energie][EMISSION] = 0 ; 
	$resultat[$energie][INCERTITUDE] = 0 ; 
	//====================
	if ( $utilise_cette_energie_logement [CHAUFFAGE] == true || $utilise_cette_energie_logement [ECS] == true 
				|| $utilise_cette_energie_logement [CUISSON] == true || $utilise_cette_energie_logement [ELECTRICITE_AUTRE] == true )
	{
		//==============================================
		// Boucle principale, conditionnée par le fait que cette énergie concerne au moins l'un des quatre usages ci-dessus
		//
		// champs de saisie pour cette énergie
		$checkbox = checkbox ( $energie , $i ) ; // retourne false si pas de saisie
		$saisie_numerique = saisie_numerique ( $energie , $i ) ; // retourne false si pas de saisie
		//if ( !$saisie_numerique )
		//	$checkbox = 'je_ne_sais_pas' ; 
		//==============================================	
		// définition des index pour les facteurs d'émission de la conso de cette énergie en fonction des usages (attention : il n'y a que pour l'électricité que ça diffère selon les usages !!!!)
		// et estimation des émissions dues à cette énergie à partir des informations de la page logement->general, sans tenir compte des valeurs de saisie
		// c'est de toutes les manières utile ensuite pour estimer les proporitions des différents usages dans la conso
		foreach ( $liste_usage as $usage )
		{
			// on parcourt les usages 
			$index = index ( $energie , $usage ) ; 
			// echo "<p>" . $index . "</p>\n" ; 
			if ( $index == false )
			{
				// pas de facteurs d'émission pour cette énergie et cet usage ; normalement, c'est que c'est l'énergie solaire, les facteurs valent zéro
				$facteur [$usage][EMISSION] = 0 ; 
				$facteur [$usage][INCERTITUDE] = 0 ; 			
			}
			else
			{
				// on a bien des facteurs
				$facteur [$usage][EMISSION] = $fe [$index] ; 
				$facteur [$usage][INCERTITUDE] = $fe [ $index . '_' . INCERTITUDE ] ; 
				
				//echo "<p>Facteur d'émissions pour : " . $usage . " (émissions) : " . $facteur [$usage][EMISSION] . "</p>\n" ; 
				//echo "<p>Facteur d'émissions pour : " . $usage . " (incertitude) : " . $facteur [$usage][INCERTITUDE] . "</p>\n" ; 
			
			}
			if ( $utilise_cette_energie_logement [$usage] == true )
			{
				// cet usage utilise l'énergie considérée
				// en fonction de l'usage, on détermine la consommation par défaut
				// chaque variable $conso_estimation [$usage] est un tableau à deux entrées : [EMISSION] et [INCERTITUDE]
				$conso_estimation [$usage] = 
					conso_usage_estimation ( $usage , $tableau_energies_des_usages [$usage] , $date_construction , $type_logement , $surface , $fe ) ; 
				
				//print_r( $conso_estimation [$usage] );
				
				//echo "<p>Estimation de la consommation pour l'usage " . $usage . " : " . $conso_estimation [$usage]['conso'] . " (incertitude : " 
				//. $conso_estimation [$usage][INCERTITUDE] . " ) </p>\n " ; 
				
				// on calcule l'émission par défaut en multipliant la conso par défaut par le facteur d'émission de l'usage
				$emissions_estimation [$usage][EMISSION] = $conso_estimation [$usage]['conso']* $facteur [$usage] [EMISSION] ;
				// on aura besoin de l'incertitude, donc de la variable $emissions_estimation [$usage][INCERTITUDE], uniquement dans le cas où il n'y a pas de saisie numérique, donc on ne la rentre pas 
				// pour le moment
			}
			else
				$emissions_estimation [$usage][EMISSION] = 0 ; 
			
			// echo "<p><strong>Estimation des émissions pour l'usage " . $usage . " : " . $emissions_estimation [$usage] [EMISSION] . "</strong></p>\n " ; 
		
		}

		//======================
		// calcul du total des émissions
		$emissions_total_estimation = 0 ; 
		foreach ( $liste_usage as $usage )
			$emissions_total_estimation = $emissions_total_estimation + $emissions_estimation [$usage] [EMISSION] ; 
		// Attention : le total peut être nul, par exemple dans le cas de l'énergie solaire
		// ... et aussi si on a indiqué un mode de chauffage collectif pour un logement individuel
		// on détermine une estimation des proportions des différents usage de cette énergie
		foreach ( $liste_usage as $usage )
			if ( $emissions_total_estimation != 0 ) 
				$proportion_estimation [$usage] = $emissions_estimation [$usage] [EMISSION] / $emissions_total_estimation ; 
			else
				$proportion_estimation [$usage] = 0 ; 
		//echo "<p>" ; 
		//print_r( $proportion_estimation );
		//echo "</p>" ; 
		
		// 
		// =====================================================================================================================================
		// si on n'a pas de saisie on détermine les variables de session d'émission pour les usages concernés par l'énergie considérée et pour l'énergie elle-même
		if ( $checkbox == 'je_ne_sais_pas' )
		{
			
			//echo "<p>L'utilisateur a coché la case 'je_ne_sais_pas' </p>\n " ; 
			
			// la variable $emissions_energie_provisoire est un tableau [EMISSION] et [INCERTITUDE] qui contient les émissions agrégées, usage après usage qui utilisent la même énergie
			// on l'initialise à zéro pour commencer, puis on va lui ajouter les émissions pour les usages utilisant l'énergie considérée
			$resultat[$energie][EMISSION] = 0 ; 
			$resultat[$energie][INCERTITUDE] = 0 ; 		
			//Allons-y usage par usage : 
			foreach ( $liste_usage as $usage )
			{
				if ( $utilise_cette_energie_logement [$usage] == true )
				{
					// cet usage utilise cette énergie
					// on récupère l'incertitude (ce qu'on n'avait pas fait plus haut)
					$emissions_estimation [$usage] [INCERTITUDE] = $conso_estimation [$usage] [INCERTITUDE] ; 
					//
					$resultat[$usage] [EMISSION] = $emissions_estimation [$usage] [EMISSION] ; 
					$resultat[$usage] [INCERTITUDE] = $emissions_estimation [$usage] [INCERTITUDE] ; 
					// et enfin on intègre ces émissions au sous-total correspondant à cette énergie					
					unset ( $liste_index_emissions_a_agreger ) ; 
					$liste_index_emissions_a_agreger = array ( $energie , $usage ) ; 
					$resultat[$energie] = agrege_emission ( $resultat , $liste_index_emissions_a_agreger ) ; 
				}
			}
		}
		else
		{
			
			// echo "<p>L'utilisateur n'a pas coché la case 'je_ne_sais_pas' </p>\n " ; 
			// echo "<p>Saisie numérique : " . $saisie_numerique . "</p>\n " ; 
			
			// ==========================================================================================		
			// on a une saisie, il faut calculer les émissions de l'énergie, et estimer la répartition par usages de ces émissions
			// en fait on le fait en même temps, car, dans le cas de l'électricité, les facteurs d'émission diffèrent suivant les usages
			// donc on commence par répartir la consommation saisie suivant les usages
			// puis on multiplie par les facteurs d'émission par usage
			// Rappel : les facteurs d'émission ci-dessous sont identiques SAUF dans le cas de l'électricité
			// On agrège tout ça (à terme mettre l'agrégation d'un nombre arbitraire d'émissions dans une fonction !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!)
			$resultat[$energie][EMISSION] = 0 ; 
			$resultat[$energie][INCERTITUDE] = 0 ; 
			foreach ( $liste_usage as $usage ) 
			{
				if ( $utilise_cette_energie_logement [$usage] == true )
				{
					$resultat[$usage] [EMISSION] = $saisie_numerique * $proportion_estimation [$usage] * $facteur [$usage] [EMISSION] ; 
					$resultat[$usage] [INCERTITUDE] = $facteur [$usage] [INCERTITUDE] ; 
					//
					unset ( $liste_index_emissions_a_agreger ) ; 
					$liste_index_emission_a_agreger = array ( $energie , $usage ) ; 
					$resultat[$energie] = agrege_emission ( $resultat , $liste_index_emission_a_agreger ) ; 
				}
			}
		}
		//
		// =================================================================================================================================== 
		//
		// =================================================================================================================================== 
		
	//echo "<p>" . $energie . "</p>" ; 
	//echo "<pre>" ; print_r ( $resultat ) ; echo "</pre>" ; 
		
		
/*
		print_r( $emissions_energie );
		print_r( $emissions_usage );

		// affichage des résultats pour vérification 
		echo "<p><strong>Energie : " . $energie 
		. "</strong></p>\n" ; 
		
		echo "<p>Emissions : " . $_SESSION['resultat']['logement'][EMISSION . '_'. $energie . '_' . $i]
		. "</p>\n <p>Incertitude : " . $_SESSION['resultat']['logement'][INCERTITUDE][EMISSION . '_'. $energie . '_' . $i]
		. "</p>\n <p>Usages</p>\n "
		. "<ul>\n " ; 
		foreach ( $liste_usage as $usage )
		{
			if ( $utilise_cette_energie_logement [$usage] == true )
			{
				echo "<li>" . $usage . " : Emissions : " . 
				$_SESSION['resultat']['logement'][EMISSION . '_' . $usage . '_' . $i]
				. " Incertitude : " . 
				$_SESSION['resultat']['logement'][INCERTITUDE][EMISSION . '_' . $usage . '_' . $i] 
				. "</li>\n " ;
			}
		}
		echo "</ul>\n " ; 
*/


	} // fin de la partie conditionnée par le fait qu'au moins un des quatre usages chauffage ou ecs ou cuisson ou autre utilise cette énergie
	//
	//echo "<pre>" ; print_r ( $resultat ) ; echo "</pre>" ; 
	return $resultat ; 
	//
} // fin de la fonction "calcul_une_energie"
//=====================================================================================================================================
// Fin de la fonction "calcul énergie"
//=====================================================================================================================================

//=====================================================================================================================================
// fonction qui retourne un tableau indexé par les usages, et dont les valeurs sont true ou false selon que l'usage utilise ou non cette énergie, pour ce logement
//=====================================================================================================================================
function utilise_cette_energie_logement ( $energie , $tableau_energies_des_usages ) 
// retourne un tableau dont les clés sont les usages et dont les valeurs sont true si $tableau_energies_des_usages [clé_usage]  utilise l'énergie $energie, falise sinon
{
	$liste_usage = liste_usage () ; // la bête liste des usages
	foreach ( $liste_usage as $usage )
	{
		// Attention : les réponses de l'utilisateur à la question : "quelle énergie pour tel usage ?" sont plus diverses que les énergies elles-mêmes
		// par exemple : gaz naturel individuel et gaz naturel collectif correspondent tous deux à l'énergie gaz naturel
		// donc on est obligé de faire appel à la fonction energie_usage ci-dessous qui fait la correspondance
		if ( $energie == energie_usage ( $usage , $tableau_energies_des_usages [$usage] ) )
			$utilise_cette_energie_logement [$usage] = true ; 
		else
			$utilise_cette_energie_logement [$usage] = false ; 
	
	}
	return $utilise_cette_energie_logement ; 
} 
//=====================================================================================================================================
// 
//=====================================================================================================================================
function checkbox ( $energie , $numero_logement )
// retourne la valeur de la saisie de l'utilisateur pour cette énergie, bouton checkbox
{
	$index = 'logement_conso_energie_' . $energie . '_checkbox' ;
	if ( isSet ( $_SESSION[REPONSE][$numero_logement . '_' . $index] ) )
		$checkbox = $_SESSION[REPONSE][$numero_logement . '_' . $index] ; 
	else
		$checkbox = false ; 
	return $checkbox ; 
}
//=====================================================================================================================================
//
//=====================================================================================================================================
function saisie_numerique ( $energie , $numero_logement )
// retourne la valeur de la saisie de l'utilisateur pour cette énergie, champ de saisie numérique
{
	$index = 'logement_conso_energie_' . $energie . '_num' ;
	if ( isSet ( $_SESSION[REPONSE][$numero_logement . '_' . $index] ) )
		$saisie = $_SESSION[REPONSE][$numero_logement . '_' . $index] ; 
	else 
		$saisie = false ; 
	return $saisie ; 
}
//=====================================================================================================================================
// Calcule une estimation de la consommation d'énergie en fonction d'un usage, du type de cet usage, et d'autres paramètres
//=====================================================================================================================================
function conso_usage_estimation ( $usage, $type_usage , $date_construction , $type_logement , $surface , $fe )
// calcule les consos moyennes par défaut de l'usage $usage, pour le type d'usage $type_usage
// retourne un tableau à deux entrées $conso ['conso'] et $conso [INCERTITUDE]
// pour ce qui est du chauffage les consos sont au m2 et par an
// pour ce qui est des autres consos elles sont par logement et par an 
// remarque : il faudrait tenir compte du nombre de personnes dans le logement ! 

// types de chauffage pour un logement collectif : 
// charbon, chauffage_urbain, electricite, fioul_collectif, fioul (au lieu de 'fioul_individuel'), gaz_naturel_collectif, gaz_naturel (au lieu de 'gaz_naturel_individuel'), gpl
// types de chauffage pour un logement individuel : 
// charbon, elctricite, fioul, gaz_naturel, gpl
// attention : il faudrait tester dans les saisies la cohérence entre le type de logement et le type de chauffage !!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// si le logement est individuel, il faudrait s'assurer que la personne ne répond pas : chauffage_urbain ni fioul_collectif ni gaz_naturel_collectif !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

// types d'ecs pour un logement collectif
// chauffage_urbain, electricite, fioul, gaz_naturel, gpl, 
// types d'ecs pour un logement individuel 
// electricite, fioul, gaz_naturel, gpl

{	
	// modif du type de chauffage pour s'adapter à la nomenclature des facteurs d'émission
	if ( $type_usage == 'fioul_individuel' )
		$type_usage = 'fioul' ; 
	if ( $type_usage == 'gaz_naturel_individuel' )
		$type_usage = 'gaz_naturel' ; 
	//
	if ( $usage == CUISSON )
	{
		$index = 'moy_logement_cuisson_conso' ; 
		// conso moyenne par logement (????? il faudrait tenir compte du nombre de personnes !!!!)
		$conso ['conso'] = $fe[ $index ] ; 
		$conso [INCERTITUDE] = $fe[ $index . '_' . INCERTITUDE ] ; 
		// la nomenclature est légèrement différente dans le tableau des facteurs d'émission, à harmoniser !!!!!!!!!!!!!!!!!!!!!!!
	}
	else
	{
		// les autres usages, soit chauffage ou ecs ou électricité
		if ( $usage == ELECTRICITE_AUTRE )
			$index = 'moy_logement_electricite_' . $type_logement . '_' . $date_construction ; 
		else
			// chauffage ou ecs
			$index = 'moy_logement_' . $usage . '_' . $type_logement . '_' . $type_usage . '_' . $date_construction ; 
		$conso ['conso'] = $fe[ $index . '_conso' ] ; 
		$conso [INCERTITUDE] = $fe[ $index . '_' . INCERTITUDE ] ;		
	}
	if ( $usage == CHAUFFAGE )
		// nécessaire de multiplier par la surface car la conso moyenne est donnée par m2 dans le cas du chauffage
		$conso ['conso'] = $conso ['conso'] * $surface ; 
	//
/*
	echo "<p>Dans la fonction d'estimation de la conso pour l'usage </p>\n " ; 
	echo "<p>Index : " . $index . "</p>\n " ; 
	echo "<p>Conso : " . $conso ['conso'] . "</p>\n " ; 
	echo "<p>Incertitude : " . $conso [INCERTITUDE] . "</p>\n " ; 
*/

	//
	return $conso ; 
}
?>
