<?php
  $all_contacts = json_decode(file_get_contents('dat/phone.dat'), true);
  if (isset($_POST['delete_pk'])){
    unset($all_contacts[$_POST['delete_pk']]);
  } else if (!isset($_POST['pk'])){
    $id = uniqid();
    $new_contact = array(
      'fname' => $_POST['fname'],
      'lname' => $_POST['lname'],
      'phone' => $_POST['phone']
    );
    $all_contacts[$id] = $new_contact;

    echo '{"id":"'.$id.'"}';
  } else {
    $all_contacts[$_POST['pk']][$_POST['name']] = $_POST['value'];
  }

  $out_file = fopen('dat/phone.dat', 'w');
  $write_str = json_encode($all_contacts);
  fwrite($out_file, $write_str);
  fclose($out_file);
?>
