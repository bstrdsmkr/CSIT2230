<?php

  require 'password.php';
  $db_host = "localhost";
  $db_user = "c2230a15";
  $db_pass = "c2230a15";
  $db_name = "c2230a15test";

  $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $sql  = "insert into users (username, password, role) values (:user, :pass, 1)";

  try {
      $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(':user', $_POST['email']);
      $stmt->bindParam(':pass', $hash);
      if ($stmt->execute()) {
        header('Location: index.html');
      } else {
        echo $stmt->errorCode();
      }
  }
  catch(PDOException $e) {
      echo $e->getMessage();
  }
?>
