<?php 

//==============================================================================
// Fonction menu_rubrique : affiche l'arborescence des rubriques
//==============================================================================
function menu_rubrique ($connexion, $rubrique_id=-1) {
	if ($rubrique_id == -1){
	echo "	
		<div class=\"menu\">";
	affiche_choix_menu("admin",$connexion); // Affiche le choix du menu (Données, Unités..)
	echo "	
			<h1> Données </h1>
			<ul>
				<li>
					<a href=\"admin.php?menu=facteur_emission&amp;rubrique_id=-1\"> "._('Racine')."</a>
				</li>";
	}
	$liste_rubrique = exec_requete ("select * from fe_rubrique where rub_prec_id = {$rubrique_id} order by rub_ordre asc", $connexion);
	
	if (($nb_Rubrique=mysql_num_rows($liste_rubrique)) > 0){
		$i=0;
		while ($rubrique = objet_suivant ($liste_rubrique)) {

			echo "	
				<li>
					<p class=\"choix_rubrique\">";
			$i++;
			if ($rubrique->rub_ordre == 1 && $rubrique->rub_ordre == mysql_num_rows($liste_rubrique))echo"<a href=\"admin.php?menu=facteur_emission&amp;rubrique_id={$rubrique->rub_id}\" >".echo_MC('NOM_RUBRIQUE',$rubrique->rub_nom)."</a>";
			else echo "	<a href=\"admin.php?menu=facteur_emission&amp;rubrique_id={$rubrique->rub_id}\" >".echo_MC('NOM_RUBRIQUE',$rubrique->rub_nom)."</a>";
			echo "</p>";

			if ($rubrique->rub_ordre > 1) {
				formulaire_fleche("rubrique",$rubrique,"haut");
			}
			if ($rubrique->rub_ordre < mysql_num_rows($liste_rubrique)) {
				formulaire_fleche("rubrique",$rubrique,"bas");
			}

			$liste_rubrique2 = exec_requete ("select * from fe_rubrique where rub_prec_id = {$rubrique->rub_id} order by rub_ordre asc", $connexion);
			if (mysql_num_rows($liste_rubrique2) > 0){
				echo "
				<ul>\n";
				menu_rubrique ($connexion, $rubrique->rub_id);
				echo "
				</ul>";
			}
			echo "
				</li>";
		}
	}

	if ($rubrique_id == -1) {
		echo "
			</ul>";
		choixChiffresSignificatifs ();
		echo "
		</div>\n";
		
	}
}

//==============================================================================
// Fonction entete_rubrique : affiche le chemin, le nom et diverses options de la rubrique courante
//==============================================================================
function entete_rubrique ($rubrique_id, $connexion) {

	if ($rubrique_id <= 0) return;
	
	echo "	
			<div class=\"Bloc\">
				<h2>".arborescence_rubrique ($rubrique_id, $connexion)."</h2>";
	
	$liste_rubrique = exec_requete ("select * from fe_rubrique where rub_id = {$rubrique_id}", $connexion);
	if ($rubrique = objet_suivant ($liste_rubrique)) {
		echo "
					<form method=\"post\" action=\"admin.php?menu=facteur_emission&amp;rubrique_id={$rubrique_id}\">
						<p>
						<input type=\"text\" name=\"rubrique_nouveau_nom\" value=\"".echo_MC('NOM_RUBRIQUE',$rubrique->rub_nom)."\" />
						<input type=\"hidden\" name=\"action_facteur_emission\" value=\"change_nom_rubrique\" />
						<input type=\"hidden\" name=\"rubrique_id\" value=\"{$rubrique_id}\" />
						<input type=\"submit\" value=\""._('Changer le nom')."\" />
						</p>
					</form>
					<form method=\"post\" action=\"admin.php?menu=facteur_emission&amp;rubrique_id={$rubrique_id}\">
						<p>"._('Déplacer dans la rubrique :')."
						<select name=\"rubrique_prec_id\">
							<option value=\"-1\">"._('Racine')."</option>";
							liste_deroulante_rubrique (-1, 1, $connexion, $rubrique_id);
		echo "
						</select>
						<input type=\"hidden\" name=\"action_facteur_emission\" value=\"deplacer_rubrique\" />
						<input type=\"hidden\" name=\"rubrique_id\" value=\"{$rubrique_id}\"/>
						<input type=\"submit\" value=\""._('Déplacer vers')."\" />
						</p>
					</form>

					<form method=\"post\" action=\"admin.php?menu=facteur_emission&amp;rubrique_id={$rubrique->rub_prec_id}\" onclick=\"return(confirm('Etes-vous sûr de vouloir supprimer cette rubrique?'));\">
						<p>
						<input type=\"hidden\" name=\"action_facteur_emission\" value=\"supprime_rubrique\" />
						<input type=\"hidden\" name=\"rubrique_id\" value=\"{$rubrique_id}\" />
						<input type=\"submit\" value=\""._('Supprimer')."\" />
						</p>
					</form>
			</div>";

	}
}

//==============================================================================
// Fonction liste_deroulante_rubrique : rempli une liste dï¿½roulante de rubriques
//==============================================================================
function liste_deroulante_rubrique ($rubrique_prec_id, $niveau, $connexion, $rubrique_ex=-1) {
	global $TEXT;
	$liste_rubrique = exec_requete ("select * from fe_rubrique where rub_prec_id = {$rubrique_prec_id} order by rub_ordre asc", $connexion);
	while ($rubrique = objet_suivant ($liste_rubrique)) {
		if ($rubrique->rub_id != $rubrique_ex) {
			for ($tabulation="", $i=1 ; $i < $niveau ; $i++, $tabulation .= "&nbsp;&nbsp;&nbsp;&nbsp;" );
			echo "
								<option value=\"{$rubrique->rub_id}\">".$tabulation.echo_MC('NOM_RUBRIQUE',$rubrique->rub_nom,1)."</option>";
			liste_deroulante_rubrique ($rubrique->rub_id, $niveau+1, $connexion, $rubrique_ex);
		}
	}
}



//==============================================================================
// Fonction menu_creer_rubrique : affiche le menu pour crï¿½er une nouvelle rubrique
//==============================================================================
function menu_creer_rubrique ($rubrique_id, $connexion) {
	
	echo "	
			<div class=\"Bloc\">
				<h2>"._('Ajouter une nouvelle rubrique');
					$objet = exec_requete ("select * from fe_rubrique where rub_id = {$rubrique_id} limit 1", $connexion);
					$rubrique = objet_suivant ($objet);
					if (! $rubrique != -1) echo _(' dans la [sous] rubrique :&nbsp;').echo_MC('NOM_RUBRIQUE',$rubrique->rub_nom);
	echo " 		</h2>
				<form method=\"post\" action=\"admin.php?menu=facteur_emission&amp;rubrique_id={$rubrique_id}\" >
					<p>
					<input type=\"hidden\" name=\"rub_prec_id\" value=\"{$rubrique_id}\" />
					<input type=\"hidden\" name=\"action_facteur_emission\" value=\"ajoute_rubrique\" />
					</p>
					<p>
					<label for=\"rub_nom\">"._('Nom : ')."</label>
					<input id=\"rub_nom\" type=\"text\" size=\"20\" maxlength=\"255\" name=\"rub_nom\" />";
					
			$liste_rubrique = exec_requete("select * from fe_rubrique where rub_prec_id={$rubrique_id} order by rub_ordre asc", $connexion);
				if (mysql_num_rows($liste_rubrique)>0){			
					echo "
					<input id=\"radio_position_rubrique_avant\" type=\"radio\" value=\"0\" name=\"position\"/>
					<label for=\"radio_position_rubrique_avant\">"._('Avant')."</label>
					<input id=\"radio_position_rubrique_apres\" type=\"radio\" checked=\"checked\" value=\"1\" name=\"position\"/>
					<label for=\"radio_position_rubrique_apres\">"._('Après')." </label>
					<select id=\"ordre\" name=\"ordre\">";
	
					while ($rubrique = objet_suivant($liste_rubrique)) {
						echo "		
					<option value=\"{$rubrique->rub_ordre}\">".echo_MC('NOM_RUBRIQUE',$rubrique->rub_nom,1)."</option>";
					}
					echo "			
					</select>";
				}
				echo "		
					<input type=\"submit\" value=\""._('Insérer')."\" />
					</p>
				</form>
			</div>";
}


//==============================================================================
// Fonction Rubrique : Affiche tous les tableaux d'une rubrique
//==============================================================================
function afficheTableauRubrique ($rubrique_id, $connexion) {
	
	$liste_tableau = exec_requete ("select * from fe_table where rub_id = {$rubrique_id} order by tab_ordre asc", $connexion);
	while ($tableau = objet_suivant ($liste_tableau)) {
		afficheTableauAdmin ($tableau, "consultation_admin", $connexion);
	}
}

//==============================================================================
// Fonction menu_creer_table : affiche le menu pour crï¿½er un nouveau tableau
//==============================================================================
function menu_creer_table ($rubrique_id, $connexion) {

	echo "	
			<div class=\"Bloc\">
				<h2>";
				$objet = exec_requete ("select * from fe_rubrique where rub_id = {$rubrique_id} limit 1", $connexion);
				$rubrique = objet_suivant ($objet);
	echo 	_('Ajouter une nouvelle table dans la [sous] rubrique :&nbsp;').echo_MC('NOM_RUBRIQUE',$rubrique->rub_nom)."</h2>
				<form method=\"post\" action=\"admin.php?menu=facteur_emission&amp;rubrique_id={$rubrique_id}\">
					<p>
					<input type=\"hidden\" name=\"rubrique_id\" value=\"{$rubrique_id}\" />
					<input type=\"hidden\" name=\"action_facteur_emission\" value=\"ajoute_table\" />
					</p>
					
					<p>
					<label for=\"tab_nom\">"._('Nom : ')."</label>
					<input type=\"text\" size=\"20\" maxlength=\"255\" name=\"tab_nom\" id=\"tab_nom\"/>


					<label for=\"tab_nom\">"._('Nombre de lignes : ')."</label>
					<input type=\"text\" size=\"2\" name=\"nombre_lignes\" id=\"nombres_lignes\" value=\"1\" />
					<label for=\"tab_nom\">"._('Nombre de colonnes : ')."</label>
					<input type=\"text\" size=\"2\" name=\"nombre_colonnes\" id=\"nombres_colonnes\" value=\"1\" />
	
					<input id=\"radio_position_table_avant\" type=\"radio\" value=\"0\" name=\"position\"/>
					<label for=\"radio_position_table_avant\">"._('Avant')."</label>
					<input id=\"radio_position_table_apres\" type=\"radio\" checked=\"checked\" value=\"1\" name=\"position\"/>
					<label for=\"radio_position_table_apres\">"._('Après')." </label>";
				
									
				$liste_table = exec_requete("select * from fe_table where rub_id={$rubrique_id} order by tab_ordre asc", $connexion);
				if (mysql_num_rows($liste_table)>0){
	echo "			
					
					<select name=\"ordre\">";
	
	while ($table = objet_suivant($liste_table)) {
		echo "			
						<option value=\"{$table->tab_ordre}\">".echo_MC('NOM_TABLE',$table->tab_nom)."</option>";
	}
	echo "				
					</select>";
				}
	echo "			

					<input type=\"submit\" value=\""._('Insérer')."\" />
					</p>
				</form>
			</div>";
}

//==============================================================================
// Fonction entete_table : affiche le chemin, le nom et diverses options de la table courante
//==============================================================================
function entete_table ($table_id, $connexion) {
	global $TEXT;
	if ($table_id > 0) {
		$liste_table = exec_requete ("select * from fe_table where tab_id = {$table_id}", $connexion);
		echo "	
			<div class=\"Bloc\">
				<h2>";
		if ($table = objet_suivant ($liste_table)) {
			echo arborescence_rubrique ($table->rub_id, $connexion)." &nbsp;&nbsp;>&nbsp;&nbsp;".
			echo_MC('NOM_TABLE',$table->tab_nom);
		}
		echo "</h2>";
				
		if ($table) {
			echo "
						<form method=\"post\" action=\"admin.php?menu=facteur_emission&amp;table_id={$table_id}\">
						<p class=\"champ_form\">
							<input type=\"text\" name=\"table_nouveau_nom\" value=\"".echo_MC('NOM_TABLE',$table->tab_nom)."\" />
							<input type=\"hidden\" name=\"action_facteur_emission\" value=\"change_nom_table\" />
							<input type=\"hidden\" name=\"table_id\" value=\"{$table_id}\" />
							<input type=\"submit\" value=\""._('Changer le nom')."\" />
						</p>
						</form>
						<form method=\"post\" action=\"admin.php?menu=facteur_emission&amp;table_id={$table_id}\">
							<p class=\"champ_form\">"._('Déplacer dans la rubrique :')."
							<select name=\"rubrique_id\">";
								liste_deroulante_rubrique (-1, 1, $connexion);
			echo "		 	
							</select>
							<input type=\"hidden\" name=\"action_facteur_emission\" value=\"deplacer_table\" />
							<input type=\"hidden\" name=\"table_id\" value=\"{$table_id}\" />
							<input type=\"submit\" value=\""._('Déplacer vers')."\" />
							</p>
						</form>";
			echo "
						<form method=\"post\" action=\"admin.php?menu=facteur_emission&amp;table_id={$table->rub_id}\" onclick=\"return(confirm('Etes-vous sï¿½r de vouloir supprimer cette table?'));\">
							<p class=\"champ_form\">
							<input type=\"hidden\" name=\"action_facteur_emission\" value=\"supprime_table\" />
							<input type=\"hidden\" name=\"table_id\" value=\"{$table_id}\" />
							<input type=\"submit\" value=\""._('Supprimer')."\" />
							</p>
						</form>
				</div>";
		}
	}
}


//==============================================================================
// Fonction afficheTableauAdmin : affiche le tableau en mode admin
//==============================================================================
function afficheTableauAdmin ($tableau, $mode, $connexion) {
	echo "
			<div class=\"Bloc_tableau\">";
	
	$liste_tableau = exec_requete ("select * from fe_table where tab_id = {$tableau->tab_id}", $connexion);
	$tableau = objet_suivant ($liste_tableau);

	echo "
			<h2>".echo_MC('NOM_TABLE',$tableau->tab_nom)."</h2>";
	if ($mode == "consultation_admin") {
		echo "			
				<form  class=\"boutton\" method=\"post\" action=\"admin.php?menu=facteur_emission&amp;table_id={$tableau->tab_id}\">
					<p class=\"boutton\"><input type=\"submit\" value=\""._('Modifier')."\"/></p>
				</form>";


		if ($tableau->tab_ordre > 1) {
			formulaire_fleche("table",$tableau,"haut");
		}
		$liste_tableau = exec_requete("select tab_id from fe_table where rub_id = {$tableau->rub_id}", $connexion);
		if ($tableau->tab_ordre < mysql_num_rows($liste_tableau)) {
			formulaire_fleche("table",$tableau,"bas");
		}
	
		echo "		
				<form class=\"boutton\" method=\"post\" action=\"admin.php?menu=facteur_emission&amp;rubrique_id={$tableau->rub_id}\" onclick=\"return(confirm('Etes-vous sï¿½r de vouloir supprimer cette table?'));\">
					<p class=\"boutton\">
					<input type=\"submit\" value=\""._("Supprimer")."\"/>
					<input type=\"hidden\" name=\"action_facteur_emission\" value=\"supprime_table\"/>
					<input type=\"hidden\" name=\"table_id\" value=\"{$tableau->tab_id}\"/>
					</p>
				</form>";
	}
	
	if ($mode == "edit_valeur") {
	echo "
				<form method=\"post\" action=\"admin.php?menu=facteur_emission&amp;table_id={$tableau->tab_id}\">
					<p>
					<input type=\"hidden\" name=\"action_facteur_emission\" value=\""._('edit_valeur')."\" />
					<input type=\"hidden\" name=\"table_id\" value=\"{$tableau->tab_id}\" />
					</p>";
					
	}
	
	echo "	
				<table class=\"tableau\" border=\"1\">
					<thead><tr>";
	if ($mode == "edit_valeur") {
		echo "
				<td class=\"titre_ligne\">
					<p>
					<label for=\"tab_titre_ligne\" > Le titre des lignes </label>
					<textarea id=\"tab_titre_ligne\" name=\"tab_titre_ligne\" cols=\"17\" rows=\"4\">".echo_MC('TITRE_LIGNE',$tableau->tab_titre_ligne)."</textarea>
					</p>
				</td>";
	}
	else{
		echo"
				<td class=\"titre_ligne\">".(echo_MC('TITRE_LIGNE',$tableau->tab_titre_ligne))."</td>";
	}
	$liste_colonne = exec_requete("select * from fe_colonne where tab_id = {$tableau->tab_id} order by col_ordre asc", $connexion);
	while ($colonnes[] = objet_suivant($liste_colonne)) {}
	
	for ($j=0 ; $j < count($colonnes)-1 ; $j++) {
		echo "		
						<td class=\"colonne\">";
		if ($mode == "edit_valeur") {
			echo "	
						<p>
						<select name=\"unite_numerateur_id_{$j}\">";
						liste_deroulante_unite($connexion, $colonnes[$j]->unite_numerateur_id);
			echo "							
						</select>
						</p>						
						&nbsp; "._('par')."
						<p>
						<select name=\"unite_denominateur_1_id_{$j}\">";
						liste_deroulante_unite($connexion, $colonnes[$j]->unite_denominateur_1_id);
			echo "							
						</select>
						</p>
						&nbsp;.&nbsp;
						<p>
						<select name=\"unite_denominateur_2_id_{$j}\">";
						liste_deroulante_unite($connexion, $colonnes[$j]->unite_denominateur_2_id);
			echo"		
						</select>
						</p>
						<div class=\"retour_ligne\">&nbsp;</div>
						<p>
						<label for=\"colonne_com_{$j}\"> Commentaire de la colonne </label>
						<textarea id=\"colonne_com_{$j}\" name=\"colonne_com_{$j}\">".($colonnes[$j]->col_commentaire)."</textarea>
						<input type=\"hidden\" name=\"colonne_id_{$j}\" value=\"{$colonnes[$j]->col_id}\" />
						</p>";
		}
		else {	
			echo "
				<p>".unite ($colonnes[$j]->unite_numerateur_id, $colonnes[$j]->unite_denominateur_1_id, $colonnes[$j]->unite_denominateur_2_id, $connexion)."</p>";
			if ($mode == "edit_ordre") {
			
				if ($j > 0) {
					formulaire_fleche("colonne",$colonnes[$j],"gauche");
				}
				if ($j < count($colonnes)-2) {
					formulaire_fleche("colonne",$colonnes[$j],"droite");
				}
			}
			if (echo_MC('COMMENTAIRE_COL',$colonnes[$j]->col_commentaire)!=""){
				echo "<p class=\"commentaire\">".echo_MC('COMMENTAIRE_COL',$colonnes[$j]->col_commentaire)."</p>";
			}/* Le commentaire */
		}
		echo "</td>";
	}
	echo "
						<td class=\"incertitude\">"._('Incertitude (en %)')."</td>";
	if ($mode == "edit_valeur") echo "
						<td class=\"supprimer\">".('Supprimer')."</td>";
	else if ($mode == "edit_ordre") echo "
						<td>&nbsp;</td>";
	echo "			
					</tr></thead>
					<tbody>";
	$liste_ligne = exec_requete("select * from fe_ligne where tab_id = {$tableau->tab_id} order by lig_ordre asc", $connexion);
	
	while ($lignes[] = objet_suivant($liste_ligne)) {}
	
	for ($i=0 ; $i < count($lignes)-1 ; $i++) {
		echo "		
					<tr>
						<td class=\"titre_ligne\">";
		if ($mode == "edit_valeur") {
			echo "
							<input type=\"text\" name=\"ligne_nom_{$i}\" value=\"".($lignes[$i]->lig_nom)."\" />
							<input type=\"hidden\" name=\"ligne_id_{$i}\" value=\"{$lignes[$i]->lig_id}\"/>";
		}
		else echo (echo_MC('NOM_LIGNE',$lignes[$i]->lig_nom))."&nbsp;";
		
		echo "  </td>";
		for ($j=0 ; $j<count($colonnes)-1 ; $j++) {
			$liste_valeur = exec_requete("select * from fe_valeur where col_id = {$colonnes[$j]->col_id} and lig_id = {$lignes[$i]->lig_id}", $connexion);
				echo "
						<td class=\"colonne\">";
			if ($valeur = objet_suivant($liste_valeur)) {
				if ($mode == "edit_valeur") {
					if (! is_null($valeur->valeur)) echo "<input type=\"text\" name=\"valeur_{$i}_{$j}\" value=\"{$valeur->valeur}\" />";
					else echo "<input type=\"text\" name=\"valeur_{$i}_{$j}\" />";
				}
				else {
					if ($valeur->valeur != NULL){					
						echo to_X_chif_signi($valeur->valeur,$_SESSION['chiffre_signi']) . "&nbsp;";
					}
					else{
						echo " - ";					
					}
				}
				echo "</td>";
			}
		}
		echo "
						<td class=\"incertitude\">";
		if ($mode == "edit_valeur") {
			if (! is_null($lignes[$i]->incertitude)) echo "<input type=\"text\" name=\"incertitude_{$i}\" value=\"{$lignes[$i]->incertitude}\" />";
			else echo "<input type=\"text\" name=\"incertitude_{$i}\" />";
		}
		else {
			if($lignes[$i]->incertitude!=NULL){
				echo $lignes[$i]->incertitude;
			}
			else{
				echo "-";
			}
		}
		echo "</td>";
		if ($mode == "edit_valeur") {
			echo "		
						<td align=\"center\" class=\"supprimer\"><input type=\"checkbox\" name=\"supp_ligne_{$i}\" /></td>";
		}
		if ($mode == "edit_ordre") {
			echo "	
						<td class=\"colonne\">";
			if ($i > 0) {
				formulaire_fleche("ligne",$lignes[$i],"haut");
			}
			if ($i < count($lignes)-2) {
				formulaire_fleche("ligne",$lignes[$i],"bas");
			}
			echo "	
						</td>";
		}
		echo "
					</tr>";
	}
	if ($mode == "edit_valeur") {
		echo "	
				<tr>
					<td class=\"colonne\">". _('Supprimer')."</td>";
		for ($j=0 ; $j<count($colonnes)-1 ; $j++) {
			echo "
					<td align=\"center\"><input type=\"checkbox\" name=\"supp_colonne_{$j}\" /></td>";
		}
		echo "
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>";
	}
	echo "	
				</tbody>
			</table>";
	// On va ajouter une zone de commentaire
	if ($mode == "edit_valeur") {
		echo "	
					<p>
					<label for=\"tab_commentaire\"> Commentaire de la table </label>
					<textarea id=\"tab_commentaire\" cols=\"100\" rows=\"5\" name=\"tab_commentaire\">".($tableau->tab_commentaire)."</textarea>
					</p>";
					echo "<script language=\"javascript\" type=\"text/javascript\">
					tinyMCE.execCommand('mceAddControl', false, 'tab_commentaire');
					</script>";
	}
	else if ($tableau->tab_commentaire!=''){
	echo "		
				<p>".html_entity_decode($tableau->tab_commentaire)."</p>";
	}
	if ($mode == "edit_valeur") {
		echo "	
					<p>
					<input type=\"submit\" value=\""._('Valider')."\"/>
					<input type=\"reset\" value=\""._("Annuler")."\" />
					</p>
				</form>
				<form method=\"post\" action=\"admin.php?menu=facteur_emission&amp;rubrique_id={$tableau->rub_id}\">
					<p><input type=\"hidden\" name=\"action_facteur_emission\" value=\""._('consultation_admin')."\" /></p>
					<p><input type=\"submit\"value=\""._("Sortie")."\" /></p> 
				</form>";
	}
	echo "
				
			</div>";
}
//==============================================================================
// Fonction menu_creer_ligne : affiche le menu pour crï¿½er une ou plusieurs nouvelles lignes
//==============================================================================
function menu_creer_ligne ($table_id, $connexion) {
	echo "	
			<div class=\"Bloc\">
				<h2>";
				$objet = exec_requete ("select * from fe_table where tab_id = {$table_id} limit 1", $connexion);
				$table = objet_suivant ($objet);
	echo 		_('Ajouter une nouvelle ligne dans la table :').echo_MC('NOM_TABLE',$table->tab_nom)."</h2>
				<form method=\"post\" action=\"admin.php?menu=facteur_emission&amp;table_id={$table_id}\">
					<p>
					<input type=\"hidden\" name=\"table_id\" value=\"{$table_id}\" />
					<input type=\"hidden\" name=\"action_facteur_emission\" value=\"ajoute_ligne\" />
					</p>
					
					<p>
					<label for=\"nb_ligne\">"._('Nombre de ligne(s) :')."</label>
					<input id=\"nb_ligne\" type=\"text\" size=\"2\" maxlength=\"255\" name=\"nb_ligne\" value=\"1\"/>
					
					<input id=\"radio_position_ligne_avant\" type=\"radio\" value=\"0\" name=\"position\"/>
					<label for=\"radio_position_ligne_avant\">"._('Avant')."</label>
					<input id=\"radio_position_ligne_apres\" type=\"radio\" checked=\"checked\" value=\"1\" name=\"position\"/>
					<label for=\"radio_position_ligne_apres\">"._('Après')." </label>";

	$liste_ligne = exec_requete("select * from fe_ligne where tab_id={$table_id} order by lig_ordre asc", $connexion);
				if (mysql_num_rows($liste_ligne)>0){
					echo"	
						
					<select name=\"ordre\">";
			while ($ligne = objet_suivant($liste_ligne)) {
				echo			
						"<option value=\"{$ligne->lig_ordre}\">".echo_MC('NOM_LIGNE',$ligne->lig_nom)."</option>";
			}
			echo "			
					</select>";
	}
	echo "			
					<input type=\"submit\" value=\""._('Insérer')."\" />
					</p>
				</form>
			</div>";
}


//==============================================================================
// Fonction menu_creer_colonne : affiche le menu pour crï¿½er une ou plusieurs nouvelles colonnes
//==============================================================================
function menu_creer_colonne ($table_id, $connexion) {
	echo "	
			<div class=\"Bloc\">
				<h2>";
					$objet = exec_requete ("select * from fe_table where tab_id = {$table_id} limit 1", $connexion);
					$table = objet_suivant ($objet);
	echo _('Ajouter une nouvelle colonne dans la table :').echo_MC('NOM_TABLE',$table->tab_nom)."
				</h2>
				<form method=\"post\" action=\"admin.php?menu=facteur_emission&amp;table_id={$table_id}\">
					<p>
					<input type=\"hidden\" name=\"table_id\" value=\"{$table_id}\" />
					<input type=\"hidden\" name=\"action_facteur_emission\" value=\"ajoute_colonne\" />
					</p>
					<p>
					<label for=\"nombre_colonnes\">"._('Nombre de colonne(s)')." : </label>
					<input id=\"nombre_colonnes\" type=\"text\" size=\"2\" maxlength=\"255\" name=\"nb_colonne\" value=\"1\" />
					<input id=\"radio_position_colonne_avant\" type=\"radio\" value=\"0\" name=\"position\"/>
					<label for=\"radio_position_colonne_avant\">"._('Avant')."</label>
					<input id=\"radio_position_colonne_apres\" type=\"radio\" checked=\"checked\" value=\"1\" name=\"position\"/>
					<label for=\"radio_position_colonne_apres\">"._('Après')." </label>";
		
						$liste_colonne = exec_requete("select * from fe_colonne where tab_id={$table_id} order by col_ordre asc", $connexion);
						if (mysql_num_rows($liste_colonne)>0){
						echo "	
							<select name=\"ordre\">";
	while ($colonne = objet_suivant($liste_colonne)) {
		echo "
							<option value=\"{$colonne->col_ordre}\">" . unite ($colonne->unite_numerateur_id, $colonne->unite_denominateur_1_id, $colonne->unite_denominateur_2_id, $connexion,1) . "</option>";
	}
	echo "					
							</select>";
						}
				echo "

					<input type=\"submit\" value=\""._('Insérer')."\" />
					</p>
				</form>
			</div>";
}

//==============================================================================
// Fonction action_facteur_emission : exï¿½cute les action_facteur_emissions spï¿½cifiï¿½es
//==============================================================================
function action_facteur_emission($connexion) {
	if ($_POST["action_facteur_emission"] == "ajoute_rubrique") creation_rubrique ($connexion);
	if ($_POST["action_facteur_emission"] == "supprime_rubrique") suppression_rubrique ($_POST["rubrique_id"], $connexion);
	if ($_POST["action_facteur_emission"] == "change_nom_rubrique") exec_requete ("update fe_rubrique set rub_nom = \"".secure($_POST["rubrique_nouveau_nom"])."\" where rub_id = {$_POST["rubrique_id"]}", $connexion);
	if ($_POST["action_facteur_emission"] == "change_nom_table") exec_requete ("update fe_table set tab_nom = \"".secure($_POST["table_nouveau_nom"])."\" where tab_id = {$_POST["table_id"]}", $connexion);
	if ($_POST["action_facteur_emission"] == "ajoute_table") creation_table ($connexion);
	if ($_POST["action_facteur_emission"] == "edit_valeur") edit_valeur ($connexion);
	if ($_POST["action_facteur_emission"] == "supprime_table") suppression_table ($_POST["table_id"], $connexion);
	if ($_POST["action_facteur_emission"] == "ajoute_ligne") creation_ligne ($connexion);
	if ($_POST["action_facteur_emission"] == "ajoute_colonne") creation_colonne ($connexion);
	if ($_POST["action_facteur_emission"] == "change_ordre_ligne" || $_POST["action_facteur_emission"] == "change_ordre_colonne") edit_ordre_lig_col ($connexion);
	if ($_POST["action_facteur_emission"] == "deplacer_rubrique") deplacer_rubrique ($connexion);
	if ($_POST["action_facteur_emission"] == "deplacer_table") deplacer_table ($connexion);
	if ($_POST["action_facteur_emission"] == "change_ordre_rubrique") edit_ordre_rub ($connexion);
	if ($_POST["action_facteur_emission"] == "change_ordre_table") edit_ordre_tab ($connexion);
}


//==============================================================================
// Fonction creation_rubrique : crï¿½e la rubrique dans la base de donnï¿½es
//==============================================================================
function creation_rubrique ($connexion) {
	$objet = exec_requete("select max(rub_id) max from fe_rubrique", $connexion);
	$rubrique = objet_suivant($objet);
	if (isset($rubrique->max) && $rubrique->max>0) $rubrique_id = $rubrique->max;
	else $rubrique_id = 0;
	$rubrique_id++;
	if (isset($_POST["ordre"])) $ordre = $_POST["ordre"] + $_POST["position"];
	else $ordre = 1;
	
	exec_requete("update fe_rubrique set rub_ordre = rub_ordre+1 where rub_prec_id = {$_POST["rub_prec_id"]} and rub_ordre >= {$ordre}",$connexion);
	exec_requete("insert into fe_rubrique (rub_id, rub_ordre, rub_nom, rub_prec_id) values ({$rubrique_id}, {$ordre}, \"".secure($_POST["rub_nom"])."\", {$_POST["rub_prec_id"]})", $connexion);
}

//==============================================================================
// Fonction suprression_rubrique : supprime la rubrique de la base de donnï¿½es
//==============================================================================
function suppression_rubrique ($rubrique_id, $connexion) {
	$liste_rubrique = exec_requete ("select * from fe_rubrique where rub_id = {$rubrique_id}", $connexion);
	if ($rubrique = objet_suivant ($liste_rubrique)){
		exec_requete ("update fe_rubrique set rub_ordre = rub_ordre-1 where rub_prec_id = {$rubrique->rub_prec_id} and rub_ordre > {$rubrique->rub_ordre}", $connexion);
		exec_requete ("delete from fe_rubrique where rub_id = {$rubrique_id} limit 1", $connexion);
		
		$liste_sous_rubrique = exec_requete ("select * from fe_rubrique where rub_prec_id = {$rubrique_id}", $connexion);
		while ($sous_rubrique = objet_suivant ($liste_sous_rubrique)) {
			suppression_rubrique ($sous_rubrique->rub_id, $connexion);
		}
		$liste_table = exec_requete ("select * from fe_table where rub_id = {$rubrique_id}", $connexion);
		while ($table = objet_suivant ($liste_table)) {
			suppression_table ($table->tab_id, $connexion);
		}
	}
}
//==============================================================================
// Fonction creation_table : crï¿½e la table dans la base de donnï¿½es
//==============================================================================
function creation_table ($connexion) {
	$objet = exec_requete("select max(tab_id) max from fe_table", $connexion);
	if ($table = objet_suivant($objet)) $table_id = $table->max;
	else $table_id = 0;
	$objet = exec_requete("select max(col_id) max from fe_colonne", $connexion);
	if ($colonne = objet_suivant($objet)) $max_colonne_id = $colonne->max;
	else $max_colonne_id = 0;
	$objet = exec_requete("select max(lig_id) max from fe_ligne", $connexion);
	if ($ligne = objet_suivant($objet)) $max_ligne_id = $ligne->max;
	else $max_ligne_id = 0;
	
	$table_id++;
	if (isset($_POST["ordre"])) $ordre = $_POST["ordre"] + $_POST["position"];
	else $ordre = 1;
	
	exec_requete("update fe_table set tab_ordre = tab_ordre+1 where rub_id = {$_POST["rubrique_id"]} and tab_ordre >= {$ordre}", $connexion);
	exec_requete("insert into fe_table (tab_id, tab_ordre, tab_nom, rub_id) values ({$table_id}, {$ordre}, \"".secure($_POST["tab_nom"])."\", {$_POST["rubrique_id"]})", $connexion);
	
	for ($i=1 ; $i<=$_POST["nombre_lignes"] ; $i++) {
		$ligne_id = $max_ligne_id + $i;
		exec_requete("insert into fe_ligne (lig_id, lig_ordre, tab_id) values ({$ligne_id}, {$i}, {$table_id})", $connexion);
	}
	
	for ($j=1 ; $j<=$_POST["nombre_colonnes"] ; $j++) {
		$colonne_id = $max_colonne_id + $j;
		exec_requete("insert into fe_colonne (col_id, col_ordre, tab_id) values ({$colonne_id}, {$j}, {$table_id})", $connexion);
	}
	
	for ($i=1 ; $i<=$_POST["nombre_lignes"] ; $i++) {
		$ligne_id = $max_ligne_id + $i;
		for ($j=1 ; $j<=$_POST["nombre_colonnes"] ; $j++) {
			$colonne_id = $max_colonne_id + $j;
			exec_requete("insert into fe_valeur (col_id, lig_id) values ({$colonne_id}, {$ligne_id})", $connexion);
		}
	}
}
//==============================================================================
// Fonction suprression_table : supprime la table de la base de donnï¿½es
//==============================================================================
function suppression_table ($table_id, $connexion) {
	$liste_table = exec_requete ("select * from fe_table where tab_id = {$table_id}", $connexion);
	if ($table = objet_suivant ($liste_table)) {
		exec_requete ("update fe_table set tab_ordre = tab_ordre-1 where rub_id = {$table->rub_id} and tab_ordre > {$table->tab_ordre}", $connexion);
		exec_requete ("delete from fe_table where tab_id = {$table->tab_id} limit 1", $connexion);

		$liste_colonne = exec_requete ("select * from fe_colonne where tab_id = {$table->tab_id}", $connexion);
		while ($colonne = objet_suivant ($liste_colonne)) {
			suppression_colonne ($colonne->col_id, $connexion);
		}
		exec_requete ("delete from fe_ligne where tab_id = {$table->tab_id}", $connexion);
	}
}
//==============================================================================
// Fonction creation_colonne : crï¿½e les colonnes dans la base de donnï¿½es
//==============================================================================
function creation_colonne ($connexion) {
	$objet = exec_requete("select max(col_id) max from fe_colonne", $connexion);
	if ($colonne = objet_suivant($objet)) $max_colonne_id = $colonne->max;
	else $max_colonne_id = 0;
	
	if (isset($_POST["ordre"])) $ordre = $_POST["ordre"] + $_POST["position"];
	else $ordre = 1;
	
	$liste_ligne = exec_requete ("select * from fe_ligne where tab_id = {$_POST["table_id"]} order by lig_ordre asc", $connexion);
	while ($lignes[] = objet_suivant ($liste_ligne)){}
	
	exec_requete("update fe_colonne set col_ordre = col_ordre+".intval($_POST["nb_colonne"])." where tab_id = {$_POST["table_id"]} and col_ordre >= {$ordre}", $connexion);
	
	for ($i=0 ; $i<$_POST["nb_colonne"] ; $i++) {
		$max_colonne_id++;
		$col_ordre = $ordre + $i;
		exec_requete ("insert into fe_colonne (col_id, col_ordre, tab_id) values ({$max_colonne_id}, {$col_ordre}, {$_POST["table_id"]})", $connexion);
		for ($j=0 ; $j<count($lignes)-1 ; $j++) {
			exec_requete ("insert into fe_valeur (lig_id, col_id) values ({$lignes[$j]->lig_id}, {$max_colonne_id})", $connexion);
		}
	}
}
//==============================================================================
// Fonction suprression_colonne : supprime la colonne de la base de donnï¿½es
//==============================================================================
function suppression_colonne ($colonne_id, $connexion) {
	$liste_colonne = exec_requete ("select * from fe_colonne where col_id = {$colonne_id}", $connexion);
	if ($colonne = objet_suivant ($liste_colonne)) {
		exec_requete ("update fe_colonne set col_ordre = col_ordre-1 where tab_id = {$colonne->tab_id} and col_ordre > {$colonne->col_ordre}", $connexion);
		exec_requete ("delete from fe_colonne where col_id = {$colonne->col_id} limit 1", $connexion);
		
		exec_requete ("delete from fe_valeur where col_id = {$colonne->col_id}", $connexion);
	}
}


//==============================================================================
// Fonction creation_ligne : crï¿½e lles lignes dans la base de donnï¿½es
//==============================================================================
function creation_ligne ($connexion) {
	$objet = exec_requete("select max(lig_id) max from fe_ligne", $connexion);
	if ($ligne = objet_suivant($objet)) $max_ligne_id = $ligne->max;
	else $max_ligne_id = 0;
	
	if (isset($_POST["ordre"])) $ordre = $_POST["ordre"] + $_POST["position"];
	else $ordre = 1;
	
	$liste_colonne = exec_requete ("select * from fe_colonne where tab_id = {$_POST["table_id"]} order by col_ordre asc", $connexion);
	while ($colonnes[] = objet_suivant ($liste_colonne)){}
	
	exec_requete("update fe_ligne set lig_ordre = lig_ordre+".intval($_POST["nb_ligne"])." where tab_id = {$_POST["table_id"]} and lig_ordre >= {$ordre}", $connexion);
	
	for ($i=0 ; $i<$_POST["nb_ligne"] ; $i++) {
		$max_ligne_id++;
		$lig_ordre = $ordre + $i;
		exec_requete ("insert into fe_ligne (lig_id, lig_ordre, tab_id) values ({$max_ligne_id}, {$lig_ordre}, {$_POST["table_id"]})", $connexion);
		for ($j=0 ; $j<count($colonnes)-1 ; $j++) {
			exec_requete ("insert into fe_valeur (col_id, lig_id) values ({$colonnes[$j]->col_id}, {$max_ligne_id})", $connexion);
		}
	}
}

//==============================================================================
// Fonction suprression_ligne : supprime la ligne de la base de donnï¿½es
//==============================================================================
function suppression_ligne ($ligne_id, $connexion) {
	$liste_ligne = exec_requete ("select * from fe_ligne where lig_id = {$ligne_id}", $connexion);
	if ($ligne = objet_suivant ($liste_ligne)){
		exec_requete ("update fe_ligne set lig_ordre = lig_ordre-1 where tab_id = {$ligne->tab_id} and lig_ordre > {$ligne->lig_ordre}", $connexion);
		exec_requete ("delete from fe_ligne where lig_id = {$ligne->lig_id} limit 1", $connexion);
		
		exec_requete ("delete from fe_valeur where lig_id = {$ligne->lig_id}", $connexion);
	}
}

//==============================================================================
// Fonction editValeur : ï¿½dite les valeurs du tableau
//==============================================================================
function edit_valeur ($connexion) {
	exec_requete ("update fe_table set tab_titre_ligne=\"".secure($_POST["tab_titre_ligne"])."\" where tab_id={$_POST["table_id"]}", $connexion);
	exec_requete ("update fe_table set tab_commentaire=\"".secure($_POST["tab_commentaire"])."\" where tab_id={$_POST["table_id"]}", $connexion);

	for ($i=0 ; isset($_POST["ligne_nom_{$i}"]) ; $i++) { 
		if ($_POST["incertitude_$i"] == '')
			exec_requete ("update fe_ligne set lig_nom=\"".secure($_POST["ligne_nom_$i"])."\", incertitude=NULL where lig_id={$_POST["ligne_id_$i"]}", $connexion);
		else
			exec_requete ("update fe_ligne set lig_nom=\"".secure($_POST["ligne_nom_$i"])."\", incertitude=".floatval($_POST["incertitude_$i"])." where lig_id={$_POST["ligne_id_$i"]}", $connexion);
		for ($j=0 ; isset($_POST["valeur_{$i}_{$j}"]) ; $j++) {
			if ($_POST["valeur_{$i}_{$j}"] == '')
				exec_requete ("update fe_valeur set valeur=NULL where col_id={$_POST["colonne_id_$j"]} and lig_id={$_POST["ligne_id_$i"]}", $connexion);
			else exec_requete ("update fe_valeur set valeur=".floatval(str_replace(",",".",$_POST["valeur_{$i}_{$j}"]))." where col_id=".intval($_POST["colonne_id_{$j}"])." and lig_id=".intval($_POST["ligne_id_{$i}"]), $connexion);
		}
		if (isset($_POST["supp_ligne_{$i}"])) suppression_ligne ($_POST["ligne_id_{$i}"], $connexion);
	}
	for ($j=0 ; isset($_POST["colonne_com_{$j}"]) ; $j++) {
			$requete = "update fe_colonne set "
				. "col_commentaire= \"" . secure($_POST["colonne_com_{$j}"]) . "\", "
				. "unite_numerateur_id= " . $_POST["unite_numerateur_id_{$j}"] . ", "
				. "unite_denominateur_1_id= " . $_POST["unite_denominateur_1_id_{$j}"].", "
				. "unite_denominateur_2_id= " . $_POST["unite_denominateur_2_id_{$j}"]
				. " where col_id = " . $_POST["colonne_id_$j"];
			exec_requete($requete, $connexion);
		if (isset($_POST["supp_colonne_{$j}"])) suppression_colonne ($_POST["colonne_id_{$j}"], $connexion);
	}
}

//==============================================================================
// Fonction edit_ordre : change l'ordre de la ligne ou de la colonne
//==============================================================================
function edit_ordre_lig_col ($connexion) {
	$action_facteur_emission = explode("_", $_POST["action_facteur_emission"]);
	$item_abr = substr($action_facteur_emission[2], 0, 3);
	exec_requete ("update fe_{$action_facteur_emission[2]} set " . $item_abr . "_ordre = " . $item_abr . "_ordre + " . $_POST["position"] . " where " . $item_abr . "_ordre = " . $_POST["ordre"] . " and " . $item_abr . "_id = " . $_POST["item_id"]. " and tab_id = " . $_POST["contenant_id"], $connexion);
	exec_requete ("update fe_{$action_facteur_emission[2]} set " . $item_abr . "_ordre = " . $item_abr . "_ordre - " . $_POST["position"] . " where " . $item_abr . "_ordre = " . $_POST["ordre"] . $_POST["position"] . " and " . $item_abr . "_id != " . $_POST["item_id"]. " and tab_id = " . $_POST["contenant_id"], $connexion);
}

//==============================================================================
// Fonction edit_ordre_rub : change l'ordre de la rubrique
//==============================================================================
function edit_ordre_rub ($connexion) {
	$action_facteur_emission = explode("_", $_POST["action_facteur_emission"]);
	$item_abr = substr($action_facteur_emission[2], 0, 3);
	exec_requete ("update fe_{$action_facteur_emission[2]} set " . $item_abr . "_ordre = " . $item_abr . "_ordre + " . $_POST["position"] . " where " . $item_abr . "_ordre = " . $_POST["ordre"] . " and " . $item_abr . "_id = " . $_POST["item_id"]. " and rub_prec_id = " . $_POST["contenant_id"], $connexion);
	exec_requete ("update fe_{$action_facteur_emission[2]} set " . $item_abr . "_ordre = " . $item_abr . "_ordre - " . $_POST["position"] . " where " . $item_abr . "_ordre = " . $_POST["ordre"] . $_POST["position"] . " and " . $item_abr . "_id != " . $_POST["item_id"]. " and rub_prec_id = " . $_POST["contenant_id"], $connexion);
}

//==============================================================================
// Fonction edit_ordre_tab : change l'ordre de la table
//==============================================================================
function edit_ordre_tab ($connexion) {
	$action_facteur_emission = explode("_", $_POST["action_facteur_emission"]);
	$item_abr = substr($action_facteur_emission[2], 0, 3);
	exec_requete ("update fe_{$action_facteur_emission[2]} set " . $item_abr . "_ordre = " . $item_abr . "_ordre + " . $_POST["position"] . " where " . $item_abr . "_ordre = " . $_POST["ordre"] . " and " . $item_abr . "_id = " . $_POST["item_id"]. " and rub_id = " . $_POST["contenant_id"], $connexion);
	exec_requete ("update fe_{$action_facteur_emission[2]} set " . $item_abr . "_ordre = " . $item_abr . "_ordre - " . $_POST["position"] . " where " . $item_abr . "_ordre = " . $_POST["ordre"] . $_POST["position"] . " and " . $item_abr . "_id != " . $_POST["item_id"]. " and rub_id = " . $_POST["contenant_id"], $connexion);
}


//==============================================================================
// Fonction liste_unite : retourne la liste des unitï¿½s
//==============================================================================
function liste_deroulante_unite ($connexion, $par_defaut) {

	
	$liste_nature_unite = exec_requete ("select * from fe_nature_unite order by nature_unite_ordre asc", $connexion);
	$selected=0;
	while ($nature_unite = objet_suivant($liste_nature_unite)) {
		$liste_unite_fondamentale = exec_requete ("select * from fe_unite_fondamentale where unite_fond_nature_unite_id = {$nature_unite->nature_unite_id} order by unite_fond_ordre asc", $connexion);
		if (mysql_num_rows($liste_unite_fondamentale)!=0){
			echo "
								<optgroup label=\"".echo_MC('NOM_NATURE_UNITE',$nature_unite->nature_unite_nom)."\">";
			while ($unite_fondamentale = objet_suivant($liste_unite_fondamentale)) {
				if ($par_defaut == $unite_fondamentale->unite_fond_id){
					echo "
									<option selected=\"selected\" value=\"{$unite_fondamentale->unite_fond_id}\">&nbsp;&nbsp;&nbsp;{$unite_fondamentale->unite_fond_symbole}</option>";
					$selected=1;
				}	
				else
					echo "
									<option value=\"{$unite_fondamentale->unite_fond_id}\">&nbsp;&nbsp;&nbsp;".echo_MC('NOM_UNITE',$unite_fondamentale->unite_fond_symbole)."</option>";
			}
			echo "</optgroup>";
		}
		
	}
	echo "
								<optgroup label=\""._('Champ Vide')."\">
									<option ";
	if ($selected==0) echo "selected=\"selected\"";
	echo " value=\"-1\">&nbsp;</option>
								</optgroup>";
			
}

//==============================================================================
// Fonction unite : retourne l' unitï¿½  sous forme de chaine de caractï¿½re
//==============================================================================
function unite ($num, $den1, $den2, $connexion) {
	$unite_retourne = '';
	if ($num > 0) {
		$liste_unite = exec_requete ("select * from fe_unite_fondamentale where unite_fond_id = {$num}", $connexion);
		if ($unite = objet_suivant ($liste_unite)) $unite_retourne .= $unite->unite_fond_symbole;
		else return "&nbsp;";
		
		if ($den1 > 0) {
			$liste_unite = exec_requete ("select * from fe_unite_fondamentale where unite_fond_id = {$den1}", $connexion);
			if ($unite = objet_suivant ($liste_unite)) {
				$unite_retourne = echo_MC('NOM_UNITE',$unite_retourne) . " par " . echo_MC('NOM_UNITE',$unite->unite_fond_symbole);
			}
			if ($den2 > 0) {
				$liste_unite = exec_requete ("select * from fe_unite_fondamentale where unite_fond_id = {$den2}", $connexion);
				if ($unite = objet_suivant ($liste_unite)) $unite_retourne = $unite_retourne . " . " . echo_MC('NOM_UNITE',$unite->unite_fond_symbole);
			}
		}
	}
	return $unite_retourne;
}
//==============================================================================
// Fonction deplacer_rubrique : dï¿½place une rubrique dans une autre rubrique
//==============================================================================
function deplacer_rubrique ($connexion) {
	$liste_rubrique = exec_requete ("select * from fe_rubrique where rub_id = ". $_POST["rubrique_id"], $connexion);
	$rubrique = objet_suivant ($liste_rubrique);
	exec_requete ("update fe_rubrique set rub_ordre = rub_ordre - 1 where rub_prec_id = " . $rubrique->rub_prec_id . " and rub_ordre > " . $rubrique->rub_ordre, $connexion);
	$liste_ordre = exec_requete ("select max(rub_ordre) max from fe_rubrique where rub_prec_id = " . $_POST["rubrique_prec_id"], $connexion);
	$ordre = objet_suivant ($liste_ordre);
	$ordre->max++;
	exec_requete ("update fe_rubrique set rub_prec_id = " . $_POST["rubrique_prec_id"] . ", rub_ordre = " . $ordre->max . " where rub_id = " . $rubrique->rub_id, $connexion);
}

//==============================================================================
// Fonction deplacer_table : dï¿½place une table dans une autre rubrique
//==============================================================================
function deplacer_table ($connexion) {
	$liste_table = exec_requete ("select * from fe_table where tab_id = ". $_POST["table_id"], $connexion);
	$table = objet_suivant ($liste_table);
	exec_requete ("update fe_table set tab_ordre = tab_ordre - 1 where rub_id = " . $table->rub_id . " and tab_ordre > " . $table->tab_ordre, $connexion);
	$liste_ordre = exec_requete ("select max(tab_ordre) max from fe_table where rub_id = " . $_POST["rubrique_id"], $connexion);
	$ordre = objet_suivant ($liste_ordre);
	$ordre->max++;
	exec_requete ("update fe_table set rub_id = " . $_POST["rubrique_id"] . ", tab_ordre = " . $ordre->max . " where tab_id = " . $table->tab_id, $connexion);
}
//==============================================================================
// Fonction formulaire_fleche : génère un formulaire de classe choix rubrique
// use : $entite etant l'entite dont on souhaite modifié l'odre (rubrique, table....)
//		 $objet étant l'objet contenant l'entité en question
//		 $sens étant le sens dans lequel on souhaite faire bouger l'entité
//==============================================================================
function formulaire_fleche($entite,$objet,$sens){
$entite_abr=substr($entite,0,3);
//On initialise les valeurs du formulaire selon le entité en question
if ($entite=="rubrique"){
	$contenant_id = $objet->rub_prec_id;
	$ordre = $objet->rub_ordre;
	$item_id = $objet->rub_id;
}
else if ($entite=="table"){
	$contenant_id = $objet->rub_id;
	$ordre = $objet->tab_ordre;
	$item_id = $objet->tab_id;
}
else if ($entite=="ligne"){
	$contenant_id = $objet->tab_id;
	$ordre = $objet->lig_ordre;
	$item_id = $objet->lig_id;
}
else if ($entite=="colonne"){
	$contenant_id = $objet->tab_id;
	$ordre = $objet->col_ordre;
	$item_id = $objet->col_id;
}
//On initialise les valeurs du formulaire selon le sens
if ($sens=="haut"){
	$direction="fleche_verticale";
	$valeur="-1";
	$symbole="&uarr;";
}
else if ($sens=="bas"){
	$direction="fleche_verticale";
	$valeur="+1";
	$symbole="&darr;";
}
else if ($sens=="gauche"){
	$direction="fleche_horizontale";
	$valeur="-1";
	$symbole="&larr;";	
}
else if ($sens=="droite"){
	$direction="fleche_horizontale";
	$valeur="+1";
	$symbole="&rarr;";	
}
echo "
					<form class=\"boutton\" method=\"post\" action=\"admin.php?".str_replace("&","&amp;",$_SERVER['QUERY_STRING'])."\">
						<p class=\"boutton\">
						<input type=\"hidden\" name=\"action_facteur_emission\" value=\"change_ordre_{$entite}\" />
						<input type=\"hidden\" name=\"contenant_id\" value=\"{$contenant_id}\" />
						<input type=\"hidden\" name=\"ordre\" value=\"{$ordre}\" />
						<input type=\"hidden\" name=\"item_id\" value=\"{$item_id}\" />
						<input type=\"hidden\" name=\"position\" value=\"{$valeur}\" />
						<input class=\"{$direction}\" type=\"submit\" value=\"{$symbole}\" />
						</p>
					</form>\n";
}



?>
