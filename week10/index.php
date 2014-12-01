<html>

<head>
  <title>PHPhonebook</title>
  <link rel="stylesheet" href="bootstrap.min.css">
  <link rel="stylesheet" href="bootstrap-table.css">
  <link rel="stylesheet" href="darkstrap.css">
  <script src="jquery-1.11.1.min.js"></script>
  <script src="bootstrap.min.js"></script>
  <script src="bootstrap-table.js"></script>
  <link href="bootstrap-editable.css" rel="stylesheet">
  <script src="bootstrap-editable.min.js"></script>
  <script>
    $(document).ready(function(){
      $.fn.editable.defaults.mode = 'inline';
      $.fn.editable.defaults.disabled = true;
      $.fn.editable.defaults.showbuttons = false;
      setEvents();
      $('.xedit, .new-field').editable({
        url: './backend.php',
        validate: function(val){
          console.log(val);
          if (!val) return 'Required field';
        }
      });
      $('.new-field').editable('enable');
      $('#edit-btn').click(function(){
        $('.xedit').not('.new-field').editable('toggleDisabled');
        $('.delete-field').toggle();
        $(this).toggleClass('btn-primary').toggleClass('btn-succe');
        $(this).text(function(i, text){
          return text === 'Edit' ? 'Done' : 'Edit';
        });
      });
      $('#close-btn').click(function() {
        $('.new-field').editable('setValue', null)
                       .removeClass('editable-unsaved');
      });
      $('#save-btn').click(function() {
        $('.new-field').editable('submit', {
          url: './backend.php',
          ajaxOptions: {
              dataType: 'json' //assuming json response
          },
          success: function(data, config) {
            if (data && data.id) { //record created, response like {"id": 2}
              var new_row = $('<tr rowid="'+data.id+'">' +
                '<td><span data-name="fname" data-pk="'+data.id+'" class="xedit fname">'+$('#fname').text()+'</span></td>' +
                '<td><span data-name="lname" data-pk="'+data.id+'" class="xedit lname">'+$('#lname').text()+'</span></td>' +
                '<td><span data-name="phone" data-pk="'+data.id+'" data-type="tel" class="xedit phone">'+$('#phone').text()+'</span></td>' +
                '<td class="delete-field"><button class="btn btn-danger delete-btn">Delete</button></td>' +
              '</tr>');

              //add the new row to the table
              $('#contact-table').append(new_row);

              //activate events
              setEvents();

              //remove unsaved class
              $('.new-field').editable('setValue', null);
              $('.new-field').removeClass('editable-unsaved');

            } else if (data && data.errors) {
              //server-side validation error, response like {"errors": {"username": "username already exist"} }
              config.error.call(this, data.errors);
            }
          },
          error: function(errors) {
            var msg = '';
            console.log(errors);
            if (errors && errors.responseText) { //ajax error, errors = xhr object
              msg = errors.responseText;
            } else { //validation error (client-side or server-side)
              console.log('validation error');
              $.each(errors, function(k, v) {
                msg += k + ": " + v + "<br>";
              });
            }
            $('#msg').removeClass('alert-success').addClass('alert-error').removeClass('hide').html(msg).show();
          }
        });
      });
    });

    function setEvents(){
      $('.delete-field').hide();
      $('.xedit').editable({
        url: './backend.php',
        validate: function(val){
          console.log(val);
          if (!val) return 'Required field';
        }
      });
      $('.delete-btn').click(function(){
        var row = $(this).closest('tr');
        $.ajax({
          type: 'POST',
          url: './backend.php',
          dataType: 'JSON',
          data: {'delete_pk': row.attr('rowid')}
        });
        row.remove();
      });
      $('#phone, .phone').editable('option', 'validate', function(val){
        regex = /^\(*?\d{3}\)*?[-\. ]*?\d{3}[-\. ]*?\d{4}$/;
        if (!regex.test(val)) return 'Invalid Phone Number';
      });
    }
  </script>
</head>

<body>
  <button class="btn btn-primary pull-right" data-toggle="modal" data-target="#nc-modal">
    New Contact
  </button>
  <button id="edit-btn" class="btn btn-primary pull-left">Edit</button>
  <div class="modal fade" id="nc-modal" tabindex="-1" role="dialog" aria-labelledby="nc-label" aria-hidden="true">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
          </button>
          <h4 class="modal-title" id="nc-label">New Contact</h4>
        </div>
        <div class="modal-body">
          <div id="msg" class="alert hide"></div>
          <table class="table table-bordered table-striped">
            <tbody>
              <tr class="even">
                <td>First Name</td>
                <td><a href="#" id="fname" class="new-field editable editable-click editable-empty" data-type="text" data-original-title="Enter First Name"></a>
                </td>
              </tr>
              <tr class="odd">
                <td>Last name</td>
                <td><a href="#" id="lname" class="new-field editable editable-click editable-empty" data-type="text" data-original-title="Enter Last Name"></a>
                </td>
              </tr>
              <tr class="even">
                <td>Phone Number</td>
                <td><a href="#" id="phone" class="new-field editable editable-click editable-empty" data-type="text" data-original-title="Enter Phone Number"></a>
                </td>
              </tr>
            </tbody>
          </table>
          <div class="modal-footer">
            <button type="button" id="close-btn" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" id="save-btn"  class="btn btn-primary" data-dismiss="modal">Save changes</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <table class="table table-striped table-bordered table-hover dataTable" id="datatable">
    <thead>
      <tr>
        <th colspan="1" rowspan="1" style="width: 180px;" tabindex="0">First Name</th>
        <th colspan="1" rowspan="1" style="width: 220px;" tabindex="0">Last Name</th>
        <th colspan="1" rowspan="1" style="width: 288px;" tabindex="0">Phone Number</th>
      </tr>
    </thead>
    <tbody id="contact-table">
      <?php
        $servername = "localhost";
        $username = "c2230a15";
        $password = "c2230a15";
        $db = "c2230a15test";
        $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
        $sql = "select * from contacts";
        $results = $conn->query($sql);
        $i=0;
        foreach($results as $row){
          $class = ( $i%2==0 ? 'even' : 'odd');
          echo sprintf('<tr rowid="%s" class="%s">', $row["id"], $class);
          echo sprintf('<td><span data-name="fname" data-pk="%s" class="xedit fname">%s</span></td>', $row["id"], $row["fname"]);
          echo sprintf('<td><span data-name="lname" data-pk="%s" class="xedit lname">%s</span></td>', $row["id"], $row["lname"]);
          echo sprintf('<td><span data-name="phone" data-pk="%s" class="xedit phone">%s</span></td>', $row["id"], $row["phone"]);
          echo sprintf('<td class="delete-field"><button class="btn btn-danger delete-btn">Delete</button></td>');
          echo sprintf('</tr>');
          $i++;
        }
      ?>
    </tbody>
  </table>
</body>

</html>
