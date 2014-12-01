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
                <input type="radio" name="distro_radio">
              </label>
            </div>
            <div class="row-content">
              <h4 class="list-group-item-heading"><?php echo $row['name']; ?></h4>
              <p class="list-group-item-text"><?php echo $row['desc']; ?></p>
            </div>
            <div class="list-group-separator"></div>
          </div>
        </div>
      <?php endforeach; ?>
        <?php if ($_SESSION['role'] == 1): ?>
          <a class="btn btn-warning">Edit</a>
        <?php endif; ?>
        <a class="btn btn-success next">Next</a>
      </fieldset>

      <fieldset id="packages_fieldset">
        <h2 class="fs-title">Select Packages</h2>
        <h3 class="fs-subtitle">These packages will be installed on the target machine</h3>
        <ul class="nav nav-tabs" style="margin-bottom: 15px;">
          <?php
            $sql = "select * from groups";
            $i = 0;
            $results = $db->query($sql);
            foreach ($results as $row){
              $template = '<li class="%s"><a href="#%s" data-toggle="tab">%s</a></li>';
              $class = ($i == 1 ? "active" : "");
              echo sprintf($template, $class, $row['id'], $row['label']);
              $i++;
            } ?>
        </ul>
        <div id="package-groups" class="tab-content">
          <?php
            $sql = "select * from groups";
            $i = 0;
            $results = $db->query($sql);
            foreach ($results as $row){
              $class = ($i == 0 ? "active in" : "");
              $template = '<div class="tab-pane fade %s" id="%s"><p>%s</p></div>';
              echo sprintf($template, $class, $row['id'], $row['label']);
              $i++;
            } ?>
        </div>

        <a class="btn btn-success previous">Previous</a>
        <?php if ($_SESSION['role'] == 1): ?>
          <a class="btn btn-warning">Edit</a>
        <?php endif; ?>
        <a class="btn btn-success next">Next</a>
      </fieldset>

      <fieldset id="config_fieldset">
        <h2 class="fs-title">Personal Details</h2>
        <h3 class="fs-subtitle">We will never sell it</h3>
        <input type="text" name="fname" placeholder="First Name" />
        <input type="text" name="lname" placeholder="Last Name" />
        <input type="text" name="phone" placeholder="Phone" />
        <textarea name="address" placeholder="Address"></textarea>

        <a class="btn btn-success previous">Previous</a>
        <a class="btn btn-success submit">Submit</a>

      </fieldset>
    </form>

    <script src="js/jquery-2.1.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/ripples.min.js"></script>
    <script src="js/material.min.js"></script>
    <script src="js/jquery.easing.min.js" type="text/javascript"></script>

    <script src="js/custom.js" type="text/javascript"></script>
    <script>
      $(document).ready(function(){
        $.material.init();
      });
    </script>
  </body>
</html>
