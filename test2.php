<?php

	phpinfo();
	?>

	DROP TABLE vrednosti;

CREATE TABLE vrednosti(
    id int(11) PRIMARY KEY AUTO_INCREMENT,
    datum datetime NOT NULL,
    vrednost float,
    sid int(11) NOT NULL,
    pid int(11) NOT NULL,
    FOREIGN KEY (sid) REFERENCES stanici(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (pid) REFERENCES parametar(id) ON UPDATE CASCADE ON DELETE CASCADE);