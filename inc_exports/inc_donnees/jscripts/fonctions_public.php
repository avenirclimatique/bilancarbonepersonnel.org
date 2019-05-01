<?php 

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////FONCTIONS GESTION DE SESSION////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

//==============================================================================
// Fonction init_Session: initialisation des variables de sessions.
//==============================================================================
function init_Session ( $connexion) {
	if (!isset($_SESSION['tab_2_convert'])){
		$_SESSION['tab_2_convert'] = array(); /* Les tableaux ? convertir */
	}
	if (!isset($_SESSION['tab_2_convert'])){
		$_SESSION['col_2_convert'] = array(); /* les colonnes ? convertir */	
	}
	$_SESSION["mode"] = "public";
}
//==============================================================================
// Fonction recupere_POST : recupere les différents éléments du dernier formulaire reçu
//==============================================================================
function recupere_POST($connexion) {

	/* On récupère les données concernant les unités de convertions */
	if (isset($_POST["tableau_id"])){ 
		$_SESSION['tab_2_convert'][] = $_POST["tableau_id"];
			$liste_tab_col = exec_requete("select * from fe_colonne where tab_id = {$_POST["tableau_id"]}" , $connexion);
			while($tab_col = objet_suivant($liste_tab_col,$connexion)){
				if ( (isset($_SESSION['col_2_convert'][$tab_col->col_id]) == FALSE)  )
				{
					$_SESSION['col_2_convert'][$tab_col->col_id] = array ("unite_num","unite_deno1","unite_deno2");
				}
				if (isset($_POST["unite_numerateur_{$tab_col->col_id}"])){
					$_SESSION['col_2_convert'][$tab_col->col_id]['unite_num'] = ($_POST["unite_numerateur_{$tab_col->col_id}"]);
				}
				if (isset($_POST["unite_denominateur_1_{$tab_col->col_id}"])  ){
					$_SESSION['col_2_convert'][$tab_col->col_id]['unite_deno1'] = ($_POST["unite_denominateur_1_{$tab_col->col_id}"]);
				}
				if (isset($_POST["unite_denominateur_2_{$tab_col->col_id}"])  ){
					$_SESSION['col_2_convert'][$tab_col->col_id]['unite_deno2'] = ($_POST["unite_denominateur_2_{$tab_col->col_id}"]);
				}
				
			}			
	}
	/* On voit si on doit remettre les untiés par défaut */
	if (isset($_POST["RAZ"])){
					resetValue("-1",$connexion);
				}
	/* On récupère le nombre de chiffres significatifs */
	if (isset($_POST["nb_ch_signi"])){
		$_SESSION['chiffre_signi'] = intval($_POST["nb_ch_signi"]);	
	}
	/* On met les nombres de chiffres significatifs à 0 par défaut*/
	if (!isset($_SESSION['chiffre_signi']) || $_SESSION['chiffre_signi'] == NULL){
		$_SESSION['chiffre_signi'] = 3;	
	}
}
//==============================================================================
// Fonction resetValue : mets les valeurs par défaut
//==============================================================================
// Si la valeur que l'on passe en paramètre est l'id d'un tableau alors, le tableau correspondant est mis à 0
// Si la valeur est -1 alors, tous les tableaux seront remis à 0
function resetValue ($tableau_id, $connexion) {
	if ($tableau_id == (-1)){
		$_SESSION['tab_2_convert'] = array(); /* Met le tableau à convertir à 0 */
		$_SESSION['col_2_convert'] = array(); /* Mets les colonnes à 0 */
	}
	else {
		$liste_col = exec_requete("select * from fe_colonne where tab_id = {$tableau_id}" , $connexion);
		while($col = objet_suivant($liste_col,$connexion)){
			unset($_SESSION['col_2_convert'][$col->col_id]);
		} 
	}

}
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////FONCTIONS GESTION UNITE/////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

//==============================================================================
// Fonction uniteID2unite  : convertit un id d'unités en unités
//==============================================================================
function uniteID2unite ($unite_id, $connexion) {
	$liste_unite = exec_requete ("select unite_fond_symbole from fe_unite_fondamentale where unite_fond_id = {$unite_id}", $connexion);
	if ($unite = objet_suivant ($liste_unite)){
		return echo_MC('NOM_UNITE',$unite->unite_fond_symbole) ;
	}
	return "";
}

//==============================================================================
// Fonction coefficient : retourne le coefficient de conversion entre les 2 unités passées en paramètres
//==============================================================================
function coefficient ($unite_fond_depart_id, $unites_fond_fin_id, $connexion) {
	$liste_coefficient = exec_requete ("select * from fe_unite_conversion where unite_fond_depart_id = {$unite_fond_depart_id} and unite_fond_fin_id = {$unites_fond_fin_id} limit 1", $connexion);
	if ($coefficient = objet_suivant($liste_coefficient)) {
		return number_format($coefficient->coefficient,9,'.','');
	}
	else return "";
}
?>
