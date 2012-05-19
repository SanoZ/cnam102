insert into _patches(name)Values("004-ajout-utilisateur-admin.sql");

INSERT INTO utilisateurs (nom, password, salt, role_id, date_creation)
VALUES ('admin', SHA1('passwordce8d96d579d389e783f95b3772785783ea1a9854'),
	'ce8d96d579d389e783f95b3772785783ea1a9854', 1, NOW());