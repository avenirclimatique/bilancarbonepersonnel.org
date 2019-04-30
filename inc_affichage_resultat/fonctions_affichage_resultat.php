<?php 


require_once('lang/fr/fr_resultats.php');


//==========================================================================================
// Mise en garde si saisies non compl�tes
//==========================================================================================
function resultat_mise_en_garde_saisies_non_completes ($lien = true) //parametre a true si on affiche le lien
{
	echo '<p><strong class="mise_en_garde_acces_resultat">' . "\n" . 
		_('Attention : vous avez demand&eacute; � acc&eacute;der � la page de r&eacute;sultats alors que vos saisies sont incompl&egrave;tes') . '</strong>.' . "\n" .
		_('Le calculateur n&apos;&eacute;tant pas con&ccedil;u pour fournir des r�sultats partiels, les r&eacute;sultats pr&eacute;sent&eacute;s en page de r�sultat sont 
		certainement incomplets, et peut-�tre erron&eacute;s. Des erreurs peuvent avoir lieu dans les calculs et des messages d&apos;erreur 
		sont susceptibles d&apos;&ecirc;tre affich&eacute;s. Nous vous invitons &agrave; compl&eacute;ter toutes les pages du questionnaire avant d&apos;acc&eacute;der aux 
		r�sultats.') . "</p>\n";
	if ( $lien )	echo '<strong><a href="index.php?type_page=' . PAGE_RESULTAT . '&amp;action=confirmer" >';
	if ( $lien )	echo _('Acc�der � la page de r�sultats') . '</a></strong>' . "\n\n" ; 
}
//==========================================================================================
// Titre et �ventuelle mise en garde
//==========================================================================================
function affiche_titre_et_mise_en_garde ()
{
	
	// =================================
	// Titre
	echo '<h2>' . _('R&eacute;sultats de votre Bilan Carbone Personnel') . '</h2>' . "\n\n" ;
	// ==============================================
	// Si les saisies sont incompl�tes on met en garde (une seconde fois) l'utilisateur
	if ( in_array ( false , $_SESSION[PAGE_COMPLETE] ) )
		resultat_mise_en_garde_saisies_non_completes (false); //false parce qu'on ne r�affiche pas le lien
}
//  ======================================================================== 
// MISE EN GARDE
//  ======================================================================== 
function mise_en_garde($complete) {
	if ( in_array ( false , $complete ) )
		echo '<p><strong class="mise_en_garde_acces_resultat">' 
			. _('(attention&nbsp;: saisie incompl&egrave;te)') . '</strong></p>' . "\n\n" ; 
}
//  ======================================================================== 
// FIGURE 
//  ======================================================================== 
function affiche_figure ( $resultat )
{
	//  ======================================================================== 
	// figure
	// on met le tableau des r�sultats en variable de session le temps d'affichage de la figure
	$_SESSION[RESULTAT] = $resultat ; 
	//echo '<pre>' ; print_r ( $_SESSION ) ; echo '</pre>' ; 
	//  ======================
	// Afficher la figure
	echo '<img class="histogramme" src="./histogramme/histogramme.php" alt="Histogramme des &eacute;missions par cat&eacute;gorie" />';
	//  ======================================================================== 
	// L�gende de la figure
	$categorie = categorie () ; // cette fonction se trouve dans le fichier fonctions_bas_niveau_affichage_resultats.php
	$sous_categorie = sous_categorie () ; // cette fonction se trouve dans le fichier fonctions_bas_niveau_affichage_resultats.php
	// 
	echo '<ul class="legende_liste_categorie">' ; 
	foreach ( $categorie['nom'] as $cle_categorie => $nom_categorie )
	{
		echo "<li>\n" ; 
			echo '<strong>' . $nom_categorie . '</strong>' ; 
			echo '<ul class="legende_liste_sous_categorie">' . "\n" ; 
				foreach ( $sous_categorie [$cle_categorie] ['nom'] as $cle_sous_categorie => $nom_sous_categorie )
				{
					// D�termination des couleurs
					$couleur ['rouge'] = '200' ; $couleur ['vert'] = '20' ; $couleur ['bleu'] = '10' ; 
					echo '<li>' 
						. '<img src="./histogramme/carre_legende.php?' 
						. 'rouge=' . $sous_categorie [$cle_categorie]['rouge'][$cle_sous_categorie] 
						. '&amp;vert=' . $sous_categorie [$cle_categorie]['vert'][$cle_sous_categorie]
						. '&amp;bleu=' . $sous_categorie [$cle_categorie]['bleu'][$cle_sous_categorie] 
						. '" alt="(l�gende de la figure)" />' 
						. $nom_sous_categorie ; 
					echo "</li> \n" ; 
				}
			echo "</ul>\n" ; 
		echo "</li>\n" ; 
	}
	echo "</ul>\n\n" ; 
	echo "<div class='separateur'></div>\n\n" ; 	
	//================================
	// Explications de la figure
	//================================
	$numero_note = 1 ; 
	echo '<p>' . _('L&apos;<strong>histogramme ci-dessus</strong> repr&eacute;sente la r&eacute;partition des &eacute;missions de gaz &agrave; effet de serre engendr&eacute;es par vos consommations
	selon les quatre grandes cat�gories d&apos;&eacute;mission correspondant aux quatre parties du questionnaire') . '<sup class="lien_note" >[<a href="#note_histogramme" id="rev_note_histogramme">' . $numero_note . '</a>]</sup>.</p>' ; 
	$numero_note ++ ; 
	
	
	
	echo '<p>' . _('La <strong>barre horizontale rouge</strong> repr&eacute;sente la hauteur moyenne que les barres de l&apos;histogrammes devraient ne pas d�passer pour 
	que votre mode de vie puisse &ecirc;tre qualifi&eacute; de durable du point de vue des &eacute;missions de gaz &agrave; effet de serre que ce mode de vie engendre. 
	Cette barre horizontale correspond &agrave; une quantit&eacute; totale annuelle d&apos;&eacute;missions de gaz &agrave; effet de serre de ' . EMISSION_TERRIEN_DURABLE . ' kg equivalent Carbone') . '<sup class="lien_note" >[<a class="lien_note" href="#note_terrien_durable" id="rev_note_terrien_durable">' . $numero_note . '</a>]</sup>. ' . 
	_( 'Sa hauteur 
	correspond donc &agrave; ' . EMISSION_TERRIEN_DURABLE . ' kg equ. Carbone, divis&eacute;s par le nombre de barres de l&apos;histogramme. Autrement dit, si vous ne poss�dez qu&apos;un seul
	logement, la hauteur correspond &agrave; ' . EMISSION_TERRIEN_DURABLE . '/4 = ' . EMISSION_TERRIEN_DURABLE/4 . ' kg equ. C, si vous en poss�dez deux la hauteur correspond &agrave; ' . EMISSION_TERRIEN_DURABLE . '/5 = ' . EMISSION_TERRIEN_DURABLE/5 . ' kg equ. C, etc.') . '</p>' . "\n\n" ; 
	$numero_note ++ ; 
}
//  ======================================================================== 
//  Total �missions et commentaires sur ce total
//  ======================================================================== 
function affiche_total_et_commentaire ( $resultat )
{
	echo '<h2>' . _('Total de vos �missions') . '</h2>' . "\n\n" ; 
	//
	// mise en garde si saisie pas complete
	mise_en_garde($_SESSION[PAGE_COMPLETE]); 
	//
	echo '<p>' . _('La quantit&eacute; totale de gaz &agrave; effet de serre &eacute;mis en moyenne chaque ann&eacute;e dans l&apos;atmosph&egrave;re afin de satisfaire les consommations
	associ&eacute;es &agrave; votre mode de vie s&apos;&eacute;l&egrave;ve &agrave;&nbsp;:')."</p> \n" ;  

	echo "<p class='resultat_total'><span class='boite_enonce_resultat_total'><strong>" 
		. number_format( $resultat[TOUTES_CATEGORIES][TOTAL][EMISSION] , 0, ',', ' ')
		. '</strong>' . _('kilogrammes &eacute;quivalent Carbone, avec une incertitude de') . '&nbsp;:<strong>' 
		. round ( $resultat[TOUTES_CATEGORIES][TOTAL][INCERTITUDE] *100 )
		. "</strong> % .</span></p>\n\n" ; 

		/*
	echo "<table class='res_total'>\n" 
		. "<tr><th>Total</th><th>Incertitude</th> \n" 
		. "<tr><td class='emission'><strong>" 
		. number_format( $_SESSION['resultat']['toutes']['emissions_total'] , 0, ',', ' ')
		. "</strong> kilogrammes �quivalent Carbone</td><td class='incertitude'><strong>" 
		. round ( $_SESSION['resultat']['toutes']['incertitude']['emissions_total'] *100 )
		. "</strong> %</td></tr> \n" 
		. "</table>" ; 
	*/

	$numero_note = 3 ; 
	echo '<p>' . _('Cette quantit&eacute; est &eacute;quivalente � la quantit&eacute; de gaz &agrave; effet de serre &eacute;mise par une voiture de faible puissance effectuant un 
	trajet de&nbsp;:') . " \n" ; 
	$distance = $resultat[TOUTES_CATEGORIES][TOTAL][EMISSION] / 0.055 ; 
	// on suppose que la voiture �met 32.72g equ. C au kilom�tre petite voiture et trajet extra-urbain
	echo '<strong class="res_equiv">' 
		. number_format( $distance , 0, ',', ' ')
		. '</strong>' . _('kilom&egrave;tres') . '<sup class="lien_note" >[<a href="#note_equivalent_voiture" id="rev_note_equivalent_voiture">' . $numero_note . '</a>]</sup>.</p>'."\n" ; 
	$numero_note ++ ; 

	/*
	echo "<p>Cette quantit� est �galement �quivalente � la quantit� de gaz � effet de serre �mise en moyenne par la respiration d'un �tre humain
	pendant&nbsp;: \n" ; 
	$duree = $resultat[TOUTES_CATEGORIES][TOTAL][EMISSION] / 43 ; 
	// on est parti d'une �mission de 0.3 gramme de CO2 par minute
	echo "<strong class='res_equiv'>" 
		. number_format( $duree , 0, ',', ' ')
		. "</strong> ann�es<sup class='lien_note' >[<a href='#note_2' id='rev_note_2'>2</a>]</sup>.</p>\n\n" ; 

	echo "<p>Si une taxe de 1 euro par kg �quivalent carbone est mise en place afin de lutter contre les �missions de gaz � effet de serre, 
	ceci entra�nera un surco�t annuel pour l'ensemble de vos consommations individuelles de&nbsp;: " ;
	$surcout = $resultat[TOUTES_CATEGORIES][TOTAL][EMISSION] ; 
	echo "<strong class='res_equiv'>" 
		. number_format( $surcout , 0, ',', ' ')
		. "</strong> euros<sup class='lien_note' >[<a href='#note_3' id='rev_note_3'>3</a>]</sup> par an.</p>\n\n" ; 
	*/
	
	//  ======================================================================== 
	// Remarques
	//  ======================================================================== 
	echo '<h4>' . _('Remarques&nbsp;') . ': </h4>' . "\n\n" ; 
	echo "<ul>\n"
		. '<li>' . _('Ceci prend en compte <em>uniquement vos &eacute;missions individuelles</em> (et non celles des &eacute;ventuelles autres personnes qui 
		partagent votre foyer). </li>') . "\n"
		. '<li>' . _('Ceci <em>ne prend pas en compte</em> les &eacute;missions associ&eacute;es &agrave; l&apos;ensemble des services 
	publics dont vous pouvez b&eacute;n&eacute;ficier en tant que citoyen.') . "</li> \n" 
		. '<li>' . _('A l&apos;exception des trajets domicile-travail, ceci <em>ne prend pas en compte</em> les &eacute;missions engendr&eacute;es par 
		votre activit&eacute; professionnelle
		(par exemple le chauffage de votre bureau si vous travaillez dans un bureau).') . "</li> \n" 
		. '<li>' . _('Le total des &eacute;missions annuelles d&apos;un fran�ais moyen (obtenues en divisant le total des &eacute;missions annuelles nationales 
		par le nombre d&apos;habitants, en prenant donc en compte l&apos;ensemble des &eacute;missions de tous les secteurs d&apos;activit� du pays) s&apos;&eacute;l&egrave;ve &agrave;&nbsp;: '
		. '<strong>' . '2800' . '</strong>' . ' kg equ. C par an.' . '<sup class="lien_note" >[<a href="#note_moyenne_francaise" id="rev_note_moyenne_francaise">' . $numero_note . '</a>]</sup></li>') . " \n" ; 
		$numero_note ++ ;
		
	/*
		echo "<li>Si l'on admet (ce qui est tr�s optimiste, voir les pages d'explications) qu'une division par 4 des �missions 
		de gaz � effet 
		de serre en France conf�rerait un caract�re 'durable' � notre mode de vie et de production, alors on peut qualifier de
		'durable' (du seul point de vue des �missions de gaz � effet de serre), le mode de vie de tout individu dont le r�sultat 
		du Bilan Carbone Personnel ne d�passerait pas&nbsp;: " 
		. "<strong>" . "550" . "</strong>" . " kg equ. C par an.</li> \n" 
	*/

	echo "</ul> \n\n" ; 
}
//  ======================================================================== 
// REPARTITION GROSSIERE
//  ======================================================================== 
function affiche_repartition_grossiere ( $resultat )
{
	global $texte;
	echo'<h2>' . _('R&eacute;partition de vos &eacute;missions') . "</h2>\n\n" ; 
	//
	// mise en garde si saisie pas complete
	mise_en_garde($_SESSION[PAGE_COMPLETE]);

	//
	echo '<table class="res_categorie">' . " \n\n"
		. '<tr class="unite">'."\n"
		. '<td class="vide"></td>'
		. '<th class="unite" scope="col" >' . _('&Eacute;missions en kg equ. C') . '</th>'
		. '<th class="unite" scope="col" >' . _('&Eacute;missions en kg equ. CO<sub>2</sub>') . '</th>'
		. '<th class="unite" scope="col" >' . _('Incertitude en %') . '</th>'
		. "\n</tr>\n\n" ;
	
	
	

	$connexion = connexion (NOM, PASSE, BASE, SERVEUR);
	$result = exec_requete ("SELECT * FROM t_rubrique ORDER BY rub_ordre", $connexion);

	while ( $myrow = ligne_suivante ($result) ) {
		if ( $myrow['rub_est_repetee'] == 'true' )
			for( $i=1 ; $i <= $_SESSION["menu_nombre"][constant(strtoupper($myrow['rub_nom']))] ; $i+=1 )
			{
				$intitule = $texte['resultat_rubrique_' . $myrow['rub_nom']] . ' ' . $i ; 
				affiche_ligne ( 'pas_1ere'  ,  constant(strtoupper($myrow['rub_nom'])) , 'sous_total' , $intitule , $resultat[constant(strtoupper($myrow['rub_nom'])) . '_' . $i][constant('TOTAL_'.strtoupper($myrow['rub_nom']))] ) ; 
			}
		else
		affiche_ligne ( 'pas_1ere' ,  constant(strtoupper($myrow['rub_nom'])), 'sous_total' , $texte['resultat_rubrique_' . $myrow['rub_nom']] , $resultat[TOUTES_CATEGORIES][constant('TOTAL_'.strtoupper($myrow['rub_nom']))] ) ; 
	}





	echo "\n</table> \n\n" ; 

}
//  ======================================================================== 
// DETAIL
//  ======================================================================== 
function affiche_repartition_detaillee ( $resultat )
{
	global $texte;
	echo"<h2>D�tail de la r�partition de vos �missions</h2>\n\n" ; 
	mise_en_garde($_SESSION[PAGE_COMPLETE]);
	echo 	'<table class="res_detail" border=0>' . "\n\n" .  //
		'<tr class= "unite" >'."\n" .
			'<td class="vide" ></td>' . 
			'<td class="vide"></td>' . 
			'<th class="unite" scope="col" >' . _('&Eacute;missions en kg equ. C') . '</th>' .
			'<th class="unite" scope="col" >' . _('&Eacute;missions en kg equ. CO<sub>2</sub>') . '</th>' .
			'<th class="unite" scope="col" >' . _('Incertitude en %') . '</th>' . "\n" .
		'</tr>' . " \n\n" ;

	$connexion = connexion (NOM, PASSE, BASE, SERVEUR);
	$resultatRequeteRubriques = exec_requete ("SELECT * FROM t_rubrique ORDER BY rub_ordre", $connexion);

	while ( $myrow = ligne_suivante ($resultatRequeteRubriques) ) {
		$sql = "SELECT t_res_poste.id 'id', t_res_poste.nom 'nom', t_res_poste.rub_id 'rub_id', t_res_poste.sous_rub_id, t_res_sous_rubrique.nom 'nom_sous_rub', segmentation_id, t_segmentation.nom 'nom_seg', t_res_poste.est_repetee 'est_repetee'
			FROM t_res_poste 
			LEFT JOIN t_res_sous_rubrique ON t_res_poste.sous_rub_id = t_res_sous_rubrique.id
			LEFT JOIN t_segmentation ON t_res_poste.segmentation_id = t_segmentation.id
			WHERE t_res_poste.rub_id=".$myrow['rub_id']." ORDER BY ordre;";
		$resultatRequeteItems = exec_requete ($sql, $connexion);
		if ( $myrow['rub_est_repetee'] == 'true' ) {
			for( $i=1 ; $i <= $_SESSION[MENU_NOMBRE][$myrow['rub_nom']] ; $i++ )
			{
				mysql_data_seek ($resultatRequeteItems , 0 );
				$nombre_lignes = 1;

				while ( $maLignePoste = ligne_suivante($resultatRequeteItems) ) {
					if ($resultat[$myrow['rub_nom'] . '_' . $i][$maLignePoste['nom']]['affiche'] == true) $nombre_lignes++;
				}
				$sql = "SELECT COUNT( * )
					FROM t_rubrique
					LEFT JOIN t_res_sous_rubrique ON t_res_sous_rubrique.rub_id = t_rubrique.rub_id
					LEFT JOIN t_segmentation ON t_segmentation.rub_id = t_rubrique.rub_id
					WHERE t_rubrique.rub_id=".$myrow['rub_id'].";";	//Oui je sais elle a une sacr� gueule la requete mais elle compte tout d'un coup.
				list($nbr_rub) = mysql_fetch_row(mysql_query($sql));
				$nombre_lignes += $nbr_rub;			//Comme ca on a le nombre de ligne avec le nombre de rubriques


				mysql_data_seek ($resultatRequeteItems , 0 );
				$intitule = $texte['resultat_rubrique_' . $myrow['rub_nom']] . ' ' . $i ;
				$premiere = '1ere';

				echo '<tr class="'.$myrow['rub_nom'].'"><th rowspan="' . $nombre_lignes . '" class="categorie" scope="row" >' . $intitule . '</th>'."\n\n";


				$sous_rub_prec = '';
				$seg_prec = '';
				while ( $maLignePoste = ligne_suivante ($resultatRequeteItems) ) {
					if ( $sous_rub_prec != '' && $sous_rub_prec != $maLignePoste['sous_rub_id'] ) {

						affiche_ligne ( 'pas_1ere' ,  $myrow['rub_nom'] , 'sous_total' , $texte[$sous_rub_pres_nom] , $resultat[$myrow['rub_nom'] . '_' . $i][$sous_rub_pres_nom] ) ; 





					}
					if ( $seg_prec != $maLignePoste['segmentation_id'] && $maLignePoste['segmentation_id'] != '' ) {
						if ( $premiere == '1ere' )
							echo "<th class = 'intitule_sous_total' colspan='4' >".$texte[$maLignePoste['nom_seg']]."</th></tr>\n\n" ; 
						else
							echo "<tr class='".$myrow['rub_nom']."'><th class = 'intitule_sous_total' colspan='4' >".$texte[$maLignePoste['nom_seg']]."</th></tr>\n\n" ; 
						$premiere = 'pas_1ere';
					}


					if ( $resultat[$myrow['rub_nom'] . '_' . $i][$maLignePoste['nom']]['affiche'] ) affiche_ligne ( $premiere ,  $myrow['rub_nom'], 'normal' , $texte[$maLignePoste['nom']] , $resultat[$myrow['rub_nom'] . '_' . $i][$maLignePoste['nom']] ) ; 
					$premiere = 'pas_1ere';




					$sous_rub_prec = $maLignePoste['sous_rub_id'];
					$sous_rub_pres_nom = $maLignePoste['nom_sous_rub'];
					$seg_prec = $maLignePoste['segmentation_id'];
				}
				affiche_ligne ( 'pas_1ere' ,  $myrow['rub_nom'] , 'total' , $intitule , $resultat[$myrow['rub_nom'] . '_' . $i]['total_'.$myrow['rub_nom']] ) ; 
			}
		} else {
				$nombre_lignes = 1;
				while ( $maLignePoste = ligne_suivante($resultatRequeteItems) ) {
					if ( $maLignePoste['est_repetee'] == 'true' ) {
						$nombre_lignes+=$_SESSION[MENU_NOMBRE][$maLignePoste['nom']];
					} else
						if ($resultat[$myrow['rub_nom']][$maLignePoste['nom']]['affiche'] == true) $nombre_lignes++;
				}
				$sql = "SELECT COUNT( * )
					FROM t_rubrique
					LEFT JOIN t_res_sous_rubrique ON t_res_sous_rubrique.rub_id = t_rubrique.rub_id
					LEFT JOIN t_segmentation ON t_segmentation.rub_id = t_rubrique.rub_id
					WHERE t_rubrique.rub_id=".$myrow['rub_id'].";";	//Oui je sais elle a une sacr� gueule la requete mais elle compte tout d'un coup.
				list($nbr_rub) = mysql_fetch_row(mysql_query($sql));
				$nombre_lignes += $nbr_rub;			//Comme ca on a le nombre de ligne avec le nombre de rubriques

				@mysql_data_seek ($resultatRequeteItems , 0 );
				$intitule = $texte['resultat_rubrique_' . $myrow['rub_nom']] ;
				$premiere = '1ere';

				echo '<tr class="'.$myrow['rub_nom'].'"><th rowspan="' . $nombre_lignes . '" class="categorie" scope="row" >' . $intitule . '</th>'."\n\n";

				$sous_rub_prec = '';
				$seg_prec = '';
				while ( $maLignePoste = ligne_suivante ($resultatRequeteItems) ) {	//Pour chacun des postes de la rubrique
					if ( $sous_rub_prec != '' && $sous_rub_prec != $maLignePoste['sous_rub_id'] ) {
						affiche_ligne ( $premiere ,  $myrow['rub_nom'] , 'sous_total' , $texte[$sous_rub_pres_nom] , $resultat[$myrow['rub_nom']][$sous_rub_pres_nom] ) ; 
						$premiere = 'pas_1ere';					
					}
					if ( $seg_prec != $maLignePoste['segmentation_id'] && $maLignePoste['segmentation_id'] != '' ) {
						if ( $premiere == '1ere' )
							echo "<th class = 'intitule_sous_total' colspan='4' >".$texte[$maLignePoste['nom_seg']]."</th></tr>\n\n" ; 
						else
							echo "<tr class='".$myrow['rub_nom']."'><th class = 'intitule_sous_total' colspan='4' >".$texte[$maLignePoste['nom_seg']]."</th></tr>\n\n" ; 
						$premiere = 'pas_1ere';
					}
					if ( $maLignePoste['est_repetee'] == 'true' ) {
						for ( $j=1 ; $j <= $_SESSION[MENU_NOMBRE][$maLignePoste['nom']] ; $j++ ){
							if ( $resultat[$myrow['rub_nom']][$maLignePoste['nom'] . '_' . $j]['affiche'] ) affiche_ligne ( $premiere ,  $myrow['rub_nom'], 'normal' , $texte[$maLignePoste['nom']] , $resultat[$myrow['rub_nom']][$maLignePoste['nom'] . '_' . $j] ) ; 
							$premiere = 'pas_1ere';
						}
					} else {
						if ( $resultat[$myrow['rub_nom']][$maLignePoste['nom']]['affiche'] ) { 
							affiche_ligne ( $premiere ,  $myrow['rub_nom'], 'normal' , $texte[$maLignePoste['nom']] , $resultat[$myrow['rub_nom']][$maLignePoste['nom']] ) ; 
							$premiere = 'pas_1ere';
						}
					}
					
					$sous_rub_prec = $maLignePoste['sous_rub_id'];
					$sous_rub_pres_nom = $maLignePoste['nom_sous_rub'];
					$seg_prec = $maLignePoste['segmentation_id'];
				}
				if ( $sous_rub_prec != '' ) {
					affiche_ligne ( $premiere ,  $myrow['rub_nom'] , 'sous_total' , $texte[$sous_rub_pres_nom] , $resultat[$myrow['rub_nom']][$sous_rub_pres_nom] ) ; 
					$premiere = 'pas_1ere';					
				}
				affiche_ligne ( $premiere ,  $myrow['rub_nom'] , 'total' , $intitule , $resultat[$myrow['rub_nom']]['total_'.$myrow['rub_nom']] ) ; 
		}
		echo '</tr>'."\n";
	}
	echo "\n<!-- fin consommation -->\n" ; 
	echo "\n</table>\n\n" ; 
}
//  ======================================================================== 
// Notes de bas de page
//  ======================================================================== 
function affiche_notes_bas_de_page ()
{
	echo "<div id='notes_bas_de_page' >  <!-- d�but des notes de bas de page r�sultat -->\n\n" ; 
	$numero_note = 1 ; 
	echo "<p><span class='lien_note'>[<a href='#rev_note_histogramme' id='note_histogramme'>" . $numero_note . "</a>]</span> " 
		. "Chaque barre est constitu�e de blocs empil�s dont les couleurs correspondent � diff�rents usages ou consommations. Les correspondances entre les couleurs et les usages ou consommations sont donn�es par la l�gence ci-dessus. Les valeurs exactes des �missions de gaz � effet de serre pour chaque usage ou consommation sont fournies dans les tableaux au bas de la page. La hauteur de chaque bloc correspond � la quantit� de gaz � effet de serre �mis du fait de l'usage ou de la consommation correspondant(e). Deux blocs successifs sont s�par�s par une barre horizontale noire.</p>\n"
		. "<p>Si vous avez indiqu� que vous disposiez de plusieurs logements, les �missions associ�es � chaque logement sont 
	repr�sent�es de mani�re ind�pendante (une barre de l'histogramme par logement).</p>\n" 
		. "<p>Le segment vertical centr� en haut de chacune des barres repr�sente l'incertitude sur le montant des �missions de gaz � effet de serre repr�sent� par cette barre. Autrement dit, pour chacune des barres de l'histogramme, il est peu vraissemblable que le montant r�el de vos �missions de gaz � effet de serre pour le logement ou la cat�gorie correspondant(e) soit plus �lev� que la valeur correspondant � l'extr�mit� sup�rieure de ce segment, comme il est peu vraissemblable que ce montant soit moins �lev� que la valeur correspondant � son extr�mit� inf�rieure.</p>\n" ; 
	$numero_note ++ ; 

	echo "<p><span class='lien_note'>[<a href='#rev_note_terrien_durable' id='note_terrien_durable'>" . $numero_note . "</a>]</span> " 
		. "Ce chiffre est obtenu en divisant par quatre le montant le total des �missions annuelles brutes d'un fran�ais en moyenne (2800 kg equivalent Carbone, voir note ci-dessous). En r�alit�, cette division par quatre est pour partie une convention, car il n'existe pas de d�finition scientifique rigoureuse de la quantit� maximale de gaz � effet de serre que l'humanit� peut se permettre d'�mettre chaque ann�e pour que son mode de vie puisse �tre qualifi� de durable. Pour davantage d'explications, voir la <a href='index.php?type_page=faq&amp;page=quel_bc_soutenable'>page de la FAQ sur cette question</a>.</p>\n" ; 
	$numero_note ++ ; 
	
	echo "<p><span class='lien_note'>[<a href='#rev_note_equivalent_voiture' id='note_equivalent_voiture'>" . $numero_note . "</a>]</span> " 
		. "Calcul bas� sur une automobile �mettant 55 grammes equivalent carbone (ou encore 202 grammes de C02) par kilom�tre. 
	La moyenne des �missions affich�es par les constructeurs pour les v�hicules neufs est actuellement d'environ 150 grammes 
	de CO2 par kilom�tre (si cette moyenne avait �t� retenue, le nombre de kilom�tres aurait donc �t� sup�rieur d'environ 30%). 
	Mais, outre que ces �missions th�oriques sont toujours d�pass�es en pratique, elles ne tiennent 
	compte ni des �missions li�es � la fabrication de la voiture, ni de celles li�es � son entretien, ni enfin de celles li�es 
	au raffinage et au transport du carburant. Le calcul propos� ici est donc bas� sur un chiffre plus proche 
	des �missions moyennes <em>r�elles</em> au kilom�tre des voitures actuelles, 
	et qui tient compte des �missions li�es � la fabrication des v�hicules (mais pas � leur entretien), ainsi qu'au raffinage 
	et au transport du carburant. 
	</p>\n\n" ; 
	$numero_note ++ ; 

	echo "<p><span class='lien_note'>[<a href='#rev_note_moyenne_francaise' id='note_moyenne_francaise'>" . $numero_note . "</a>]</span> " 
		. "Le chiffre de 2800 kg equivalent Carbone est obtenu en divisant le total des �missions <em>brutes</em> de gaz � effet de serre ayant lieu chaque ann�e sur le territoire fran�ais par le nombre d'habitants de ce territoire. Ce chiffre repr�sente donc le total des �missions annuelles brutes de gaz � effet de serre d'un citoyen fran�ais en moyenne. Pour davantage d'explications, voir la <a href='index.php?type_page=faq&amp;page=quel_bc_moyenne_francaise'>page de la FAQ sur cette question</a>.</p>\n\n" ; 
	$numero_note ++ ; 
	//
	echo "</div>  <!-- fin des notes de bas de page r�sultat -->\n\n" ; 
	/*
	echo "Le calcul correspond � une automobile �mettant 32.7 grammes equivalent carbone (ou encore 120 grammes de C02) par kilom�tre, 
	ce qui est le cas aujourd'hui pour une voiture faiblement �mittrice de CO2 (la moyenne des v�hicules neufs est actuellement de 150 grammes
	de CO2 par kilom�tres) et qui correspond � un objectif de l'Union Europ�enne pour la moyenne des v�hicules neufs vendus en 2012. 
	Ce chiffre ne tient toutefois pas 
	compte des �missions li�es � la fabrication de la voiture, ni de celles li�es � son entretien, ni enfin de celles li�es 
	au raffinage et au transport du combustible (en tenir compte augmenterait l'�mission au kilom�tre d'environ 30%, et diminuerait donc la
	distance parcourue dans la m�me proportion). 
	</p>" ; 
	*/

	/*
	echo "<p>[<a href='#rev_note_2' id='note_2'>2</a>] 
	Un �tre humain au repos �met en moyenne (par sa respiration) environ 0.3 grammes de C02 (soit 0.082 grammes �quivalent 
	carbone) par minute. Cette comparaison a pour but de donner un ordre de grandeur de que repr�sente le total de vos 
	�missions de gaz � effet de serre une fois traduites en termes de m�tabolisme humain. 
	Les rendements du Vivant �tant souvent tr�s faibles,
	le total de votre consommation d'�nergie repr�sente donc bien davantage que l'�nergie 
	m�canique pouvant �tre restitu�e par un �tre humain pendant une telle p�riode. 
	</p>\n\n<p>
	NB&nbsp;: 
	les �missions de CO2 produites par la respiration humaine ne proviennent pas d'un stock de carbone fossile, 
	mais directement du CO2 de l'atmosph�re
	synth�tis� par les plantes agricoles. 
	Par cons�quent elles ne contribuent pas en tant que telles � l'augmentation de la concentration
	de CO2 dans l'atmosph�re ni donc � l'effet de serre (ce qui contribue � l'effet de serre, ce sont les �missions de CO2 d'origine
	d'origine fossile et d'autres gaz � effet de serre - m�thane, protoxyde d'azote - <em>induites</em> 
	par l'agriculture et l'�levage). 
	</p>\n\n" ; 
	// echo " nombre de voitures : " . $nombre_voiture  ; echo " nombre de deux_roues : " . $nombre_deux_roues  ; echo " nombre de vol_avion : " . $nombre_vol_avion  ; 
		
	echo "<p>[<a href='#rev_note_3' id='note_3'>3</a>] 
	Une telle taxe correspondrait en gros (pour la France) � un doublement de la taxe int�rieure sur les produits p�troliers, ou � un 
	quadruplement du prix du p�trole sur les march�s internationaux (ou � un peu des deux), ce qui n'est pas invraisemblable
	sur une � deux d�cennies 
	(il faut imaginer cette hausse corrig�e de l'inflation et �ventuellement corrig�e de la hausse 
	du pouvoir d'achat sur la p�riode, ce qui se traduit en une hausse des co�ts, exprim�s en euros dans dix ou vingt ans, encore plus importante). 
	Rappelons que le prix du baril de p�trole brut vient d'�tre multipli� par 6 en moins de 10 ans (moins de 15 dollars en janvier 1999, 
	plus de 90 dollars en octobre 2007)." ; 
	*/
	
	/*
	echo "Un surco�t �quivalent pour le consommateur proviendrait d'une hausse d'environ 1000 euros du cours de la tonne �quivalent 
	Carbone sur les mach�s internationaux 
	de permis d'�mission. Ce cours est d'environ 1.25 euros en octobre 2007 (autrement dit �mettre des gaz � effet de serre est � cette date, 
	et au niveau international, essentiellement gratuit). " ; 
	*/
}



?>
