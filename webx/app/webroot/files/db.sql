CREATE DATABASE `webx`;
USE `webx`;

CREATE TABLE `webx`.`urls`(
`id` INT NOT NULL AUTO_INCREMENT ,
`url` VARCHAR(255) ,
`visited` ENUM('yes','no') DEFAULT 'no' ,
PRIMARY KEY (`id`) );

CREATE TABLE `webx`.`emails`(
`id` INT NOT NULL AUTO_INCREMENT ,
`email` VARCHAR(255) ,
PRIMARY KEY (`id`)  );

-- exemplo de URL inicial
INSERT INTO `webx`.`urls`(url) VALUES('https://www.google.com.br/search?q=webx&safe=off');