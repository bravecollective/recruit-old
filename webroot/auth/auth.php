<?php if (!defined('RECRUIT')) die('go away'); ?>
<?php

function authCheck() {
    global $dbr, $cfg_cookie_name, $cfg_expire_session;

    $cookie = preg_replace("/[^A-Za-z0-9]/", '', $_COOKIE[$cfg_cookie_name]);
    if (empty($cookie)) {
	return "";
    }

    $stm = $dbr->prepare('DELETE FROM session WHERE createdAt < :time;');
    $stm->bindValue(':time', time() - $cfg_expire_session);
    if (!$stm->execute()) { die('sql error'); };

    $stm = $dbr->prepare('SELECT * FROM session WHERE sessionId = :sessionId;');
    $stm->bindValue(':sessionId', $cookie);
    if (!$stm->execute()) { die('sql error'); };
    $row = $stm->fetch();
    if (!$row) {
	return "";
    }
    return $row['charId'];
}

function authSession($charId) {
    global $dbr, $cfg_cookie_name, $cfg_expire_session, $cfg_cookie_https_only;

    require('PassHash.class.php');
    $ph = new PassHash();
    $cookie = $ph->gen_salt(32);
    setcookie($cfg_cookie_name, $cookie, time() + $cfg_expire_session, '/', null, $cfg_cookie_https_only, true);

    $stm = $dbr->prepare('INSERT INTO session (charId, sessionId, createdAt) VALUES (:charId, :sessionId, :createdAt);');
    $stm->bindValue(':charId', $charId);
    $stm->bindValue(':sessionId', $cookie);
    $stm->bindValue(':createdAt', time());
    if (!$stm->execute()) { die('sql error'); };
}

function authStatic($tpl) {
    tpl_header();
    require($tpl);
    tpl_footer();
}

function authInit() {
    global $cfg_core_endpoint, $cfg_core_application_id, $cfg_core_private_key, $cfg_core_public_key, $cfg_url_auth_success, $cfg_url_auth_fail;
    define('USE_EXT', 'GMP');
    require 'vendor/autoload.php';
    try {
	$api = new Brave\API($cfg_core_endpoint, $cfg_core_application_id, $cfg_core_private_key, $cfg_core_public_key);
	$info_data = array( 'success' => $cfg_url_auth_success, 'failure' => $cfg_url_auth_fail);
	$result = $api->core->authorize($info_data);
	header("Location: " . $result->location);
    } catch(\Exception $e) {
	authStatic("auth/tpl_auth_needed.php");
	return;
    }
}

function authVerify() {
    global $cfg_core_endpoint, $cfg_core_application_id, $cfg_core_private_key, $cfg_core_public_key, $cfg_url_base;
    $token = preg_replace("/[^A-Za-z0-9]/", '', $_GET['token']);
    if (empty($token)) {
	authStatic("auth/tpl_auth_needed.php");
	return;
    }
    define('USE_EXT', 'GMP');
    require 'vendor/autoload.php';
    try {
	$api = new Brave\API($cfg_core_endpoint, $cfg_core_application_id, $cfg_core_private_key, $cfg_core_public_key);
	$result = $api->core->info(array('token' => $token));
    } catch(\Exception $e) {
	authStatic("auth/tpl_auth_needed.php");
	return;
    }

    $charId = $result->character->id;
    $charName = $result->character->name;
    $tags = $result->tags;

    if (!in_array("alliance.corporation.bni.recruiters", $tags)) {
	authStatic("auth/tpl_auth_needed.php");
	return;
    }

    authSession($charId);
    header("Location: " . $cfg_url_base);
}

?>
