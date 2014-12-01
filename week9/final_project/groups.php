<?php
require 'includes/db_connection.php';

$sql = "select id, label from groups";
$result = $db->query($sql);
$options = array();
foreach($result as $row){
  $options[] = array('value' => $row['id'],
                     'text'  => $row['label']);
}

echo json_encode($options);
