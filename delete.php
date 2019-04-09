<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require('config.php');
require('permissions.php');

if (isset($_GET['id'])) {
  if ($_GET['id'] != $_SESSION['id']) {
    if (hasPermission(3, $permissionsJson)) {
      $foreignId = $_GET['id'];
      $foreignPermissionsJson = '';
      $connection = new mysqli(MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DATABASE);
      if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
      }
      $uuid = mysqli_real_escape_string($connection, $_SESSION["uuid"]);
      $result = $connection->query("SELECT permissions FROM users WHERE id='$foreignId'");
      if ($result->num_rows == 1) {
          while($row = $result->fetch_assoc()) {
              $foreignPermissionsJson = $row["permissions"];
          }
      }
      if (hasPermission(0, $foreignPermissionsJson)) {
        if (hasPermission(2, $permissionsJson)) {
          $result = $connection->query("DELETE FROM users WHERE id='$foreignId'");
        }
      } else {
        if (hasPermission(3, $permissionsJson)) {
          $result = $connection->query("DELETE FROM users WHERE id='$foreignId'");
        }
      }
      
      $connection->close();
      header('Location: users.php?msg=deletedUser');
    } else {
      header('Location: users.php?msg=noPermission');
    }
  } else {
    header('Location: users.php?msg=deleteSelf');
  }
} else {
  header('Location: users.php?msg=unknown');
}

?>