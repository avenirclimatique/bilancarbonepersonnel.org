<?php 
// ---------------------------------------------------------------------
// Textes pour le menu du questionnaire
// ---------------------------------------------------------------------
$texte = array() ;

$texte[AJOUTER]="Ajouter ";
//===========================================
// Logement
//===========================================
$texte[LOGEMENT]="Logement";
//
$texte[GENERAL]="G&eacute;n&eacute;ral";
$texte[CONSO_ENERGIE]="Consommation d'énergie";
$texte[EQUIPEMENT]="Equipement";
$texte[AJOUTER . '_' . LOGEMENT]="Ajouter un logement";
//===========================================
// Transports
//===========================================
$texte[TRANSPORT]="Transports";
//
$texte[VOITURE]="Voiture";
$texte[AJOUTER . '_' . VOITURE]="Ajouter une voiture";
$texte[DEUX_ROUES]="Deux-roues";
$texte[AJOUTER . '_' . DEUX_ROUES]="Ajouter un deux-roues";
$texte[VOL_AVION]="Vol en avion";
$texte[AJOUTER . '_' . VOL_AVION]="Ajouter un vol en avion";
$texte[TRANSPORT_COMMUN]="Transports en commun";
//===========================================
// Alimentation
//===========================================
$texte[ALIMENTATION]="Alimentation";
//
$texte[VIANDE_POISSON_LAITAGE]="Viande, poisson, laitages";
$texte[FRUIT_LEGUME]="Fruits et l&eacute;gumes";
$texte[BOISSON]="Boissons";
//===========================================
// Consommation
//===========================================
$texte[CONSOMMATION]="Consommation";
//
$texte[HABILLEMENT]="Habillement";
$texte[VIE_QUOTIDIENNE]="Vie quotidienne";
$texte[LOISIR]="Loisirs";

$TEXT['menu_questionnaire'] = $texte ; 

// ==============
// REPONSES
// ==============
$texte = array() ;

$texte['valider'] = "Valider" ; 
require_once ( "./lang/fr/fr_questionnaire_reponse.php" ) ; 

$TEXT['questionnaire'] = $texte ; 

// ---------------------------------------------------------------------
// QUESTIONS
// ---------------------------------------------------------------------

// ---------------------------------------------------------------------
// LOGEMENT
// ---------------------------------------------------------------------

// ---------------------------------------------------------------------
// Page Logement -> Général
// ---------------------------------------------------------------------
$texte = array() ;

$texte['nb_personne_intitule'] = "Combien de personnes vivent dans ce logement&nbsp;?";
$texte['nb_personne_aide'] = "<p>Pour estimer les &eacute;missions qui doivent vous être imput&eacute;es &agrave; titre individuel, l'ensemble des &eacute;missions li&eacute;es &agrave; ce logement seront divis&eacute;es par ce nombre. </p><p>Vous pouvez entrer un nombre d&eacute;cimal (c-a-d &agrave; virgules) pour tenir compte d'une situation particuli&egrave;re. Pour davantage d'informations suivre le lien vers la page d'explications associ&eacute;e &agrave; la question.</p>" ; 
// -----------------------------------
$texte['date_intitule'] = "A quelle date ce logement a-t-il &eacute;t&eacute; construit&nbsp;?";
$texte['date_aide'] = "<p>Pr&eacute;cision&nbsp;: la date attendue est la date d'achêvement. Si votre logement a &eacute;t&eacute; construit avant 1975 mais a fait l'objet d'importants travaux d'isolation, veuillez s&eacute;lectionner 'Apr&egrave;s 1975'.</p>";

// -----------------------------------
$texte['surface_intitule'] = "Quelle est la surface de ce logement&nbsp;?";
$texte['surface_aide'] = "<p>Cette valeur ne doit pas prendre en compte les surfaces non chauff&eacute;es telles que garage, combles, grenier et sous-sol non am&eacute;nag&eacute;s.</p>";
// -----------------------------------
$texte['departement_intitule'] = "Dans quel d&eacute;partement se trouve ce logement&nbsp;?";
$texte['departement_aide'] = "<p>Selon les zones g&eacute;ographiques, la puissance de chauffage requise n'est pas la même. Cette r&eacute;ponse	sera utilis&eacute;e pour estimer les &eacute;missions li&eacute;es au chauffage de ce logement (et uniquement dans l'&eacute;ventualit&eacute; où vous ne puissiez pas vous appuyer sur une facture pour r&eacute;pondre avec pr&eacute;cision aux questions de la page 'Logement->Consommations').</p> ";

// -----------------------------------
$texte['individuel_collectif_intitule'] = "S'agit-il d'un logement individuel ou collectif&nbsp;?";
$texte['individuel_collectif_aide'] = "<p>Logement individuel&nbsp;: maison particuli&egrave;re. Logement collectif&nbsp;: appartement d'un immeuble collectif (comprenant au moins deux appartements), chambre d'une cit&eacute; universitaire, ...</p>";
//$texte['logement_individuel'] = "Logement individuel" ; 
//$texte['logement_collectif'] = "Logement collectif" ; 
// -----------------------------------
$texte['type_chauffage_intitule'] = "Quel est le type de chauffage de ce logement&nbsp;?";
$texte['type_chauffage_aide'] = "<p>Le type de chauffage attendu est le type de chauffage dominant&nbsp;: par exemple, si vous avez une chaudi&egrave;re individuelle au fioul pour le chauffage, un chauffage &eacute;lectrique d'appoint dans un bureau, et une chemin&eacute;e d'appoint pour les soir&eacute;es d'hiver, s&eacute;lectionnez 'Fioul individuel'.</p>";
// -----------------------------------
$texte['type_ecs_intitule'] = "Quel est le type de chauffage de l'eau chaude sanitaire de ce logement&nbsp;?";
$texte['type_ecs_aide'] = "<p>Si vous disposez de plusieurs types de chauffage de l'eau chaude sanitaire de votre logement, veuillez indiquer le type dominant, celui qui couvre la majorit&eacute; de vos besoins.</p>";
// -----------------------------------
$texte['type_cuisson_intitule'] = "Quel est le type d'&eacute;nergie utilis&eacute;e pour la cuisson des aliments dans ce logement&nbsp;?";
$texte['type_cuisson_aide'] = "<p>Si vous utilisez &agrave; la fois le Gaz Naturel et l'Electricit&eacute; pour la cuisson, indiquez le mode de cuisson dominant.</p>";

$TEXT[GENERAL] = $texte ; 

// ---------------------------------------------------------------------
// Page Logement -> Consommations
// ---------------------------------------------------------------------
$texte = array() ;
// -------------------------------------------------------------------------------------------------------
// Consommation d'Electricit&eacute;
// -------------------------------------------------------------------------------------------------------
// $texte['conso_mise_en_garde_aide'] = "<p>Attention&nbsp;: les questions pos&eacute;es ci-dessous d&eacute;pendent des r&eacute;ponses apport&eacute;es dans la page logement->g&eacute;n&eacute;ral pour ce logement. Par cons&eacute;quent&nbsp;:</p><ul><li>Vous devez pr&eacute;alablement avoir compl&eacute;t&eacute; la page logement->g&eacute;n&eacute;ral pour ce logement avant de saisir toute information sur cette page.</li><li>Toute modification de la page logement->g&eacute;n&eacute;ral pour ce logement n&eacute;cessitera de votre part une nouvelle visite de cette pr&eacute;sente page logement->consommation afin de vous assurer que vos saisies sont bien conserv&eacute;es, compl&egrave;tes, et toujours valides.</li></ul>" ; 
// -------------------------------------------------------------------------------------------------------
// Consommation d'Electricit&eacute;
// -------------------------------------------------------------------------------------------------------
$texte['electricite_intitule'] = "Quelle est la consommation <strong class='facteur_temporel'>annuelle</strong> en Electricit&eacute; de ce logement&nbsp;?";
// tout &eacute;lectrique
$texte['electricite_aide'] = "<p>La valeur num&eacute;rique requise ici est la consommation totale de ce logement en Electricit&eacute; (en kWh et <strong class='facteur_temporel'>par an</strong>). Si vous avez indiqué en page Logement->general qu'un ou plusieurs usages (parmi le chauffage, l'eau chaude sanitaire, et la cuisson des aliments) fonctionnent à l'électricité pour ce logement, la consommation indiquée doit prendre en compte ce ou ces usage(s). </p>";
// -------------------------------------------------------------------------------------------------------
// Consommation de Gaz Naturel
// -------------------------------------------------------------------------------------------------------
$texte['gaz_naturel_intitule'] = "Quelle est la consommation <strong class='facteur_temporel'>annuelle</strong> en Gaz Naturel de ce logement&nbsp;?";
$texte['gaz_naturel_aide'] ="<p>La valeur num&eacute;rique requise ici est la consommation totale de ce logement en Gaz Naturel (en kWh et <strong class='facteur_temporel'>par an</strong>). Cette consommation doit prendre en compte le ou les usage(s) (parmi le chauffage, l'eau chaude sanitaire, et la cuisson des aliments) dont vous avez indiqué en page Logement->general qu'ils fonctionnaient au gaz naturel.
.</p>
<p>NB&nbsp;: une bouteille de 13 kg de gaz repr&eacute;sente 165 kWh.</p>";
// -------------------------------------------------------------------------------------------------------
// Consommation de fioul
// -------------------------------------------------------------------------------------------------------
$texte['fioul_intitule'] = "Quelle est la consommation <strong class='facteur_temporel'>annuelle</strong> en Fioul de ce logement&nbsp;?";
// chauffage et eau chaude sanitaire au fioul
$texte['fioul_aide'] = "<p>La valeur num&eacute;rique requise ici est la consommation totale de ce logement en Fioul (en kWh et <strong class='facteur_temporel'>par an</strong>). Cette consommation doit prendre en compte le ou les usage(s) (parmi le chauffage et l'eau chaude sanitaire) dont vous avez indiqué en page Logement->general qu'ils fonctionnaient au fioul.</p>
<p>NB&nbsp;: on pourra prendre l'&eacute;quivalence suivante&nbsp;: 1 litre de Fioul = 10 kWh pour calculer cette valeur.</p>";
// -------------------------------------------------------------------------------------------------------
// Consommation de GPL
// -------------------------------------------------------------------------------------------------------
$texte['gpl_intitule'] = "Quelle est la consommation <strong class='facteur_temporel'>annuelle</strong> en GPL (Butane ou Propane) de ce logement&nbsp;?";
$texte['gpl_aide'] = "<p>La valeur num&eacute;rique requise ici est la consommation totale de ce logement en GPL (en kWh et <strong class='facteur_temporel'>par an</strong>). Cette consommation doit prendre en compte le ou les usage(s) (parmi le chauffage et l'eau chaude sanitaire) dont vous avez indiqué en page Logement->general qu'ils fonctionnaient au GPL.</p>
<p>NB&nbsp;: une bouteille de 13 kg de gaz repr&eacute;sente 165 kWh.</p>";
// -------------------------------------------------------------------------------------------------------
// Consommation de charbon
// -------------------------------------------------------------------------------------------------------
$texte['charbon_intitule'] = "Quelle est la consommation <strong class='facteur_temporel'>annuelle</strong> en Charbon de ce logement&nbsp;?";
$texte['charbon_aide'] = "<p>La valeur num&eacute;rique requise ici est la consommation de ce logement en Charbon (en kWh et <strong class='facteur_temporel'>par an</strong>) utilis&eacute; pour le chauffage. </p>
<p>NB&nbsp;: on prendra 1 kg de charbon = 8,5 kWh pour calculer cette valeur.</p>";
// -------------------------------------------------------------------------------------------------------
// Consommation de chauffage urbain
// -------------------------------------------------------------------------------------------------------
$texte['chauffage_urbain_intitule'] = "Quelle est la consommation <strong class='facteur_temporel'>annuelle</strong> en chauffage urbain de ce logement&nbsp;?";
$texte['chauffage_urbain_aide'] = "<p>La valeur num&eacute;rique requise ici est la consommation totale de ce logement en chauffage urbain (en kWh et <strong class='facteur_temporel'>par an</strong>). Cette consommation doit prendre en compte le ou les usage(s) (parmi le chauffage et l'eau chaude sanitaire) dont vous avez indiqué en page Logement->general qu'ils fonctionnaient au chauffage urbain.</p>";
// -------------------------------------------------------------------------------------------------------
// Consommation d'&eacute;lectricit&eacute; parties communes
// -------------------------------------------------------------------------------------------------------
$texte['electricite_parties_communes_intitule'] = "Quelle est la consommation <strong class='facteur_temporel'>annuelle</strong> en Electricit&eacute; des parties communes de ce logement collectif&nbsp;?";
$texte['electricite_parties_communes_aide'] = "<p>La valeur num&eacute;rique requise ici est la consommation &eacute;lectrique (en kWh et <strong class='facteur_temporel'>par an</strong>) utilis&eacute;e pour le chauffage, l'&eacute;clairage des parties communes ainsi que l'ascenceur, la VMC, les pompes de circulation (si chauffage / climatisation centrale). Il faut bien entendu donner une valeur au prorata du nombre d'habitants ou de vos milli&egrave;mes (part des charges communes). Exemple&nbsp;: Si vous êtes &eacute;tudiant dans une cit&eacute; Universitaire de 200 &eacute;tudiants et que la consommation totale des parties communes est de 10 000 kWh par an, vous saisirez la valeur de 50 kWh.</p>";

$TEXT[CONSO_ENERGIE] = $texte ; 

// ---------------------------------------------------------------------
// Page Logement -> &eacute;quipement (appareils) meubles et travaux
// ---------------------------------------------------------------------
$texte = array() ;

$texte['intitule_intitule'] = "Pour chacun des types d'appareils suivants, veuillez indiquer le nombre d'appareils de ce type achet&eacute;(s) il y a <strong class='facteur_temporel'>moins de dix ans</strong>, dont ce logement est &eacute;quip&eacute;.";
$texte['nb_frigo_intitule'] = "R&eacute;frig&eacute;rateur(s) achet&eacute;(s) il y a moins de 10 ans&nbsp;:";
$texte['nb_frigo_aide'] = "<p>Avec ou sans partie cong&eacute;lateur int&eacute;gr&eacute;e.</p>";
$texte['nb_congelateur_intitule'] = "Cong&eacute;lateur(s) achet&eacute;(s) il y a moins de 10 ans&nbsp;:";
$texte['nb_congelateur_aide'] = "<p>Seulement les cong&eacute;lateurs ind&eacute;pendants du r&eacute;frig&eacute;rateur.</p>";
$texte['nb_lave_vaisselle_intitule'] = "Lave-vaisselle achet&eacute;(s) il y a moins de 10 ans&nbsp;:";
$texte['nb_lave_linge_intitule'] = "Lave-linge achet&eacute;(s) il y a moins de 10 ans&nbsp;:";
$texte['nb_seche_linge_intitule'] = "S&egrave;che-linge achet&eacute;(s) il y a moins de 10 ans&nbsp;:";
$texte['nb_cuisiniere_intitule'] = "Cuisini&egrave;re(s) achet&eacute;e(s) il y a moins de 10 ans&nbsp;:";
// -----------------------------------
$texte['meuble_intitule'] = "Combien d&eacute;pensez-vous en moyenne (et en ordre de grandeur) <strong class='facteur_temporel'>par an</strong> en achats de meubles (neufs) pour ce logement&nbsp;?";
$texte['meuble_aide'] = "<p>Faire par exemple la moyenne sur les cinq derni&egrave;res ann&eacute;es. Si vous ne disposez pas du nombre pr&eacute;cis, entrez simplement un ordre de grandeur. Ne tenez pas compte des meubles achetés d'occasion et/ou sur des brocantes.</p>" ; 
// -----------------------------------
$texte['travaux_intitule'] = "Combien d&eacute;pensez-vous en moyenne (et en ordre de grandeur) <strong class='facteur_temporel'>par an</strong> en travaux de petite r&eacute;novation (isolation, plomberie, peinture, moquette, &eacute;lectricit&eacute;, ...) pour ce logement&nbsp;?";
$texte['travaux_aide'] = "<p>Faire par exemple la moyenne sur les cinq derni&egrave;res ann&eacute;es. Si vous ne disposez pas du nombre pr&eacute;cis, entrez simplement un ordre de grandeur.</p>" ; 

$TEXT[EQUIPEMENT] = $texte ; 
// ---------------------------------------------------------------------
// TRANSPORTS
// ---------------------------------------------------------------------

// ---------------------------------------------------------------------
// Page Transports -> Voiture
// ---------------------------------------------------------------------
$texte = array() ;

// -----------------------------------
$texte['motorisation_intitule'] = "De quel type de motorisation est &eacute;quip&eacute;e cette voiture&nbsp;?";	
// -----------------------------------
$texte['kilometrage_intitule'] = "Combien de kilom&egrave;tres parcourez-vous en moyenne <strong class='facteur_temporel'>par an</strong>  avec cette voiture&nbsp;?";	
$texte['kilometrage_aide'] = "<p>Vous pouvez d&eacute;duire cette valeur du compteur kilom&eacute;trique de la voiture (en divisant une distance totale parcourue par la p&eacute;riode correspondante en nombre d'ann&eacute;es) ou donner une valeur d'estimation moyenne.</p>";
// -----------------------------------
$texte['puissance_intitule'] = "Quelle est la puissance fiscale de cette voiture (en CV = Chevaux Fiscaux)&nbsp;?";	
$texte['puissance_aide'] = "<p>La puissance fiscale d'un v&eacute;hicule est notifi&eacute;e &agrave; la colonne 'P6' sur les nouvelles cartes grises. Vous pouvez aussi la retrouver sur le site 
<a href='http://fiches-auto.lacentrale.fr/' onclick='window.open(this.href); return false;' >http://fiches-auto.lacentrale.fr/</a>.</p>";
// -----------------------------------
$texte['type_trajet_intitule'] = "Quel type de trajet effectuez-vous avec cette voiture&nbsp;?";
// -----------------------------------
$texte['consommation_intitule'] = "Quelle est la consommation moyenne de cette voiture en litres de carburant pour 100 kilom&egrave;tres parcourus&nbsp;?";
$texte['consommation_aide'] = "<p>Vous pouvez d&eacute;duire cette information du nombre de kilom&egrave;tres moyens que vous parcourez avec un plein. Certaines voitures r&eacute;centes donnent ce type d'information directement sur le tableau de bord.</p>";
// -----------------------------------
$texte['age_voiture_intitule'] = "Quel est l'âge de cette voiture&nbsp;?";
// -----------------------------------
$texte['responsabilite_intitule'] = "Quel est le nombre de personnes qui utilisent cette voiture&nbsp;?";
$texte['responsabilite_aide'] = "<p>Cette question permet de ne vous affecter qu'une partie des émissions associées
à l'utilisation de cette voiture. Les &eacute;missions qui vous seront imput&eacute;es sont le total des &eacute;missions engendr&eacute;es par l'utilisation de cette voiture divis&eacute; par le nombre d'utilisateurs. Si vous êtes le seul utilisateur de cette 
voiture répondez '1'.</p>";
//$texte['transport_voiture_responsabilite_reponse1'] = "Je les prends &agrave; mon compte";
//$texte['transport_voiture_responsabilite_reponse2'] = "Je les r&eacute;partis (indiquer alors le nombre de personnes)";

$TEXT[VOITURE] = $texte ; 
// ---------------------------------------------------------------------
// Page Transports -> Deux-roues
// ---------------------------------------------------------------------
$texte = array() ;

// -----------------------------------
$texte['puissance_intitule'] = "Quelle est la cylindrée de ce deux-roues motoris&eacute;&nbsp;?";
$texte['puissance_aide'] = "<p>La cat&eacute;gorie 'cyclomoteur 50 cm3' regroupe les mobylettes et les scooters.</p>";
// -----------------------------------
$texte['distance_intitule'] = "Quelle distance parcourez-vous en moyenne <strong class='facteur_temporel'>par an</strong> avec ce deux-roues motoris&eacute;&nbsp;?"; 
$texte['distance_aide'] = "<p>Vous pouvez d&eacute;duire cette valeur du compteur kilom&eacute;triques de votre deux-roues motoris&eacute; (en divisant une distance totale parcourue par la p&eacute;riode correspondante en nombre d'ann&eacute;es). Si plusieurs personnes utilisent ce deux-roues, saisissez le prorata de la distance correspondant &agrave; votre utilisation. Ne comptez que la moiti&eacute; de la distance parcourue &agrave; deux - vous et une autre personne - sur ce deux-roues.</p>";
// -----------------------------------
$texte['consommation_intitule'] = "Quelle est la consommation moyenne de ce deux-roues motoris&eacute; en litres de carburant pour 100 kilom&egrave;tres parcourus&nbsp;?";
$texte['consommation_aide'] = "<p>Vous pouvez d&eacute;duire cette information du nombre de kilom&egrave;tres moyens que vous parcourez avec un plein.</p>";

$TEXT[DEUX_ROUES] = $texte ;

// ---------------------------------------------------------------------
// Page Transports -> Avion
// ---------------------------------------------------------------------
$texte = array() ;

$texte['aide_aide'] = "<p>Saisissez tous les vols en avion effecutés depuis <strong class='facteur_temporel'>un an</strong> (vous pouvez également saisir le nombre de  
de vols en avion que vous effectuez <strong class='facteur_temporel'>en moyenne chaque année</strong>). 
</p><p>
Vous pouvez regrouper en une seule saisie tous les vols (aller simple) d'un même 'type', c'est-&agrave;-dire de même longueur en km (ou &agrave; peu pr&egrave;s) et effectu&eacute;s dans une même classe. Il vous suffit d'indiquer ci-dessous le nombre de vol de ce 'type'. 
</p><p>
Les vols d'un 'type' diff&eacute;rent, c'est-&agrave;-dire dont la longueur en km est sensiblement diff&eacute;rente, ou qui ont &eacute;t&eacute; effectu&eacute;s dans une classe diff&eacute;rente, doivent être saisis s&eacute;par&eacute;ment (en utilisant le bouton 'ajouter vol en avion' dans le menu d'accès aux pages du questionnaire).
</p><p>
Si vous effectuez en moyenne un nombre non entier de vols en avion d'un certain type par an (par exemple un vol aller-retour tous les trois ans), vous pouvez saisir un nombre à virgule dans le champ 'nombre de vols aller-simple' ci-dessous.
</p>";
// -----------------------------------
$texte['nb_vols_intitule'] = "Combien de vols aller-simple de ce type avez-vous effectu&eacute;&nbsp;?";
$texte['nb_vols_aide'] = "<p>Indiquez ici le nombre total de vols aller-simple.</p>";
// -----------------------------------
$texte['distance_intitule'] = "Quelle est la longueur en km de ce(s) vol(s) aller-simple&nbsp;?";
$texte['distance_aide'] = "<p>Vous pouvez calculer la distance entre deux a&eacute;roports sur le site 
<a href='http://www.notre-planete.info/geographie/outils/distances.php' >http://www.notre-planete.info/geographie/outils/distances.php</a>.</p>";
// -----------------------------------
$texte['classe_intitule'] = "Lors de ce(s) vol(s), en quelle classe avez-vous voyag&eacute;&nbsp;?";

$TEXT[VOL_AVION] = $texte ;

// ---------------------------------------------------------------------
// Page Transports -> Transports en commun
// ---------------------------------------------------------------------
$texte = array() ;

$texte['train_intitule'] = "Quelle distance parcourez-vous en moyenne <strong class='facteur_temporel'>par mois</strong> en train&nbsp;?"; 
$texte['train_aide'] = "<p>La valeur attendue est une estimation de la distance parcourue <strong class='facteur_temporel'>par mois</strong>, moyenn&eacute;e sur l'ann&eacute;e. Pour estimer pr&eacute;cis&eacute;ment les distances, vous pouvez utiliser le site <a href='http://www.mappy.fr' onclick='window.open(this.href); return false;' >http://www.mappy.fr</a>.</p>";
// -----------------------------------
$texte['bus_intitule'] = "Combien de temps (en <strong class='facteur_temporel'>minutes</strong>) passez-vous en moyenne <strong class='facteur_temporel'>par semaine</strong> dans des transports en commun de proximit&eacute; non propuls&eacute;s &agrave; l'Electricit&eacute;&nbsp;?";
$texte['bus_aide'] = "<p>Transports en commun de proximit&eacute; non propuls&eacute;s &agrave; l'Electricit&eacute;&nbsp;: autobus (diesel ou GPL), autocars, train express r&eacute;gional sur ligne non &eacute;lectrifi&eacute;e. </p><p>La valeur attendue est une estimation du temps pass&eacute; <strong class='facteur_temporel'>par semaine</strong>, moyenn&eacute;e sur l'ann&eacute;e.</p>";
// -----------------------------------
$texte['rer_intitule'] = "Combien de temps (en <strong class='facteur_temporel'>minutes</strong>) passez-vous en moyenne <strong class='facteur_temporel'>par semaine</strong> dans des transports en commun de proximit&eacute; propuls&eacute;s &agrave; l'Electricit&eacute;&nbsp;?";
$texte['rer_aide'] = "<p>Transports en commun de proximit&eacute; propuls&eacute;s &agrave; l'Electricit&eacute;&nbsp;: tramway, m&eacute;tro, RER, autobus &agrave; propulsion &eacute;lectrique (trolley-bus), train express r&eacute;gional sur ligne &eacute;lectrique. </p><p>La valeur attendue est une estimation du temps pass&eacute; <strong class='facteur_temporel'>par semaine</strong>, moyenn&eacute;e sur l'ann&eacute;e.</p>";

$TEXT[TRANSPORT_COMMUN] = $texte ;

// ---------------------------------------------------------------------
// ALIMENTATION
// ---------------------------------------------------------------------

// ---------------------------------------------------------------------
// Page Alimentation -> viandes poissons laitages
// ---------------------------------------------------------------------
$texte = array() ;

$texte['aide'] ="<p>Les informations demand&eacute;es sur cette page ne concernent que votre consommation individuelle (et non celle de l'ensemble des personnes partageant votre foyer). Elles doivent tenir compte des repas que vous prenez en collectivit&eacute; ou au restaurant, même dans un cadre professionnel.</p><p>Une estimation même impr&eacute;cise suffit &agrave; donner un bon ordre de grandeur. </p><p>Vous pouvez saisir des nombres  d&eacute;cimaux (c-à-d &agrave; virgules).</p>";
// --------------------------------------------------
$texte['viande_rouge_intitule'] = "Quelle quantit&eacute; de viande rouge consommez-vous <strong class='facteur_temporel'>par mois</strong> en moyenne&nbsp;?";
$texte['viande_rouge_aide'] = "<p>Viande rouge&nbsp;: ovins, bovins et &eacute;quid&eacute;s, c'est-&agrave;-dire&nbsp;: boeuf, veau, mouton, cheval, ... </p><p>Aide&nbsp;: un steak hach&eacute; = 100 g, un steak = 150 g, une entrecôte = 170 g, une escalope de veau = 120 g, foie de boeuf = 160 g.</p>";
// --------------------------------------------------
$texte['porc_intitule'] = "Quelle quantit&eacute; de viande de porc (charcuterie comprise) consommez-vous <strong class='facteur_temporel'>par mois</strong> en moyenne&nbsp;?";
$texte['porc_aide'] = "<p>Saucisson, jambon, lard, pât&eacute;, côte de porc, ... </p><p>Aide&nbsp;: une tranche de jambon blanc = 45 g, une côte de porc = 140 g, une saucisse ou une merguez = 55 g.</p>";
// --------------------------------------------------
$texte['viande_blanche_intitule'] = "Quelle quantit&eacute; de volaille consommez-vous <strong class='facteur_temporel'>par mois</strong> en moyenne&nbsp;?";	$texte['viande_blanche_aide'] = "<p>Volaille&nbsp;: poulet, canard, dinde, ... </p><p>Aide&nbsp;: un poulet = 1 &agrave; 2.5 kg, une cuisse de poulet = 180 g, une escalope de dinde = 120 g.</p>";
// --------------------------------------------------
$texte['poisson_intitule'] = "Quelle quantit&eacute; de poisson consommez-vous <strong class='facteur_temporel'>par mois</strong> en moyenne&nbsp;?";
$texte['poisson_aide'] = "<p>Poisson frais, poisson surgel&eacute;, produits de la mer, y compris en boîtes de conserves.</p>";
// --------------------------------------------------
$texte['provenance_poisson_intitule'] = "Votre consommation de poisson est majoritairement constitu&eacute;e de&nbsp;:";
$texte['provenance_poisson_aide'] = "<p>Poisson de mer&nbsp;: thon, sardine, merlu, crevettes, fruits de mer, ... </p><p>Poisson de rivi&egrave;re&nbsp;: truite, saumon, carpe, ...</p>";
// --------------------------------------------------
$texte['fromage_intitule'] = "Quelle quantit&eacute; de fromage et de beurre consommez-vous <strong class='facteur_temporel'>par mois</strong> en moyenne&nbsp;?";
$texte['fromage_aide'] = "<p>Un camembert = 250 g, un coulommier = 350 g, une plaquette de beurre = 250 g.</p>";
// --------------------------------------------------
$texte['yaourt_intitule'] = "Quelle quantit&eacute; de laitages (yaourts, fromage blanc) consommez-vous <strong class='facteur_temporel'>par mois</strong> en moyenne&nbsp;?";
$texte['yaourt_aide'] = "<p>un yaourt = 125 g, un pot de fromage blanc = 500 g ou 1 kg.</p>";
// --------------------------------------------------
$texte['lait_intitule'] = "Quelle quantit&eacute; de lait consommez-vous <strong class='facteur_temporel'>par mois</strong> en moyenne&nbsp;?";
// --------------------------------------------------
$texte['bio_intitule'] = "En moyenne, quel pourcentage de vos consommations de viande, poisson, et laitages est constitu&eacute; de produits d'origine biologique (bio)&nbsp;?";
$texte['bio_aide'] = "<p>Entrez un nombre entre 0 et 100.</p>";

$TEXT[VIANDE_POISSON_LAITAGE]= $texte ;

// ---------------------------------------------------------------------
// Page Alimentation -> Fruits et l&eacute;gumes
// ---------------------------------------------------------------------
$texte = array() ;

$texte['aide_aide'] ="<p>Les informations demand&eacute;es sur cette page ne concernent que votre consommation individuelle (et non celle de l'ensemble	des personnes partageant votre foyer). Elles doivent tenir compte des repas que vous prenez en collectivit&eacute; ou au restaurant, même dans un cadre professionnel.</p><p>Une estimation même impr&eacute;cise suffit &agrave; donner un bon ordre de grandeur. </p><p>Vous pouvez saisir des nombres d&eacute;cimaux (c-à-d &agrave; virgules).</p><p>Pour connaître les saisons des fruits et légumes en France, vous pouvez (par exemple) consulter le tableau proposé par <a href='http://www.defipourlaterre.org/fraise/pdf/affiche-saisons.pdf' onclick='window.open(this.href); return false;' >la fondation Nicolas Hulot</a> ou par <a href='http://www.consoglobe.com/pgz52-pgz_manger-mieux.html'>le site Consoglobe</a> sur le sujet.</p>";
// --------------------------------------------------
$texte['tomates_intitule'] = "Quelle quantit&eacute; de tomates (fraîches) consommez-vous <strong class='facteur_temporel'>par an</strong> hors saison (de d&eacute;cembre &agrave; mai) en moyenne&nbsp;?";
// --------------------------------------------------
$texte['fraises_intitule'] = "Quelle quantit&eacute; de fruits rouges (fraises, cerises, ...) consommez-vous <strong class='facteur_temporel'>par an</strong> hors saison (d'octobre &agrave; avril) en moyenne&nbsp;?";
// --------------------------------------------------
$texte['raisins_intitule'] = "Quelle quantit&eacute; de raisin	consommez-vous <strong class='facteur_temporel'>par an</strong> hors saison (de d&eacute;cembre &agrave; juillet) en moyenne&nbsp;?";
// --------------------------------------------------
$texte['exotiques_intitule'] = "Quelle quantit&eacute; de fruits et l&eacute;gumes tropicaux (banane, ananas, mangue, avocat, ...) consommez-vous <strong class='facteur_temporel'>par an</strong> en moyenne&nbsp;?";
// --------------------------------------------------
$texte['saisons_intitule'] = "Quelle quantit&eacute; de fruits et l&eacute;gumes de saison consommez-vous <strong class='facteur_temporel'>par semaine</strong> en moyenne&nbsp;?";
$texte['saisons_aide'] = "<p>Pour connaître les saisons des fruits et légumes en France, vous pouvez (par exemple) consulter le tableau proposé par <a href='http://www.defipourlaterre.org/fraise/pdf/affiche-saisons.pdf' onclick='window.open(this.href); return false;' >la fondation Nicolas Hulot</a> ou par <a href='http://www.consoglobe.com/pgz52-pgz_manger-mieux.html'>le site Consoglobe</a> sur le sujet.</p>" ; 
// --------------------------------------------------
$texte['bio_intitule'] = "En moyenne, quel pourcentage de vos consommations de fruits et l&eacute;gumes est constitu&eacute; de produits d'origine biologique (bio)&nbsp;?";
$texte['bio_aide'] = "<p>Entrez un nombre entre 0 et 100.</p>";
// --------------------------------------------------
$texte['autre_bio_intitule'] = "En moyenne, quel pourcentage de vos consommations alimentaires hors viande, poisson, produits laitiers, fruits et l&eacute;gumes (c'est-&agrave;-dire vos consommations de c&eacute;r&eacute;ales, l&eacute;gumineuses, pain, oeufs, huile, sucre, &eacute;picerie, ...) est constitu&eacute; de produits d'origine biologique (bio)&nbsp;?";
$texte['autre_bio_aide'] = "<p>NB : aucune question ne porte sur les quantités consommées de ces produits. Les émissions correspondantes seront calculées à partir de quantités moyennes (en tenant simplement compte de votre réponse ci-dessous sur le pourcentage de produits issus de l'agriculture biologique).</p><p>Entrez un nombre entre 0 et 100.</p>";

$TEXT[FRUIT_LEGUME]= $texte ;

// ---------------------------------------------------------------------
// Page Alimentation -> Boissons
// ---------------------------------------------------------------------
$texte = array() ;

$texte['eau_intitule'] = "Buvez-vous en majorit&eacute; de l'eau en bouteille ou de l'eau du robinet&nbsp;?";
// --------------------------------------------------
$texte['alcool_intitule'] = "Quelle quantit&eacute; de boissons alcoolis&eacute;es consommez-vous <strong class='facteur_temporel'>par mois</strong> en moyenne&nbsp;?";
$texte['alcool_aide'] = "<p>Cette quantit&eacute; ne concerne que votre consommation individuelle (et non celle de l'ensemble	des personnes partageant votre foyer). Elle doit tenir compte des repas que vous prenez en collectivit&eacute; ou au restaurant, même dans un cadre professionnel.</p><p>NB : c'est bien de la quantité de boissons alcoolisées qu'il s'agit, et non de la seule quantité d'alcool contenue dans ces boissons alcoolisées.</p>";
// --------------------------------------------------

$TEXT[BOISSON] = $texte ;

// ---------------------------------------------------------------------
// CONSOMMATION
// ---------------------------------------------------------------------

// ---------------------------------------------------------------------
// Page Consommation -> vie quotidienne
// ---------------------------------------------------------------------
$texte = array() ;

$texte['aide_aide'] = "<p>Les informations demand&eacute;es sur cette page ne concernent que votre consommation individuelle (et non celle de l'ensemble	des personnes partageant votre foyer). Vous ne devez donc saisir que le prorata des d&eacute;penses qu'il est l&eacute;gitime de vous affecter. Autrement dit, les &eacute;missions associ&eacute;es aux consommations que vous saisirez sur cette page vous seront enti&egrave;rement imput&eacute;es (elles ne seront pas divis&eacute;es par le nombre de personnes partageant votre foyer).</p>";
// --------------------------------------------------
$texte['budget_ordis_teles_intitule'] = "Quel est en moyenne votre budget <strong class='facteur_temporel'>annuel</strong> en achats d'ordinateurs (unit&eacute;s centrales, &eacute;crans, ordinateurs portables) et de t&eacute;l&eacute;visions&nbsp;?";
$texte['budget_ordis_teles_aide'] = "<p>Vous pouvez par exemple prendre la moyenne sur les trois dernières années. 
</p><p>Ne saisissez que le prorata des d&eacute;penses qu&acute;il est l&eacute;gitime de vous affecter. Par exemple, pour un ordinateur portable dont vous êtes le seul utilisateur, vous devez inclure son prix en totalit&eacute; dans le nombre que vous allez saisir. En revanche, pour un ordinateur fixe partag&eacute; par vous et d'autres personnes de votre foyer, vous devez diviser son prix par le nombre d'utilisateurs, ou plus exactement le prorata de son prix correspondant &agrave; votre part dans son utilisation.</p>" ; 
// --------------------------------------------------
$texte['budget_petit_informatique_intitule'] = "Quel est en moyenne votre budget <strong class='facteur_temporel'>annuel</strong> en petit mat&eacute;riel technologique (informatique et &eacute;lectronique hors ordinateurs et t&eacute;l&eacute;visions)&nbsp;?";
$texte['budget_petit_informatique_aide'] = "<p>Imprimantes, scanners, t&eacute;l&eacute;phones, fax, r&eacute;pondeurs, chaînes hi-fi, appareils photo, cam&eacute;ras, supports (cds, dvds, cassettes), ...</p>";
// --------------------------------------------------
$texte['budget_petits_achats_intitule'] = "Quel est en moyenne votre budget <strong class='facteur_temporel'>mensuel</strong> en petits consommables tels que&nbsp;: papeterie, livres, petits produits manufactur&eacute;s, ustensiles de cuisine, produits de soin ...&nbsp;?";
$texte['budget_petits_achats_aide'] = "<p>Astuce&nbsp;: ce montant correspond &agrave; ce qui reste de vos d&eacute;penses lorsque vous otez les frais de d&eacute;placements, du logement, de l'alimentation ainsi que des sorties.</p>";

$texte['assurance_intitule'] = "Quel est le montant <strong class='facteur_temporel'>annuel</strong> de vos d&eacute;penses d'assurance et de mutuelle&nbsp;?";
// --------------------------------------------------
$texte['facture_telecom_intitule'] = "Quel est le montant <strong class='facteur_temporel'>mensuel</strong> de vos d&eacute;penses de t&eacute;l&eacute;phonie (mobile et fixe)&nbsp;?";
// --------------------------------------------------
$texte['km_personnels_intitule'] = "Si des employ&eacute;s de maison (femme de m&eacute;nage, jardinier, garde d'enfant, aide &agrave; la personne, ...) viennent travailler &agrave; votre domicile, &agrave; combien de kilom&egrave;tres <strong class='facteur_temporel'>par semaine</strong> estimez-vous leurs d&eacute;placements pour s'y rendre&nbsp;?";
$texte['km_personnels_aide'] = "<p>La distance que vous devez saisir ci-dessous est le cumul de toutes les distances aller-retour effectu&eacute;es <strong class='facteur_temporel'>par semaine</strong>, divis&eacute; par le nombre de personnes qui partagent votre foyer.</p>" ;
// Indiquez ensuite le mode de transport principal. (&agrave; rajouter !!!!!!!!!!)

// --------------------------------------------------
$texte['nourriture_chien1_intitule'] = "Si vous avez des animaux domestiques (chien, chat, ...), &agrave; combien estimez-vous la quantit&eacute; de nourriture qu'ils consomment <strong class='facteur_temporel'>par mois</strong>&nbsp;?";
// --------------------------------------------------
$texte['poubelle_intitule'] = "Savez-vous comment sont trait&eacute;s principalement vos d&eacute;chets sur votre commune&nbsp;?";
$texte['poubelle_aide'] =  "Cette information peut s'obtenir aupr&egrave;s de votre mairie ou en demandant au SIOM (Syndicat Intercommunal d'Ordures M&eacute;nag&egrave;res) de votre commune.";

$TEXT[VIE_QUOTIDIENNE] = $texte ;

// ---------------------------------------------------------------------
// Page Consommation -> habillement
// ---------------------------------------------------------------------
$texte = array() ;

$texte['aide_aide'] = "<p>Les informations demand&eacute;es sur cette page ne concernent que votre consommation individuelle	(et non celle de l'ensemble des personnes partageant votre foyer).</p>" ;
// --------------------------------------------------
$texte['achat_chaussure_intitule'] = "Quel est votre budget <strong class='facteur_temporel'>annuel</strong> moyen pour vos achats de chaussures&nbsp;?";
// --------------------------------------------------
$texte['budget_intitule'] = "Quel est votre budget <strong class='facteur_temporel'>annuel</strong> vestimentaire moyen, hors achats de chaussures&nbsp;?";
$texte['budget_aide'] = "<p>Si vous connaissez ce budget, indiquez-le ci-dessous, ne cochez pas le bouton 'Je ne sais pas estimer ce budget', et <span class='warning'><strong>ne r&eacute;pondez pas</strong></span> aux questions qui suivent et qui concernent le nombre d'articles achet&eacute;s.</p><p>Si vous ne connaissez pas ce budget, cochez le bouton 'Je ne sais pas estimer ce budget' et r&eacute;pondez aux questions qui suivent et qui concernent le nombre d'articles achet&eacute;s.</p>";
$texte['nb_pantalons_intitule'] = "Combien de pantalons achetez-vous <strong class='facteur_temporel'>par an</strong> en moyenne&nbsp;?";
$texte['nb_tshirts_intitule'] = "Combien de tee-shirts, chemises, hauts, achetez-vous <strong class='facteur_temporel'>par an</strong> en moyenne&nbsp;?";
$texte['nb_pulls_intitule'] = "Combien de pulls achetez-vous <strong class='facteur_temporel'>par an</strong> en moyenne&nbsp;?";
$texte['nb_manteaux_intitule'] = "Combien de manteaux ou vestes achetez-vous <strong class='facteur_temporel'>par an</strong> en moyenne&nbsp;?";

$TEXT[HABILLEMENT] = $texte ;

// ---------------------------------------------------------------------
// Page Consommation -> loisirs
// ---------------------------------------------------------------------
$texte = array() ;

// --------------------------------------------------
$texte['ski_intitule'] = "Combien de semaines passez-vous en moyenne <strong class='facteur_temporel'>par an</strong> aux sports d'hiver&nbsp;?";
$texte['ski_aide'] = "<p>Attention, si ces s&eacute;jours aux sports d'hiver ont lieu dans une une r&eacute;sidence secondaire dont vous êtes propri&eacute;taire, ne rentrez aucune valeur ici, cela ferait double compte avec les logements d&eacute;clar&eacute;s dans la rubrique logement.</p>";
// --------------------------------------------------
$texte['location_intitule'] = "Combien de semaines, en dehors des sports d'hiver, passez-vous en moyenne <strong class='facteur_temporel'>par an</strong> en appartement ou en maison de location&nbsp;?";
// --------------------------------------------------
$texte['voilier_intitule'] = "Si vous avez un ou plusieurs bateau(x), mobil-home(s) ou caravane(s), qui ont &eacute;t&eacute; produits il y a moins de dix ans, quel est le poids approximatif (en tonnes) de tous ces &eacute;l&eacute;ments r&eacute;unis&nbsp;?";
$texte['voilier_aide'] = "<p>Vous devez diviser ce poids par le nombre de personnes (vous compris) qui profitent de ces biens (ce peut être par exemple le nombre de personnes de votre foyer), afin que seule la part des &eacute;missions associ&eacute;es &agrave; votre consommation individuelle soit comptabilis&eacute;e par le calculateur.</p><p>Poids moyen d'un voilier&nbsp;: environ 1,5 à 3,5 tonnes</p><p>Poids moyen d'une caravanne&nbsp;: environ 0,5 à 2 tonne(s)</p><p>Si vous ne disposez d'aucun de ces biens, saisissez '0'.</p>"; 

$TEXT[LOISIR] = $texte ;

?>
