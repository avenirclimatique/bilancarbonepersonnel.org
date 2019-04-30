<?php 
//==========================================================================================
// Nombre de comptes ouverts, etc
//==========================================================================================
function afficher_compte ( $connexion )
{
	//========================================
	echo "<h3>Récapitulatif</h3>\n" ; 
	//
	$donnees = exec_requete ( "SELECT COUNT(*) FROM t_utilisateur
			WHERE util_est_valide_courriel = 'true' ", $connexion) ; 
	$nombre_utilisateur = mysql_fetch_row($donnees) ;
	//
	$donnees = exec_requete ( "SELECT COUNT(*) FROM t_sauvegarde ", $connexion) ; 
	$nombre_sauvegarde = mysql_fetch_row($donnees) ;
	//
	$donnees = exec_requete ( "SELECT COUNT(*) FROM t_sauvegarde WHERE sauv_est_saisie_complete = 'true' ", $connexion) ; 
	$nombre_sauvegarde_complete = mysql_fetch_row($donnees) ;
	// 
	$nombre_moyen_sauvegarde = $nombre_sauvegarde[0] / $nombre_utilisateur[0] ; 
	$nombre_moyen_sauvegarde_complete = $nombre_sauvegarde_complete [0] / $nombre_utilisateur[0] ; 
	
	echo "<table>\n" 
		. "<tr><td>Nombre de comptes ouverts</td><td>" . $nombre_utilisateur[0] . "</td></tr>\n" 
		. "<tr><td>Nombre de sauvegardes réalisées</td><td>" . $nombre_sauvegarde[0] . "</td></tr>\n" 
		. "<tr><td>Nombre de sauvegardes complètes</td><td>" . $nombre_sauvegarde_complete[0] . "</td></tr>\n" 
		. "<tr><td>Nombre moyen de sauvegardes par utilisateur</td><td>" . $nombre_moyen_sauvegarde . "</td></tr>\n" 
		. "<tr><td>Nombre moyen de sauvegardes complètes par utilisateur</td><td>" . $nombre_moyen_sauvegarde_complete . "</td></tr>\n" 
		. "</table>\n\n" ; 
	
	//========================================
	// Ouvertures de comptes
	//========================================
	echo "<h3>Agenda des ouvertures de comptes</h3>\n" ; 
	//
	$donnees_date_creation_compte = exec_requete ("
	select date_format( date( util_date_time_premiere_validation_courriel ) , '%Y-%m-%d (%a %d %M)' ) date , count(*) c
	from t_utilisateur
	where util_est_valide_courriel = 'true'
	group by date( util_date_time_premiere_validation_courriel )
	order by date( util_date_time_premiere_validation_courriel ) desc", $connexion);
	//
	echo "<table>\n" 
		. "<tr><th>Date</th><th>Total</th><th>Nombre</th></tr>\n" ; 
	$total = $nombre_utilisateur[0] ; 
	while ( $donnees_date_creation_compte_ligne = objet_suivant ( $donnees_date_creation_compte ) )
		{	
			echo "<tr><td>" . $donnees_date_creation_compte_ligne->date
			. "</td><td>" . $total . "</td>"
			. "</td><td> +" . $donnees_date_creation_compte_ligne->c . "</td></tr>\n";			
			$total= $total - $donnees_date_creation_compte_ligne->c;
		}
	echo "</table>\n\n" ; 
	//========================================
	// Sauvegardes
	//========================================
	echo "<h3>Agenda des sauvegardes (complètes)</h3>\n" ; 
	//
	$donnees_date_sauvegarde = exec_requete ("
	select date_format( date( sauv_date_time ) , '%Y-%m-%d (%a %d %M)' ) date , count(*) c
	from t_sauvegarde
	where sauv_est_saisie_complete = 'true'
	group by date( sauv_date_time )
	order by date( sauv_date_time ) desc", $connexion);
	//
	echo "<table>\n" 
		. "<tr><th>Date</th><th>Total</th><th>Nombre</th></tr>\n" ; 
	$total = $nombre_sauvegarde[0] ; 
	while ( $donnees_date_sauvegarde_ligne = objet_suivant ( $donnees_date_sauvegarde ) )
		{	
			echo "<tr><td>" . $donnees_date_sauvegarde_ligne->date
			. "</td><td>" . $total . "</td>"
			. "</td><td> +" . $donnees_date_sauvegarde_ligne->c . "</td></tr>\n";			
			$total= $total - $donnees_date_sauvegarde_ligne->c;
		}
	echo "</table>\n\n" ; 
}
//==========================================================================================
// Numéroter les éléments du questionnaire (pour pouvoir ensuite aller les chercher facilement
//==========================================================================================
function numeroter_questionnaire ( $connexion )
{
	$rub_prec_id = -1 ; 
	$rub_ordre = 0 ; 
	while ( $rub_id = rubrique_suivante_id ( $rub_prec_id , $connexion ) )
	{
		$rub_ordre ++ ; 
		exec_requete ( "UPDATE t_rubrique SET rub_ordre = '$rub_ordre' WHERE rub_id = '$rub_id' " , $connexion ) ; 
		numeroter_contenu_rubrique ( $rub_id , $connexion ) ; 
		$rub_prec_id = $rub_id ; 
	}
	echo "<p>Les éléments du questionnaire contenus dans la base ont été renumérotés</p>" ; 
}
//==========================================================================================
function numeroter_contenu_rubrique ( $rub_id , $connexion ) 
{
	$page_prec_id = -1 ; 
	$page_ordre = 0 ; 
	while ( $page_id = page_suivante_id ( $page_prec_id , $rub_id , $connexion ) )
	{
		$page_ordre ++ ; 
		exec_requete ( "UPDATE t_page SET page_ordre = '$page_ordre' WHERE page_id = '$page_id' " , $connexion ) ; 
		numeroter_contenu_page ( $page_id , $connexion ) ; 
		$page_prec_id = $page_id ; 
	}
}
//==========================================================================================
function numeroter_contenu_page ( $page_id , $connexion ) 
{
	$quest_prec_id = -1 ; 
	$quest_ordre = 0 ; 
	while ( $quest_id = quest_suivante_id ( $quest_prec_id , $page_id , $connexion ) )
	{
		$quest_ordre ++ ; 
		exec_requete ( "UPDATE t_question SET quest_ordre = '$quest_ordre' WHERE quest_id = '$quest_id' " , $connexion ) ; 
		numeroter_contenu_question ( $quest_id , $connexion ) ; 
		$quest_prec_id = $quest_id ; 
	}
}
//==========================================================================================
function numeroter_contenu_question ( $quest_id , $connexion ) 
{
	$rep_prec_id = -1 ; 
	$rep_ordre = 0 ; 
	while ( $rep_id = rep_suivante_id ( $rep_prec_id , $quest_id , $connexion ) )
	{
		$rep_ordre ++ ; 
		exec_requete ( "UPDATE t_reponse SET rep_ordre = '$rep_ordre' WHERE rep_id = '$rep_id' " , $connexion ) ; 
		$rep_prec_id = $rep_id ; 
	}
}
//==========================================================================================
function rubrique_suivante_id ( $rub_prec_id , $connexion )
{
	$rub_id = false ; 
	$donnees_rubrique = exec_requete ( "SELECT rub_id FROM t_rubrique WHERE rub_prec_id = '$rub_prec_id' " , $connexion ) ; 
	if ( $objet_rubrique = objet_suivant ( $donnees_rubrique ) )
		$rub_id = $objet_rubrique->rub_id ; 
	return $rub_id ; 
}
//==========================================================================================
function page_suivante_id ( $page_prec_id , $rub_id , $connexion )
{
	$page_id = false ; 
	$donnees_page = exec_requete ( "SELECT page_id FROM t_page WHERE page_prec_id = '$page_prec_id' AND page_rub_id = '$rub_id' " , $connexion ) ; 
	if ( $objet_page = objet_suivant ( $donnees_page ) )
		$page_id = $objet_page->page_id ; 
	return $page_id ; 
}
//==========================================================================================
function quest_suivante_id ( $quest_prec_id , $page_id , $connexion )
{
	$quest_id = false ; 
	$donnees_question = exec_requete ( "SELECT quest_id FROM t_question WHERE quest_prec_id = '$quest_prec_id' AND quest_page_id = '$page_id' " , $connexion ) ; 
	if ( $objet_question = objet_suivant ( $donnees_question ) )
		$quest_id = $objet_question->quest_id ; 
	return $quest_id ; 
}
//==========================================================================================
function rep_suivante_id ( $rep_prec_id , $quest_id , $connexion )
{
	$rep_id = false ; 
	$donnees_reponse = exec_requete ( "SELECT rep_id FROM t_reponse WHERE rep_prec_id = '$rep_prec_id' AND rep_quest_id = '$quest_id' " , $connexion ) ; 
	if ( $objet_reponse = objet_suivant ( $donnees_reponse ) )
		$rep_id = $objet_reponse->rep_id ; 
	return $rep_id ; 
}
//===========================================================================================
function backup_bdd ( ) {
	$fichiers = scandir(BCKPDIR);
	$saves = 'auto';
	echo '<h3>Sauvegardes automatiques:</h3>';
	echo '<ul>';
	$totalSize=0;
	foreach($fichiers as $backup) { //pour chaque fichier
		if ( $backup[0] != '.' ) { //Si le fichier n'est pas caché on affiche
			//Calcul de la taille du fichier
			$file_size = array_reduce (
			    array (" o", " Ko", " Mo"), create_function (
				'$a,$b', 'return is_numeric($a)?($a>=1024?$a/1024:number_format($a,2).$b):$a;'
			    ), filesize (BCKPDIR.'/'.$backup)
			);
			$totalSize += filesize (BCKPDIR.'/'.$backup);	//Ajout a la taille totale
			if ( $backup[0] == 'm' && $saves == 'auto' ) { echo '</ul><h3>Sauvegardes manuelles:</h3><ul>'; $saves = 'manuel'; }
			echo '<li> ' . $backup . ' (' .$file_size . ')'
				. ' [ <a href="exportBackup.php?file='.$backup.'">T&eacute;l&eacute;charger</a> ] '
				. '[ <a href="admin.php?page=remove_bckp&amp;file=' . $backup . '" >Supprimer</a> ]<br/>'
				. '</li>';
		}
	}
	echo '</ul>';
	$totalSize = array_reduce (
	    array (" o", " Ko", " Mo"), create_function (
		'$a,$b', 'return is_numeric($a)?($a>=1024?$a/1024:number_format($a,2).$b):$a;'
	    ), $totalSize
	);
	echo 'Espace total utilis&eacute;: ' . $totalSize;
	echo '<br/>[ <a href="admin.php?page=saveBDD">Effectuer une sauvegarde manuelle</a> ]';
	
}

function remove_backup ($backup) {
	unlink(BCKPDIR.'/'.$backup);
	echo '<p>Le backup a &eacute;t&eacute; supprim&eacute; correctement.</p>';
	backup_bdd ( );
}

function add_backup () {
	echo exec('/home.10.2/risler/sauvegardeBD/backup.sql.BD_BCP.php.sh');
	echo '<p>Nouveau backup cr&eacute;&eacute; correctement.</p>';
	backup_bdd ( );
}
?>








