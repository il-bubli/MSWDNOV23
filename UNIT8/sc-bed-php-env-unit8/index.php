<?php

require 'DBConnect.php';
$conn = DBConnect::getInstance()->getConnection();

$sql = 'SELECT COUNT(id) AS prodCount FROM Product';
$sth = $conn->prepare($sql);
$sth->execute();
$result = $sth->fetch(PDO::FETCH_OBJ);

printf('We currently have %d courses available.</p>', $result->prodCount);
echo "<p>View our <a href='short.php'>short</a> or <a href='long.php'>long</a> courses</p>.";

// class Product
// {
//     public $id;
//     public $name;
//     public $price;
//     public $description;
//     public $featuredImage;
//     public $requiresDeposit;

// }
// try{
//     $dbh = new PDO("mysql:host=mariadb;dbname=ShoppingCart", "root", "root");
//     // $sth = $dbh->query("SELECT * FROM Product");
//     // var_dump($sth->fetchAll(PDO::FETCH_OBJ));

//     // Prepared Statement
//     $sql = 'SELECT * FROM Product WHERE price < :price AND requiresDeposit = :deposit';
//     $sth = $dbh->prepare($sql);
//     $sth->execute(['price'=>110, 'deposit'=>false]);
//     $result = $sth->fetchAll(PDO::FETCH_OBJ);
//     foreach($result as $row) {
//         printf('<p>%s (€%.2f)</p>', $row->name, $row->price);
//     }
//     // Inserting a record in a table
//     // $sql = 'INSERT INTO Product(name, price, description, featuredImage, requiresDeposit) VALUES
//     // (:name, :price, :description, :featuredImage, :requiresDeposit)';
//     // $sth = $dbh->prepare($sql);
//     // $sth->execute([
//     //     'name'=>'My New Product',
//     //     'price'=> 100,
//     //     'description'=> 'This is some new product',
//     //     'featuredImage'=>'some_image.png',
//     //     'requiresDeposit' =>0
//     // ]);
//     // printf('<p>%d rows inserted into database</p>', $sth->rowCount());

//     // Updating a record
//     // $sql = 'UPDATE Product SET price = :price WHERE id = :id';
//     // $sth = $dbh->prepare($sql);
//     // $sth->execute(['price'=>90, 'id'=>7]);
//     // printf('<p>%d rows updated in the database.</p>', $sth->rowCount());

//     // Deleting a record
//     // $sql = 'DELETE FROM Product WHERE id >= :id';
//     // $sth = $dbh->prepare($sql);
//     // $sth->execute(['id' => 5]);
//     // printf('<p>%d rows deleted from database.</p>', $sth->rowCount());
// }
// catch(PDOException $e){
//     echo $e->getMessage();
// }