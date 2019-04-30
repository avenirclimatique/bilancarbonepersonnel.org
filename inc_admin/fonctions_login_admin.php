<?php 
//==========================================================================================
// Login administrateur
//==========================================================================================
function formulaire_login_admin () 
{
	echo "<form method='post' action='admin.php'>\n \n"
		. "<div>\n\n"
		. "<input type='hidden' name='formulaire_login_admin' value='true' />\n\n" 
		. "<table>\n\n"
		. "<tr><td><label for='login_admin'>Login : </label></td>\n"
		. "<td><input type='text' id='login_admin' name='login_admin' size='15' /></td>"
		. "</tr>\n\n<tr>"
		. "<td><label for='pass_admin'>Mot de passe : </label></td>\n"
		. "<td><input type='password' id='pass_admin' name='pass_admin' size='15' /></td>\n"
		. "</tr>\n\n</table>\n\n"
		. "<input type='submit' value='identification' />\n\n"
		. "</div>\n\n</form>\n\n" ; 
}
//==========================================================================================
// Retourne le mot de passe administrateur
//==========================================================================================
function pass_admin ()
{
	$pass_admin = 'coaltoliquid' ;
	$pass_admin = md5 ( $pass_admin ) ;
	return $pass_admin ;
}
//==========================================================================================
// Teste si le login admin est le bon
//==========================================================================================
function est_valide_login_admin ( $login_admin , $pass_admin )
{
	$est_valide_login_admin = true ; 
	if ( $login_admin != 'admin' )
		$est_valide_login_admin = false ; 
	if ( md5 ( $pass_admin ) != pass_admin () )
		$est_valide_login_admin = false ; 
	return $est_valide_login_admin ; 
}

/*
//==========================================================================================
// Afficher qu'on est déjà en mode adiminstrateur
//==========================================================================================
function afficher_deja_admin ()
{
	echo "<p>Vous êtes déjà identifié en tant qu'administrateur. </p>"
		. "<p><strong><a href='index.php'>Cliquez sur ce lien</a></strong> pour accéder au site du calculateur en tant qu'administrateur.</p>"
		. "<p><strong><a href='index.php?" . ACTION . "=deconnexion_admin'>Cliquez sur ce lien</a></strong> pour vous déconneter et accéder au site du calculateur en tant qu'utilisateur anonyme.</p>" ; 
}
*/

?>
