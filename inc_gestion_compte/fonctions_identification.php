<?php 
//==========================================================================================
//        SE CONNECTER
//==========================================================================================
function se_connecter () 
{
	echo "<p>Si vous vous connectez pour la <strong>premi�re fois</strong>, cliquez sur l'onglet &quot;<strong>Cr�er un compte</strong>&quot;.</p>\n"
		. "<p>Si vous d�sirez vous connecter � un compte que vous avez cr�� auparavant, cliquez sur l'onglet &quot;<strong>Me connecter � un compte existant</strong>&quot;.</p>\n\n"
		. "<ul class='confirmation_annulation' >\n"
		. "<li>\n<a href='./index.php?type_page=" . GESTION_COMPTE . "&amp;page=" . CREER_COMPTE . "'>"
		. "Cr�er un compte"
		. "</a>\n</li>\n"
		. "<li>\n<a href='./index.php?type_page=" . GESTION_COMPTE . "&amp;page=" . S_IDENTIFIER . "'>"
		. "Me connecter � un compte existant"
		. "</a>\n</li>\n</ul>\n\n" ; 
}
//================================
//    COURRIEL = ACCES PAR GET
//================================
function creer_compte ()
{
	echo "<p>Pour cr�er un compte, vous devez indiquer dans le champ ci-dessous une adresse de <strong>courriel valide</strong>, 
		puis cliquer sur &quot;<strong>Valider</strong>&quot;.
		Un mot de passe vous sera alors envoy� � cette adresse.</p>\n\n" ; 
	formulaire_saisie_courriel ( POST_SAISIE_COURRIEL_CREATION_COMPTE ) ; 
	echo "<p>Ce courriel sera sauvegard� sous forme <strong>crypt�e</strong>. 
	Cette sauvegarde permet que vous puissiez retrouver vos saisie par la suite lorsque vous vous re-connecterez avec la m�me adresse de courriel. 
	En revanche, il est impossible de retrouver votre v�ritable adresse de courriel � partir de sa forme crypt�e. Autrement dit, il sera impossible de vous identifier ou de vous contacter � partir de cette sauvegarde (et m�me si une personne malveillante parvenait � mettre la main sur notre base de donn�es, elle ne pourrait ni vous identifier ni vous contacter). Par cons�quent, les sauvegardes de vos r�ponses aux questions pos�es par le calculateur peuvent �tre consid�r�es comme <strong>anonymes</strong>.</p>" ; 
}
//==========================================================================================
function s_identifier ()
{
	echo "<p>Veuillez indiquer ci-dessous le <strong>courriel</strong> utilis� pour la cr�ation de ce compte.</p>\n" ; 
	formulaire_saisie_courriel ( POST_SAISIE_COURRIEL_IDENTIFICATION ) ; 
	echo "<p>Le mot de passe associ� � ce courriel vous sera demand� � l'�tape suivante. Si vous ne retrouvez plus ce mot de passe vous pourrez en demander un nouveau.</p>" ; 
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
		// le courriel est invalide, on affiche � nouveau le formulaire de cr�ation du compte 
		echo "<p class='courriel_invalide' ><strong>Courriel invalide</strong>, veuillez recommencer.</p>" ; 
		creer_compte () ; 
	}
	else if ( !accepte_courriels_serveur ( $courriel ) )
	{
		// le serveur n'accepte pas les courriels 
		echo "<p class='courriel_invalide' ><strong>Courriel invalide</strong> 
		(le serveur indiqu� n'accepte pas les courriels), veuillez recommencer.</p>" ; 
		creer_compte () ; 
	}
	else if ( $util_id = est_enregistre_courriel_retourner_util_id ( $courriel , $connexion ) )
	{
		// le courriel est d�j� enregistr� dans la base ! on propose donc � l'utilisateur de saisir son mot de passe, ou de s'en faire envoyer un nouveau par courriel
		echo "<p>L'adresse courriel <strong>" . $courriel . "</strong> correspond � un compte d�j� cr��.</p>\n" 
			. "<ul>\n<li>Pour <strong>vous connecter � ce compte</strong>, saisissez ci-dessous le dernier mot de passe qui vous a �t� envoy� par le webmestre (adresse : &quot;webmestre at bilancarbonepersonnel.org&quot;) � cette adresse de courriel (si vous n'avez jamais redemand� de mot de passe il s'agit du mot de passe envoy� lors de l'ouverture du compte), puis cliquez sur &quot;Valider&quot;.</li>\n" 
			. "<li>Si <strong>vous souhaitez vous connecter � ce compte mais que vous ne retrouvez plus le mot de passe associ�</strong>, "
			. "<strong><a href='index.php?type_page=" . GESTION_COMPTE . "&amp;page=" . DEMANDE_NOUVEAU_PASS . "&amp;courriel=" . $courriel . "&amp;util_id=" . $util_id . "'>cliquez ici</a></strong>" 
			. " pour qu'un nouveau mot de passe vous soit envoy� � l'adresse courriel &quot;" . $courriel . "&quot;.</li>\n" 
			. "<li>Pour <strong>cr�er un compte en utilisant une autre adresse courriel</strong>, <strong><a href='index.php?type_page=". GESTION_COMPTE . "&amp;page=" . CREER_COMPTE . "'>cliquez ici</a></strong></li>\n</ul>\n" ; 
		formulaire_saisie_pass ( $util_id , $courriel , POST_SAISIE_PASS_IDENTIFICATION ) ;
	}
	else
	{
		// le courriel est valide et n'est pas enregistr� dans la base
		$pass = creer_pass () ; 
		envoyer_pass ( $courriel, $pass ); 
		$util_id = enregistrer_courriel_pass_retourner_util_id ( $courriel, $pass, $connexion ) ; 
		echo "<p>Un <strong>mot de passe vous a �t� envoy�</strong> par courrier �lectronique � l'adresse : <strong>" . $courriel . "</strong>.</p>\n"
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
		// le courriel est bien enregistr� en bdd
		echo "<p>L'adresse courriel <strong>" . $courriel . "</strong> correspond bien � un compte cr�� sur ce site.</p>" 
			. "<p>Pour achever votre identification, veuillez saisir ci-dessous le dernier mot de passe qui vous a �t� envoy� par le webmestre (adresse courriel : &quot;webmestre at bilancarbonepersonnel.org&quot;) � l'adresse courriel &quot;" . $courriel . "&quot; (si vous n'avez jamais redemand� de mot de passe il s'agit du mot de passe envoy� lors de l'ouverture du compte), puis cliquez sur &quot;Valider&quot;.</p>\n" 
			. "<p><strong>Si vous ne retrouvez plus ce mot de passe</strong>, "
			. "<strong><a href='index.php?type_page=" . GESTION_COMPTE . "&amp;page=" . DEMANDE_NOUVEAU_PASS . "&amp;courriel=" . $courriel . "&amp;util_id=" . $util_id . "'>cliquez ici</a></strong>" 
			. " pour qu'un nouveau mot de passe vous soit envoy� � l'adresse courriel &quot;" . $courriel . "&quot;." ; 
		//
		formulaire_saisie_pass ( $util_id , $courriel , POST_SAISIE_PASS_IDENTIFICATION ) ;
	}
	else
	{
		echo "<p>L'adresse courriel <strong>" . $courriel . "</strong> ne correspond � <strong>aucun compte d�j� cr��</strong>.</p>\n" 
			. "<p>Vous pouvez cr�er un compte � partir de cette adresse, ou vous identifier � un compte existant en utilisant une autre adresse, en cliquant sur l'un des liens ci-dessous.</p>\n" ; 
		//
		se_connecter () ; 
	}
}
//===========================================================================================================================
function formulaire_saisie_pass ( $util_id , $courriel , $type_saisie_pass ) 
// indispensable d'avoir le courriel au cas o� l'utilisateur ait besoin de se faire envoyer un nouveau mot de passe car on ne sait pas retrouver le courriel � partir 
{
	echo "<form action = 'index.php' method='post'>\n" ; 
	echo "<p>Mot de passe : " ; 
	echo "<input type='password' size='40' name='pass' value=''/></p>\n"
		. "<p>\n<input type='submit' value='Valider' name='" . $type_saisie_pass . "' />\n" ; 
	//
	// s'il s'agit d'une identification, on propose de faire parvenir un nouveau mot de passe
	// if ( $type_saisie_pass == POST_SAISIE_PASS_IDENTIFICATION ) 
	//	echo "</p>\n<p>\n<input type='submit' value='Me faire parvenir un nouveau mot de passe � cette adresse' name='" 
	//		. POST_SAISIE_COURRIEL_IDENTIFICATION . "' />\n" ; 
	//
	// on propose dans tous les cas de faire parvenir un nouveau mot de passe
	//echo "</p>\n<p>\n<input type='submit' value='Me faire parvenir un nouveau mot de passe � l'adresse <strong>" . $courriel . "</strong>' name='" 
	//	. POST_NOUVEAU_MOT_DE_PASSE . "' />\n" ; 
	//
	// s'il s'agit de la cr�ation d'un compte, inutile car on vient d'en envoyer un ! 
	echo "<input type='hidden' name='util_id' value='" . $util_id . "' />\n" // envoi de l'identifiant de l'utilisateur en cach�
		. "<input type='hidden' name='courriel' value='" . $courriel . "' />\n" // envoi du courriel en cach�
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
	echo "<p>Un <strong>nouveau mot de passe</strong> vous a �t� envoy� � l'adresse " . $courriel . ".</p>"
		. "<p>Pour vous identifier sur le compte associ� � cette adresse, veuillez saisir ce mot de passe dans le champ ci-dessous.</p>" ; 
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
			echo "<p>Le mot de passe indiqu� est valide, votre compte a �t� cr��, et <strong>vous �tes � pr�sent identifi�</strong>.</p>" ; 
		else 
			echo "<p><strong>Le mot de passe indiqu� est valide, vous �tes � pr�sent identifi�</strong>.</p>" ; 
		echo  "<p>A tout instant, vous pouvez :</p>\n" 
			. "<ul>\n<li><strong>Sauvegarder vos saisies</strong> en cliquant sur l'onglet &quot;<strong>Sauvegarder</strong>&quot; ci-dessus</li>\n" 
			. "<li><strong>Acc�der � vos sauvegardes</strong> en cliquant sur l'onglet &quot;<strong>Mes 
				sauvegardes</strong>&quot; ci-dessus</li>\n"
			. "<li><strong>Vous d�connecter</strong> (par exemple afin d'utiliser un autre courriel pour cr�er un nouveau compte, ou vous connecter � un autre compte existant, ou c�der la main � un autre utilisateur) en cliquant sur l'onglet &quot;<strong>Me d�connecter</strong>&quot; ci-dessus (apr�s cette op�ration, vous ne serez plus identifi�, mais vos donn�es de session - celles que vous pouvez visualiser en parcourant les pages du questionnaire - seront conserv�es).</li>\n"
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
			. "<p>Le mot de passe que vous avez indiqu� n'est pas celui associ� � l'adresse courriel <strong>" . $courriel . "</strong>.</p>\n" 
			. "<p>Veuillez saisir � nouveau votre mot de passe.</p>\n" ; 
		formulaire_saisie_pass ( $util_id , $courriel , $type_saisie_pass ) ; 
		echo "<p>L'envoi du mot de passe peut prendre quelques minutes. Si toutefois vous ne receviez pas ce mot de passe, vous pouvez demander l'envoi d'un nouveau mot de passe � l'adresse courriel &quot;" . $courriel . "&quot; en " 
			.	"<strong><a href='index.php?type_page=" . GESTION_COMPTE . "&amp;page=" . DEMANDE_NOUVEAU_PASS . "&amp;courriel=" . $courriel . "&amp;util_id=" . $util_id . "'>cliquant ici</a></strong>" ; 
	}
}
//==========================================================================================
//        SE DECONNECTER
//==========================================================================================
function demande_confirmation_se_deconnecter () 
{
	echo "<p><strong>Etes-vous s�r de vouloir vous d�connecter&nbsp;?</strong></p>" 
		. "<p>Cette op�ration annule votre identification (vous ne pourrez plus sauvegarder vos saisies, ni acc�der � vos sauvegardes, sans vous reconnecter � nouveau). En revanche cette op�ration ne remet pas � z�ro vos variables de session (celles qui s'affichent dans les champs des pages du questionnaire), celles-ci seront conserv�es.</p>" ; 
	$url_confirmation = "./index.php?type_page=" . GESTION_COMPTE . "&amp;page=" . CONFIRMER_SE_DECONNECTER ; 
	$url_annulation = "./index.php?type_page=" . GESTION_COMPTE . "&amp;page=" . ANNULER_SE_DECONNECTER ; 
	demande_confirmation ( 'Confirmer' , $url_confirmation , 'Annuler' , $url_annulation ) ; 
}
//==========================================================================================
function se_deconnecter () 
{
	echo "<p>Suite � cette confirmation <strong>vous n'�tes plus identifi�</strong> sur ce site. Vous pouvez vous identifier � nouveau (avec le m�me courriel ou un courriel diff�rent) ou cr�er un compte (avec un courriel diff�rent) en cliquant sur l'onglet &quot;Se connecter&quot; ci-dessus.</p>\n" ;
	if ( isSet ( $_SESSION [EST_SAISIE_EFFECTUEE] ) )
		echo "<p>Vos saisies (que vous pouvez consulter en parcourant les pages du questionnaire � l'aide du menu ci-contre) n'ont pas �t� modifi�es par cette op�ration, vous pouvez donc poursuivre le calcul de votre BILAN CARBONE� Personnel. Si vous souhaitez remettre ces saisies � z�ro, cliquez sur l'onglet &quot;Remettre � z�ro&quot; ci-dessus.</p>" ; 
	else
		echo "<p>Vous pouvez poursuivre le calcul de votre BILAN CARBONE� Personnel
			en acc�dant aux pages du questionnaire � l'aide du menu ci-contre.</p>\n" ; 
}
//==========================================================================================
function annuler_se_deconnecter () 
{
	echo "<p>Suite � cette annulation <strong>vous �tes toujours identifi�</strong>. Vous pouvez toujours sauvegarder vos saisies ou acc�der � vos sauvegardes � l'aide des onglets ci-dessus. Et vous pouvez bien s�r poursuivre le calcul de votre BILAN CARBONE� Personnel
		en acc�dant aux pages du questionnaire � l'aide du menu ci-contre.</p>\n" ; 
}

?>