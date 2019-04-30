<?php 
//=====================================================================================================================================
function nom_energie ()
// il faudrait mettre ça en fichier de langue, à améliorer !!!!!
{
	$nom [ELECTRICITE] = 'Electricité' ; 	
	$nom [GAZ_NATUREL] = 'Gaz naturel' ; 	
	$nom [FIOUL] = 'Fioul' ; 	
	$nom [CHARBON] = 'Charbon' ; 	
	$nom [GPL] = 'GPL' ; 	
	$nom [CHAUFFAGE_URBAIN] = 'Chauffage urbain' ; 	
	$nom [BOIS] = 'Bois' ; 	
	$nom [SOLAIRE] = 'Solaire' ; 	
	return $nom ; 
}
//=====================================================================================================================================
function nom_usage ()
// il faudrait mettre ça en fichier de langue, à améliorer !!!!!
{
	$nom [CHAUFFAGE] = 'Chauffage' ; 
	$nom [ECS] = 'Eau chaude sanitaire' ; 
	$nom [CUISSON] = 'Cuisson des aliments' ; 
	$nom [ELECTRICITE_AUTRE] = 'Electricité hors chauffage, eau chaude sanitaire, cuisson des aliments, et électricité des parties communes' ; 
	// il faudrait distinguer suivant que logement collectif ou individuel, ne parler de parties communes que si logement collectif
	return $nom ; 
}

//=====================================================================================================================================
function affiche_energie ( $energie , $type_usage ) 
// retourne true si l'énergie est utilisée par un des usages, cette fonction est utilisée pour l'affichage
{
	$liste_usage = liste_usage () ; // voir le fichier /inc_donnee_calcul/fonctions_calcul_logement_nomenclature.php
	$affiche = false ; 
	foreach ( $liste_usage as $usage )
	{
		if ( energie_usage ( $usage , $type_usage [$usage] ) == $energie )// voir le fichier /inc_donnee_calcul/fonctions_calcul_logement_nomenclature.php
			$affiche = true ; 
	}
	return $affiche ; 
}

// ============================================================================================
// Tableau des couleurs de base
// ============================================================================================
function couleur_base ()
{
	$tab = array ( 'rouge' , 'vert' , 'bleu' ) ; 
	return $tab ; 
}
// ============================================================================================
// Tableau des catégories
// ============================================================================================
function categorie ()
{
	// $tab = array ( LOGEMENT , 'transports' , ALIMENTATION , CONSOMMATION ) ; 
	$tab ['nom'][LOGEMENT] = 'Logement' ;
	$tab ['nom'][TRANSPORT] = 'Transports' ;
	$tab ['nom'][ALIMENTATION] = 'Alimentation' ;
	$tab ['nom'][CONSOMMATION] = 'Consommation' ;
	//
	// On saisit les couleurs qui vont intervenir pour la réalisation de la figure
	$tab ['vert'][LOGEMENT] = true ;
	$tab ['bleu'][LOGEMENT] = true ;
	//
	$tab ['rouge'][TRANSPORT] = true ;
	$tab ['bleu'][TRANSPORT] = true ;
	//
	$tab ['rouge'][ALIMENTATION] = true ;
	$tab ['vert'][ALIMENTATION] = true ;
	//
	$tab ['rouge'][CONSOMMATION] = true ; 
	$tab ['vert'][CONSOMMATION] = true ; 
	$tab ['bleu'][CONSOMMATION] = true ; 

	return $tab ; 
}
// ============================================================================================
// Taleau des sous-catégories
// ============================================================================================
function sous_categorie ()
{
	/*
	$tab[LOGEMENT] = array ( 'chauffage' , 'ecs' , 'cuisson' , 'electricite_autre' , 'construction' , 'equipement_travaux' ) ; 
	$tab['transports'] = array ( 'voitures', 'deux_roues' , 'avions' , 'transports_commun' ) ; 
	$tab[ALIMENTATION] = array ( 'viandes_poissons' , 'laitages' , 'fruits_legumes' , 'autre_alimentation' , 'boissons' ) ; 
	$tab[CONSOMMATION] = array ( 'vetements' , 'vie_quotidienne' , 'loisirs' ) ; 
	*/
	
	// =====================
	$tab[LOGEMENT]['nom'][CHAUFFAGE] = 'Chauffage' ; 
	$tab[LOGEMENT]['nom'][ECS] = 'Eau chaude sanitaire' ; 
	$tab[LOGEMENT]['nom'][CUISSON] = 'Cuisson' ; 
	$tab[LOGEMENT]['nom'][ELECTRICITE_AUTRE] = 'Electricité - autres usages' ; 
	$tab[LOGEMENT]['nom'][ENERGIE_PARTIE_COMMUNE] = 'Electricité - parties communes (si logement collectif)' ; 	
	$tab[LOGEMENT]['nom'][CONSTRUCTION] = 'Construction' ; 
	$tab[LOGEMENT]['nom'][TOTAL_EQUIPEMENT_TRAVAUX] = 'Equipement - travaux' ; 
	// =====================
	$tab[TRANSPORT]['nom'][TOTAL_VOITURE] = 'Voiture(s)' ; 
	$tab[TRANSPORT]['nom'][TOTAL_DEUX_ROUES] = 'Deux roues' ; 
	$tab[TRANSPORT]['nom'][TOTAL_VOL_AVION] = 'Vol(s) en avion' ; 
	$tab[TRANSPORT]['nom'][TOTAL_TRANSPORT_COMMUN] = 'Transports en commun' ; 
	// =====================
	$tab[ALIMENTATION]['nom'][TOTAL_VIANDE_POISSON] = 'Viandes et poissons' ; 
	$tab[ALIMENTATION]['nom'][TOTAL_LAITAGE] = 'Laitages' ; 
	$tab[ALIMENTATION]['nom'][TOTAL_FRUIT_LEGUME] = 'Fruits et légumes' ; 
	$tab[ALIMENTATION]['nom'][AUTRE_ALIMENTATION] = 'Alimentation - autre' ; 
	$tab[ALIMENTATION]['nom'][TOTAL_BOISSON] = 'Boissons' ; 
	// =====================
	$tab[CONSOMMATION]['nom'][TOTAL_HABILLEMENT] = 'Vêtements' ; 
	$tab[CONSOMMATION]['nom'][TOTAL_VIE_QUOTIDIENNE] = 'Vie quotidienne' ; 
	$tab[CONSOMMATION]['nom'][TOTAL_LOISIR] = 'Loisirs' ; 
	// ===============================================
	// On utilise la fonction ci-dessous pour affecter automatiquement des numéros de couleur à ces sous-catégories
	$tab = couleurs ( $tab ) ; 
	return $tab ; 
}
// ============================================================================================
// Détermination des couleurs des sous-catégories
// ============================================================================================
function couleurs ( $sous_categorie )
// l'objectif est de déterminer des numéros de couleur aux variables $sous_categorie [$categorie][$couleur][$cle_sous_categorie] où $couleur = 'rouge', 'vert', 'bleu' 
{
	$marge_inf_couleur = 80 ; 
	$marge_sup_couleur = 40 ; 
	$categorie = categorie () ; 
	$couleur_base = couleur_base () ; 
	foreach ( $categorie['nom'] as $cle_categorie => $nom_categorie ) // on s'en fout du nom
	// les 4 clés c'est LOGEMENT, etc
	{
		foreach ( $couleur_base as $numero_couleur_base => $cle_couleur_base )
		// on parcourt les trois couleurs de base
		{
			$nombre_sous_categorie = count ( $sous_categorie [$cle_categorie]['nom'] ) ; // on compte combien de sous-catégorie
			$ecart_couleur = ( 256 - ( $marge_inf_couleur + $marge_sup_couleur ) ) / ( $nombre_sous_categorie -1 ) ;
			$poids_couleur = $marge_inf_couleur ; 
			foreach ( $sous_categorie [$cle_categorie]['nom'] as $cle_sous_categorie => $nom_sous_categorie ) // on s'en fout du nom
			{
				// on parcourt les sous-catégories
				if ( isSet ( $categorie [$cle_couleur_base] [$cle_categorie] ) )
				{
					// cette couleur de base intervient
					$poids = round ( $poids_couleur ) ; 
				}
				else
				{
					// cette couleur de base n'intervient pas 
					$poids = 0 ; 
				}
				$sous_categorie [$cle_categorie][$cle_couleur_base][$cle_sous_categorie] = $poids ; 
				$poids_couleur += $ecart_couleur ; 
			}
		}
	}
	return $sous_categorie ; 
}
 
/*
// ============================================================================================
// Affiche pour les énergies (conditionnalité sur le fait que cette énergie est utilisée)
// ============================================================================================
function ligne_energie ( $energie_select , $nom_energie )
{
	if ( $_SESSION['reponse'][$i]['logement_general_type_chauffage_select'] == $energie_select || 
			$_SESSION['reponse'][$i]['logement_general_type_ecs_select'] == $energie_select ) 
	{
		$index = 'emissions_' . $energie_select . '_' . $i ; 
		affiche_ligne ( $categorie , $nom_energie , $index , 'normal' ) ; 
	}
}

*/
// ============================================================================================
// Affiche une ligne du tableau de résultats
// ============================================================================================
function affiche_ligne ( $premiere_ou_pas , $categorie , $style , $intitule , $resultat )
{
	if ( $premiere_ou_pas == 'pas_1ere' )
		echo "<tr class='" . $categorie . "'>\n" ;
	//
	echo "<th class='intitule" . "_" . $style . "' scope='row' >" . $intitule 
		. "</th><td class='emission" . "_" . $style . "'>" 
		.	number_format( $resultat [EMISSION] , 1, ',', ' ') 
		. "</td><td class='emission" . "_" . $style . "'>" 
		.	number_format( ( $resultat [EMISSION] * 44 / 12 ) , 1, ',', ' ') 
		. "</td><td class='incertitude" . "_" . $style . "'>" 
		. round ( $resultat[INCERTITUDE] * 100 ) 
		. "</td>\n</tr>\n\n" ;
}
?>
