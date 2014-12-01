<?php
  require 'includes/db_connection.php';

  try {
      if (isset($_POST['delete_pk'])){
        $sql = $db->prepare("delete from packages where id = ?");
        $sql->execute(array($_POST['delete_pk']));
      } else if (!isset($_POST['pk'])){
        $id = uniqid();
        $sql = $db->prepare("insert into packages (id, distro_id, group_id, sys_name, human_name, description) values (?,?,?,?,?,?)");
        $sql->execute(array(
                        $id,
                        $_POST['distro_id'],
                        $_POST['group_id'],
                        $_POST['sys_name'],
                        $_POST['human_name'],
                        $_POST['description']
        ));
        $response = array(
                      "id"          => $id,
                      "distro_id"   => $_POST['distro_id'],
                      "group_id"    => $_POST['group_id'],
                      "sys_name"    => $_POST['sys_name'],
                      "human_name"  => $_POST['human_name'],
                      "description" => $_POST['description']
        );
        echo json_encode($response);
      } else {
        $sql = $db->prepare("update packages set $_POST[name] = ? where id = ?");
        $sql->execute(array(
                        $_POST['value'],
                        $_POST['pk']
        ));
      }

  } catch(PDOException $e){
      echo $e->getMessage();
  }
?>
