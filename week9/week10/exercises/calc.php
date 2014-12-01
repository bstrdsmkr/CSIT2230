<html>
<head><title>PHP - Number Range</title></head>
<body>
<?php
if (!isset($_POST['submit'])){
  print '<form method="post" action="calc.php">';
  print '<table>';
  print '<tr align="center"><td>Number 1: <input type="text" name="num1"></td></tr>';
  print '<tr align="center"><td>Number 2: <input type="text" name="num2"></td></tr>';
  print '<tr align="center"><td><input type="submit" name="submit" value="Add"></td></tr>';
  print '<tr align="center"><td><input type="submit" name="submit" value="Subtract"></td></tr>';
  print '</table>';
  print '</form>';
} else {
    if ($_POST['submit'] == "Add"){
      print $_POST['num1'] + $_POST['num2'];
    } else {
      print $_POST['num1'] - $_POST['num2'];
    }
}
?>

</body>
</html>
