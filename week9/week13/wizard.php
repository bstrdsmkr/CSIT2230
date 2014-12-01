<!-- http://thecodeplayer.com/walkthrough/jquery-multi-step-form-with-progress-bar -->
<!-- http://fezvrasta.github.io/bootstrap-material-design/ -->
<!-- http://vitalets.github.io/x-editable/docs.html -->

<?php
require 'login_protection.php';
require 'includes/db_connection.php';
?>

<html>
  <head>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/material-wfont.min.css">
    <link rel="stylesheet" type="text/css" href="css/ripples.min.css">
    <link rel="stylesheet" type="text/css" href="css/custom.css">
  </head>
  <body>
    <!-- multistep form -->
    <form id="msform">
      <!-- progressbar -->
      <ul id="progressbar">
        <li class="active">Choose Distro</li>
        <li>Select Packages</li>
        <li>Enable Customizations</li>
      </ul>

      <!-- fieldsets -->
      <fieldset id="distro_fieldset">
        <h2 class="fs-title">Select a Distribution</h2>
        <?php
$sql = "select * from distros";
$results = $db->query($sql);
foreach ($results as $row): ?>
        <div class="list-group">
          <div class="list-group-item col-sm-3">
            <div class="list-group-separator"></div>
            <div class="radio row-action-primary">
              <label>
                <input type="radio" name="distro_radio" id="<?php echo $row['id']; ?>">
              </label>
            </div>
            <div class="row-content">
              <h4 class="list-group-item-heading"><?php echo $row['name']; ?></h4>
              <p class="list-group-item-text"><?php echo $row['description']; ?></p>
            </div>
            <div class="list-group-separator"></div>
          </div>
        </div>
      <?php endforeach; ?>
        <div class="col-sm-12">
        <?php if ($_SESSION['role'] == 1): ?>
          <a href="distro_admin.php" class="btn btn-warning">Edit</a>
        <?php endif; ?>
          <a class="btn btn-success next">Next</a>
        </div>
      </fieldset>

      <fieldset id="packages_fieldset">
        <div id="pkg-tabs-container">
          <!-- This gets filled in via ajax -->
        </div>
        <div class="col-sm-12" id="pkg-btns">
          <a class="btn btn-success previous">Previous</a>
          <?php if ($_SESSION['role'] == 1): ?>
            <a href="pkg_admin.php" class="btn btn-warning">Edit</a>
          <?php endif; ?>
          <a class="btn btn-success next">Next</a>
        </div>
      </fieldset>

      <fieldset id="config_fieldset">
        <h2 class="fs-title">Personal Details</h2>
        <h3 class="fs-subtitle">We will never sell it</h3>
        <input type="text" name="fname" placeholder="First Name" />
        <input type="text" name="lname" placeholder="Last Name" />
        <input type="text" name="phone" placeholder="Phone" />
        <textarea name="address" placeholder="Address"></textarea>

        <div class="col-sm-12">
          <a class="btn btn-success previous">Previous</a>
          <a class="btn btn-success submit">Submit</a>
        </div>

      </fieldset>
    </form>

    <script src="js/jquery-2.1.1.min.js"  type="text/javascript"></script>
    <script src="js/bootstrap.min.js"     type="text/javascript"></script>
    <script src="js/ripples.min.js"       type="text/javascript"></script>
    <script src="js/material.min.js"      type="text/javascript"></script>
    <script src="js/jquery.easing.min.js" type="text/javascript"></script>
    <script src="js/arrive-2.0.0.min.js"  type="text/javascript"></script>

    <script src="js/custom.js" type="text/javascript"></script>
    <script>
      $(document).ready(function(){
        $.material.ripples();
        $.material.radio();
        $.material.input();
        $.material.checkbox();
        $('input[type=radio][name=distro_radio]').on('change', function(){
          $(document).unbindArrive('#package-groups *');
          $(document).arrive('#package-groups input[type=checkbox]', function() {
            console.log(this);
            $.material.checkbox("#package-groups input[type=checkbox]");
            // $.material.ripples(this);
          });
          $.ajax({
            url:    "build_package_groups.php",
            data:   {"distro": this.id},
            type:   "GET",
            success: function(html){
              $('#pkg-tabs-container').html(html);
            }
          })
          console.log(this.id);
        });
      });
    </script>
  </body>
</html>
