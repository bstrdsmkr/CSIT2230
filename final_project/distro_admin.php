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
    <link rel="stylesheet" type="text/css" href="css/material-wfont.min.css">
    <link rel="stylesheet" type="text/css" href="css/ripples.min.css">
    <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Montserrat">
    <link rel="stylesheet" type="text/css" href="css/custom-admin.css">
  </head>

  <body>
    <div class="navbar navbar-success">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="wizard.php">Linux Loader</a>
      </div>
      <div class="navbar-collapse collapse navbar-responsive-collapse">
        <ul class="nav navbar-nav navbar-right">
          <li><a class="mdi-action-exit-to-app" href="logout.php">Logout</a></li>
        </ul>
      </div>
    </div>
    <div id="btn-drawer" class="pull-right">
      <a id="new-btn"  class="btn btn-primary btn-fab btn-fab-float mdi-content-add"    tooltip-title="New Distro" data-toggle="modal" data-target="#nc-modal"></a>
      <a id="edit-btn" class="btn btn-success btn-fab btn-fab-float mdi-content-create" tooltip-title="Edit"></a>
    </div>
    <div class="modal fade" id="nc-modal" tabindex="-1" role="dialog" aria-labelledby="nc-label" aria-hidden="true">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
            </button>
            <h4 class="modal-title" id="nc-label">New Distro</h4>
          </div>
          <div class="modal-body">
            <div id="msg" class="alert hide"></div>
            <form id="new-form">
              <fieldset>
                <!-- <div class="form-group"> -->
                  <h2 class="fs-title form-group">Details</h2>

                  <div class="form-group">
                    <input class="form-control floating-label" id="focusedInput" type="text" required placeholder="Icon Path"   name="icon"     data-hint="Please choose an icon">
                  </div>
                  <div class="form-group">
                    <input class="form-control floating-label" id="focusedInput" type="text" required placeholder="Distro"      name="name"     data-hint="Please enter a distribution name">
                  </div>
                  <div class="form-group">
                    <input class="form-control floating-label" id="focusedInput" type="text" required placeholder="Description" name="description" data-hint="Please enter a description for this distribution">
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
    <div class="col-xs-10 col-xs-offset-1 well">
      <table class="table table-striped table-hover dataTable" id="datatable">
        <thead>
          <tr>
            <th colspan="1" rowspan="1" tabindex="0">Icon</th>
            <th colspan="1" rowspan="1" tabindex="0">Distro</th>
            <th colspan="1" rowspan="1" tabindex="0">Description</th>
          </tr>
        </thead>
          <tbody id="contact-table">
            <?php
              $sql = "select * from distros";
              $results = $db->query($sql);
              $i=0;
              foreach($results as $row){
                $class = ( $i%2==0 ? 'even' : 'odd');
                // echo sprintf('<tr rowid="%s" class="%s">', $row["id"], $class);
                echo sprintf('<tr rowid="%s">', $row["id"]);
                echo sprintf('<td><span data-name="icon" data-pk="%s" class="xedit icon">%s</span></td>', $row["id"], $row["icon"]);
                echo sprintf('<td><span data-name="name" data-pk="%s" class="xedit name ">%s</span></td>', $row["id"], $row["name"]);
                echo sprintf('<td><span data-name="description" data-pk="%s" class="xedit description ">%s</span></td>', $row["id"], $row["description"]);
                echo sprintf('<td class="delete-field"><a href="#" class="btn btn-fab btn-danger mdi-content-clear delete-btn"></a></td>');
                echo sprintf('</tr>');
                $i++;
              }
            ?>
          </tbody>
        </table>
    </div>

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
        // $.fn.editable.defaults.showbuttons = false;
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
          $(this).toggleClass('mdi-content-create').toggleClass('mdi-action-done-all');
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
          var new_row = $('<tr rowid="'+data.id+'">' +
            '<td><span data-name="icon" data-pk="'+data.id+'" class="xedit icon">'+data.icon+'</span></td>' +
            '<td><span data-name="name" data-pk="'+data.id+'" class="xedit name">'+data.name+'</span></td>' +
            '<td><span data-name="description" data-pk="'+data.id+'" class="xedit description">'+data.description+'</span></td>' +
            '<td class="delete-field"><a href="javascript:void(0)" class="btn btn-danger btn-fab btn-raised mdi-content-clear"></a></td>' +
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
          url: './distro_backend.php',
          validate: function(val){
            console.log(val);
            if (!val) return 'Required field';
          }
        });
        $('.delete-btn').click(function(){
          var row = $(this).closest('tr');
          $.ajax({
            type: 'POST',
            url: './distro_backend.php',
            dataType: 'JSON',
            data: {'delete_pk': row.attr('rowid')}
          });
          row.remove();
        });
        $.material.init();
        $('#btn-drawer > .btn').each(function(){
          console.log(this);
          $(this).tooltip({
            'placement': 'left',
            'title'    : $(this).attr('tooltip-title')
          });
        });
      }
    </script>
  </body>

</html>
