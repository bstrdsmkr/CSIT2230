<?php
  $servername = "localhost";
  $username = "c2230a15";
  $password = "c2230a15";
  $db = "c2230a15test";

  try {
      $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
      if (isset($_POST['delete_pk'])){
        $sql = sprintf("delete from contacts where id = '%s'", $_POST['delete_pk']);
      } else if (!isset($_POST['pk'])){
        $id = uniqid();
        $sql = sprintf("insert into contacts (id, fname, lname, phone) values (\"%s\", \"%s\", \"%s\", \"%s\")",
                       $id, $_POST['fname'], $_POST['lname'], $_POST['phone']);

        echo '{"id":"'.$id.'", "fname": "'.$_POST['fname'].'", "lname": "'.$_POST['lname'].'", "phone": "'.$_POST['phone'].'"}';
      } else {
        $sql = sprintf("update contacts set %s=\"%s\" where id=\"%s\"",
                       $_POST['name'], $_POST['value'], $_POST['pk']);
      }

      // echo $sql;
      $stmt = $conn->prepare($sql);
      $stmt->execute();
      // $conn->commit();

  } catch(PDOException $e){
      echo $e->getMessage();
  }
?>
