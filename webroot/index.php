<?php

define('RECRUIT', 23);

require("config.php");

try {
    $dbr = new PDO($cfg_sql_url, $cfg_sql_user, $cfg_sql_pass);
} catch (PDOException $e) {
    die('database init failed');
}

require("helper.php");
require("tpl/tpl.php");
require("auth/auth.php");

$authCharId = authCheck();

if (empty($authCharId)) {

    $pAuth = preg_replace("/[^a-z]/", '', $_GET['auth']);

    if ($pAuth == "") {
	authStatic("auth/tpl_auth_needed.php");
    }

    if ($pAuth == "init") {
	authInit();
    }

    if ($pAuth == "verify") {
	authVerify();
    }

} else {

    tpl_header();

    $pNav = preg_replace("/[^a-z]/", '', $_GET['nav']);

    if ($pNav == "") {
	tpl_app_history("");
    }

    if ($pNav == "char") {
	$charId = preg_replace("/[^0-9]/", '', $_GET['charId']);
	tpl_char_details($charId);
	tpl_app_history($charId);
	tpl_char_employment($charId);
    }

    tpl_footer();
}

?>
