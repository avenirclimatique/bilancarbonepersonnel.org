<?php 


//==============================================================================
// Fonction menu_unite : affiche la liste des natures d'unité
//==============================================================================
function menu_nature_unite ($connexion) {
	echo "	
		<ul id=\"menu_facteur_emission\">
						<li>
							<p class=\"choix_menu\"><a href=\"index.php?type_page=consultation_base&amp;page=".FACTEURS_EMISSION."&amp;menu=facteur_emission\">"._("Données")."</a></p>
							<p class=\"choix_menu\"><a href=\"index.php?type_page=consultation_base&amp;page=".FACTEURS_EMISSION."&amp;menu=unites\">"._('Unités')."</a></p>
							<div class=\"retour_ligne\">&nbsp;</div>
						</li>";
	$liste_nature_unite = exec_requete ("select * from fe_nature_unite where nature_unite_type!='discret' order by nature_unite_ordre asc", $connexion);
	if (mysql_num_rows($liste_nature_unite) > 0){
		echo "
			<li class=\"titre_menu_consultation\"><h1>Unités</h1></li>";
		while ($nature_unite = objet_suivant ($liste_nature_unite)) {
			echo "	
				<li  class=\"titre_unite\">	
					<a class=\"nom\" href=\"index.php?type_page=consultation_base&amp;page=".FACTEURS_EMISSION."&amp;menu=unites&amp;nature_unite_id={$nature_unite->nature_unite_id}\" >".echo_MC('NOM_NATURE_UNITE',$nature_unite->nature_unite_nom)."</a>";
			echo "
				</li>";
		}
	}
	echo "
		</ul>";
}


//==============================================================================
// Fonction afficheTableConversion : affiche la table de conversion entre les unités d'une même nature, permet également de changer les noms des unités
//==============================================================================
function afficheTableConversion ($nature_unite_id, $connexion) {

	$liste_nature_unite = exec_requete("select * from fe_nature_unite where nature_unite_id = {$nature_unite_id}", $connexion);
	$nature_unite=objet_suivant($liste_nature_unite);
	if ($nature_unite->nature_unite_type=="discret"){
	}
	else{
	echo "
			<p>"._('Note : les unités sont liées par : [unité sur la ligne] = [unité sur la colonne] * [coefficient]')."</p>";
	$liste_unite = exec_requete("select * from fe_unite_fondamentale where unite_fond_nature_unite_id = {$nature_unite_id} order by unite_fond_ordre asc", $connexion);
	$nombre_unite = mysql_num_rows($liste_unite);

	$liste_nature_unite = exec_requete ("select * from fe_nature_unite where nature_unite_id = {$nature_unite_id} limit 1", $connexion);
	if (! $nature_unite->nature_unite_id = objet_suivant($liste_nature_unite)) return;
	
	echo "	
			<div class=\"Bloc_tableau\">
				<h2>".echo_MC('NOM_NATURE_UNITE',$nature_unite->nature_unite_nom)."</h2>
				<form method=\"post\" action=\"public.php?menu=unites&amp;nature_unite_id={$nature_unite_id}\">
				<table class=\"table_conversion_unite\" border=\"1\">
				<thead><tr>
					<td>&nbsp;</td>";
	if ($nombre_unite != 0){
		while ($unites[] = objet_suivant($liste_unite)) {
			 $temp = count($unites)-1;

			echo "	
					<td class=\"symbole\">".echo_MC('NOM_UNITE',$unites[$temp]->unite_fond_symbole)."</td>";
		}
		echo "
				</tr></thead>
				<tbody>";
		
		for ($i=0 ; $i<$nombre_unite ; $i++) {
			echo "	
				<tr>
					<td class=\"symbole\">".echo_MC('NOM_UNITE',$unites[$i]->unite_fond_symbole)."</td>";
			for ($j=0 ; $j<$nombre_unite ; $j++) {
				echo "
					<td>".to_X_chif_signi(coefficient($unites[$i]->unite_fond_id, $unites[$j]->unite_fond_id, $connexion),6)."</td>";
			}
			echo "
				</tr>";
		}
	}	
	echo "
				</tbody>
				</table>";
	if (echo_MC("NOM_NATURE_UNITE",$nature_unite->nature_unite_commentaire)!= ""){
		echo "
				<p>".echo_MC("NOM_NATURE_UNITE",$nature_unite->nature_unite_commentaire)."</p>";
	}

	echo "	
			</form>
			</div>";
}
}

?>
