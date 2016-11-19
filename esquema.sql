DROP TABLE IF EXISTS personas;
CREATE TABLE personas
(
codigo int NOT NULL AUTO_INCREMENT PRIMARY KEY,
nombre varchar(100),
apellidos varchar(100)
);

insert into personas ( nombre, apellidos ) values ( "javier", "lopez martinez");


insert into personas ( nombre, apellidos ) values ( "german", "alba martinez");
