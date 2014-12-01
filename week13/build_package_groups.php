<?php
  require 'includes/db_connection.php';
  session_start();
  echo '<h2 class="fs-title">Select Packages</h2>';
  echo '<h3 class="fs-subtitle">These packages will be installed on the target machine</h3>';
  echo '<ul class="nav nav-tabs">';
  $header_qry = $db->prepare('select distinct groups.id, label from packages join groups on packages.group_id = groups.id where distro_id = ?');
  $header_qry->execute(array($_GET['distro']));
  $i = 0;
  while($row = $header_qry->fetch()){
    $template = '<li><a href="#%s" data-toggle="tab">%s</a></li>';
    echo sprintf($template, $row['id'], $row['label']);
    $i++;
  }
  echo '</ul>';
  echo '<div id="package-groups" class="tab-content">';

  $i = 0;
  $group_qry = $db->prepare("select * from groups");
  $group_qry->execute();
  while ($row = $group_qry->fetch()){
    $class = ($i == 0 ? " active in" : "");
    $template = '<div class="tab-pane fade%s" id="%s">';
    echo sprintf($template, $class, $row['id']);
    $pkg_qry = $db->prepare("select * from packages where distro_id = ? and group_id = ?");
    $pkg_qry->execute(array($_GET['distro'], $row['id']));
    while ($pkg = $pkg_qry->fetch()){
      $pkg_block = <<<EOF
      <div class="list-group-separator"></div>
      <div class="list-group">
        <div class="list-group-item col-sm-4">
          <div class="row-action-primary checkbox">
            <label><input type="checkbox" id="$pkg[id]" ></label>
          </div>
          <div class="row-content">
            <h4 class="list-group-item-heading">$pkg[human_name]</h4>
            <p class="list-group-item-text">$pkg[description]</p>
          </div>
        </div>
      </div>
      <div class="list-group-separator"></div>
EOF;
      echo $pkg_block;
    }
    echo '</div>';
    $i++;
  }

  echo '</div>';


  ///////////////////////////////////////////////////////////////////////
//   $i = 0;
//   $group_qry = $db->prepare("select * from groups");
//   $group_qry->execute();
//   while ($row = $group_qry->fetch()){
//     $class = ($i == 0 ? "active in" : "");
//     $template = '<div class="tab-pane fade %s" id="%s">';
//     echo sprintf($template, $class, $row['id']);
//     $pkg_qry = $db->prepare("select * from packages where distro_id = ? and group_id = ?");
//     $pkg_qry->execute(array($_GET['distro'], $row['id']));
//     while ($pkg = $pkg_qry->fetch()){
//       $pkg_block = <<<EOF
//         <div class="list-group-separator"></div>
//         <div class="list-group">
//           <div class="list-group-item col-sm-4">
//             <div class="row-action-primary checkbox">
//               <label><input type="checkbox" id="$pkg[id]" ></label>
//             </div>
//             <div class="row-content">
//               <h4 class="list-group-item-heading">$pkg[human_name]</h4>
//               <p class="list-group-item-text">$pkg[description]</p>
//             </div>
//           </div>
//         </div>
//         <div class="list-group-separator"></div>
// EOF;
//       echo $pkg_block;
//     }
//     echo '</div>';
//     $i++;
//   }
?>
