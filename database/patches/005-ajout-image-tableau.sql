insert into _patches(name) Values("005-ajout-image-tableau.sql");

alter table articles add column image varchar(50);