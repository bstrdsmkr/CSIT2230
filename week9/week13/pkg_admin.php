<?php
  require 'login_protection.php';
  require 'includes/db_connection.php';
?>
<html>

  <head>
    <title>PHPhonebook</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap-table.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap-editable.css">
    <link rel="stylesheet" type="text/css" href="css/material.min.css">
    <link rel="stylesheet" type="text/css" href="css/material-wfont.min.css">
    <link rel="stylesheet" type="text/css" href="css/ripples.min.css">
    <link rel="stylesheet" type="text/css" href="css/custom-admin.css">
  </head>

  <body>
    <button class="btn btn-primary pull-right" data-toggle="modal" data-target="#nc-modal">
      New Package
    </button>
    <a id="edit-btn" class="btn btn-success">Edit</a>
    <div class="modal fade" id="nc-modal" tabindex="-1" role="dialog" aria-labelledby="nc-label" aria-hidden="true">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
            </button>
            <h4 class="modal-title" id="nc-label">New Package</h4>
          </div>
          <div class="modal-body">
            <div id="msg" class="alert hide"></div>
            <form id="new-form">
              <fieldset>
                  <h2 class="fs-title form-group">Details</h2>

                  <div class="form-group">
                    <input class="form-control floating-label" id="focusedInput" type="select"   required placeholder="Distro"        name="distro_id"   data-hint="Please choose an icon">
                  </div>
                  <div class="form-group">
                    <input class="form-control floating-label" id="focusedInput" type="select"   required placeholder="Package group" name="group_id"    data-hint="Please enter a distribution name">
                  </div>
                  <div class="form-group">
                    <input class="form-control floating-label" id="focusedInput" type="text"     required placeholder="Package name"  name="sys_name"    data-hint="Please enter a description for this distribution">
                  </div>
                  <div class="form-group">
                    <input class="form-control floating-label" id="focusedInput" type="text"     required placeholder="Friendly name" name="human_name"  data-hint="Please enter a description for this distribution">
                  </div>
                  <div class="form-group">
                    <input class="form-control floating-label" id="focusedInput" type="textarea" required placeholder="Description"   name="description" data-hint="Please enter a description for this distribution">
                  </div>

              </fieldset>
            </form>
            <div class="modal-footer">
              <button type="reset"  id="close-btn" class="btn btn-default" data-dismiss="modal" form="new-form">Close</button>
              <button type="submit" id="save-btn"  class="btn btn-primary" form="new-form">Save changes</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <table class="table table-striped table-bordered table-hover dataTable" id="datatable">
      <thead>
        <tr>
          <th colspan="1" rowspan="1" tabindex="0">Distro</th>
          <th colspan="1" rowspan="1" tabindex="0">Group</th>
          <th colspan="1" rowspan="1" tabindex="0">Package</th>
          <th colspan="1" rowspan="1" tabindex="0">Friendly Name</th>
          <th colspan="1" rowspan="1" tabindex="0">Description</th>
        </tr>
      </thead>
      <tbody id="contact-table">
        <?php
          $sql = "select packages.id, distro_id, name, label, group_id, sys_name, human_name, packages.description from packages join distros on packages.distro_id = distros.id join groups on packages.group_id = groups.id";
          $results = $db->query($sql);
          $i=0;
          foreach($results as $row){
            $class = ( $i%2==0 ? 'even' : 'odd');
            echo sprintf('<tr rowid="%s" class="%s">', $row["id"], $class);
            echo sprintf('<td><span data-name="distro_id"   data-pk="%s" data-type="select"   class="xedit" data-value="%s" data-source="distros.php">%s</span></td>', $row["id"], $row["distro_id"], $row["name"]);
            echo sprintf('<td><span data-name="group_id"    data-pk="%s" data-type="select"   class="xedit" data-value="%s" data-source="groups.php">%s</span></td>', $row["id"], $row["group_id"],  $row["label"]);
            echo sprintf('<td><span data-name="sys_name"    data-pk="%s" data-type="text"     class="xedit">%s</span></td>', $row["id"], $row["sys_name"]);
            echo sprintf('<td><span data-name="human_name"  data-pk="%s" data-type="text"     class="xedit">%s</span></td>', $row["id"], $row["human_name"]);
            echo sprintf('<td><span data-name="description" data-pk="%s" data-type="textarea" class="xedit">%s</span></td>', $row["id"], $row["description"]);
            echo sprintf('<td class="delete-field"><button class="btn btn-danger delete-btn">Delete</button></td>');
            echo sprintf('</tr>');
            $i++;
          }
        ?>
      </tbody>
    </table>

    <script src="js/jquery-2.1.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-table.js"></script>
    <script src="js/bootstrap-editable.min.js"></script>
    <script src="js/ripples.min.js"></script>
    <script src="js/material.min.js"></script>

    <script>
      $(document).ready(function(){
        $.fn.editable.defaults.mode = 'inline';
        $.fn.editable.defaults.disabled = true;
        setEvents();
        $('.xedit, .new-field').editable({
          url: './distro_backend.php',
          validate: function(val){
            console.log(val);
            if (!val) return 'Required field';
          }
        });
        $('.new-field').editable('enable');
        $('#edit-btn').click(function(){
          $('.xedit').not('.new-field').editable('toggleDisabled');
          $('.delete-field').toggle();
          // $(this).toggleClass('btn-primary').toggleClass('btn-success');
          $(this).text(function(i, text){
            return text === 'Edit' ? 'Done' : 'Edit';
          });
        });
        $('#close-btn').click(function() {
          $('.new-field').editable('setValue', null)
                         .removeClass('editable-unsaved');
        });
        $('#new-form').submit(function(e) {
          $('#nc-modal').modal('toggle');
          e.preventDefault();
          $.ajax({
            type: "POST",
            url: "./distro_backend.php",
            data: $(this).serialize(),
            dataType: "json",
            success: createRow
          });
        });
      });
      function createRow(data){
        if (data && data.id) { //record created, response like {"id": 2}
          // var new_row = $('<tr rowid="'+data.id+'">' +
          //   '<td><span data-name="icon" data-pk="'+data.id+'" class="xedit icon">'+data.icon+'</span></td>' +
          //   '<td><span data-name="name" data-pk="'+data.id+'" class="xedit name">'+data.name+'</span></td>' +
          //   '<td><span data-name="description" data-pk="'+data.id+'" class="xedit description">'+data.description+'</span></td>' +
          //   '<td class="delete-field"><button class="btn btn-danger delete-btn">Delete</button></td>' +
          // '</tr>');
          var new_row = $('<tr rowid="'+data.id+'">' +
            '<td><span data-name="distro"      data-pk="'+data.id+'" data-type="select"   class="xedit" data-value="'+data.distro_id+'" data-source="distros.php">'+data.name+'</span></td>' +
            '<td><span data-name="group"       data-pk="'+data.id+'" data-type="select"   class="xedit" data-value="'+data.distro_id+'" data-source="groups.php">'+data.label+'</span></td>' +
            '<td><span data-name="sys_name"    data-pk="'+data.id+'" data-type="text"     class="xedit">'+data.sys_name   +'</span></td>' +
            '<td><span data-name="human_name"  data-pk="'+data.id+'" data-type="text"     class="xedit">'+data.human_name +'</span></td>' +
            '<td><span data-name="description" data-pk="'+data.id+'" data-type="textarea" class="xedit">'+data.description+'</span></td>' +
            '<td class="delete-field"><button class="btn btn-danger delete-btn">Delete</button></td>' +
            '</tr>');

          //add the new row to the table
          $('#contact-table').append(new_row);

          //activate events
          setEvents();

          //remove unsaved class
          $('.new-field').editable('setValue', null);
          $('.new-field').removeClass('editable-unsaved');

        } else {
          alert('Error adding row');
        }
      }
      function setEvents(){
        $('.delete-field').hide();
        $('.xedit').editable({
          url: './pkg_backend.php',
          validate: function(val){
            console.log(val);
            if (!val) return 'Required field';
          }
        });
        $('.delete-btn').click(function(){
          var row = $(this).closest('tr');
          $.ajax({
            type: 'POST',
            url: './pkg_backend.php',
            dataType: 'JSON',
            data: {'delete_pk': row.attr('rowid')}
          });
          row.remove();
        });
        $.material.init();
      }
    </script>
  </body>

</html>
