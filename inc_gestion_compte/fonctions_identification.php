<?php 
//==========================================================================================
//        SE CONNECTER
//==========================================================================================
function se_connecter () 
{
	echo "<p>Si vous vous connectez pour la <strong>première fois</strong>, cliquez sur l'onglet &quot;<strong>Créer un compte</strong>&quot;.</p>\n"
		. "<p>Si vous désirez vous connecter à un compte que vous avez créé auparavant, cliquez sur l'onglet &quot;<strong>Me connecter à un compte existant</strong>&quot;.</p>\n\n"
		. "<ul class='confirmation_annulation' >\n"
		. "<li>\n<a href='./index.php?type_page=" . GESTION_COMPTE . "&amp;page=" . CREER_COMPTE . "'>"
		. "Créer un compte"
		. "</a>\n</li>\n"
		. "<li>\n<a href='./index.php?type_page=" . GESTION_COMPTE . "&amp;page=" . S_IDENTIFIER . "'>"
		. "Me connecter à un compte existant"
		. "</a>\n</li>\n</ul>\n\n" ; 
}
//================================
//    COURRIEL = ACCES PAR GET
//================================
function creer_compte ()
{
	echo "<p>Pour créer un compte, vous devez indiquer dans le champ ci-dessous une adresse de <strong>courriel valide</strong>, 
		puis cliquer sur &quot;<strong>Valider</strong>&quot;.
		Un mot de passe vous sera alors envoyé à cette adresse.</p>\n\n" ; 
	formulaire_saisie_courriel ( POST_SAISIE_COURRIEL_CREATION_COMPTE ) ; 
	echo "<p>Ce courriel sera sauvegardé sous forme <strong>cryptée</strong>. 
	Cette sauvegarde permet que vous puissiez retrouver vos saisie par la suite lorsque vous vous re-connecterez avec la même adresse de courriel. 
	En revanche, il est impossible de retrouver votre véritable adresse de courriel à partir de sa forme cryptée. Autrement dit, il sera impossible de vous identifier ou de vous contacter à partir de cette sauvegarde (et même si une personne malveillante parvenait à mettre la main sur notre base de données, elle ne pourrait ni vous identifier ni vous contacter). Par conséquent, les sauvegardes de vos réponses aux questions posées par le calculateur peuvent être considérées comme <strong>anonymes</strong>.</p>" ; 
}
//==========================================================================================
function s_identifier ()
{
	echo "<p>Veuillez indiquer ci-dessous le <strong>courriel</strong> utilisé pour la création de ce compte.</p>\n" ; 
	formulaire_saisie_courriel ( POST_SAISIE_COURRIEL_IDENTIFICATION ) ; 
	echo "<p>Le mot de passe associé à ce courriel vous sera demandé à l'étape suivante. Si vous ne retrouvez plus ce mot de passe vous pourrez en demander un nouveau.</p>" ; 
}
//==========================================================================================
function formulaire_saisie_courriel ( $nom_action )
{
	echo "<form action = 'index.php' method='post'>\n" ; 
	echo "<p>Courriel : " ; 
	echo "<input type='text' size='40' name='courriel' value=''/></p>\n"
		. "<p>\n<input type='submit' value='Valider' name='" . $nom_action . "' />\n"
		//. "<input type='hidden' value='true' name='" . POST_GESTION_COMPTE . "' />\n" // pour aiguiller en index.php
		. "</p>\n" ; 
	echo "</form>\n\n" ; 
}
//==========================================================================================
// =======
// MOT DE PASSE = ACCES PAR POST
//===========================================================================================================================
function traitement_post_saisie_courriel_creation_compte ( $connexion )
{
	$courriel = $_POST ['courriel'] ; 
	if ( !est_valide_courriel ($courriel) )
	{
		// le courriel est invalide, on affiche à nouveau le formulaire de création du compte 
		echo "<p class='courriel_invalide' ><strong>Courriel invalide</strong>, veuillez recommencer.</p>" ; 
		creer_compte () ; 
	}
	else if ( !accepte_courriels_serveur ( $courriel ) )
	{
		// le serveur n'accepte pas les courriels 
		echo "<p class='courriel_invalide' ><strong>Courriel invalide</strong> 
		(le serveur indiqué n'accepte pas les courriels), veuillez recommencer.</p>" ; 
		creer_compte () ; 
	}
	else if ( $util_id = est_enregistre_courriel_retourner_util_id ( $courriel , $connexion ) )
	{
		// le courriel est déjà enregistré dans la base ! on propose donc à l'utilisateur de saisir son mot de passe, ou de s'en faire envoyer un nouveau par courriel
		echo "<p>L'adresse courriel <strong>" . $courriel . "</strong> correspond à un compte déjà créé.</p>\n" 
			. "<ul>\n<li>Pour <strong>vous connecter à ce compte</strong>, saisissez ci-dessous le dernier mot de passe qui vous a été envoyé par le webmestre (adresse : &quot;webmestre at bilancarbonepersonnel.org&quot;) à cette adresse de courriel (si vous n'avez jamais redemandé de mot de passe il s'agit du mot de passe envoyé lors de l'ouverture du compte), puis cliquez sur &quot;Valider&quot;.</li>\n" 
			. "<li>Si <strong>vous souhaitez vous connecter à ce compte mais que vous ne retrouvez plus le mot de passe associé</strong>, "
			. "<strong><a href='index.php?type_page=" . GESTION_COMPTE . "&amp;page=" . DEMANDE_NOUVEAU_PASS . "&amp;courriel=" . $courriel . "&amp;util_id=" . $util_id . "'>cliquez ici</a></strong>" 
			. " pour qu'un nouveau mot de passe vous soit envoyé à l'adresse courriel &quot;" . $courriel . "&quot;.</li>\n" 
			. "<li>Pour <strong>créer un compte en utilisant une autre adresse courriel</strong>, <strong><a href='index.php?type_page=". GESTION_COMPTE . "&amp;page=" . CREER_COMPTE . "'>cliquez ici</a></strong></li>\n</ul>\n" ; 
		formulaire_saisie_pass ( $util_id , $courriel , POST_SAISIE_PASS_IDENTIFICATION ) ;
	}
	else
	{
		// le courriel est valide et n'est pas enregistré dans la base
		$pass = creer_pass () ; 
		envoyer_pass ( $courriel, $pass ); 
		$util_id = enregistrer_courriel_pass_retourner_util_id ( $courriel, $pass, $connexion ) ; 
		echo "<p>Un <strong>mot de passe vous a été envoyé</strong> par courrier électronique à l'adresse : <strong>" . $courriel . "</strong>.</p>\n"
			. "<p>Veuillez reporter ce mot de passe dans le champ ci-dessous, et cliquer sur &quot;Valider&quot;.</p>\n"
			. "<p>Attention, l'envoi du mot de passe peut prendre quelques minutes.</p>\n" ; 
	// =============================================
	// formulaire mot de passe
	formulaire_saisie_pass ( $util_id , $courriel , POST_SAISIE_PASS_CREATION_COMPTE ) ; 
	}
}
// =================================================================================================
function traitement_post_saisie_courriel_identification ( $connexion )
{
	$courriel = $_POST ['courriel'] ; 
	if ( $util_id = est_enregistre_courriel_retourner_util_id ( $courriel , $connexion ) )
	{
		// le courriel est bien enregistré en bdd
		echo "<p>L'adresse courriel <strong>" . $courriel . "</strong> correspond bien à un compte créé sur ce site.</p>" 
			. "<p>Pour achever votre identification, veuillez saisir ci-dessous le dernier mot de passe qui vous a été envoyé par le webmestre (adresse courriel : &quot;webmestre at bilancarbonepersonnel.org&quot;) à l'adresse courriel &quot;" . $courriel . "&quot; (si vous n'avez jamais redemandé de mot de passe il s'agit du mot de passe envoyé lors de l'ouverture du compte), puis cliquez sur &quot;Valider&quot;.</p>\n" 
			. "<p><strong>Si vous ne retrouvez plus ce mot de passe</strong>, "
			. "<strong><a href='index.php?type_page=" . GESTION_COMPTE . "&amp;page=" . DEMANDE_NOUVEAU_PASS . "&amp;courriel=" . $courriel . "&amp;util_id=" . $util_id . "'>cliquez ici</a></strong>" 
			. " pour qu'un nouveau mot de passe vous soit envoyé à l'adresse courriel &quot;" . $courriel . "&quot;." ; 
		//
		formulaire_saisie_pass ( $util_id , $courriel , POST_SAISIE_PASS_IDENTIFICATION ) ;
	}
	else
	{
		echo "<p>L'adresse courriel <strong>" . $courriel . "</strong> ne correspond à <strong>aucun compte déjà créé</strong>.</p>\n" 
			. "<p>Vous pouvez créer un compte à partir de cette adresse, ou vous identifier à un compte existant en utilisant une autre adresse, en cliquant sur l'un des liens ci-dessous.</p>\n" ; 
		//
		se_connecter () ; 
	}
}
//===========================================================================================================================
function formulaire_saisie_pass ( $util_id , $courriel , $type_saisie_pass ) 
// indispensable d'avoir le courriel au cas où l'utilisateur ait besoin de se faire envoyer un nouveau mot de passe car on ne sait pas retrouver le courriel à partir 
{
	echo "<form action = 'index.php' method='post'>\n" ; 
	echo "<p>Mot de passe : " ; 
	echo "<input type='password' size='40' name='pass' value=''/></p>\n"
		. "<p>\n<input type='submit' value='Valider' name='" . $type_saisie_pass . "' />\n" ; 
	//
	// s'il s'agit d'une identification, on propose de faire parvenir un nouveau mot de passe
	// if ( $type_saisie_pass == POST_SAISIE_PASS_IDENTIFICATION ) 
	//	echo "</p>\n<p>\n<input type='submit' value='Me faire parvenir un nouveau mot de passe à cette adresse' name='" 
	//		. POST_SAISIE_COURRIEL_IDENTIFICATION . "' />\n" ; 
	//
	// on propose dans tous les cas de faire parvenir un nouveau mot de passe
	//echo "</p>\n<p>\n<input type='submit' value='Me faire parvenir un nouveau mot de passe à l'adresse <strong>" . $courriel . "</strong>' name='" 
	//	. POST_NOUVEAU_MOT_DE_PASSE . "' />\n" ; 
	//
	// s'il s'agit de la création d'un compte, inutile car on vient d'en envoyer un ! 
	echo "<input type='hidden' name='util_id' value='" . $util_id . "' />\n" // envoi de l'identifiant de l'utilisateur en caché
		. "<input type='hidden' name='courriel' value='" . $courriel . "' />\n" // envoi du courriel en caché
		// . "<input type='hidden' value='true' name='" . POST_GESTION_COMPTE . "' />\n" // pour aiguiller dans index.php
		. "</p>\n" ; 
	echo "</form>\n\n" ; 
}
//===========================================================================================================================
function traitement_demande_nouveau_mot_de_passe ( $util_id , $courriel , $connexion )
{
	$pass = creer_pass () ; 
	envoyer_pass ( $courriel, $pass ); 
	enregistrer_nouveau_pass ( $util_id , $pass , $connexion ) ; 
	echo "<p>Un <strong>nouveau mot de passe</strong> vous a été envoyé à l'adresse " . $courriel . ".</p>"
		. "<p>Pour vous identifier sur le compte associé à cette adresse, veuillez saisir ce mot de passe dans le champ ci-dessous.</p>" ; 
	formulaire_saisie_pass ( $util_id , $courriel , POST_SAISIE_PASS_IDENTIFICATION ) ;
}
//==============================================================================
// 2 EME POST APRES SAISIE MOT DE PASSE
//==========================================================================================
function traitement_post_saisie_pass ( $util_id , $est_valide_pass , $connexion )
{
	if ( $est_valide_pass )    
	{
		if ( isSet ( $_POST[POST_SAISIE_PASS_CREATION_COMPTE] ) )
			echo "<p>Le mot de passe indiqué est valide, votre compte a été créé, et <strong>vous êtes à présent identifié</strong>.</p>" ; 
		else 
			echo "<p><strong>Le mot de passe indiqué est valide, vous êtes à présent identifié</strong>.</p>" ; 
		echo  "<p>A tout instant, vous pouvez :</p>\n" 
			. "<ul>\n<li><strong>Sauvegarder vos saisies</strong> en cliquant sur l'onglet &quot;<strong>Sauvegarder</strong>&quot; ci-dessus</li>\n" 
			. "<li><strong>Accéder à vos sauvegardes</strong> en cliquant sur l'onglet &quot;<strong>Mes 
				sauvegardes</strong>&quot; ci-dessus</li>\n"
			. "<li><strong>Vous déconnecter</strong> (par exemple afin d'utiliser un autre courriel pour créer un nouveau compte, ou vous connecter à un autre compte existant, ou céder la main à un autre utilisateur) en cliquant sur l'onglet &quot;<strong>Me déconnecter</strong>&quot; ci-dessus (après cette opération, vous ne serez plus identifié, mais vos données de session - celles que vous pouvez visualiser en parcourant les pages du questionnaire - seront conservées).</li>\n"
			. "</ul>\n\n" ; 
	}
	else
	{
		$courriel = $_POST ['courriel'] ; 
		if ( isSet ( $_POST[POST_SAISIE_PASS_CREATION_COMPTE] ) )
			$type_saisie_pass = POST_SAISIE_PASS_CREATION_COMPTE ; 
		else 
			$type_saisie_pass = POST_SAISIE_PASS_IDENTIFICATION ; 			
		echo "<p class='courriel_invalide' ><strong>Mot de passe invalide</strong>.</p>\n" 
			. "<p>Le mot de passe que vous avez indiqué n'est pas celui associé à l'adresse courriel <strong>" . $courriel . "</strong>.</p>\n" 
			. "<p>Veuillez saisir à nouveau votre mot de passe.</p>\n" ; 
		formulaire_saisie_pass ( $util_id , $courriel , $type_saisie_pass ) ; 
		echo "<p>L'envoi du mot de passe peut prendre quelques minutes. Si toutefois vous ne receviez pas ce mot de passe, vous pouvez demander l'envoi d'un nouveau mot de passe à l'adresse courriel &quot;" . $courriel . "&quot; en " 
			.	"<strong><a href='index.php?type_page=" . GESTION_COMPTE . "&amp;page=" . DEMANDE_NOUVEAU_PASS . "&amp;courriel=" . $courriel . "&amp;util_id=" . $util_id . "'>cliquant ici</a></strong>" ; 
	}
}
//==========================================================================================
//        SE DECONNECTER
//==========================================================================================
function demande_confirmation_se_deconnecter () 
{
	echo "<p><strong>Etes-vous sûr de vouloir vous déconnecter&nbsp;?</strong></p>" 
		. "<p>Cette opération annule votre identification (vous ne pourrez plus sauvegarder vos saisies, ni accéder à vos sauvegardes, sans vous reconnecter à nouveau). En revanche cette opération ne remet pas à zéro vos variables de session (celles qui s'affichent dans les champs des pages du questionnaire), celles-ci seront conservées.</p>" ; 
	$url_confirmation = "./index.php?type_page=" . GESTION_COMPTE . "&amp;page=" . CONFIRMER_SE_DECONNECTER ; 
	$url_annulation = "./index.php?type_page=" . GESTION_COMPTE . "&amp;page=" . ANNULER_SE_DECONNECTER ; 
	demande_confirmation ( 'Confirmer' , $url_confirmation , 'Annuler' , $url_annulation ) ; 
}
//==========================================================================================
function se_deconnecter () 
{
	echo "<p>Suite à cette confirmation <strong>vous n'êtes plus identifié</strong> sur ce site. Vous pouvez vous identifier à nouveau (avec le même courriel ou un courriel différent) ou créer un compte (avec un courriel différent) en cliquant sur l'onglet &quot;Se connecter&quot; ci-dessus.</p>\n" ;
	if ( isSet ( $_SESSION [EST_SAISIE_EFFECTUEE] ) )
		echo "<p>Vos saisies (que vous pouvez consulter en parcourant les pages du questionnaire à l'aide du menu ci-contre) n'ont pas été modifiées par cette opération, vous pouvez donc poursuivre le calcul de votre BILAN CARBONE™ Personnel. Si vous souhaitez remettre ces saisies à zéro, cliquez sur l'onglet &quot;Remettre à zéro&quot; ci-dessus.</p>" ; 
	else
		echo "<p>Vous pouvez poursuivre le calcul de votre BILAN CARBONE™ Personnel
			en accédant aux pages du questionnaire à l'aide du menu ci-contre.</p>\n" ; 
}
//==========================================================================================
function annuler_se_deconnecter () 
{
	echo "<p>Suite à cette annulation <strong>vous êtes toujours identifié</strong>. Vous pouvez toujours sauvegarder vos saisies ou accéder à vos sauvegardes à l'aide des onglets ci-dessus. Et vous pouvez bien sûr poursuivre le calcul de votre BILAN CARBONE™ Personnel
		en accédant aux pages du questionnaire à l'aide du menu ci-contre.</p>\n" ; 
}

?>