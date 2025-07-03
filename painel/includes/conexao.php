
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>


<?php
$host = "localhost";
$user = "u981260588_boleiros";
$pass = "3830Boleiros";
$dbname = "u981260588_boleiros";

// Conexão
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}
?>

