<?php 

//==============================================================================
// Fonction menu_unite : affiche la liste des natures d'unité
//==============================================================================
function menu_nature_unite ($connexion) {
	echo "	
			<div class=\"menu\">";
	affiche_choix_menu("admin",$connexion);

	$liste_nature_unite = exec_requete ("select * from fe_nature_unite order by nature_unite_ordre asc", $connexion);
	if (mysql_num_rows($liste_nature_unite) > 0){
		echo "
				<h1> Unités </h1>
				<ul>";
		while ($nature_unite = objet_suivant ($liste_nature_unite)) {
	
			echo "	
					<li>
						<p class=\"choix_rubrique\"><a href=\"admin.php?menu=unites&amp;nature_unite_id={$nature_unite->nature_unite_id}\" >"
						.echo_MC('NOM_NATURE_UNITE',$nature_unite->nature_unite_nom)."</a></p> ";
			if ($nature_unite->nature_unite_ordre > 1) {
			echo "	
						<form class=\"boutton\"method=\"post\" action=\"admin.php?menu=unites\">
							<p class=\"boutton\">
							<input type=\"hidden\" name=\"action_unite\" value=\"change_ordre_nature_unite\" />
							<input type=\"hidden\" name=\"ordre\" value=\"{$nature_unite->nature_unite_ordre}\" />
							<input type=\"hidden\" name=\"item_id\" value=\"{$nature_unite->nature_unite_id}\" />
							<input type=\"hidden\" name=\"position\" value=\"-1\" />
							<input class=\"fleche_verticale\" type=\"submit\" value=\"&uarr;\"/>
							</p>
						</form>";
			}
			if ($nature_unite->nature_unite_ordre < mysql_num_rows($liste_nature_unite)) {
			echo "	
						<form class=\"boutton\"method=\"post\" action=\"admin.php?menu=unites\">
							<p class=\"boutton\">
							<input type=\"hidden\" name=\"action_unite\" value=\"change_ordre_nature_unite\" />
							<input type=\"hidden\" name=\"ordre\" value=\"{$nature_unite->nature_unite_ordre}\" />
							<input type=\"hidden\" name=\"item_id\" value=\"{$nature_unite->nature_unite_id}\" />
							<input type=\"hidden\" name=\"position\" value=\"+1\" />
							<input class=\"fleche_verticale\" type=\"submit\" value=\"&darr;\"/>
							</p>
							
						</form>";
			}

		echo "
					</li>";
		}

		echo "
				</ul>";
	}
	echo "
			</div>";
}

//==============================================================================
// Fonction menu_creer_nature : affiche le menu pour créer une nouvelle nature d'unité
//==============================================================================
function menu_creer_nature_unite ($connexion) {
	echo "	
				<div class=\"Bloc\">
				<h2>".('Ajouter une nouvelle nature d\'unité')."</h2>
					<form method=\"post\" action=\"admin.php?menu=unites\" >
					<p ><input type=\"hidden\" name=\"action_unite\" value=\"ajoute_nature_unite\" /></p>
					<p>
					<label for=\"nature_unite_nom\">".('Nom : ')."</label>
					<input id=\"nature_unite_nom\" type=\"text\" size=\"30\" maxlength=\"100\" name=\"nature_unite_nom\" />
					<input id=\"radio_position_nature_unite_avant\" type=\"radio\" value=\"0\" name=\"position\"/>
					<label for=\"radio_position_nature_unite_avant\">"._('Avant')."</label>
					<input id=\"radio_position_nature_unite_apres\" type=\"radio\" checked=\"checked\" value=\"1\" name=\"position\"/>
					<label for=\"radio_position_nature_unite_apres\">"._('Après')." </label>
					<select name=\"ordre\">";
	$liste_nature_unite = exec_requete("select * from fe_nature_unite order by nature_unite_ordre asc", $connexion);
	while ($nature_unite = objet_suivant($liste_nature_unite)){
		echo	"
						<option value=\"{$nature_unite->nature_unite_ordre}\">".echo_MC('NOM_NATURE_UNITE',$nature_unite->nature_unite_nom,1)."</option>";
	}
	echo "			
					</select>
					<input type=\"submit\" value=\""._('Insérer')."\" />
					</p>
				</form>
			</div>";
}

//==============================================================================
// Fonction entete_nature_unite : affiche la possiblité de changer le nom de la nature d'unité
//==============================================================================
function entete_nature_unite ($nature_unite_id, $connexion) {
	if ($nature_unite_id <= 0) return;
	
	$liste_nature_unite = exec_requete ("select * from fe_nature_unite where nature_unite_id = {$nature_unite_id} limit 1", $connexion);
	if (! $nature_unite = objet_suivant($liste_nature_unite)) return;
	
	echo "	
			<div class=\"Bloc\">
				<h2>"._('Nature d\'unité : ').echo_MC('NOM_NATURE_UNITE',$nature_unite->nature_unite_nom)."</h2>
					<form method=\"post\" action=\"admin.php?menu=unites&amp;nature_unite_id={$nature_unite_id}\">
						<p>
						<input type=\"text\" name=\"nature_unite_nouveau_nom\" value=\"".echo_MC('NOM_NATURE_UNITE',$nature_unite->nature_unite_nom)."\" />
						<input type=\"hidden\" name=\"action_unite\" value=\"change_nom_nature_unite\" />
						<input type=\"hidden\" name=\"nature_unite_id\" value=\"{$nature_unite_id}\" />
						<input type=\"submit\" value=\""._('Changer le nom')."\" />
						</p>
					</form>
					<form method=\"post\" action=\"admin.php?menu=unites\">
						<p>
						<input type=\"hidden\" name=\"action_unite\" value=\"supprime_nature_unite\" />
						<input type=\"hidden\" name=\"nature_unite_id\" value=\"{$nature_unite_id}\" />
						<input type=\"submit\" value=\"Supprimer\" onclick=\"return(confirm('"._('Etes-vous sûr de vouloir supprimer cette nature ?')."'));\" />
						</p>
					</form>
			</div>";
}

//==============================================================================
// Fonction menu_unite : affiche le menu pour créer une nouvelle colonne
//==============================================================================
function menu_creer_unite ($nature_unite_id, $connexion) {

$liste_nature_unite = exec_requete ("select * from fe_nature_unite where nature_unite_id = {$nature_unite_id} limit 1", $connexion);
$nature_unite = objet_suivant ($liste_nature_unite);	
	echo "	
			<div class=\"Bloc\">
				<h2>"._('Ajouter une nouvelle unité dans la nature d\'unité :').echo_MC('NOM_NATURE_UNITE',$nature_unite->nature_unite_nom)."</h2>
				<form method=\"post\" action=\"admin.php?menu=unites&amp;nature_unite_id={$nature_unite_id}\">
					<p>
					<input type=\"hidden\" name=\"nature_unite_id\" value=\"{$nature_unite_id}\" />
					<input type=\"hidden\" name=\"action_unite\" value=\"ajout_unite\" />
					</p>
						<p class=\"champ_boutton\">"._('Symbole de l\'unité :')." <input type=\"text\" size=\"30\" maxlength=\"100\" name=\"nom_unite\" />
						<input id=\"radio_position_unite_avant\" type=\"radio\" value=\"0\" name=\"position\"/>
						<label for=\"radio_position_unite_avant\">"._('Avant')."</label>
						<input id=\"radio_position_unite_apres\" type=\"radio\" checked=\"checked\" value=\"1\" name=\"position\"/>
						<label for=\"radio_position_unite_apres\">"._('Après')." </label>
						<select name=\"ordre\">";
	$liste_unite = exec_requete("select * from fe_unite_fondamentale where unite_fond_nature_unite_id={$nature_unite_id} order by unite_fond_ordre asc", $connexion);
	while ($unite = objet_suivant($liste_unite)) {
		echo "
							<option value=\"{$unite->unite_fond_ordre}\">{$unite->unite_fond_symbole}</option>";
	}
	echo "				
						</select>
						<input type=\"submit\" value=\""._('Insérer')."\" />
						</p>
				</form>
			</div>";
}

//==============================================================================
// Fonction afficheTableModifOrdre : affiche la liste des unités pour une nature d'unité donnée où l'on peut changer leur ordre ou les supprimer
//==============================================================================
function afficheTableModifOrdre ($nature_unite_id, $connexion) {
	$liste_unite = exec_requete("select * from fe_unite_fondamentale where unite_fond_nature_unite_id = {$nature_unite_id} order by unite_fond_ordre asc", $connexion);
	if (mysql_num_rows($liste_unite)!=0){
	echo "
			<div class=\"Bloc\">
			<h2>"._("Modifier l'odre des unités")."</h2>
			<table class=\"table_ordre_unite\" border=\"1\">";
	while ($unite = objet_suivant($liste_unite)) {
		echo "	
			<tr>
				<td class=\"symbole\">
					{$unite->unite_fond_symbole}
				</td>
				<td class=\"option\">";
		if ($unite->unite_fond_ordre > 1)
			echo "		
					<form class=\"boutton\" method=\"post\" action=\"admin.php?menu=unites&amp;nature_unite_id={$nature_unite_id}\">
						<p class=\"boutton\">
						<input type=\"hidden\" name=\"action_unite\" value=\"change_ordre_unite\" />
						<input type=\"hidden\" name=\"ordre\" value=\"{$unite->unite_fond_ordre}\" />
						<input type=\"hidden\" name=\"nature_unite_id\" value=\"{$nature_unite_id}\" />
						<input type=\"hidden\" name=\"unite_fond_id\" value=\"{$unite->unite_fond_id}\" />
						<input type=\"hidden\" name=\"position\" value=\"-1\" />
						<input class=\"fleche_verticale\" type=\"submit\" value=\"&uarr;\"/>
						</p>
					</form>";
		if ($unite->unite_fond_ordre < mysql_num_rows($liste_unite)) 
			echo "		
					<form class=\"boutton\" method=\"post\" action=\"admin.php?menu=unites&amp;nature_unite_id={$nature_unite_id}\">
						<p class=\"boutton\">
						<input type=\"hidden\" name=\"action_unite\" value=\"change_ordre_unite\" />
						<input type=\"hidden\" name=\"ordre\" value=\"{$unite->unite_fond_ordre}\" />
						<input type=\"hidden\" name=\"nature_unite_id\" value=\"{$nature_unite_id}\" />
						<input type=\"hidden\" name=\"unite_fond_id\" value=\"{$unite->unite_fond_id}\"/>
						<input type=\"hidden\" name=\"position\" value=\"+1\" />
						<input class=\"fleche_verticale\" type=\"submit\" value=\"&darr;\"/>
						</p>
					</form>";
			echo "
				</td>
				<td class=\"option\">		
					<form class=\"boutton\" method=\"post\" action=\"admin.php?menu=unites&amp;nature_unite_id={$nature_unite_id}\" >
						<p class=\"boutton\">
						<input type=\"hidden\" name=\"action_unite\" value=\"supprime_unite\" />
						<input type=\"hidden\" name=\"unite_fond_id\" value=\"{$unite->unite_fond_id}\" />
						<input type=\"submit\" value=\""._('Supprimer')."\" onclick=\"return(confirm('"._('Etes-vous sûr de vouloir supprimer cette unité ?')."'));\" />
						</p>
					</form>
				</td>
			</tr>";	
	}
	echo "
			</table>
		</div>";
	}
}

//==============================================================================
// Fonction afficheTableConversion : affiche la table de conversion entre les unités d'une même nature, permet également de changer les noms des unités
//==============================================================================
function afficheTableConversion ($nature_unite_id, $connexion) {
	$liste_nature_unite = exec_requete("select * from fe_nature_unite where nature_unite_id = {$nature_unite_id}", $connexion);
	$nature_unite=objet_suivant($liste_nature_unite);
	
	if ($nature_unite->nature_unite_type!="discret"){
	$liste_unite = exec_requete("select * from fe_unite_fondamentale where unite_fond_nature_unite_id = {$nature_unite_id} order by unite_fond_ordre asc", $connexion);
	$nombre_unite = mysql_num_rows($liste_unite);
	if ($nombre_unite != 0){
	echo "
			
			<div class=\"Bloc\">
			<p>"._('Note : les unités sont liées par : [unité sur la ligne] = [unité sur la colonne] * [coefficient]')."</p>
				<form method=\"post\" action=\"admin.php?menu=unites&amp;nature_unite_id={$nature_unite_id}\">
					<table class=\"table_conversion_unite\" border=\"1\">
					<thead><tr>
						<td>&nbsp;</td>"; // La première case du tableau ne contient aucune donnée.
	
		while ($unites[] = objet_suivant($liste_unite)) {
			 $temp = count($unites)-1;
			// On affiche les unités de la nature demandée
			echo "		
						<td class=\"symbole\">".echo_MC('NOM_UNITE',$unites[$temp]->unite_fond_symbole)."</td>";
		}
		echo "
					</tr></thead>
					<tbody>";
		for ($i=0 ; $i<$nombre_unite ; $i++) {
			echo "	
					<tr>
						<td class=\"symbole\"><input type=\"text\" name=\"symbole_unite_{$i}\" value=\"".echo_MC('NOM_UNITE',$unites[$i]->unite_fond_symbole)."\" /></td>";
			for ($j=0 ; $j<$nombre_unite ; $j++) {
				echo "
						<td>";
				if ($i == ($j+1)) {
					
					echo "<input class=\"coefficient\" type=\"text\" name=\"coef_{$i}_{$j}\" value=\"" . (coefficient($unites[$i]->unite_fond_id, $unites[$j]->unite_fond_id, $connexion)) . "\"/>";
				}
				else {
					echo "<p>".to_X_chif_signi(coefficient($unites[$i]->unite_fond_id, $unites[$j]->unite_fond_id, $connexion),6)."</p>";
				}
				echo "</td>";
			}
			echo "
					</tr>";
		}
		
	echo "
					</tbody>
					</table>";
	mysql_data_seek($liste_unite, 0);
	
	$temp=0;
	while ($unites[] = objet_suivant($liste_unite)) {
	
	
	echo "	
				<p><input type=\"hidden\" name=\"unite_fin_".$temp."\" value=\"{$unites[$temp]->unite_fond_id}\"/></p>";	
		$temp++;
	}

	for ($i=0 ; $i<$nombre_unite ; $i++) {
		echo "	
					<p><input type=\"hidden\" name=\"unite_depart_{$i}\" value=\"{$unites[$i]->unite_fond_id}\"/></p>";
	}
	echo "	
					<p>
					<label for=\"tab_commentaire\"> Commentaire de la table </label>
					<textarea id=\"tab_commentaire\" cols=\"70\" rows=\"3\" name=\"tab_commentaire\">".(echo_MC('COMMENTAIRE',$nature_unite->nature_unite_commentaire))."</textarea>
					</p>
					<p>
					<input type=\"hidden\" name=\"nature_unite_id\" value=\"{$nature_unite->nature_unite_id}\" />
					<input type=\"hidden\" name=\"nombre_unite\" value=\"{$nombre_unite}\" />
					<input type=\"hidden\" name=\"action_unite\" value=\""._('edit_coefficient')."\" />
					<input type=\"submit\" value=\""._('Editer')."\" />
					</p>
				</form>
			</div>";
	}
	
}
}

//==============================================================================
// Fonction coefficient : retourne le coefficient de conversion entre les 2 unités passées en paramètres
//==============================================================================
function coefficient ($unite_fond_depart_id, $unites_fond_fin_id, $connexion) {
	$liste_coefficient = exec_requete ("select * from fe_unite_conversion where unite_fond_depart_id = ". intval($unite_fond_depart_id)." and unite_fond_fin_id = ".intval($unites_fond_fin_id)." limit 1", $connexion);
	if ($coefficient = objet_suivant($liste_coefficient)) {
		return number_format($coefficient->coefficient,9,'.','');
	}
	else return "";
}

//==============================================================================
// Fonction action_unite : exécute les action_unites spécifiées
//==============================================================================
function action_unite($connexion) {
	if ($_POST["action_unite"] == "ajoute_nature_unite") creation_nature_unite ($connexion);
	if ($_POST["action_unite"] == "supprime_nature_unite") suppression_nature_unite(intval($_POST["nature_unite_id"]), $connexion);
	if ($_POST["action_unite"] == "change_nom_nature_unite") exec_requete ("update fe_nature_unite set nature_unite_nom = \"".secure($_POST["nature_unite_nouveau_nom"])."\" where nature_unite_id = ".intval($_POST["nature_unite_id"])."", $connexion);
	if ($_POST["action_unite"] == "edit_coefficient") edit_coefficient ($connexion);
	if ($_POST["action_unite"] == "ajout_unite") creation_unite ($connexion);
	if ($_POST["action_unite"] == "supprime_unite") suppression_unite (intval($_POST["unite_fond_id"]), $connexion);
	if ($_POST["action_unite"] == "change_ordre_nature_unite") edit_ordre_nature_unite ($connexion);
	if ($_POST["action_unite"] == "change_ordre_unite") edit_ordre_unite ($connexion);
}

//==============================================================================
// Fonction creation_nature_unite : crée la nature d'unité dans la base de données
//==============================================================================
function creation_nature_unite ($connexion) {
	$objet = exec_requete("select max(nature_unite_id) max from fe_nature_unite", $connexion);
	$nature_unite = objet_suivant($objet);
	if (isset($nature_unite->max) && $nature_unite->max>0) $nature_unite_id = $nature_unite->max;
	else $nature_unite_id = 0;
	$nature_unite_id++;
	if (isset($_POST["ordre"])) $ordre = $_POST["ordre"] + $_POST["position"];
	else $ordre = 1;

	exec_requete("update fe_nature_unite set nature_unite_ordre = nature_unite_ordre+1 where nature_unite_ordre >= {$ordre}",$connexion);
	exec_requete("insert into `fe_nature_unite` (nature_unite_id, nature_unite_ordre, nature_unite_nom) values (".intval($nature_unite_id).", {$ordre}, \"".secure($_POST["nature_unite_nom"])."\")", $connexion);
}

//==============================================================================
// Fonction suprression_nature_unite : supprime la nature d'unité de la base de données et les unités qui en découlent
//==============================================================================
function suppression_nature_unite ($nature_unite_id, $connexion) {

	$liste_nature_unite = exec_requete ("select * from fe_nature_unite where nature_unite_id = {$nature_unite_id}", $connexion);
	if ($nature_unite = objet_suivant ($liste_nature_unite)){
		exec_requete ("update fe_nature_unite set nature_unite_ordre = nature_unite_ordre-1 where nature_unite_ordre > {$nature_unite->nature_unite_ordre}", $connexion);
		exec_requete ("delete from fe_nature_unite where nature_unite_id = {$nature_unite_id} limit 1", $connexion);
		
		$liste_unite = exec_requete ("select * from fe_unite_fondamentale where unite_fond_nature_unite_id = {$nature_unite_id}", $connexion);
		while ($unite = objet_suivant ($liste_unite)) {
			suppression_unite ($unite->unite_fond_id, $connexion);
		}
	}
}

//==============================================================================
// Fonction creation_unite : crée l'unité dans la base de données
//==============================================================================
function creation_unite ($connexion) {
	$liste_unite = exec_requete("select max(unite_fond_id) max from fe_unite_fondamentale", $connexion);
	if ($unite = objet_suivant($liste_unite)) $max_unite_id = $unite->max;
	else $max_unite_id = 0;
	
	if (isset($_POST["ordre"])) $ordre = $_POST["ordre"] + $_POST["position"];
	else $ordre = 1;
	
	$liste_unite = exec_requete ("select * from fe_unite_fondamentale where unite_fond_nature_unite_id = ".intval($_POST["nature_unite_id"])." order by unite_fond_ordre asc", $connexion);
	while ($unites[] = objet_suivant ($liste_unite)){}
	
	exec_requete("update fe_unite_fondamentale set unite_fond_ordre = unite_fond_ordre+1 where unite_fond_nature_unite_id = ".intval($_POST["nature_unite_id"])."  and unite_fond_ordre >= {$ordre}", $connexion);
	
	$max_unite_id++;
	exec_requete ("insert into fe_unite_fondamentale (unite_fond_id, unite_fond_symbole, unite_fond_nature_unite_id, unite_fond_ordre) values ({$max_unite_id}, \"".secure($_POST["nom_unite"])."\", ".intval($_POST["nature_unite_id"])." , {$ordre})", $connexion);

	for ($i=0 ; $i<mysql_num_rows($liste_unite) ; $i++) {
		exec_requete ("insert into fe_unite_conversion (unite_fond_depart_id, unite_fond_fin_id) values ({$unites[$i]->unite_fond_id}, {$max_unite_id})", $connexion);
		exec_requete ("insert into fe_unite_conversion (unite_fond_depart_id, unite_fond_fin_id) values ({$max_unite_id}, {$unites[$i]->unite_fond_id})", $connexion);
	}
	exec_requete ("insert into fe_unite_conversion (unite_fond_depart_id, unite_fond_fin_id, coefficient) values ({$max_unite_id}, {$max_unite_id}, 1)", $connexion);
}

//==============================================================================
// Fonction suppression_unite : supprime l'unité dans la base de données
//==============================================================================
function suppression_unite ($unite_fond_id, $connexion) {
	$liste_unite = exec_requete ("select * from fe_unite_fondamentale where unite_fond_id = {$unite_fond_id} limit 1", $connexion);
	if ($unite = objet_suivant($liste_unite)) {
		exec_requete ("update fe_unite_fondamentale set unite_fond_ordre = unite_fond_ordre-1 where unite_fond_nature_unite_id = {$unite->unite_fond_nature_unite_id} and unite_fond_ordre > {$unite->unite_fond_ordre}", $connexion);
		exec_requete ("delete from fe_unite_fondamentale where unite_fond_id = {$unite->unite_fond_id} limit 1", $connexion);
	}
	exec_requete ("delete from fe_unite_conversion where unite_fond_depart_id = {$unite_fond_id} or unite_fond_fin_id = {$unite_fond_id}", $connexion);
}

//==============================================================================
// Fonction edit_ordre_unite : édite le nouvel ordre d'affichage des unités au sein d'une nature d'unité
//==============================================================================
function edit_ordre_unite ($connexion) {
	exec_requete ("update fe_unite_fondamentale set unite_fond_ordre = unite_fond_ordre + " . $_POST["position"] . " where unite_fond_ordre = " . $_POST["ordre"] . " and unite_fond_id = " . $_POST["unite_fond_id"]. " and unite_fond_nature_unite_id = " . $_POST["nature_unite_id"], $connexion);
	exec_requete ("update fe_unite_fondamentale set unite_fond_ordre = unite_fond_ordre - " . $_POST["position"] . " where unite_fond_ordre = " . $_POST["ordre"] . $_POST["position"] . " and unite_fond_id != " . $_POST["unite_fond_id"]. " and unite_fond_nature_unite_id = " . $_POST["nature_unite_id"], $connexion);
}

//==============================================================================
// Fonction edit_ordre_nature_unite : édite le nouvel ordre d'affichage des unités au sein d'une nature d'unité
//==============================================================================
function edit_ordre_nature_unite ($connexion) {
	exec_requete ("update fe_nature_unite set nature_unite_ordre = nature_unite_ordre + " . $_POST["position"] . " where nature_unite_ordre = " . $_POST["ordre"] . " and nature_unite_id = " . $_POST["item_id"], $connexion);
	exec_requete ("update fe_nature_unite set nature_unite_ordre = nature_unite_ordre - " . $_POST["position"] . " where nature_unite_ordre = " . $_POST["ordre"] . $_POST["position"] . " and nature_unite_id != " . $_POST["item_id"], $connexion);
}
//==============================================================================
// Fonction complete_case : complète le tableau des conversions
//==============================================================================
function complete_case_haut ($i,$j,$coef,$connexion) {
	if  (!isset($coef[$i.($j-1)]) || !isset($coef[($j-1).$j])){
		$coef[$i.($j-1)]=complete_case_haut($i,$j-1,$coef,$connexion);
		$coef[($j-1).$j]=complete_case_haut($j-1,$j,$coef,$connexion);
	}
	$c =  $coef[$i.($j-1)]*$coef[($j-1).$j];
		if ( $coef[$i.($j-1)]*$coef[($j-1).$j] ==0){
			$c='NULL';
		}

	exec_requete ("update fe_unite_conversion set coefficient=" . $c . " where unite_fond_depart_id=" . intval($_POST["unite_depart_".($i)]) ." and unite_fond_fin_id=" . intval($_POST["unite_fin_".($j)]) . " limit 1", $connexion);
	return ($coef[$i.($j-1)]*$coef[($j-1).$j]);
}

//==============================================================================
// Fonction complete_case : complète le tableau des conversions
//==============================================================================
function complete_case_bas ($i,$j,$coef,$connexion) {
	if (!isset($coef[$i.$j])){
		if  (!isset($coef[$i.($j-1)]) || !isset($coef[($i-1).$j])){
			$coef[($i).($j+1)]=complete_case_bas($i,$j+1,$coef,$connexion);
			$coef[($i-1).$j]=complete_case_bas($i-1,$j,$coef,$connexion);
		}
		$c = $coef[($i).($i-1)]*$coef[($i-1).$j] ;
		if ($coef[($i).($i-1)]*$coef[($i-1).$j]==0){
			$c='NULL';
		}

		exec_requete ("update fe_unite_conversion set coefficient=" . $c . " where unite_fond_depart_id=" . intval($_POST["unite_depart_".($i)]) ." and unite_fond_fin_id=" . intval($_POST["unite_fin_".($j)]) . " limit 1", $connexion);
		return $c;
	}
	return $coef[$i.$j];
}
//==============================================================================
// Fonction edit_coefficient : édite les coefficients entre les unités d'une même nature d'unité
//==============================================================================
function edit_coefficient ($connexion) {
	$coef=array();

	for ($i=0 ; $i < $_POST["nombre_unite"] ; $i++) {
		$coef[$i.$i]=1;
		exec_requete ("update fe_unite_fondamentale set unite_fond_symbole='" .secure($_POST["symbole_unite_{$i}"]) . "' where unite_fond_id=" . $_POST["unite_depart_{$i}"], $connexion);
		
		for ($j=$_POST["nombre_unite"]-1 ; $j >= 0 ; $j--) {
			if (isset($_POST["coef_{$i}_{$j}"])) {
				if ($_POST["coef_{$i}_{$j}"] != '') {
					exec_requete ("update fe_unite_conversion set coefficient=" . strval(floatval($_POST["coef_{$i}_{$j}"])) . " where unite_fond_depart_id=" . intval($_POST["unite_depart_{$i}"]) ." and unite_fond_fin_id=" . intval($_POST["unite_fin_{$j}"]) . " limit 1", $connexion); 
					$coef[$i.$j]= strval(floatval($_POST["coef_{$i}_{$j}"]));
					$temp='NULL';
					if ($_POST["coef_{$i}_{$j}"]!=0){
						$temp = (double) (1/$_POST["coef_{$i}_{$j}"]);
						if ($temp==0){
							$temp='NULL';
						}
					}
					exec_requete ("update fe_unite_conversion set coefficient=" .$temp. " where unite_fond_depart_id=" . intval($_POST["unite_depart_{$j}"]) ." and unite_fond_fin_id=" . intval($_POST["unite_fin_{$i}"]) . " limit 1", $connexion);
					$coef[$j.$i]= $temp;
					
				}
				else {
	
					exec_requete ("update fe_unite_conversion set coefficient=NULL where unite_fond_depart_id=" . intval($_POST["unite_depart_{$i}"]) ." and unite_fond_fin_id=" . intval($_POST["unite_fin_{$j}"]) . " limit 1", $connexion);
					$coef[$i.$j] = 0;
					exec_requete ("update fe_unite_conversion set coefficient=NULL where unite_fond_depart_id=" . intval($_POST["unite_depart_{$j}"]) ." and unite_fond_fin_id=" . intval($_POST["unite_fin_{$i}"]) . " limit 1", $connexion);
					$coef[$j.$i] = 0;
				}
			
				
			}
		}
	}

	if ($_POST["nombre_unite"]>2){
		for ($i=0;$i<$_POST["nombre_unite"]-1;$i++){

			complete_case_haut($i,$_POST["nombre_unite"]-1,$coef,$connexion);
			complete_case_bas($_POST["nombre_unite"]-1-$i,0,$coef,$connexion);
		}
	}
	$coef=array();
	
	/* On met à jour le commentaire de la table */
	if ($_POST["tab_commentaire"]==NULL){
	exec_requete ("update fe_nature_unite set nature_unite_commentaire=NULL where nature_unite_id=".intval($_POST["nature_unite_id"]), $connexion);
	}
	else {
		exec_requete ("update fe_nature_unite set nature_unite_commentaire=\"".secure($_POST["tab_commentaire"])."\" where nature_unite_id=".intval($_POST["nature_unite_id"]), $connexion);
	}
}

?>
