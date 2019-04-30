<?php 
//===============================================================
// nombre maximal de sauvegardes par personnes
define ("NOMBRE_SAUVEGARDE_MAX", 5 );
//===============================================================
// décommenter une ligne ou l'autre suivant qu'on est en local ou non
//define ("CONFIG", "local_emmanuel" );
//define ("CONFIG", "local_maxime" );
define ("CONFIG", "en_ligne");
//===============================================================
// décommenter une ligne ou l'autre suivant qu'on souhaite ou non l'affichage du menu de sauvegarde
//define ("AFFICHE_MENU_SAUVEGARDE", "non" );
define ("AFFICHE_MENU_SAUVEGARDE", "oui" );
//===============================================================
// décommenter une ligne ou l'autre suivant qu'on souhaite ou non le lien de création de la sortie pdf
//define ("PROPOSE_SORTIE_PDF", "non" );
define ("PROPOSE_SORTIE_PDF", "oui" );
//===============================================================
if ( CONFIG == 'local_emmanuel' )
{
	// définitions de constantes pour connexion MySQL
	define ('NOM' , "root");
	define ('PASSE' , "");
	define ('SERVEUR' , "localhost");
	define ('BASE' , "bilan_carbone_personnel");
	define ('PORT', "3306");
	define ('MYSQLDUMP_PATH', 'mysqldump'); //S'il suffit de taper "mysql" en ligne de commande pour accéder au fichier, inutile d'écrire un chemin
	define ('GZIP_PATH', 'gzip'); //S'il suffit de taper "mysql" en ligne de commande pour accéder au fichier, inutile d'écrire un chemin
	
	// niveau d'erreur maximum
	error_reporting (E_ALL) ; 

	// fonction checkdnsrr pour vérifier qu'un serveur accepte les emails, ne fonctionne pas sous windows
	define ('EXISTE_FONCTION_CHECKDNSRR' , FALSE );
}
//===============================================================
else if ( CONFIG == 'local_maxime' )
{
	// définitions de constantes pour connexion MySQL
	define ('NOM' , "root");
	define ('PASSE' , "wfusdfcf");
	define ('SERVEUR' , "localhost");
	define ('BASE' , "BCP");
	define ('PORT', "3306");
	define ('MYSQLDUMP_PATH', 'mysqldump'); //S'il suffit de taper "mysql" en ligne de commande pour accéder au fichier, inutile d'écrire un chemin
	define ('GZIP_PATH', 'gzip'); //S'il suffit de taper "mysql" en ligne de commande pour accéder au fichier, inutile d'écrire un chemin
	
	// niveau d'erreur maximum
	error_reporting (E_ALL) ; 

	// fonction checkdnsrr pour vérifier qu'un serveur accepte les emails, ne fonctionne pas sous windows
	define ('EXISTE_FONCTION_CHECKDNSRR' , FALSE );
}
//===============================================================
else if ( CONFIG == 'en_ligne' )
{
	// définitions de constantes pour connexion MySQL
/*
	define ('NOM' , "risler_bcp");
	define ('PASSE' , "aHIxDYjv");
	define ('SERVEUR' , "mysql5-7");
	define ('BASE' , "risler_bcp");

	define ('NOM' , "avenirclbcp");
	define ('PASSE' , "pzPfaO0g");
	define ('SERVEUR' , "mysql5-11.bdb");
	define ('BASE' , "avenirclbcp");
*/
	
	define ('NOM' , "avenirclbcp1");
	define ('PASSE' , "COKN5EsA");
	define ('SERVEUR' , "mysql51-20.pro");
	define ('BASE' , "avenirclbcp1");
	
	// niveau d'erreur maximum
	error_reporting (E_ALL) ; 
	//error_reporting(E_ERROR | E_WARNING | E_PARSE);

	// fonction checkdnsrr pour vérifier qu'un serveur accepte les emails, ne fonctionne pas sous windows
	define ('EXISTE_FONCTION_CHECKDNSRR' , true );
}
//===============================================================
