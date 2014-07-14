<?php if (!defined('RECRUIT')) die('go away'); ?>
<?php

function charIdtoName() {
    $stm = $dbr->prepare('SELECT charName FROM char WHERE criteria = :criteria;');
    $stm->bindValue(':criteria', $criteria);
    if (!$stm->execute()) { raiseError('ban query failed'); };
    if ($stm->fetch()) {
	return true;
    }
    return $stm['charName'];
}

function reasonToColor($reason) {
    switch ($reason) {
	case 16:
	    return "#f3f600";
	case 17:
	    return "#dc0606";
	case 18:
	    return "#078e00";
	case 21:
	    return "#dc06bb";
	case 128:
	    return "#0ad400";
	case 129:
	    return "#006f6e";
	case 130:
	    return "#00b5b3";
    }

    return "#dc0606";
}

function reasonToString($reason) {
    switch ($reason) {
	case 16:
	    return "APPLIED";
	case 17:
	    return "REJECTED";
	case 18:
	    return "ACCEPTED";
	case 21:
	    return "LEFT";
	case 128:
	    return "JOINED";
	case 129:
	    return "DECLINED";
	case 130:
	    return "WITHDRAWN";
    }

    return "UNKNOWN";
}

function shortenText($text, $length) {
    if ($length <= 3) {
	return $text;
    }
    
    if (strlen($text) > $length - 3) {
	return substr($text, 0, $length - 3) . "...";
    }

    return $text;
}

function charNameToDisplay($charName) {
    if (empty($charName)) {
	return "&lt;Unknown&gt;";
    }
    return $charName;
}

function linkOrText($exist, $link, $text) {
    if (empty($exist)) {
	return $text;
    }
    return "<a href='" . $link . "'>" . $text . "</a>";
}

function corporationLookup($cid) {
    global $dbr;
    $stm = $dbr->prepare('SELECT * FROM corporation_lookup WHERE corporationId = :corpId');
    $stm->bindValue(':corpId', $cid);
    if (!$stm->execute()) die('sql error');
    return $stm->fetch();
}

function allianceLookup($aid) {
    global $dbr;
    $stm = $dbr->prepare('SELECT * FROM alliance_lookup WHERE allianceId = :allianceId');
    $stm->bindValue(':allianceId', $aid);
    if (!$stm->execute()) die('sql error');
    return $stm->fetch();
}
?>
