<?php
  $all_contacts = json_decode(file_get_contents('dat/phone.dat'), true);
  $all_contacts[$_POST['pk']][$_POST['name']] = $_POST['value'];
  $out_file = fopen('dat/phone.dat', 'w');
  $write_str = json_encode($all_contacts);
  fwrite($out_file, $write_str);
  fclose($out_file);
?>
