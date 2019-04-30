<?php 
//==========================================================================================
// FONCTION transfert
//==========================================================================================
function transfert_fe_vers_bdd ( $connexion ) 
{
	$fe = charger_facteurs () ;
	echo "<table>" ; 
	foreach ( $fe as $cle=>$valeur ) 
	{
		if ( strpos ( $cle , "unite" ) )
			echo "<tr><td>" . $cle . "</td><td>" . $valeur . "</td></tr>" ; 
	}
	echo "</table>" ; 
}
//==========================================================================================
// FONCTION liste des unités des facteurs d'émission
//==========================================================================================
function liste_unites_fe ( $connexion ) 
{
	echo "<table>" ; 
	$donnees_unite = exec_requete ( "SELECT * FROM t_unite" , $connexion ) ; 
	while ( $objet_unite = objet_suivant ( $donnees_unite ) )
	{
		$string = ''; 
		$donnees_numerateur_unite = exec_requete ( "SELECT * FROM t_element_unite , t_unite_fondamentale WHERE element_unite_unite_id = " . $objet_unite->unite_id . " AND element_unite_position = 'numerateur' AND element_unite_unite_fond_id = unite_fond_id " , $connexion ) ; 
		while ( $objet_numerateur_unite = objet_suivant ( $donnees_numerateur_unite ) )
			$string = $string . $objet_numerateur_unite->unite_fond_symbole . ' ' ; 
		$string = $string . 'par ' ; 
		$donnees_denominateur_unite = exec_requete ( "SELECT * FROM t_element_unite , t_unite_fondamentale WHERE element_unite_unite_id = " . $objet_unite->unite_id . " AND element_unite_position = 'denominateur' AND element_unite_unite_fond_id = unite_fond_id " , $connexion ) ; 
		while ( $objet_denominateur_unite = objet_suivant ( $donnees_denominateur_unite ) )
			$string = $string . $objet_denominateur_unite->unite_fond_symbole . ' ' ; 
		echo "<tr><td>" . $objet_unite->unite_id . "</td><td>" . $string . "</td></tr>" ; 
	}
	echo "</table>" ; 
}
?>