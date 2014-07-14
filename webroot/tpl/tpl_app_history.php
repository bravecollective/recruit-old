<?php if (!defined('RECRUIT')) die('go away'); ?>
<?php
if (empty($charId)) {
    $stm = $dbr->prepare('SELECT * FROM application_history INNER JOIN character_lookup ON application_history.charId = character_lookup.charId ORDER BY issuedAt DESC, notificationId DESC LIMIT 1000');
} else {
    $stm = $dbr->prepare('SELECT * FROM application_history INNER JOIN character_lookup ON application_history.charId = character_lookup.charId WHERE application_history.charId = :charId ORDER BY issuedAt DESC, notificationId DESC LIMIT 1000');
    $stm->bindValue(':charId', $charId);
}
if (!$stm->execute()) die('sql error');
?>

<!-- History -->
    <div class="container">
	<h3>History <span class="text-muted"><small>30 minute update interval</small></span></h3>
    </div>

      <div class="container">
        <table class="table table-striped table-condensed">

          <thead>
            <tr>
              <th class="col-sm-2">Date</th>
              <th class="col-sm-1">State</th>
	    <?php if (empty($charId)) { echo "<th class='col-sm-2'>Character</th>"; } ?>
              <th class="">Text</th>
            </tr>
          </thead>

          <tbody>
<?php 
while ($row = $stm->fetch()) {
	echo "<tr>";
	    echo "<td>" . date("Y-m-d H:i", $row['issuedAt']) . "</td>";
	    echo "<td><span style='color:#FFFFFF;padding:3px;font-weight:bold;background-color:" . reasonToColor($row['reason']) . ";'>" . reasonToString($row['reason']) . "</span></td>";
	    if (empty($charId)) { echo "<td><a href='?nav=char&charId=" . $row['charId'] . "'>" . charNameToDisplay($row['charName']) . "</a></td>";}
	    if (empty($charId)) { echo "<td>" . shortenText($row['text'], 80) . "</td>"; } else { echo "<td>" . $row['text'] . "</td>"; }
	echo "</tr>";
}
?>

          </tbody>
        </table>
      </div>
<!-- History -->
