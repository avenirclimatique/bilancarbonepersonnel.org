<?php 

// Inclusions.
include_once('../inc/constantes_config.php');
include_once('../inc/fonctions_generales.php');


class exportBCP extends DOMDocument {
	protected $elements = array();	//tableau des differents elements.
	protected $NoElement = 0; 	//numero du dernier element créé.
	protected $root;		//élément racine.

	public function exportBCP ($mail) {	//Constructeur de la classe, définit la structure de base du DOM utilisé
		$this->__construct('1.0', 'iso-8859-1');

		//Création de la racine <element>
		$this->root = $this->createElement('element');
		$this->root = $this->appendChild($this->root);
		//De son attribut name
		$attr = $this->createAttribute ('name');
		$attr = $this->root->appendChild($attr);
		$attr->value = "bcp";
		//Puis de son attribut xmlns
		$attr2 = $this->createAttribute ('xmlns');
		$attr2 = $this->root->appendChild($attr2);
		$attr2->value = 'http://relaxng.org/ns/structure/1.0';

		//Création de l'élément qui va contenir le Mail (identifiant) de l'utilisateur.
		//Création de l'element
		$identifiant = $this->createElement('element');
		$identifiant = $this->root->appendChild($identifiant);
		//De son attribut name (name="identity")
		$attr3 = $this->createAttribute('name');
		$attr3 = $identifiant->appendChild($attr3);
		$attr3->value = 'identity';
		//de son attribut
		$attr4 = $this->CreateTextNode($mail);
		$attr4 = $identifiant->appendChild($attr4);
	}
	// METHODES
	/*
		initType() : définit un élément dédié à un type de source d'émission.
		initElement() : ???
		addSource() : ajoute un élément fils à un élément type.
		addType() : ajoute l'élément type à l'élément racine.
		addDigest() : ajoute un élément total d'émission à l'élément racine.
	*/
	public function initType($name) {	//création d'un nouvel élement a partir de son nom.
		$this->NoElement++;	//On incrémente le numero du dernier element.
		$this->elements[$this->NoElement] = $this->createElement('element'); //création du nouvel élement

		$attr = $this->createAttribute('name');
		$attr = $this->elements[$this->NoElement]->appendChild($attr);
		$attr->value = $name;
		return $this->elements[$this->NoElement];
	}
	public function addType($element) {
		$element = $this->root->appendChild($element);
		return $element;
	}
	
}
////////////////////////////////////////////////////////////////////////////////
//////////////////////////// ICI TESTS DE LA CLASSE ////////////////////////////
////////////////////////////////////////////////////////////////////////////////

$connexion = connexion (NOM, PASSE, BASE, SERVEUR);

$xml = new exportBCP('Maxou@Liberty-Tree.net');	//Création du DOM

$requete = 'SELECT * FROM `t_rubrique` ORDER BY `t_rubrique`.`rub_ordre` ASC';
$resultat = exec_requete ($requete, $connexion);


//génération du fichier source par source pour chaque type
while ( $ligne = ligne_suivante ($resultat) ) {
	$type = $xml->initType($ligne['rub_nom']);			//Création d'un Type
	$type = $xml->addType($type);			//Ajout du Type a la racine du DOM
	$requeteSource = 'SELECT * FROM `t_rubrique` ORDER BY `t_rubrique`.`rub_ordre` ASC';
	$resultatSource = exec_requete ($requeteSource, $connexion);
	while ( $ligneSource = ligne_suivante ($resultatSource) ) {
		$source = $xml->initType($ligneSource['page_nom']);
	}
}




echo $xml->saveXML();				//Export xml

?>
















