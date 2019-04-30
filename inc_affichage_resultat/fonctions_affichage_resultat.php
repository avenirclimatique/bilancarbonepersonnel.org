<?php 


require_once('lang/fr/fr_resultats.php');


//==========================================================================================
// Mise en garde si saisies non complètes
//==========================================================================================
function resultat_mise_en_garde_saisies_non_completes ($lien = true) //parametre a true si on affiche le lien
{
	echo '<p><strong class="mise_en_garde_acces_resultat">' . "\n" . 
		_('Attention : vous avez demand&eacute; à acc&eacute;der à la page de r&eacute;sultats alors que vos saisies sont incompl&egrave;tes') . '</strong>.' . "\n" .
		_('Le calculateur n&apos;&eacute;tant pas con&ccedil;u pour fournir des résultats partiels, les r&eacute;sultats pr&eacute;sent&eacute;s en page de résultat sont 
		certainement incomplets, et peut-être erron&eacute;s. Des erreurs peuvent avoir lieu dans les calculs et des messages d&apos;erreur 
		sont susceptibles d&apos;&ecirc;tre affich&eacute;s. Nous vous invitons &agrave; compl&eacute;ter toutes les pages du questionnaire avant d&apos;acc&eacute;der aux 
		résultats.') . "</p>\n";
	if ( $lien )	echo '<strong><a href="index.php?type_page=' . PAGE_RESULTAT . '&amp;action=confirmer" >';
	if ( $lien )	echo _('Accéder à la page de résultats') . '</a></strong>' . "\n\n" ; 
}
//==========================================================================================
// Titre et éventuelle mise en garde
//==========================================================================================
function affiche_titre_et_mise_en_garde ()
{
	
	// =================================
	// Titre
	echo '<h2>' . _('R&eacute;sultats de votre Bilan Carbone Personnel') . '</h2>' . "\n\n" ;
	// ==============================================
	// Si les saisies sont incomplètes on met en garde (une seconde fois) l'utilisateur
	if ( in_array ( false , $_SESSION[PAGE_COMPLETE] ) )
		resultat_mise_en_garde_saisies_non_completes (false); //false parce qu'on ne réaffiche pas le lien
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
	// on met le tableau des résultats en variable de session le temps d'affichage de la figure
	$_SESSION[RESULTAT] = $resultat ; 
	//echo '<pre>' ; print_r ( $_SESSION ) ; echo '</pre>' ; 
	//  ======================
	// Afficher la figure
	echo '<img class="histogramme" src="./histogramme/histogramme.php" alt="Histogramme des &eacute;missions par cat&eacute;gorie" />';
	//  ======================================================================== 
	// Légende de la figure
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
					// Détermination des couleurs
					$couleur ['rouge'] = '200' ; $couleur ['vert'] = '20' ; $couleur ['bleu'] = '10' ; 
					echo '<li>' 
						. '<img src="./histogramme/carre_legende.php?' 
						. 'rouge=' . $sous_categorie [$cle_categorie]['rouge'][$cle_sous_categorie] 
						. '&amp;vert=' . $sous_categorie [$cle_categorie]['vert'][$cle_sous_categorie]
						. '&amp;bleu=' . $sous_categorie [$cle_categorie]['bleu'][$cle_sous_categorie] 
						. '" alt="(légende de la figure)" />' 
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
	selon les quatre grandes catégories d&apos;&eacute;mission correspondant aux quatre parties du questionnaire') . '<sup class="lien_note" >[<a href="#note_histogramme" id="rev_note_histogramme">' . $numero_note . '</a>]</sup>.</p>' ; 
	$numero_note ++ ; 
	
	
	
	echo '<p>' . _('La <strong>barre horizontale rouge</strong> repr&eacute;sente la hauteur moyenne que les barres de l&apos;histogrammes devraient ne pas dépasser pour 
	que votre mode de vie puisse &ecirc;tre qualifi&eacute; de durable du point de vue des &eacute;missions de gaz &agrave; effet de serre que ce mode de vie engendre. 
	Cette barre horizontale correspond &agrave; une quantit&eacute; totale annuelle d&apos;&eacute;missions de gaz &agrave; effet de serre de ' . EMISSION_TERRIEN_DURABLE . ' kg equivalent Carbone') . '<sup class="lien_note" >[<a class="lien_note" href="#note_terrien_durable" id="rev_note_terrien_durable">' . $numero_note . '</a>]</sup>. ' . 
	_( 'Sa hauteur 
	correspond donc &agrave; ' . EMISSION_TERRIEN_DURABLE . ' kg equ. Carbone, divis&eacute;s par le nombre de barres de l&apos;histogramme. Autrement dit, si vous ne possédez qu&apos;un seul
	logement, la hauteur correspond &agrave; ' . EMISSION_TERRIEN_DURABLE . '/4 = ' . EMISSION_TERRIEN_DURABLE/4 . ' kg equ. C, si vous en possédez deux la hauteur correspond &agrave; ' . EMISSION_TERRIEN_DURABLE . '/5 = ' . EMISSION_TERRIEN_DURABLE/5 . ' kg equ. C, etc.') . '</p>' . "\n\n" ; 
	$numero_note ++ ; 
}
//  ======================================================================== 
//  Total émissions et commentaires sur ce total
//  ======================================================================== 
function affiche_total_et_commentaire ( $resultat )
{
	echo '<h2>' . _('Total de vos émissions') . '</h2>' . "\n\n" ; 
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
		. "</strong> kilogrammes équivalent Carbone</td><td class='incertitude'><strong>" 
		. round ( $_SESSION['resultat']['toutes']['incertitude']['emissions_total'] *100 )
		. "</strong> %</td></tr> \n" 
		. "</table>" ; 
	*/

	$numero_note = 3 ; 
	echo '<p>' . _('Cette quantit&eacute; est &eacute;quivalente à la quantit&eacute; de gaz &agrave; effet de serre &eacute;mise par une voiture de faible puissance effectuant un 
	trajet de&nbsp;:') . " \n" ; 
	$distance = $resultat[TOUTES_CATEGORIES][TOTAL][EMISSION] / 0.055 ; 
	// on suppose que la voiture émet 32.72g equ. C au kilomètre petite voiture et trajet extra-urbain
	echo '<strong class="res_equiv">' 
		. number_format( $distance , 0, ',', ' ')
		. '</strong>' . _('kilom&egrave;tres') . '<sup class="lien_note" >[<a href="#note_equivalent_voiture" id="rev_note_equivalent_voiture">' . $numero_note . '</a>]</sup>.</p>'."\n" ; 
	$numero_note ++ ; 

	/*
	echo "<p>Cette quantité est également équivalente à la quantité de gaz à effet de serre émise en moyenne par la respiration d'un être humain
	pendant&nbsp;: \n" ; 
	$duree = $resultat[TOUTES_CATEGORIES][TOTAL][EMISSION] / 43 ; 
	// on est parti d'une émission de 0.3 gramme de CO2 par minute
	echo "<strong class='res_equiv'>" 
		. number_format( $duree , 0, ',', ' ')
		. "</strong> années<sup class='lien_note' >[<a href='#note_2' id='rev_note_2'>2</a>]</sup>.</p>\n\n" ; 

	echo "<p>Si une taxe de 1 euro par kg équivalent carbone est mise en place afin de lutter contre les émissions de gaz à effet de serre, 
	ceci entraînera un surcoût annuel pour l'ensemble de vos consommations individuelles de&nbsp;: " ;
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
		. '<li>' . _('Le total des &eacute;missions annuelles d&apos;un français moyen (obtenues en divisant le total des &eacute;missions annuelles nationales 
		par le nombre d&apos;habitants, en prenant donc en compte l&apos;ensemble des &eacute;missions de tous les secteurs d&apos;activité du pays) s&apos;&eacute;l&egrave;ve &agrave;&nbsp;: '
		. '<strong>' . '2800' . '</strong>' . ' kg equ. C par an.' . '<sup class="lien_note" >[<a href="#note_moyenne_francaise" id="rev_note_moyenne_francaise">' . $numero_note . '</a>]</sup></li>') . " \n" ; 
		$numero_note ++ ;
		
	/*
		echo "<li>Si l'on admet (ce qui est très optimiste, voir les pages d'explications) qu'une division par 4 des émissions 
		de gaz à effet 
		de serre en France confèrerait un caractère 'durable' à notre mode de vie et de production, alors on peut qualifier de
		'durable' (du seul point de vue des émissions de gaz à effet de serre), le mode de vie de tout individu dont le résultat 
		du Bilan Carbone Personnel ne dépasserait pas&nbsp;: " 
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
	echo"<h2>Détail de la répartition de vos émissions</h2>\n\n" ; 
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
					WHERE t_rubrique.rub_id=".$myrow['rub_id'].";";	//Oui je sais elle a une sacré gueule la requete mais elle compte tout d'un coup.
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
					WHERE t_rubrique.rub_id=".$myrow['rub_id'].";";	//Oui je sais elle a une sacré gueule la requete mais elle compte tout d'un coup.
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
	echo "<div id='notes_bas_de_page' >  <!-- début des notes de bas de page résultat -->\n\n" ; 
	$numero_note = 1 ; 
	echo "<p><span class='lien_note'>[<a href='#rev_note_histogramme' id='note_histogramme'>" . $numero_note . "</a>]</span> " 
		. "Chaque barre est constituée de blocs empilés dont les couleurs correspondent à différents usages ou consommations. Les correspondances entre les couleurs et les usages ou consommations sont données par la légence ci-dessus. Les valeurs exactes des émissions de gaz à effet de serre pour chaque usage ou consommation sont fournies dans les tableaux au bas de la page. La hauteur de chaque bloc correspond à la quantité de gaz à effet de serre émis du fait de l'usage ou de la consommation correspondant(e). Deux blocs successifs sont séparés par une barre horizontale noire.</p>\n"
		. "<p>Si vous avez indiqué que vous disposiez de plusieurs logements, les émissions associées à chaque logement sont 
	représentées de manière indépendante (une barre de l'histogramme par logement).</p>\n" 
		. "<p>Le segment vertical centré en haut de chacune des barres représente l'incertitude sur le montant des émissions de gaz à effet de serre représenté par cette barre. Autrement dit, pour chacune des barres de l'histogramme, il est peu vraissemblable que le montant réel de vos émissions de gaz à effet de serre pour le logement ou la catégorie correspondant(e) soit plus élevé que la valeur correspondant à l'extrêmité supérieure de ce segment, comme il est peu vraissemblable que ce montant soit moins élevé que la valeur correspondant à son extrêmité inférieure.</p>\n" ; 
	$numero_note ++ ; 

	echo "<p><span class='lien_note'>[<a href='#rev_note_terrien_durable' id='note_terrien_durable'>" . $numero_note . "</a>]</span> " 
		. "Ce chiffre est obtenu en divisant par quatre le montant le total des émissions annuelles brutes d'un français en moyenne (2800 kg equivalent Carbone, voir note ci-dessous). En réalité, cette division par quatre est pour partie une convention, car il n'existe pas de définition scientifique rigoureuse de la quantité maximale de gaz à effet de serre que l'humanité peut se permettre d'émettre chaque année pour que son mode de vie puisse être qualifié de durable. Pour davantage d'explications, voir la <a href='index.php?type_page=faq&amp;page=quel_bc_soutenable'>page de la FAQ sur cette question</a>.</p>\n" ; 
	$numero_note ++ ; 
	
	echo "<p><span class='lien_note'>[<a href='#rev_note_equivalent_voiture' id='note_equivalent_voiture'>" . $numero_note . "</a>]</span> " 
		. "Calcul basé sur une automobile émettant 55 grammes equivalent carbone (ou encore 202 grammes de C02) par kilomètre. 
	La moyenne des émissions affichées par les constructeurs pour les véhicules neufs est actuellement d'environ 150 grammes 
	de CO2 par kilomètre (si cette moyenne avait été retenue, le nombre de kilomètres aurait donc été supérieur d'environ 30%). 
	Mais, outre que ces émissions théoriques sont toujours dépassées en pratique, elles ne tiennent 
	compte ni des émissions liées à la fabrication de la voiture, ni de celles liées à son entretien, ni enfin de celles liées 
	au raffinage et au transport du carburant. Le calcul proposé ici est donc basé sur un chiffre plus proche 
	des émissions moyennes <em>réelles</em> au kilomètre des voitures actuelles, 
	et qui tient compte des émissions liées à la fabrication des véhicules (mais pas à leur entretien), ainsi qu'au raffinage 
	et au transport du carburant. 
	</p>\n\n" ; 
	$numero_note ++ ; 

	echo "<p><span class='lien_note'>[<a href='#rev_note_moyenne_francaise' id='note_moyenne_francaise'>" . $numero_note . "</a>]</span> " 
		. "Le chiffre de 2800 kg equivalent Carbone est obtenu en divisant le total des émissions <em>brutes</em> de gaz à effet de serre ayant lieu chaque année sur le territoire français par le nombre d'habitants de ce territoire. Ce chiffre représente donc le total des émissions annuelles brutes de gaz à effet de serre d'un citoyen français en moyenne. Pour davantage d'explications, voir la <a href='index.php?type_page=faq&amp;page=quel_bc_moyenne_francaise'>page de la FAQ sur cette question</a>.</p>\n\n" ; 
	$numero_note ++ ; 
	//
	echo "</div>  <!-- fin des notes de bas de page résultat -->\n\n" ; 
	/*
	echo "Le calcul correspond à une automobile émettant 32.7 grammes equivalent carbone (ou encore 120 grammes de C02) par kilomètre, 
	ce qui est le cas aujourd'hui pour une voiture faiblement émittrice de CO2 (la moyenne des véhicules neufs est actuellement de 150 grammes
	de CO2 par kilomètres) et qui correspond à un objectif de l'Union Européenne pour la moyenne des véhicules neufs vendus en 2012. 
	Ce chiffre ne tient toutefois pas 
	compte des émissions liées à la fabrication de la voiture, ni de celles liées à son entretien, ni enfin de celles liées 
	au raffinage et au transport du combustible (en tenir compte augmenterait l'émission au kilomètre d'environ 30%, et diminuerait donc la
	distance parcourue dans la même proportion). 
	</p>" ; 
	*/

	/*
	echo "<p>[<a href='#rev_note_2' id='note_2'>2</a>] 
	Un être humain au repos émet en moyenne (par sa respiration) environ 0.3 grammes de C02 (soit 0.082 grammes équivalent 
	carbone) par minute. Cette comparaison a pour but de donner un ordre de grandeur de que représente le total de vos 
	émissions de gaz à effet de serre une fois traduites en termes de métabolisme humain. 
	Les rendements du Vivant étant souvent très faibles,
	le total de votre consommation d'énergie représente donc bien davantage que l'énergie 
	mécanique pouvant être restituée par un être humain pendant une telle période. 
	</p>\n\n<p>
	NB&nbsp;: 
	les émissions de CO2 produites par la respiration humaine ne proviennent pas d'un stock de carbone fossile, 
	mais directement du CO2 de l'atmosphère
	synthétisé par les plantes agricoles. 
	Par conséquent elles ne contribuent pas en tant que telles à l'augmentation de la concentration
	de CO2 dans l'atmosphère ni donc à l'effet de serre (ce qui contribue à l'effet de serre, ce sont les émissions de CO2 d'origine
	d'origine fossile et d'autres gaz à effet de serre - méthane, protoxyde d'azote - <em>induites</em> 
	par l'agriculture et l'élevage). 
	</p>\n\n" ; 
	// echo " nombre de voitures : " . $nombre_voiture  ; echo " nombre de deux_roues : " . $nombre_deux_roues  ; echo " nombre de vol_avion : " . $nombre_vol_avion  ; 
		
	echo "<p>[<a href='#rev_note_3' id='note_3'>3</a>] 
	Une telle taxe correspondrait en gros (pour la France) à un doublement de la taxe intérieure sur les produits pétroliers, ou à un 
	quadruplement du prix du pétrole sur les marchés internationaux (ou à un peu des deux), ce qui n'est pas invraisemblable
	sur une à deux décennies 
	(il faut imaginer cette hausse corrigée de l'inflation et éventuellement corrigée de la hausse 
	du pouvoir d'achat sur la période, ce qui se traduit en une hausse des coûts, exprimés en euros dans dix ou vingt ans, encore plus importante). 
	Rappelons que le prix du baril de pétrole brut vient d'être multiplié par 6 en moins de 10 ans (moins de 15 dollars en janvier 1999, 
	plus de 90 dollars en octobre 2007)." ; 
	*/
	
	/*
	echo "Un surcoût équivalent pour le consommateur proviendrait d'une hausse d'environ 1000 euros du cours de la tonne équivalent 
	Carbone sur les machés internationaux 
	de permis d'émission. Ce cours est d'environ 1.25 euros en octobre 2007 (autrement dit émettre des gaz à effet de serre est à cette date, 
	et au niveau international, essentiellement gratuit). " ; 
	*/
}



?>
