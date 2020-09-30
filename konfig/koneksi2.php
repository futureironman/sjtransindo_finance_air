<?php
$host='localhost';
$db = 'sjt_crm';
$username = 'sjt_root';
$password = "XNzZ7882sSSRJp5y";
$dsn = "pgsql:host=$host;port=5432;dbname=$db;user=$username;password=$password";
 
try{
 // create a PostgreSQL database connection
 $pdo = new PDO($dsn);
 
 $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
}catch (PDOException $e){
 // report error message
 echo $e->getMessage();
}