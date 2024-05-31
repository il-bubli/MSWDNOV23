CREATE DATABASE IF NOT EXISTS kahuna;

USE kahuna;

CREATE TABLE IF NOT EXISTS Product(
    id              INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    serial          VARCHAR(255) NOT NULL UNIQUE,
    name            VARCHAR(255) NOT NULL,
    warrantyLength  INT(11) NOT NULL
);



CREATE TABLE IF NOT EXISTS User(
    id              INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    email           VARCHAR(255) NOT NULL UNIQUE,
    password        VARCHAR(255) NOT NULL,
    accessLevel     CHAR(10) NOT NULL DEFAULT 'user'
);

CREATE TABLE IF NOT EXISTS AccessToken(
    id              INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    userId          INT NOT NULL,
    token           VARCHAR(255) NOT NULL,
    birth           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT c_accesstoken_user
        FOREIGN KEY(userId) REFERENCES User(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS ProductOwner (
    id              INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    productId       INT NOT NULL,
    userId          INT NOT NULL,
    purchaseDate    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    warrantyEndDate DATE NOT NULL,
    CONSTRAINT c_productowner_product
        FOREIGN KEY (productId) REFERENCES Product(id),
    CONSTRAINT c_productowner_user
        FOREIGN KEY (userId) REFERENCES User(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    UNIQUE (productId)
);

CREATE TABLE IF NOT EXISTS Ticket (
    id            INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    userId              INT NOT NULL,
    productId           INT NOT NULL,
    issueDescription    TEXT NOT NULL,
    status              VARCHAR(50)  NOT NULL DEFAULT 'open',
    submissionDate      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT c_ticket_user
        FOREIGN KEY (userId) REFERENCES User(id),
    CONSTRAINT c_ticket_product
        FOREIGN KEY (productId) REFERENCES Product(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Message (
    id       INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    ticketId        INT NOT NULL,
    userId          INT NOT NULL,
    message         TEXT NOT NULL,
    timestamp       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT c_ticketmessage_ticket
        FOREIGN KEY (ticketId) REFERENCES Ticket(id),
    CONSTRAINT c_ticketmessage_user
        FOREIGN KEY (userId) REFERENCES User(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
);

INSERT INTO Product (serial, name, warrantyLength) VALUES
('KHWM8199911', 'CombiSpin Washing Machine', 2),
('KHWM8199912', 'CombiSpin + Dry Washing Machine', 2),
('KHMW789991', 'CombiGrill Microwave', 1),
('KHWP890001', 'K5 Water Pump', 5),
('KHWP890002', 'K5 Heated Water Pump', 5),
('KHSS988881', 'Smart Switch Lite', 2),
('KHSS988882', 'Smart Switch Pro', 2),
('KHSS988883', 'Smart Switch Pro V2', 2),
('KHHM89762', 'Smart Heated Mug', 1),
('KHSB0001', 'Smart Bulb 001', 1);
