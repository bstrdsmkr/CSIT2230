<?php
require 'includes/db_connection.php';

$sql = "select id, name from distros";
$result = $db->query($sql);
$options = array();
foreach($result as $row){
  $options[] = array('value' => $row['id'],
                     'text'  => $row['name']);
}

echo json_encode($options); 
