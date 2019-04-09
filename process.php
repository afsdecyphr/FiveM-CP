<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require "config.php";
require "rcon.php";
require "permissions.php";

if (!isset($_GET['data']) || !isset($username))
  die('nopee');

$rcon = new q3query(CONFIG_RCONIP, CONFIG_RCONPORT, $success);

if (!$success) {
  die ("oho, not good");
}

$command = $_GET['data'];

$rcon->setRconpassword(CONFIG_RCONPASSWORD);
$rcon->rcon("clog " . $username . ": " . $command);

$commandA = explode(" ", $command);
if ($commandA[0] == "stop" && !hasPermission(7, $permissionsJson)) {
  $rcon->quit();
  die('nope');
}
if ($commandA[0] == "restart" && !hasPermission(8, $permissionsJson)) {
  $rcon->quit();
  die('nope');
}
if ($commandA[0] == "start" && !hasPermission(9, $permissionsJson)) {
  $rcon->quit();
  die('nope');
}
if ($commandA[0] == "clientkick" && !hasPermission(10, $permissionsJson)) {
  $rcon->quit();
  die('nope');
}
if ($commandA[0] == "status" && !hasPermission(11, $permissionsJson)) {
  $rcon->quit();
  die('nope');
}
$rcon->rcon($command);
$rcon->quit();
?>
