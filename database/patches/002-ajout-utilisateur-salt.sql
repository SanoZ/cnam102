insert into _patches(name) Values("002-ajout-utilisateur-salt.sql");

alter table utilisateurs
add salt varchar(50);