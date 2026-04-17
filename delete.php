<?php
require "includes/auth.php";

//connect to database
require 'includes/connect.php';

//get ID from url
$userID = $_GET['id'];

// create the query 
$sql = "DELETE from photos WHERE id = :id";

//prepare 
$stmt = $pdo->prepare($sql);

//bind 
$stmt->bindParam(':id', $playerId);

//execute
$stmt->execute();
 
header("Location: photos.php"); 
exit;
