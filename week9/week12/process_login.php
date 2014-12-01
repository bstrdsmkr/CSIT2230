<?php
require 'password.php';
require 'includes/db_connection.php';

$user = $_POST['email'];
$pass = $_POST['pass'];
try {
    $sql = 'select password, role from users where username = :user limit 1';
    $stmt = $db->prepare($sql);
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
      $_SESSION['role'] = $row->role;
      echo "wizard.php";
    } else {
      header("HTTP/1.1 401 Unauthorized");
      echo "Invalid Login";
    }

}
catch(PDOException $e) {
    echo $e->getMessage();
}
?>
