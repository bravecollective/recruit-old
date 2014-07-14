<?php if (!defined('RECRUIT')) die('go away'); ?>
<?php

function tpl_header() {
    require("tpl/tpl_header.php");
}

function tpl_footer() {
    require("tpl/tpl_footer.php");
}

function tpl_char_details($charId) {
    global $dbr;
    require("tpl/tpl_char_details.php");
}
function tpl_char_employment($charId) {
    global $dbr;
    require("tpl/tpl_char_employment.php");
}

function tpl_app_history($charId) {
    global $dbr;
    require("tpl/tpl_app_history.php");
}


?>
