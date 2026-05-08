<?php 
// Connection template for MySQL database using PDO 
// to return the variable $connection to index.php. 

$textSite = "https://localhost:8080/";

// ON FIRST USE:
// Fill in the database connection details below.

 // Save this file as 'connector.php' and include it in your project.

 // To keep your credentials secure, add the file to your .gitignore file.
 // before the next COMMIT and PUSH to github.
 // e.g., command line:  $ echo 'function/connector.php' >> .gitignore
 // or manually add 'function/connector.php' 
 // to your project's .gitignore.

$host = "sasrdsmp01.uits.iu.edu";
$db = "whooper_lsa_11";
$port = 3306;
$user = "whooper_lsa_11";
//$pass = "Bad5%Crab&7";
$pass = "QXKtHgl-&1Cz^3k0";

$connection = mysqli_connect($host, $user, $pass, $db, $port);

/* $dsn ="mysql:dbname=$db;host=$host;port=$port";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_NAMED,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::ATTR_PERSISTENT => true
];
try {
    $connection = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
    
    $connection_error = $e->getMessage();
}
 */
?>
