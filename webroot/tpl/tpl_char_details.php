<?php if (!defined('RECRUIT')) die('go away'); ?>
<?php
$stm = $dbr->prepare('SELECT * FROM character_lookup WHERE charId = :charId LIMIT 1');
$stm->bindValue(':charId', $charId);
if (!$stm->execute()) die('sql error');
$row = $stm->fetch();

$charName = $row['charName'];
$charDisplay = charnameToDisplay($charName);
$charNameUrl = urlencode($charName);
?>

<!-- Character -->
    <div class="container">
	<h3>Character</h3>
    </div>

    <div class="container">
	<table border="0">
	<tr>
	    <?php echo "<td><img src='http://image.eveonline.com/Character/" . $charId . "_128.jpg'></td>" ?>
	    <td style="vertical-align:text-top;padding-left:10px;padding-top:0px;">
	    <?php echo "<span style='font-size:250%;font-weight:bold;'>" . $charDisplay . "</span><br>"; ?>
	    <?php echo "<strong>Profile:</strong> " . linkOrText($charName, "https://gate.eveonline.com/Profile/" . $charName, "EVE Gate") . ", " . linkOrText($charName, "http://evewho.com/pilot/" . $charNameUrl, "Eve Who") . ", " . linkOrText($charName, "http://eveboard.com/pilot/" . preg_replace("/[\ ]/", '_', $charName), "eveboard") . "<br>"; ?>
	    <?php echo "<strong>Bazaar:</strong> " . linkOrText($charName, "https://forums.eveonline.com/default.aspx?g=search&postedby=" . $charNameUrl . "&forumID=277", "Posted By") . ", " . linkOrText($charName, "https://forums.eveonline.com/default.aspx?g=search&search=%22" . $charNameUrl . "%22&forumID=277", "Contains") . "<br>"; ?>
	    <?php echo "<strong>Killboard:</strong> <a href='https://zkillboard.com/character/" . $charId . "/'>zKillboard</a>, <a href='http://eve-kill.net/?a=pilot_detail&plt_external_id=" . $charId . "'>EVE-Kill</a>" ?>
	</td>
	</tr>
	</table>
    </div>
<!-- Character -->

