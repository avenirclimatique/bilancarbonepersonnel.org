<?php 
//==========================================================================================
// mettre à jour la liste des pages
//==========================================================================================
function mettre_a_jour_liste_page ( $connexion ) 
{
	$liste_page = array () ; 
	$donnees_rubrique = exec_requete ( "SELECT rub_nom , rub_est_repetee , rub_id FROM t_rubrique ORDER BY rub_ordre" , $connexion ) ; 
	while ( $objet_rubrique = objet_suivant ( $donnees_rubrique ) ) 
		if ( $objet_rubrique->rub_est_repetee == true ) 
			for ( $i=1 ; $i <= $_SESSION[MENU_NOMBRE][$objet_rubrique->rub_nom] ; $i++ ) 
				$liste_page = mettre_a_jour_liste_page_rubrique ( $liste_page , $objet_rubrique->rub_id , $i , $connexion ) ; 
		else
			mettre_a_jour_liste_page_rubrique ( $liste_page , $objet_rubrique->rub_id , false , $connexion ) ; 	}
	return $liste_page ; 
}
//==========================================================================================
// mettre à jour la liste des pages d'une rubrique
//==========================================================================================
function mettre_a_jour_liste_page_rubrique ( $liste_page , $rub_id , $rub_numero , $connexion )
{
	$donnees_page = exec_requete ( "SELECT page_nom FROM t_page WHERE page_rub_id = $rub_id ORDER BY page_ordre" , $connexion ) ; 
	while ( $objet_page = objet_suivant ( $donnees_page ) )
		if ( $rub_numero ) 
			$liste_page[] = $objet_page->page_nom . "%" . $rub_numero ; 
		else
			$liste_page[] = $objet_page->page_nom ; 
	return $liste_page ; 
}
?>
