DROP DATABASE IF EXISTS ENSAIEI;
CREATE DATABASE ENSAIEI;
USE ENSAIEI;

CREATE TABLE `users` (
                         `id` int NOT NULL AUTO_INCREMENT,
                         `name` varchar(255) NOT NULL,
                         `email` varchar(255) NOT NULL,
                         `password` varchar(255) NOT NULL,
                         `photo` varchar(255) DEFAULT 'https://upload.wikimedia.org/wikipedia/commons/0/03/Twitter_default_profile_400x400.png',
                         `username` varchar(60) NOT NULL,
                         `bio` varchar(300) NOT NULL,
                         `role` ENUM('ADMIN', 'STANDARD') NOT NULL,
                         `deleted` bool DEFAULT FALSE,
                         `emailVerified` bool DEFAULT FALSE,
                         `verificationCode` varchar(255) DEFAULT NULL,
                         `verificationCodeExpires` datetime DEFAULT NULL,
                         PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE TABLE `events` (
                          `id` int NOT NULL AUTO_INCREMENT,
                          `title` varchar(200) NOT NULL,
                          `description` text,
                          `location` varchar(255),
                          `latitude` decimal(10, 8),
                          `longitude` decimal(11, 8),
                          `startDatetime` datetime NOT NULL,
                          `endDatetime` datetime NOT NULL,
                          `deleted` bool DEFAULT FALSE,
                          `organizerId` int not null,
                          PRIMARY KEY (`id`),
                          FOREIGN KEY (`organizerId`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE TABLE `attractions` (
                         `id` int NOT NULL AUTO_INCREMENT,
                         `name` varchar(255) NOT NULL,
                         `type` ENUM('MUSIC', 'VISUAL', 'THEATER', 'DANCE', 'CINEMA', 'OTHER') NOT NULL,
                         `eventId` int NOT NULL,
                         `startDatetime` datetime NOT NULL,
                         `endDatetime` datetime NOT NULL,
                         `specificLocation` varchar(255),
                         `deleted` bool DEFAULT FALSE,
                         PRIMARY KEY (`id`),
                         FOREIGN KEY (`eventId`) REFERENCES `events` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE TABLE `attractions_performers` (
                          `id` INT NOT NULL AUTO_INCREMENT,
                          `attractionId` INT NOT NULL,
                          `userId` INT NOT NULL,
                          PRIMARY KEY (`id`),
                          FOREIGN KEY (`attractionId`) REFERENCES `attractions` (`id`),
                          FOREIGN KEY (`userId`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE TABLE `questions_types` (
                                   `id` int NOT NULL AUTO_INCREMENT,
                                   `description` varchar(255) NOT NULL,
                                   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

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
