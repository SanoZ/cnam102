insert into _patches(name) Values("003-ajout-de-role.sql");
	
insert into utilisateurs (nom, prenom, adresse1, adresse2, cp, ville,email, date_creation, role_id, password)
	values("Durand", "Paul", "15 rue de paris", "", "75000", "Paris", "paul_durand@gmail.com", now(), "2", sha1("voicimonmotdePasse"));