<?php
require 'password.php';

$db_host = "localhost";
$db_user = "c2230a15";
$db_pass = "c2230a15";
$db_name = "c2230a15test";
$user = $_POST['email'];
$pass = $_POST['pass'];
try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $sql = 'select password from users where username = :user limit 1';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user', $user);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_OBJ);
    // var_dump($stmt);
    // var_dump($row);

    if (password_verify($pass, $row->password)){
    // if (crypt($pass, $row->hash) === $user->hash){
      // header('Location: secure_page.html');
      session_start();
      $_SESSION['login'] = true;
      echo "secure_page.php";
    } else {
      header("HTTP/1.1 401 Unauthorized");
      echo "Invalid Login";
    }

}
catch(PDOException $e) {
    echo $e->getMessage();
}
?>
