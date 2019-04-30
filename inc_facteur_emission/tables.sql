CREATE TABLE t_nature_unite (
	nature_unite_id INTEGER AUTO_INCREMENT NOT NULL PRIMARY KEY, 
	nature_unite_nom VARCHAR (100)
);

CREATE TABLE t_unite_fondamentale (
	unite_fond_id INTEGER AUTO_INCREMENT NOT NULL PRIMARY KEY, 
	unite_fond_nom VARCHAR (100),
	unite_fond_symbole VARCHAR (100),
	unite_fond_nature_unite_id INTEGER,
	FOREIGN KEY (unite_fond_nature_unite_id) REFERENCES t_nature_unite	
);

CREATE TABLE t_unite (
	unite_id  INTEGER AUTO_INCREMENT NOT NULL PRIMARY KEY
);

CREATE TABLE t_element_unite (
	element_unite_id  INTEGER AUTO_INCREMENT NOT NULL PRIMARY KEY, 
	element_unite_unite_id INTEGER,
	element_unite_unite_fond_id INTEGER,
	element_unite_position ENUM ('numerateur','denominateur'),
	FOREIGN KEY (element_unite_unite_id) REFERENCES t_unite,
	FOREIGN KEY (element_unite_unite_fond_id) REFERENCES t_unite_fondamentale	
);

CREATE TABLE t_mot_cle (
	mot_cle_id INTEGER AUTO_INCREMENT NOT NULL PRIMARY KEY, 
	mot_cle_nom  VARCHAR (100)
);

CREATE TABLE t_fe (
	fe_id INTEGER AUTO_INCREMENT NOT NULL PRIMARY KEY, 
	fe_unite_id INTEGER,
	fe_valeur FLOAT,
	fe_incertitude FLOAT,
	FOREIGN KEY (fe_unite_id) REFERENCES t_unite	
);

CREATE TABLE t_nomenclature (
	nomenc_id INTEGER AUTO_INCREMENT NOT NULL PRIMARY KEY, 
	nomenc_mot_cle_id INTEGER,
	nomenc_fe_id INTEGER,
	nomenc_ordre INTEGER,
	FOREIGN KEY (nomenc_fe_id) REFERENCES t_fe,
	FOREIGN KEY (nomenc_mot_cle_id) REFERENCES t_mot_cle		
);
