CREATE DATABASE IF NOT EXISTS ShoppingCart;

USE ShoppingCart;

CREATE TABLE `Product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `price` float NOT NULL,
  `description` varchar(255) NOT NULL,
  `featuredImage` varchar(100) DEFAULT NULL,
  `requiresDeposit` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `User` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstName` varchar(100) NOT NULL,
  `lastName` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `accessLevel` char(8) NOT NULL DEFAULT 'standard',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `Cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'PENDING',
  PRIMARY KEY (`id`),
  KEY `c_cart_user` (`userId`),
  CONSTRAINT `c_cart_user` FOREIGN KEY (`userId`) REFERENCES `User` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `CartItem` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cartId` int(11) NOT NULL,
  `productId` int(11) NOT NULL,
  `price` float NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `c_cartitem_cart` (`cartId`),
  KEY `c_cartitem_product` (`productId`),
  CONSTRAINT `c_cartitem_cart` FOREIGN KEY (`cartId`) REFERENCES `Cart` (`id`),
  CONSTRAINT `c_cartitem_product` FOREIGN KEY (`productId`) REFERENCES `Product` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO Product(id, name, price, description, featuredImage, requiresDeposit) VALUES 
    (1, "Getting Started with React", 90, "Get quickly up and running with React and create a profile website in just 3 hours!", "masterclass_react.png", 0),
    (2, "AI with TensorFlow & Python", 90, "Create a machine learning model using Python and TensorFlow, focusing on image recognition and classification.", "masterclass_ai.png", 0),
    (3, "Game Development with Unity", 360, "Create a clone of the popular 2D platformer featuring an Italian plumber, starting from scratch.", "masterclass_game.png", 1),
    (4, "Introduction to CSS FlexBox", 90, "FlexBox can revolutionise how you create responsive websites. Learn how in just 3 hours!", "masterclass_css.png", 0);
