CREATE DATABASE podatoci_vozduh CHARACTER SET utf8 COLLATE utf8_general_ci;;
USE podatoci_vozduh;

CREATE TABLE parametar (
	id int(11) AUTO_INCREMENT PRIMARY KEY,
	ime VARCHAR(10) NOT NULL,
	opis VARCHAR(30),
	edinica_merka VARCHAR(10) DEFAULT 'mcg/m3'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_general_ci;

CREATE TABLE podatoci_uhmr (
	id int(11) AUTO_INCREMENT PRIMARY KEY,
	datum datetime NOT NULL,
	pritisok float,
	temp float,
	vlaznost float,
	brzina float,
	pravec int(11) DEFAULT '-1',
	dozd float,
	stanica_id int(11) NOT NULL,
	FOREIGN KEY (stanica_id) REFERENCES stanici(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_general_ci;

CREATE TABLE stanici(
	id int(11) AUTO_INCREMENT PRIMARY KEY,
	ime_stanica VARCHAR(60) NOT NULL,
	longituda float,
	latituda float
) ENGINE=InnoDB DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_general_ci;

CREATE TABLE vrednosti_skopje(
	id int(11) AUTO_INCREMENT PRIMARY KEY,
	datum datetime NOT NULL,
	vrednost float,
	sid int(11) NOT NULL,
	pid int(11) NOT NULL,
	FOREIGN KEY sid REFERENCES stanici(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY pid REFERENCES parametar(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_general_ci;

CREATE TABLE vrednosti_eastern(
	id int(11) AUTO_INCREMENT PRIMARY KEY,
	datum datetime NOT NULL,
	vrednost float,
	sid int(11) NOT NULL,
	pid int(11) NOT NULL,
	FOREIGN KEY sid REFERENCES stanici(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY pid REFERENCES parametar(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_general_ci;

CREATE TABLE vrednosti_western(
	id int(11) AUTO_INCREMENT PRIMARY KEY,
	datum datetime NOT NULL,
	vrednost float,
	sid int(11) NOT NULL,
	pid int(11) NOT NULL,
	FOREIGN KEY sid REFERENCES stanici(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY pid REFERENCES parametar(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_general_ci;