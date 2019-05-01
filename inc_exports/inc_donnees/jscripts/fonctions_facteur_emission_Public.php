<?php 

//==============================================================================
// Fonction menu_rubrique : affiche l'arborescence des rubriques
//==============================================================================
function menu_rubrique ($connexion, $rubrique_id=-1) {
	if ($rubrique_id == -1){
			echo "	
					<ul id=\"menu_facteur_emission\">
						<li>
							<p class=\"choix_menu\"><a href=\"index.php?type_page=consultation_base&amp;page=".FACTEURS_EMISSION."&amp;menu=facteur_emission\">"._("Données")."</a></p>
							<p class=\"choix_menu\"><a href=\"index.php?type_page=consultation_base&amp;page=".FACTEURS_EMISSION."&amp;menu=unites\">"._('Unités')."</a></p>
							<div class=\"retour_ligne\">&nbsp;</div>
							
						</li>
						<li  class=\"titre_menu_consultation\"><h1>Données</h1></li>";
	}
	
	$liste_rubrique = exec_requete ("select * from fe_rubrique where rub_prec_id = {$rubrique_id} order by rub_ordre asc", $connexion);
	if (mysql_num_rows($liste_rubrique) > 0){
		
		while ($rubrique = objet_suivant ($liste_rubrique)) {
			$liste_table = exec_requete ("select * from fe_table where rub_id = {$rubrique->rub_id}", $connexion);
			echo "
						<li>";
			if (mysql_num_rows($liste_table)==0){ /* Si la rubrique est une rubrique Principale ex: logement, alimentation.. */
				echo "
					<div class=\"titre\"><strong class='nom'>";
				echo echo_MC('NOM_RUBRIQUE',$rubrique->rub_nom);
				echo "
					</strong></div>";
			}
			else { /* Sinon c'est une table avec des facteurs d'?missions */
				echo "	
							<a class=\"nom\" href=\"index.php?type_page=consultation_base&amp;page=".FACTEURS_EMISSION."&amp;menu=facteur_emission&amp;rubrique_id={$rubrique->rub_id}\">".echo_MC('NOM_RUBRIQUE',$rubrique->rub_nom)."</a><br/>";
			}
			$liste_rubrique2 = exec_requete ("select * from fe_rubrique where rub_prec_id = {$rubrique->rub_id} order by rub_ordre asc", $connexion);
			if (mysql_num_rows($liste_rubrique2) > 0){
				echo "
					<ul>";
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
						<li>";
		choixChiffresSignificatifs ();
		echo "
						</li>
					</ul>";
		
	}	
}


//==============================================================================
// Fonction afficheTableauRubrique : Affiche tous les tableaux d'une rubrique
//==============================================================================
function afficheTableauRubrique ($rubrique_id, $connexion) {
	
	$liste_tableau = exec_requete ("select * from fe_table where rub_id = {$rubrique_id} order by tab_ordre asc", $connexion);
	echo "
		<div class=\"Bloc_donnees\"><h2>";
	echo arborescence_rubrique ($rubrique_id, $connexion);
	echo "</h2>";
	while ($tableau = objet_suivant ($liste_tableau)) {
	echo"
		<div class=\"Bloc_tableau\">
		<form  method=\"post\" action=\"index.php?type_page=consultation_base&amp;page=".FACTEURS_EMISSION."&amp;menu=facteur_emission&amp;rubrique_id={$rubrique_id}\">";
		afficheTableauPublic($tableau, $connexion,$rubrique_id);
		echo "
		</form></div>";
	}
	echo"</div>";
}

//==============================================================================
// Fonction afficheTableauPublic_Conversion : Affiche la ligne de conversion des unités
//==============================================================================
function afficheTableauPublic_Conversion ($tableau, $connexion,$rubrique_id) {
	
	echo "
			<td class=\"titre_ligne\">
			<input name=\"button\" type=\"submit\" value=\""._('Convertir')."\"/><br/></&>
			<a href=\"index.php?type_page=consultation_base&amp;page=".FACTEURS_EMISSION."&amp;menu=facteur_emission&amp;rubrique_id={$rubrique_id}&amp;tableauDef={$tableau->tab_id}\"> "._('Unité par défaut')."</a>
			</td>";
	     
	$liste_colonne = exec_requete("select * from fe_colonne where tab_id = {$tableau->tab_id} order by col_ordre asc", $connexion);	
	while ($colonnes = objet_suivant($liste_colonne)) {

	echo "
			<td class=\"colonne\">";
	/* Le numérateur */
	/* On selectionne tous les unités dans laquelle elle peut être convertie */
	$liste_nature_num = exec_requete("select * from fe_nature_unite, fe_unite_fondamentale where fe_unite_fondamentale.unite_fond_id = {$colonnes->unite_numerateur_id} and fe_nature_unite.nature_unite_id = fe_unite_fondamentale.unite_fond_nature_unite_id",$connexion);
	$nature_num = objet_suivant($liste_nature_num);
		
	$liste_unite_fond_num = exec_requete("select unite_fond_fin_id from fe_unite_conversion 
			where unite_fond_depart_id = {$colonnes->unite_numerateur_id}" , $connexion);
	
	echo "
				<p><select name = \"unite_numerateur_{$colonnes->col_id}\"";
	if ($nature_num->nature_unite_type=="discret"){
		echo  " disabled=\"disabled\"";
	}
	echo ">";
	while ( $unite_fond_num = objet_suivant($liste_unite_fond_num)){ 
		echo "
				<option";
		/* On choisi par défaut l'unité contenue dans la varible de session*/
		if  (isset($_SESSION['col_2_convert'][$colonnes->col_id]['unite_num']) ){
			if ($_SESSION['col_2_convert'][$colonnes->col_id]['unite_num'] == $unite_fond_num->unite_fond_fin_id){
				echo " selected=\"selected\" ";
			}
		}
		else if( $unite_fond_num->unite_fond_fin_id == $colonnes->unite_numerateur_id) {
			echo " selected=\"selected\"  ";
		}
		echo " value=\"{$unite_fond_num->unite_fond_fin_id}\"> " ;
		echo uniteID2unite($unite_fond_num->unite_fond_fin_id,$connexion) ;
		echo " </option>";	
	}
	
	echo "	
				</select></p>";
	/* Le premier d?nominateur */
	$liste_nature_dem1 = exec_requete("select * from fe_nature_unite, fe_unite_fondamentale where 			fe_unite_fondamentale.unite_fond_id = {$colonnes->unite_denominateur_1_id} and fe_nature_unite.nature_unite_id = fe_unite_fondamentale.unite_fond_nature_unite_id",$connexion);
	$nature_dem1 = objet_suivant($liste_nature_dem1);

	$liste_unite_fond_dem1= exec_requete("select unite_fond_fin_id from fe_unite_conversion 
			where unite_fond_depart_id = {$colonnes->unite_denominateur_1_id}" , $connexion);
	 /* On selectionne tous les unités dans laquelle elle peut ?tre convertie */
	 if ( mysql_num_rows($liste_unite_fond_dem1) != 0){
		echo "	
				"._('par')."
				<p><select name = \"unite_denominateur_1_{$colonnes->col_id}\"";
		if ($nature_dem1->nature_unite_type == "discret" ){
			echo  " disabled=\"disabled\"";
		}
		echo ">";
		while ( $unite_fond_dem1 = objet_suivant($liste_unite_fond_dem1)){ 
		echo "	
				<option ";
			if  (isset($_SESSION['col_2_convert'][$colonnes->col_id]["unite_deno1"]) ){
				if ($_SESSION['col_2_convert'][$colonnes->col_id]['unite_deno1'] == $unite_fond_dem1->unite_fond_fin_id){
					echo " selected=\"selected\" ";
				}
			}
			else if( $unite_fond_dem1->unite_fond_fin_id == $colonnes->unite_denominateur_1_id) {
				echo " selected=\"selected\" ";
			}
			echo " value = \"{$unite_fond_dem1->unite_fond_fin_id}\"> " ;
			echo uniteID2unite($unite_fond_dem1->unite_fond_fin_id,$connexion) ;
			echo " </option>";
		}
		echo "
				</select></p>";
	}

		/* Le deuxi?me d?nominateur */
		$liste_nature_dem2 = exec_requete("select * from fe_nature_unite, fe_unite_fondamentale where 			fe_unite_fondamentale.unite_fond_id = {$colonnes->unite_denominateur_2_id} and fe_nature_unite.nature_unite_id = fe_unite_fondamentale.unite_fond_nature_unite_id",$connexion);
		$nature_dem2 = objet_suivant($liste_nature_dem2);
		
		$liste_unite_fond_dem2 = exec_requete("select unite_fond_fin_id from fe_unite_conversion where unite_fond_depart_id = {$colonnes->unite_denominateur_2_id}" , $connexion); 
		/* On selectionne tous les unités dans laquelle elle peut ?tre convertie */
		if ( mysql_num_rows($liste_unite_fond_dem2) != 0){
			echo "
				.
				<p><select name = \"unite_denominateur_2_{$colonnes->col_id}\"";
			if ($nature_dem2->nature_unite_type=="discret"){
				echo " disabled=\"disabled\"";
			}
			echo ">";
			while ( $unite_fond_dem2 = objet_suivant($liste_unite_fond_dem2)){ 
				echo "
					<option";
				/*On v?rifie si une conversion a été demand? par l'utilisateur */
				if  (isset($_SESSION['col_2_convert'][$colonnes->col_id]['unite_deno2']) ){
					if ($_SESSION['col_2_convert'][$colonnes->col_id]['unite_deno2'] == $unite_fond_dem2->unite_fond_fin_id){
						echo " selected=\"selected\" ";
					}
				}
				/*sinon on trouve l'unités par d?faut */
				else if( $unite_fond_dem2->unite_fond_fin_id == $colonnes->unite_denominateur_2_id) {
					echo " selected=\"selected\" ";
				}
				echo " value = \"{$unite_fond_dem2->unite_fond_fin_id}\"> ";
				echo uniteID2unite($unite_fond_dem2->unite_fond_fin_id,$connexion) ;
				echo "</option>";
			}
			
			echo "
				</select></p>";
		}
		echo "
				<input type=\"hidden\" name=\"tableau_id\" value=\"{$tableau->tab_id}\" />
			</td>";
	} /* Fin du parcours des colonnes */
	
	echo "
			<td class=\"colonne\"> &nbsp;</td>";
	
}

//==============================================================================
// Fonction afficheTableauPublic : Affiche un tableau d'une rubrique
//==============================================================================
function afficheTableauPublic ($tableau, $connexion,$rubrique_id) {
		
		echo "<h2>".echo_MC('NOM_TABLE',$tableau->tab_nom)."</h2>";
		$liste_tableau = exec_requete ("select * from fe_table where tab_id = {$tableau->tab_id}", $connexion);
		$tableau = objet_suivant ($liste_tableau);/* On prend le tableau d'id idTableau */
		echo "
			<table class=\"tableau\" border=\"1\">
			<thead>
			<tr>";
						
		afficheTableauPublic_Conversion ($tableau, $connexion,$rubrique_id);
		echo "
			</tr>
			<tr>
				<td class=\"titre_ligne\">".echo_MC('TITRE_LIGNE',$tableau->tab_titre_ligne)."</td>";
			$liste_colonne = exec_requete("select * from fe_colonne where tab_id = {$tableau->tab_id} order by col_ordre asc", $connexion);
			while ($colonnes = objet_suivant($liste_colonne)) {
				echo "
				<td class=\"colonne\">";
			
				if (isset($_SESSION['col_2_convert'][$colonnes->col_id]['unite_num'])) {
					echo uniteID2unite($_SESSION['col_2_convert'][$colonnes->col_id]['unite_num'],$connexion);
				}
				else {
					echo uniteID2unite($colonnes->unite_numerateur_id,$connexion);
				}
				if (isset($_SESSION['col_2_convert'][$colonnes->col_id]['unite_deno1']) && 
							$_SESSION['col_2_convert'][$colonnes->col_id]['unite_deno1']>0){
					echo " "._('par')."&nbsp;";
					echo uniteID2unite($_SESSION['col_2_convert'][$colonnes->col_id]['unite_deno1'],$connexion);
				}
				else if( $colonnes->unite_denominateur_1_id > 0){
					echo " "._('par')."&nbsp;";
					echo uniteID2unite($colonnes->unite_denominateur_1_id,$connexion);
				}
				
				if (isset($_SESSION['col_2_convert'][$colonnes->col_id]['unite_deno2']) && 
							$_SESSION['col_2_convert'][$colonnes->col_id]['unite_deno2']>0){
					echo " . ";
					echo uniteID2unite($_SESSION['col_2_convert'][$colonnes->col_id]['unite_deno2'],$connexion);
				}
				else if($colonnes->unite_denominateur_2_id > 0){
					echo " . ";
					echo uniteID2unite($colonnes->unite_denominateur_2_id,$connexion);
				}
					
				
				if (echo_MC('COMMENTAIRE_COL',$colonnes->col_commentaire)!=""){
				echo "<p class=\"commentaire\">".echo_MC('COMMENTAIRE_COL',$colonnes->col_commentaire)."</p>";
			}/* Le commentaire */
			echo "</td>";
			}
			echo "
				<td class=\"incertitude\"> "._('Incertitude (en %)')."</td>
			</tr>
			</thead>
			<tbody>"; /* Contenu du tableau */
	$liste_ligne = exec_requete("select * from fe_ligne where tab_id = {$tableau->tab_id} order by lig_ordre asc", $connexion);
	
	while ($ligne = objet_suivant($liste_ligne)) { /* Pour chaque ligne du tableau */
		echo "
			<tr>
		     		<td class=\"titre_ligne\">".echo_MC('NOM_LIGNE',$ligne->lig_nom)."</td>";
		if ( mysql_data_seek($liste_colonne, 0) != FALSE ){ /* On remet le  pointeur des résultats de colonnes à  0 */
			while ($colonnes = objet_suivant($liste_colonne)){ 
				$liste_valeur = exec_requete("select * from fe_valeur 
					where col_id = {$colonnes->col_id} and lig_id = {$ligne->lig_id}", $connexion);
				while ($valeur = objet_suivant($liste_valeur)){ /* Pour chaque valeur trouv?e */
					echo "
				<td class=\"colonne\">"; /* On rajoute une colonne */
					$var_Coef_num = 1;
					$var_Coef_deno1 = 1;
					$var_Coef_deno2 = 1;
					
					if  (isset($_SESSION['col_2_convert'][$colonnes->col_id]['unite_num']) ){ 
						/* D'abord le num?rateur */
						$liste_coef = exec_requete("select * from fe_unite_conversion 
							where unite_fond_depart_id={$colonnes->unite_numerateur_id} and 
							unite_fond_fin_id = {$_SESSION['col_2_convert'][$colonnes->col_id]['unite_num']} ", $connexion);
						if ($coef = objet_suivant($liste_coef, $connexion)){
							$var_Coef_num = $coef->coefficient;
						}
						/* Ensuite le premier d?nominateur */
						if (($colonnes->unite_denominateur_1_id != -1) && isset($_SESSION['col_2_convert'][$colonnes->col_id]['unite_deno1'])){
							$liste_coef = exec_requete("select * from fe_unite_conversion where unite_fond_depart_id= 						 	{$colonnes->unite_denominateur_1_id} and unite_fond_fin_id = {$_SESSION['col_2_convert'][$colonnes->col_id]['unite_deno1']} ", $connexion);
							if ($coef = objet_suivant($liste_coef, $connexion)){
								$var_Coef_deno1= $coef->coefficient;
							}
						}
						/*Enfin le deuxi?me éventuelle d?nominateur */
						if (($colonnes->unite_denominateur_2_id != -1) && isset($_SESSION['col_2_convert'][$colonnes->col_id]['unite_deno2'])){
							$liste_coef = exec_requete("select * from fe_unite_conversion where unite_fond_depart_id= {$colonnes->unite_denominateur_2_id} and unite_fond_fin_id = {$_SESSION['col_2_convert'][$colonnes->col_id]['unite_deno2']} ", $connexion);
							if ($coef = objet_suivant($liste_coef, $connexion)){
								$var_Coef_deno2= $coef->coefficient;
							}
						}
					}
					if ( ($var_Coef_num == NULL)  ){
						echo _('Conversion impossible... pour l\'instant');
					}
					else if ( ($var_Coef_deno1 == NULL) || ($var_Coef_deno2 == NULL) ){
						echo _('DIVISION PAR 0??!!');
					}
					else {	
						if (!($valeur->valeur == NULL)){
							$resultat = (double)($valeur->valeur)*(double)($var_Coef_num) *( 1/((double)($var_Coef_deno2)*(double)($var_Coef_deno1)));
							$n = $_SESSION['chiffre_signi'];
							$n = (integer)$n;
							echo to_X_chif_signi($resultat,$n);
						}
						else{
							echo " - ";
						}
					}
					
					echo "&nbsp;</td>";
				}
			}
			/* On rajoute la colonne incertitude */
			echo "
				<td class=\"colonne\">{$ligne->incertitude}</td>
			</tr>";
		}
	}
	echo "	
			</tbody>
		</table>";
	// On va ajouter une zone de commentaire
	if (echo_MC('COMMENTAIRE',$tableau->tab_commentaire)!=''){
		echo "
		<p >".html_entity_decode($tableau->tab_commentaire)."</p>";
	}

	
}

?>
