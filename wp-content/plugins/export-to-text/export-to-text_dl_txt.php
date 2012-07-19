<?php
$core = $_POST['download'].'wp-load.php';
include( $core );
header('Content-disposition: attachment; filename=export-to-text.txt');
header('Content-type: text/plain');

sre2t_ajax();

?>
