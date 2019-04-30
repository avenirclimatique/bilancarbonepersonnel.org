<?php 
//==========================================================================================
// Fonction de calcul des consommations d'�nergie du logement
//==========================================================================================
function calcul_toutes_energies_logement ( $i , $date_construction , $surface , $type_logement , $fe ) 
{
	// variables de saisie n�cessaires pour effectuer les calcul
	//
	if ( isSet ( $_SESSION[REPONSE][$i . '_logement_general_departement_select'] ) )
		$departement = $_SESSION[REPONSE][$i . '_logement_general_departement_select'] ; 
	else
		$departement = "01-ain" ; 
	$facteur_zone = facteur_zone ( $departement , $fe ) ; 
	//
	$tableau_energies_des_usages = tableau_energies_des_usages ( $i ) ; // fct ci -dessous
	// ==================================================================
	// On appelle, �nergie par �nergie, la fonction calcul_energie , qui calcule les �missions li�es � cette �nergie 
	// en fonction des caract�ristiques des diff�rents usages (chauffage, ecs, cuisson, autres usages de l'�lectricit�)
	// Attention, ceci ne prend pas en compte la conso d'�lectricit� des parties communes d'un logement collectif
	// La fonction est juste en-dessous
	// l'appel de la fonction est juste apr�s
	// 
	// Appels de cette fonction
	//
	$liste_energie = liste_energie () ; // la b�te liste des �nergies
	$resultat = array () ; 
	foreach ( $liste_energie as $energie )
	{
		if ( $energie != BOIS && $energie != SOLAIRE ) // car sinon comme on n'a pas de facteur d'�mission �a cr�e une erreur
		{
			$resultat_une_energie = calcul_une_energie_logement 
				( $energie , $i , $surface , $date_construction , $type_logement , $tableau_energies_des_usages , $fe ) ; 
			$resultat = array_merge ( $resultat , $resultat_une_energie ) ; 
		}
	}
	//
	// si jamais la variable .. n'est pas d�finie, on la d�finit (pour �viter les bugs en page de r�sultat) 
	$liste_usage = liste_usage () ; 
	foreach ( $liste_usage as $usage ) 
		if ( !isSet ( $resultat[$usage] ) )
		{
			$resultat[$usage][EMISSION] = 0 ; 
			$resultat[$usage][INCERTITUDE] = 0 ; 
		}
	// idem avec les �nergies
	foreach ( $liste_energie as $energie ) 
		if ( !isSet ( $resultat[$energie] ) )
		{
			$resultat[$energie][EMISSION] = 0 ; 
			$resultat[$energie][INCERTITUDE] = 0 ; 
		}
	return $resultat ; 
}
//=====================================================================================================================================
// Fonction de d�termination du tableau des �nergies des usages ; utilis�e ci-dessus ET en page de pr�sentation
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
// Fonction de d�termination du type de logement
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
// Fonction de calcul des contributions des diff�rentes �nergies
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
		// Boucle principale, conditionn�e par le fait que cette �nergie concerne au moins l'un des quatre usages ci-dessus
		//
		// champs de saisie pour cette �nergie
		$checkbox = checkbox ( $energie , $i ) ; // retourne false si pas de saisie
		$saisie_numerique = saisie_numerique ( $energie , $i ) ; // retourne false si pas de saisie
		//if ( !$saisie_numerique )
		//	$checkbox = 'je_ne_sais_pas' ; 
		//==============================================	
		// d�finition des index pour les facteurs d'�mission de la conso de cette �nergie en fonction des usages (attention : il n'y a que pour l'�lectricit� que �a diff�re selon les usages !!!!)
		// et estimation des �missions dues � cette �nergie � partir des informations de la page logement->general, sans tenir compte des valeurs de saisie
		// c'est de toutes les mani�res utile ensuite pour estimer les proporitions des diff�rents usages dans la conso
		foreach ( $liste_usage as $usage )
		{
			// on parcourt les usages 
			$index = index ( $energie , $usage ) ; 
			// echo "<p>" . $index . "</p>\n" ; 
			if ( $index == false )
			{
				// pas de facteurs d'�mission pour cette �nergie et cet usage ; normalement, c'est que c'est l'�nergie solaire, les facteurs valent z�ro
				$facteur [$usage][EMISSION] = 0 ; 
				$facteur [$usage][INCERTITUDE] = 0 ; 			
			}
			else
			{
				// on a bien des facteurs
				$facteur [$usage][EMISSION] = $fe [$index] ; 
				$facteur [$usage][INCERTITUDE] = $fe [ $index . '_' . INCERTITUDE ] ; 
				
				//echo "<p>Facteur d'�missions pour : " . $usage . " (�missions) : " . $facteur [$usage][EMISSION] . "</p>\n" ; 
				//echo "<p>Facteur d'�missions pour : " . $usage . " (incertitude) : " . $facteur [$usage][INCERTITUDE] . "</p>\n" ; 
			
			}
			if ( $utilise_cette_energie_logement [$usage] == true )
			{
				// cet usage utilise l'�nergie consid�r�e
				// en fonction de l'usage, on d�termine la consommation par d�faut
				// chaque variable $conso_estimation [$usage] est un tableau � deux entr�es : [EMISSION] et [INCERTITUDE]
				$conso_estimation [$usage] = 
					conso_usage_estimation ( $usage , $tableau_energies_des_usages [$usage] , $date_construction , $type_logement , $surface , $fe ) ; 
				
				//print_r( $conso_estimation [$usage] );
				
				//echo "<p>Estimation de la consommation pour l'usage " . $usage . " : " . $conso_estimation [$usage]['conso'] . " (incertitude : " 
				//. $conso_estimation [$usage][INCERTITUDE] . " ) </p>\n " ; 
				
				// on calcule l'�mission par d�faut en multipliant la conso par d�faut par le facteur d'�mission de l'usage
				$emissions_estimation [$usage][EMISSION] = $conso_estimation [$usage]['conso']* $facteur [$usage] [EMISSION] ;
				// on aura besoin de l'incertitude, donc de la variable $emissions_estimation [$usage][INCERTITUDE], uniquement dans le cas o� il n'y a pas de saisie num�rique, donc on ne la rentre pas 
				// pour le moment
			}
			else
				$emissions_estimation [$usage][EMISSION] = 0 ; 
			
			// echo "<p><strong>Estimation des �missions pour l'usage " . $usage . " : " . $emissions_estimation [$usage] [EMISSION] . "</strong></p>\n " ; 
		
		}

		//======================
		// calcul du total des �missions
		$emissions_total_estimation = 0 ; 
		foreach ( $liste_usage as $usage )
			$emissions_total_estimation = $emissions_total_estimation + $emissions_estimation [$usage] [EMISSION] ; 
		// Attention : le total peut �tre nul, par exemple dans le cas de l'�nergie solaire
		// ... et aussi si on a indiqu� un mode de chauffage collectif pour un logement individuel
		// on d�termine une estimation des proportions des diff�rents usage de cette �nergie
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
		// si on n'a pas de saisie on d�termine les variables de session d'�mission pour les usages concern�s par l'�nergie consid�r�e et pour l'�nergie elle-m�me
		if ( $checkbox == 'je_ne_sais_pas' )
		{
			
			//echo "<p>L'utilisateur a coch� la case 'je_ne_sais_pas' </p>\n " ; 
			
			// la variable $emissions_energie_provisoire est un tableau [EMISSION] et [INCERTITUDE] qui contient les �missions agr�g�es, usage apr�s usage qui utilisent la m�me �nergie
			// on l'initialise � z�ro pour commencer, puis on va lui ajouter les �missions pour les usages utilisant l'�nergie consid�r�e
			$resultat[$energie][EMISSION] = 0 ; 
			$resultat[$energie][INCERTITUDE] = 0 ; 		
			//Allons-y usage par usage : 
			foreach ( $liste_usage as $usage )
			{
				if ( $utilise_cette_energie_logement [$usage] == true )
				{
					// cet usage utilise cette �nergie
					// on r�cup�re l'incertitude (ce qu'on n'avait pas fait plus haut)
					$emissions_estimation [$usage] [INCERTITUDE] = $conso_estimation [$usage] [INCERTITUDE] ; 
					//
					$resultat[$usage] [EMISSION] = $emissions_estimation [$usage] [EMISSION] ; 
					$resultat[$usage] [INCERTITUDE] = $emissions_estimation [$usage] [INCERTITUDE] ; 
					// et enfin on int�gre ces �missions au sous-total correspondant � cette �nergie					
					unset ( $liste_index_emissions_a_agreger ) ; 
					$liste_index_emissions_a_agreger = array ( $energie , $usage ) ; 
					$resultat[$energie] = agrege_emission ( $resultat , $liste_index_emissions_a_agreger ) ; 
				}
			}
		}
		else
		{
			
			// echo "<p>L'utilisateur n'a pas coch� la case 'je_ne_sais_pas' </p>\n " ; 
			// echo "<p>Saisie num�rique : " . $saisie_numerique . "</p>\n " ; 
			
			// ==========================================================================================		
			// on a une saisie, il faut calculer les �missions de l'�nergie, et estimer la r�partition par usages de ces �missions
			// en fait on le fait en m�me temps, car, dans le cas de l'�lectricit�, les facteurs d'�mission diff�rent suivant les usages
			// donc on commence par r�partir la consommation saisie suivant les usages
			// puis on multiplie par les facteurs d'�mission par usage
			// Rappel : les facteurs d'�mission ci-dessous sont identiques SAUF dans le cas de l'�lectricit�
			// On agr�ge tout �a (� terme mettre l'agr�gation d'un nombre arbitraire d'�missions dans une fonction !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!)
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

		// affichage des r�sultats pour v�rification 
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


	} // fin de la partie conditionn�e par le fait qu'au moins un des quatre usages chauffage ou ecs ou cuisson ou autre utilise cette �nergie
	//
	//echo "<pre>" ; print_r ( $resultat ) ; echo "</pre>" ; 
	return $resultat ; 
	//
} // fin de la fonction "calcul_une_energie"
//=====================================================================================================================================
// Fin de la fonction "calcul �nergie"
//=====================================================================================================================================

//=====================================================================================================================================
// fonction qui retourne un tableau index� par les usages, et dont les valeurs sont true ou false selon que l'usage utilise ou non cette �nergie, pour ce logement
//=====================================================================================================================================
function utilise_cette_energie_logement ( $energie , $tableau_energies_des_usages ) 
// retourne un tableau dont les cl�s sont les usages et dont les valeurs sont true si $tableau_energies_des_usages [cl�_usage]  utilise l'�nergie $energie, falise sinon
{
	$liste_usage = liste_usage () ; // la b�te liste des usages
	foreach ( $liste_usage as $usage )
	{
		// Attention : les r�ponses de l'utilisateur � la question : "quelle �nergie pour tel usage ?" sont plus diverses que les �nergies elles-m�mes
		// par exemple : gaz naturel individuel et gaz naturel collectif correspondent tous deux � l'�nergie gaz naturel
		// donc on est oblig� de faire appel � la fonction energie_usage ci-dessous qui fait la correspondance
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
// retourne la valeur de la saisie de l'utilisateur pour cette �nergie, bouton checkbox
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
// retourne la valeur de la saisie de l'utilisateur pour cette �nergie, champ de saisie num�rique
{
	$index = 'logement_conso_energie_' . $energie . '_num' ;
	if ( isSet ( $_SESSION[REPONSE][$numero_logement . '_' . $index] ) )
		$saisie = $_SESSION[REPONSE][$numero_logement . '_' . $index] ; 
	else 
		$saisie = false ; 
	return $saisie ; 
}
//=====================================================================================================================================
// Calcule une estimation de la consommation d'�nergie en fonction d'un usage, du type de cet usage, et d'autres param�tres
//=====================================================================================================================================
function conso_usage_estimation ( $usage, $type_usage , $date_construction , $type_logement , $surface , $fe )
// calcule les consos moyennes par d�faut de l'usage $usage, pour le type d'usage $type_usage
// retourne un tableau � deux entr�es $conso ['conso'] et $conso [INCERTITUDE]
// pour ce qui est du chauffage les consos sont au m2 et par an
// pour ce qui est des autres consos elles sont par logement et par an 
// remarque : il faudrait tenir compte du nombre de personnes dans le logement ! 

// types de chauffage pour un logement collectif : 
// charbon, chauffage_urbain, electricite, fioul_collectif, fioul (au lieu de 'fioul_individuel'), gaz_naturel_collectif, gaz_naturel (au lieu de 'gaz_naturel_individuel'), gpl
// types de chauffage pour un logement individuel : 
// charbon, elctricite, fioul, gaz_naturel, gpl
// attention : il faudrait tester dans les saisies la coh�rence entre le type de logement et le type de chauffage !!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// si le logement est individuel, il faudrait s'assurer que la personne ne r�pond pas : chauffage_urbain ni fioul_collectif ni gaz_naturel_collectif !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

// types d'ecs pour un logement collectif
// chauffage_urbain, electricite, fioul, gaz_naturel, gpl, 
// types d'ecs pour un logement individuel 
// electricite, fioul, gaz_naturel, gpl

{	
	// modif du type de chauffage pour s'adapter � la nomenclature des facteurs d'�mission
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
		// la nomenclature est l�g�rement diff�rente dans le tableau des facteurs d'�mission, � harmoniser !!!!!!!!!!!!!!!!!!!!!!!
	}
	else
	{
		// les autres usages, soit chauffage ou ecs ou �lectricit�
		if ( $usage == ELECTRICITE_AUTRE )
			$index = 'moy_logement_electricite_' . $type_logement . '_' . $date_construction ; 
		else
			// chauffage ou ecs
			$index = 'moy_logement_' . $usage . '_' . $type_logement . '_' . $type_usage . '_' . $date_construction ; 
		$conso ['conso'] = $fe[ $index . '_conso' ] ; 
		$conso [INCERTITUDE] = $fe[ $index . '_' . INCERTITUDE ] ;		
	}
	if ( $usage == CHAUFFAGE )
		// n�cessaire de multiplier par la surface car la conso moyenne est donn�e par m2 dans le cas du chauffage
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
