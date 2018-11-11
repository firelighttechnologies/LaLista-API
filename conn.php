<?php
$myfile = fopen("/run/secrets/api.lalista.conn.user", "r") or die("Unable to open file!");
$username = trim(fgets($myfile));
fclose($myfile);

$myfile = fopen("/run/secrets/api.lalista.conn.pass", "r") or die("Unable to open file!");
$password = trim(fgets($myfile));
fclose($myfile);

$myfile = fopen("/run/secrets/api.lalista.auth.key", "r") or die("Unable to open file!");
$testsecretkey = trim(fgets($myfile));
fclose($myfile);

  $host = "api.lalista.firelighttechnologies.com";
  $dbname = "lalista";

//Create connection
$conn = mysqli_connect($host, $username, $password, $dbname);
//Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
//mysqli_close($conn);

?>
