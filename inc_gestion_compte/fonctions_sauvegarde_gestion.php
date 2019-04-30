<?php 
//==========================================================================================
// Sauvegarde
//==========================================================================================
function sauvegarder ( $util_id , $connexion )
{
	if ( !isSet ( $_SESSION [SAUV_ID] ) )
	{
		// Le jeu de saisie ne porte encore aucun nom
		//==========
		// compter le nombre de sauvegardes de cet utilisateur
		$ns = nombre_sauvegarde ( $util_id , $connexion ) ; 
		if ( $ns < NOMBRE_SAUVEGARDE_MAX )
		{
			$numero_sauvegarde = $ns + 1 ; 
			echo "<p>Choisissez un nom pour cette sauvegarde, et cliquez sur &quot;Valider&quot;.</p>\n" ; 
			formulaire_nouvelle_sauvegarde ( $numero_sauvegarde ) ; 
		}
		else
		{
			echo "<p>Vous avez déjà réalisé " . NOMBRE_SAUVEGARDE_MAX . " sauvegardes de saisies. Le calculateur ne permet pas de réaliser davantage de sauvegardes. Pour pouvoir sauvegarder vos saisies, vous devez tout d'abord supprimer une de vos sauvegardes antérieures. Pour cela, veuillez cliquer sur l'onglet &quot;Mes sauvegardes&quot; ci-dessus.</p>" ; 
		}
	}
	else
	{
		// la saisie a déjà été sauvegardée (ou chargée) donc elle possède un nom
		$nom_sauvegarde = nom_sauvegarde ( $connexion ) ; 
		$sauv_id = $_SESSION [SAUV_ID] ; 
		echo "<p>Vous êtes sur le point de remplacer votre dernière sauvegarde de &quot;" . $nom_sauvegarde . "&quot; par votre saisie actuelle. Pour confirmer cette demande, cliquez sur &quot;Confirmer&quot;, sinon cliquez sur &quot;Annuler&quot;</p>\n" ; 
	$url_confirmation = "./index.php?type_page=" . GESTION_COMPTE 
		. "&amp;page=" . CONFIRMER_SAUVEGARDER
		. "&amp;util_id=" . $util_id 
		. "&amp;sauv_id=" . $sauv_id 
		. "&amp;nom_sauvegarde=" . $nom_sauvegarde ; 
	//
	$url_annulation = "./index.php?type_page=" . GESTION_COMPTE 
		. "&amp;page=" . ANNULER_SAUVEGARDER
		. "&amp;nom_sauvegarde=" . $nom_sauvegarde ; 
	//
	demande_confirmation ( 'Confirmer' , $url_confirmation , 'Annuler' , $url_annulation ) ; 
	// la fonction  demande_confirmation se trouve dans fonctions_generales.php
		
	}
}
//==========================================================================================
// Annuler sauvegarder
//==========================================================================================
function annuler_sauvegarder ( $util_id , $nom_sauvegarde , $connexion )
{
	echo "<p>Suite à cette annulation, votre saisie actuelle n'a pas été sauvegardée à la place de votre dernière 
	sauvegarde de &quot;" . $nom_sauvegarde . "&quot;.</p>\n" ; 
	// echo "<p>Liste de vos sauvegardes :</p>" ; 
	// tableau_sauvegarde ( $util_id , $connexion ) ; 
	//
	lien_retour_menu_page_sans_action_non_voyants () ; 
}
//==========================================================================================
// Afficher le tableau des sauvegardes
//==========================================================================================
function tableau_sauvegarde ( $util_id , $connexion )
{
	echo "<table id ='tableau_sauvegarde' >\n<tr class='en_tete' >
		<th>Numéro</th>\n
		<th>Date et heure</th>\n
		<th>Saisie</th>\n
		<th>Nom</th>\n<th colspan='3' >Actions</th>\n</tr>\n" ; 
	$donnees_sauvegarde = exec_requete ("
		select sauv_id , sauv_nom , sauv_date_time  , sauv_est_saisie_complete
		from t_sauvegarde 
		where sauv_util_id='$util_id'
		order by sauv_date_time desc", $connexion ) ; 
	$numero = 1 ; 
	while ( $objet_sauvegarde = objet_suivant ( $donnees_sauvegarde ) )
	{
		$nom_sauvegarde_url = urlEncode ( $objet_sauvegarde->sauv_nom ) ; 
		echo "<tr>\n<td>" . $numero . "</td>\n"
			. "<td>" . $objet_sauvegarde->sauv_date_time . "</td>\n<td>" ; 
		if ( $objet_sauvegarde->sauv_est_saisie_complete == 'true' )
			echo "<span class='complet'>complète</span>" ; 
		else
			echo "<span class='incomplet'>incomplète</span>" ; 
		echo "</td>\n<td>" . $objet_sauvegarde->sauv_nom . "</td>\n" ; 
		// ========================
		// si pas de saisie réalisée, pas la peine de demander une confirmation pour charger une sauvegarde
		if ( isSet ( $_SESSION[EST_SAISIE_EFFECTUEE] ) ) 
			$action_charger_sauvegarde = CHARGER_SAUVEGARDE ; 
		else
			$action_charger_sauvegarde = CHARGER_SAUVEGARDE ; // finalement si, on laisse pareil !!!
		// =========
		// charger
		echo "<td><a href='index.php?type_page=" . GESTION_COMPTE 
			. "&amp;page=" . $action_charger_sauvegarde 
			. "&amp;util_id=" . $util_id 
			. "&amp;sauv_id=" . $objet_sauvegarde->sauv_id 
			. "&amp;nom_sauvegarde=" . $nom_sauvegarde_url
			. "' >Charger</a></td>\n"
		// renommer
			. "<td><a href='index.php?type_page=" . GESTION_COMPTE 
			. "&amp;page=" . RENOMMER_SAUVEGARDE 
			. "&amp;util_id=" . $util_id 
			. "&amp;sauv_id=" . $objet_sauvegarde->sauv_id 
			. "&amp;nom_sauvegarde=" . $nom_sauvegarde_url
			. "' >Renommer</a></td>\n"
		// supprimer
			. "<td><a href='index.php?type_page=" . GESTION_COMPTE 
			. "&amp;page=" . SUPPRIMER_SAUVEGARDE 
			. "&amp;util_id=" . $util_id 
			. "&amp;sauv_id=" . $objet_sauvegarde->sauv_id 
			. "&amp;nom_sauvegarde=" . $nom_sauvegarde_url
			. "' >Supprimer</a></td>\n</tr>\n" ; 
		$numero++ ; 
	}
	echo "</table>\n" ; 
}
//==========================================================================================
// Sauvegarde
//==========================================================================================
function formulaire_nouvelle_sauvegarde ( $numero_nouvelle_sauvegarde )
{
	echo "<form action='index.php' method='post' >\n"
		. "<p>Nom : "
		. "<input type='text' size='40' name='nom_sauvegarde' value='Mon BILAN CARBONE Personnel numéro " . $numero_nouvelle_sauvegarde . "'/>\n"
		//. "<input type='hidden' nom='util_id' value='" . $util_id . "' />" 
		//. "<input type='hidden' nom='sauv_est_saisie_complete' value='" . $util_id . "' />" 
		//. "<input type='hidden' nom='" . PAYS_ID . "' value='" . $_SESSION[PAYS_ID] . "' />" 
		//. "<input type='hidden' nom='" . VERSION_ID . "' value='" . $_SESSION[VERSION_ID] . "' />" 
		//. "<input type='hidden' nom='" . TYPE_BC_ID . "' value='" . $_SESSION[TYPE_BC_ID] . "' />" 
		. "<input type='submit' value='Sauvegarder' name='" . POST_NOUVELLE_SAUVEGARDE . "' /></p>\n"
		. "</form>\n\n" ; 
	// on a fait passer les variables de session par POST 
}
//==========================================================================================
// Menu sauvegardes
//==========================================================================================
function menu_sauvegarde ( $util_id , $connexion )
{
	echo "<h2>Vos sauvegardes</h2>\n\n" ; 
	$ns = nombre_sauvegarde ( $util_id , $connexion ) ; 
	if ( $ns == 0 ) 
	{
		echo "<p>Vous n'avez aucune sauvegarde en mémoire.</p>\n" ;
		echo "<p>Vous pouvez réaliser jusqu'à " . NOMBRE_SAUVEGARDE_MAX . " sauvegardes.</p>\n" ; 
	}
	else
	{
		echo "<p>Liste de vos sauvegardes :</p>" ; 
		tableau_sauvegarde ( $util_id , $connexion ) ; 
	}
	if ( $ns < NOMBRE_SAUVEGARDE_MAX )
	{
		$ns_restant = NOMBRE_SAUVEGARDE_MAX - $ns ; 
		if ( $ns_restant > 1 )
			echo "<p>Vous pouvez réaliser encore " . $ns_restant . " sauvegardes supplémentaires.</p>\n" ; 
		else
			echo "<p>Vous pouvez réaliser encore une sauvegarde supplémentaire.</p>\n" ; 
		echo "<p>Pour réaliser une nouvelle sauvegarde à partir de votre saisie actuelle, veuillez lui donner un nom à indiquer dans le champ ci-dessous et cliquez sur &quot;Sauvegarder&quot;.</p>" ; 
		$numero_nouvelle_sauvegarde = $ns + 1 ; 
		formulaire_nouvelle_sauvegarde ( $numero_nouvelle_sauvegarde ) ; 
	}
	else
		echo "<p>Vous ne pouvez réaliser aucune sauvegarde supplémentaire.</p>\n" ; 
}
//==========================================================================================
// Renommer sauvegarde
//==========================================================================================
function renommer_sauvegarde ( $util_id , $sauv_id , $nom_sauvegarde , $connexion )
{
	echo "<p>Veuillez indiquer ci-dessous le nouveau nom que vous souhaitez affecter à cette sauvegarde.</p>\n\n" ; 
	echo "<form action='index.php' method='post' >\n"
		. "<p>\nNom de cette sauvegarde \n"
		. "<input type='text' size='40' name='nouveau_nom_sauvegarde' value='" . $nom_sauvegarde . "'/>\n"
		//. "<input type='hidden' name='util_id' value='" . $util_id . "' />\n" 
		. "<input type='hidden' name='sauv_id' value='" . $sauv_id . "' />\n" 
		. "<input type='submit' value='Valider' name='" . POST_RENOMMER_SAUVEGARDE . "' />\n</p>\n"
	
		. "</form>\n\n" ; 
}
//==========================================================================================
// Renommer sauvegarde
//==========================================================================================
function traitement_post_renommer_sauvegarde ( $util_id , $connexion ) // par POST on n'a pas la variable $sauv_id
{
	if ( $util_id )
	{
		// session toujours active
		$sauv_id = $_POST['sauv_id'] ; 
		$nouveau_nom_sauvegarde = $_POST['nouveau_nom_sauvegarde'] ; 
		exec_requete ("UPDATE t_sauvegarde SET sauv_nom = '$nouveau_nom_sauvegarde' 
			WHERE sauv_id = '$sauv_id' ", $connexion ) ; 
	}
}
//==========================================================================================
// Renommer sauvegarde
//==========================================================================================
function afficher_post_renommer_sauvegarde ( $util_id , $connexion ) // par POST on n'a pas la variable $sauv_id
{
	if ( $util_id )
	{
		// session toujours active
		echo "<p>La modification du nom de votre sauvegarde a bien été enregistrée.</p>\n"
			. "<p>Liste de vos sauvegardes : </p>\n" ; 
		tableau_sauvegarde ( $util_id , $connexion ) ; 
	}
	else
		// session a expiré
		annoncer_expiration_session () ; 
}
//==========================================================================================
// Demande confirmation supprimer sauvegarde
//==========================================================================================
function demande_confirmation_supprimer_sauvegarde ( $util_id , $sauv_id , $nom_sauvegarde , $connexion )
{
	echo "<p>Vous êtes sur le point de supprimer la sauvegarde intitulée : &quot;" . $nom_sauvegarde . "&quot;. Pour confirmer cette suppression, cliquez sur &quot;Confirmer&quot;, sinon cliquez sur &quot;Annuler&quot;.</p>\n" ; 
	//
	$url_confirmation = "./index.php?type_page=" . GESTION_COMPTE 
		. "&amp;page=" . CONFIRMER_SUPPRIMER_SAUVEGARDE 
		. "&amp;util_id=" . $util_id 
		. "&amp;sauv_id=" . $sauv_id 
		. "&amp;nom_sauvegarde=" . $nom_sauvegarde ; 
	//
	$url_annulation = "./index.php?type_page=" . GESTION_COMPTE 
		. "&amp;page=" . ANNULER_SUPPRIMER_SAUVEGARDE
		. "&amp;nom_sauvegarde=" . $nom_sauvegarde ; 
	//
	demande_confirmation ( 'Confirmer' , $url_confirmation , 'Annuler' , $url_annulation ) ; 
	// la fonction  demande_confirmation se trouve dans fonctions_generales.php
}
//==========================================================================================
// Annuler supprimer sauvegarde
//==========================================================================================
function annuler_supprimer_sauvegarde ( $util_id , $nom_sauvegarde , $connexion )
{
	echo "<p>Suite à cette annulation, votre sauvegarde &quot;" . $nom_sauvegarde . "&quot; a bien été conservée. Vous pouvez consulter ci-dessous le tableau de vos sauvegardes.</p>\n" ; 
	tableau_sauvegarde ( $util_id , $connexion ) ; 
}
//==========================================================================================
// Demande confirmation charger sauvegarde
//==========================================================================================
function demande_confirmation_charger_sauvegarde ( $util_id , $sauv_id , $nom_sauvegarde , $connexion )
{
	echo "<p>Vous êtes sur le point de charger la sauvegarde intitulée &quot;" . $nom_sauvegarde . "&quot;. Cette opération supprime toutes les saisies actuellement visibles sur les pages du questionnaire et les remplace par les données de la sauvegarde que vous avez demandé à charger. Pour confirmer votre demande, cliquez sur &quot;Confirmer&quot;, sinon cliquez sur &quot;Annuler&quot;.</p>" ; 
	$url_confirmation = "./index.php?type_page=" . GESTION_COMPTE 
		. "&amp;page=" . CONFIRMER_CHARGER_SAUVEGARDE 
		. "&amp;util_id=" . $util_id 
		. "&amp;sauv_id=" . $sauv_id 
		. "&amp;nom_sauvegarde=" . $nom_sauvegarde ; 
	//
	$url_annulation = "./index.php?type_page=" . GESTION_COMPTE 
		. "&amp;page=" . ANNULER_CHARGER_SAUVEGARDE
		. "&amp;nom_sauvegarde=" . $nom_sauvegarde ; 
	//
	demande_confirmation ( 'Confirmer' , $url_confirmation , 'Annuler' , $url_annulation ) ; 
	// la fonction  demande_confirmation se trouve dans fonctions_generales.php
}
//==========================================================================================
// Demande confirmation charger sauvegarde
//==========================================================================================
function afficher_charger_sauvegarde ( $nom_sauvegarde )
{
	echo "<p>Suite à cette confirmation, votre sauvegarde intitulée &quot;" . $nom_sauvegarde . "&quot; a été chargée en variable de sessions (ce qui signifie que les saisies associées à cette sauvegarde apparaissent maintenant sur les pages du questionnaire).</p>\n" ; 
}

?>