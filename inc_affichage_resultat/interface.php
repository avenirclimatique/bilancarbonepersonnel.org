<?php 



function nouvelleForme($resultat) {
	$newTab = array();
	$listeEnergies =  liste_energie ();
	foreach($resultat as $nomRub => $rubTab) {
		foreach ($rubTab as $nomPost => $postVal) {
			$newTab[$nomRub][$nomPost] = $postVal;
			if (ereg ("(.*)_([[:digit:]])", $nomRub, $regs) && in_array  ( $nomPost , $listeEnergies )) {
				$tableau_energies_des_usages = tableau_energies_des_usages ( $regs[2] );
				$newTab[$nomRub][$nomPost]['affiche'] = affiche_energie ( $nomPost , $tableau_energies_des_usages );
			} else {
				$newTab[$nomRub][$nomPost]['affiche'] = true;
			}	
		}
	}
	return $newTab;
}
?>
