<?php
//SECURITY PURPOSES WE WILL NOT ADD USERNAME AND PASSWORD OR DNS INFO
$dsn = ;
$user = ;
$password = ;
 
try {
    $dbh = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    die();
}
return $dbh;
?>
