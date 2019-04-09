<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$__DEFAULTPERMISSIONS__ = '{"permissions":[{"0":0,"1":0,"2":0,"3":0,"4":0,"5":0,"6":0,"7":0,"8":0,"9":0}]}';
$__ADMINPERMISSIONS__ = '{"permissions":[{"0":1,"1":1,"2":1,"3":1,"4":1,"5":1,"6":1,"7":1,"8":1,"9":1}]}';

function getURL() {
  if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
    $link = "https";
  else
      $link = "http";

  // Here append the common URL characters.
  $link .= "://";

  // Append the host(domain name, ip) to the URL.
  $link .= $_SERVER['HTTP_HOST'];

  // Append the requested resource location to the URL
  $link .= $_SERVER['REQUEST_URI'];

  // Print the link
  return $link;
}

$configJson = json_decode(file_get_contents(__DIR__ . '\_private\config.json'));

//print_r($configJson->users[0]->admins[0]->username);
define('CONFIG_SETUPCOMPLETE', $configJson->setupComplete);
if (!CONFIG_SETUPCOMPLETE && basename($_SERVER['SCRIPT_FILENAME']) != 'index.php') {
  header('Location: index.php');
}
define('MYSQL_HOST', $configJson->mysqlHost);
define('MYSQL_USERNAME', $configJson->mysqlUsername);
define('MYSQL_PASSWORD', $configJson->mysqlPassword);
define('MYSQL_DATABASE', $configJson->mysqlDatabase);
define('CONFIG_RCONIP', $configJson->rconIp);
define('CONFIG_RCONPORT', $configJson->rconPort);
define('CONFIG_RCONPASSWORD', $configJson->rconPassword);
define('CONFIG_LOGFILE', $configJson->logFile);

if (isset($_SESSION['uuid'])) {
  $connection = new mysqli(MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DATABASE);
  if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
  }
  $uuid = mysqli_real_escape_string($connection, $_SESSION["uuid"]);
  $result = $connection->query("SELECT username, permissions FROM users WHERE uuid='$uuid'");
  if ($result->num_rows == 1) {
      while($row = $result->fetch_assoc()) {
          $username = $row["username"];
          $permissionsJson = $row["permissions"];
      }
  } else {
      header('Location: index.php');
  }
  $connection->close();
}

if (isset($_POST['doSetup'])
&& isset($_POST['rconIp'])
&& isset($_POST['rconPort'])
&& isset($_POST['rconPassword'])
&& isset($_POST['logFile'])
&& isset($_POST['mysqlHost'])
&& isset($_POST['mysqlUsername'])
&& isset($_POST['mysqlPassword'])
&& isset($_POST['mysqlDatabase'])
&& isset($_POST['rootUser'])
&& isset($_POST['rootPassword'])
&& isset($_POST['specialKey'])) {
  if (!CONFIG_SETUPCOMPLETE) {
    $__RCONIP__ = $_POST['rconIp'];
    $__RCONPORT__ = $_POST['rconPort'];
    $__RCONPASSWORD__ = $_POST['rconPassword'];
    $__LOGFILE__ = $_POST['logFile'];
    $__MYSQLHOST__ = $_POST['mysqlHost'];
    $__MYSQLUSERNAME__ = $_POST['mysqlUsername'];
    $__MYSQLPASSWORD__ = $_POST['mysqlPassword'];
    $__MYSQLDATABASE__ = $_POST['mysqlDatabase'];
    $__ROOTUSER__ = $_POST['rootUser'];
    $__ROOTPASSWORD__ = $_POST['rootPassword'];
    $contents = json_decode(file_get_contents(__DIR__ . '\_private\config.json'), true);
    if ($contents['specialKey'] == $_POST['specialKey']) {
      $connection = new mysqli($__MYSQLHOST__, $__MYSQLUSERNAME__, $__MYSQLPASSWORD__, $__MYSQLDATABASE__);
      if ($connection->connect_error) {
          die("Connection failed: " . $connection->connect_error);
      }
      $__HASHEDPASSWORD__ = password_hash($__ROOTPASSWORD__, PASSWORD_DEFAULT);
      $result = $connection->query("INSERT INTO users VALUES (DEFAULT, DEFAULT, '$__ROOTUSER__', '$__HASHEDPASSWORD__', '$__ADMINPERMISSIONS__')");
      $connection->close();
      $contents['setupComplete'] = true;
      $contents['rconIp'] = $__RCONIP__;
      $contents['rconPort'] = $__RCONPORT__;
      $contents['rconPassword'] = $__RCONPASSWORD__;
      $contents['logFile'] = $__LOGFILE__;
      $contents['mysqlHost'] = $__MYSQLHOST__;
      $contents['mysqlUsername'] = $__MYSQLUSERNAME__;
      $contents['mysqlPassword'] = $__MYSQLPASSWORD__;
      $contents['mysqlDatabase'] = $__MYSQLDATABASE__;
      file_put_contents(__DIR__ . '\_private\config.json', json_encode($contents));
      die('success');
    } else {
      die('invalidKey');
    }
  } else {
    die("Whoops, I'm already setup. Stop pulling my chain!");
  }
} else if (isset($_GET['getLog'])) {
  die(nl2br(file_get_contents(CONFIG_LOGFILE)));
} else if (isset($_GET['getStatus'])) {
  $content = json_decode(file_get_contents("http://" . CONFIG_RCONIP . ":" . CONFIG_RCONPORT . "/info.json"), true);
  if ($content) {
    die(1);
  } else {
  	die(0);
  }
}

function setSpecialKey() {
  $__SPECIALKEY__ = genUUID();
  $contents = json_decode(file_get_contents(__DIR__ . '\_private\config.json'), true);
  if ($contents['specialKey'] == '') {
    $contents['specialKey'] = $__SPECIALKEY__;
    file_put_contents(__DIR__ . '\_private\config.json', json_encode($contents));
    return $__SPECIALKEY__;
  } else {
    return $contents['specialKey'];
  }
}

function genUUID() {
  return sprintf(
    '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
    mt_rand( 0, 0xffff ),
    mt_rand( 0, 0xffff ),
    mt_rand( 0, 0xffff ),
    mt_rand( 0, 0x0fff ) | 0x4000,
    mt_rand( 0, 0x3fff ) | 0x8000,
    mt_rand( 0, 0xffff ),
    mt_rand( 0, 0xffff ),
    mt_rand( 0, 0xffff )
  );
}
?>
