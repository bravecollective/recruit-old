<?php if (!defined('RECRUIT')) die('go away'); ?>
<!-- Employment -->
    <div class="container">
	<h3>Employment <span class="text-muted"><small>updated on new notification</small></span></h3>
    </div>

    <div class="container">
	<table class="table table-striped table-condensed">
	    <thead>
		<tr>
		    <th class="col-sm-1">Joined</th>
		    <th class="col-sm-1">Corporation</th>
		    <th class="col-sm-3">Alliance</th>
		</tr>
	    </thead>
	    <tbody>
<?php
$stmc = $dbr->prepare('SELECT * FROM employment_history WHERE charId = :charId ORDER BY since DESC, recordId DESC');
$stmc->bindValue(':charId', $charId);
if (!$stmc->execute()) die('sql error');
while($rowc = $stmc->fetch()) {
    $corpId = $rowc['corporationId'];
    $trow =  corporationLookup($corpId);
    $corpName = $trow['corporationName'];
    $corpJoined = date("Y-m-d H:i", $rowc['since']);

    echo "<tr>";
	echo "<td>" . $corpJoined . "</td>";
	echo "<td><img src='http://image.eveonline.com/Corporation/" . $corpId . "_32.png'> <a href='http://evewho.com/corp/" . urlencode($corpName) . "'>" . $corpName . "</a></td>";
	    $stma = $dbr->prepare('SELECT * FROM alliance_history WHERE corporationId = :corpId ORDER BY joinedAt DESC');
	    $stma->bindValue(':corpId', $corpId);
	    if (!$stma->execute()) die('sql error');
	echo "<td>";
	    while($rowa = $stma->fetch()) {
		$aId = $rowa['allianceId'];
		$trow = allianceLookup($aId);
		$aName = $trow['allianceName'];
		$aTicker = $trow['allianceTicker'];
		echo "<img src='http://image.eveonline.com/Alliance/" . $aId . "_32.png'> <a href='http://evewho.com/alli/" . urlencode($aName) . "'>" . $aName . " [" . $aTicker . "]</a> &nbsp;";
	    }
	echo "</td>";
    echo "</tr>";
}
?>
	    </tbody>
	</table>
    </div>
<!-- Employment -->
