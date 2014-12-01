<?php
  require 'includes/db_connection.php';

  try {
      if (isset($_POST['delete_pk'])){
        $sql = sprintf("delete from distros where id = '%s'", $_POST['delete_pk']);
      } else if (!isset($_POST['pk'])){
        $id = uniqid();
        $sql = sprintf("insert into distros (id, icon, name, description) values (\"%s\", \"%s\", \"%s\", \"%s\")",
                       $id, $_POST['icon'], $_POST['name'], $_POST['description']);

        $response = array(
                      "id"=>          $id,
                      "icon"=>        $_POST['icon'],
                      "name"=>        $_POST['name'],
                      "description"=> $_POST['description']
        );
        echo json_encode($response);
        // echo '{"id":"'.$id.'", "icon": "'.$_POST['icon'].'", "name": "'.$_POST['name'].'", "description": "'.$_POST['description'].'"}';
      } else {
        $sql = sprintf("update distros set %s=\"%s\" where id=\"%s\"",
                       $_POST['name'], $_POST['value'], $_POST['pk']);
      }

      // echo $sql;
      $stmt = $db->prepare($sql);
      $stmt->execute();
      // $db->commit();

  } catch(PDOException $e){
      echo $e->getMessage();
  }
?>
