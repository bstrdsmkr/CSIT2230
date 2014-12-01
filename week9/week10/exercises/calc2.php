<html>
<head><title>PHP - Number Range</title></head>
<body>
<script>
function btn_handler(op){
  $("input[name='op_field']").text(op);
}
</script>
<?php
if (!isset($_POST['op_field'])){
  print '<form method="post" action="calc2.php">';
  print '<table>';
  print '<tr align="center"><td>Number 1: <input type="text" name="num1"></td></tr>';
  print '<tr align="center"><td>Number 2: <input type="text" name="num2"></td></tr>';
  print '<tr align="center"><td><button onClick="button_handler(0)" value="Add"></td></tr>';
  print '<tr align="center"><td><button onClick="button_handler(1)" value="Subtract"></td></tr>';
  print '</table>';
  print '<input type="hidden" name="op_field" />';
  print '</form>';
} else {
    if ($_POST['op_field'] == 0){
      print $_POST['num1'] + $_POST['num2'];
    } else if ($_POST['op_field'] == 1) {
      print $_POST['num1'] - $_POST['num2'];
    }
}
?>

</body>
</html>
