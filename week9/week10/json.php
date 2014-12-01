<?php
  header('Content-type: application/json');
  if (file_exists('./dat/phone.dat')) {
    echo json_decode(file_get_contents('dat/phone.dat', true));
  } else {
    $fh = fopen('dat/phone.dat', 'w');
    fwrite($fh, '{}');
    fclose($fh);
    echo "{}";
  }
?>
