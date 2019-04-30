<?php 
        $lang=@file_get_contents("lang.tmp");
        @include("lang/languages.php");
        @include("lang/fr.php");
        @include("lang/$lang.php");
?>
