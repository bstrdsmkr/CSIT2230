<html>
<head><title>PHP - Number Range</title></head>
<body>
<?php
if (!array_key_exists("miles", $_POST)){
  print '<form method="post" action="mpg_out.php">';
  print '<table>';
  print '<tr align="center"><td>Miles Traveled <input type="text" name="miles"></td></tr>';
  print '<tr align="center"><td>Gallons Used <input type="text" name="gallons"></td></tr>';
  print '<tr align="center"><td><input type="submit" value="Run"></td></tr>';
  print '</table>';
  print '</form>';
} else {
  $mpg = $_POST["miles"]/$_POST["gallons"];
  $template = "You traveled %s miles on %s gallons which is %s mpg";
  echo sprintf($template, $_POST["miles"], $_POST["gallons"], $mpg);
}
?>

</body>
</html>
