<?php 
ob_start();
session_start();
require_once ('inc_admin/inclusions_admin.php') ; 
ob_end_clean();	//Pour ne pas avoir le tampon "pollué" par les inclusions.
if ( !isSet ( $_SESSION[MODE_ADMIN] ) || $_SESSION[MODE_ADMIN] != true ) {
	header("Location: admin.php"); 
} else {
	header('Content-Disposition: attachment; filename="'.$_GET['file'].'"');
	header("Content-Type: text/sql; charset=ISO-8859-1");
	readfile  (BCKPDIR.'/'.$_GET['file']);
}
?>
