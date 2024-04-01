<?php
require 'DBConnect.php';
$conn = DBConnect::getInstance()->getConnection();
$sql = "SELECT Product.name, Product.price FROM Product WHERE requiresDeposit
= :deposit";
$sth = $conn->prepare($sql);
$sth->bindValue('deposit', true, PDO::PARAM_BOOL);
$sth->execute();
$result = $sth->fetchAll(PDO::FETCH_OBJ);
echo '<h1>Our Long Courses</h1>';
foreach ($result as $row) {
 printf('<p>Course: %s, Price: €%.2f</p>', $row->name, $row->price);
}