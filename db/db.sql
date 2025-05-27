#  DROP DATABASE ENSAIEI;
CREATE DATABASE IF NOT EXISTS ENSAIEI;
USE ENSAIEI;

-- Tabela de tipos de usuário
CREATE TABLE `users_types` (
                               `id` int NOT NULL AUTO_INCREMENT,
                               `description` varchar(255) NOT NULL,
                               PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Tabela de usuários

CREATE TABLE `users` (
                         `id` int NOT NULL AUTO_INCREMENT,
                         `idType` int NOT NULL,
                         `name` varchar(255) NOT NULL,
                         `email` varchar(255) NOT NULL,
                         `password` varchar(255) NOT NULL,
                         `photo` varchar(255) DEFAULT 'https://upload.wikimedia.org/wikipedia/commons/0/03/Twitter_default_profile_400x400.png',
                         `username` varchar(60) NOT NULL,
                        `bio` varchar(300) NOT NULL,
                         `deleted` bool DEFAULT FALSE,
                         PRIMARY KEY (`id`),
                         KEY `fk_users_users_types1_idx` (`idType`),
                         CONSTRAINT `fk_users_users_types1` FOREIGN KEY (`idType`) REFERENCES `users_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Tabela de peças teatrais
CREATE TABLE `plays` (
                         `id` int NOT NULL AUTO_INCREMENT,
                         `name` varchar(255) NOT NULL,
                         `genre` varchar(100) NOT NULL,
                         `script` text NOT NULL,
                         `directorId` int NOT NULL,
                         `deleted` bool DEFAULT FALSE,
                         PRIMARY KEY (`id`),
                         KEY `fk_plays_users1_idx` (`directorId`),
                         CONSTRAINT `fk_plays_users1` FOREIGN KEY (`directorId`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Tabela de atores
CREATE TABLE `actors` (
                          `id` int NOT NULL AUTO_INCREMENT,
                          `name` varchar(255) NOT NULL,
                          `deleted` bool DEFAULT FALSE,
                          PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Tabela de relacionamento entre atores e peças
CREATE TABLE `actors_plays` (
                                `actorId` int NOT NULL,
                                `playId` int NOT NULL,
                                PRIMARY KEY (`actorId`,`playId`),
                                KEY `fk_actors_plays_plays1_idx` (`playId`),
                                CONSTRAINT `fk_actors_plays_actors1` FOREIGN KEY (`actorId`) REFERENCES `actors` (`id`),
                                CONSTRAINT `fk_actors_plays_plays1` FOREIGN KEY (`playId`) REFERENCES `plays` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Tabela de tipos de perguntas
CREATE TABLE `questions_types` (
                                   `id` int NOT NULL AUTO_INCREMENT,
                                   `description` varchar(255) NOT NULL,
                                   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Tabela de perguntas
CREATE TABLE `questions` (
                             `id` int NOT NULL AUTO_INCREMENT,
                             `idType` int NOT NULL,
                             `question` varchar(255) NOT NULL,
                             `answer` text NOT NULL,
                             `deleted` bool DEFAULT FALSE,
                             PRIMARY KEY (`id`),
                             KEY `fk_questions_types_idx` (`idType`),
                             CONSTRAINT `fk_questions_types` FOREIGN KEY (`idType`) REFERENCES `questions_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Tabela de figurinos
CREATE TABLE `costumes` (
                            `id` int NOT NULL AUTO_INCREMENT,
                            `playId` int NOT NULL,
                            `description` text NOT NULL,
                            `deleted` bool DEFAULT FALSE,
                            PRIMARY KEY (`id`),
                            KEY `fk_costumes_plays1_idx` (`playId`),
                            CONSTRAINT `fk_costumes_plays1` FOREIGN KEY (`playId`) REFERENCES `plays` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Tabela de relacionamento entre figurinos e peças
CREATE TABLE `costumes_plays` (
                                  `costumeId` int NOT NULL,
                                  `playId` int NOT NULL,
                                  PRIMARY KEY (`costumeId`, `playId`),
                                  KEY `fk_costumes_plays_costumes1_idx` (`costumeId`),
                                  KEY `fk_costumes_plays_plays1_idx` (`playId`),
                                  CONSTRAINT `fk_costumes_plays_costumes1` FOREIGN KEY (`costumeId`) REFERENCES `costumes` (`id`),
                                  CONSTRAINT `fk_costumes_plays_plays1` FOREIGN KEY (`playId`) REFERENCES `plays` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


-- Inserir tipos de usuário básicos
INSERT INTO `users_types` (`description`) VALUES
                                              ('Administrador'),
                                              ('Diretor'),
                                              ('Ator'),
                                              ('Usuário');

-- Inserir tipos de perguntas básicos
INSERT INTO `questions_types` (`description`) VALUES
                                                  ('Geral'),
                                                  ('Técnico'),
                                                  ('Financeiro'),
                                                  ('Suporte');