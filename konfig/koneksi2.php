<?php
$host='localhost';
$db = 'sjtransindo_crm';
$username = 'lunata';
$password = 'r4h4si4Kit4Bers4m4';
$dsn = "pgsql:host=$host;port=5432;dbname=$db;user=$username;password=$password";
 
try{
 // create a PostgreSQL database connection
 $pdo = new PDO($dsn);
 
 $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
}catch (PDOException $e){
 // report error message
 echo $e->getMessage();
}