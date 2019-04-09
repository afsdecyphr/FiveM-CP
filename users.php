<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require('config.php');

if (!isset($_SESSION['uuid'])) {
  header('Location: index.php');
} else {
  $uri = $_SERVER['REQUEST_URI'];
  $parent_dir = dirname($_SERVER['SCRIPT_NAME']) . '/';
  $uri = str_replace($parent_dir, "", $uri);

  if (strpos($uri, 'assets') !== false) {
    $uri = str_replace("log/", "", $uri);
    $fh = fopen($uri, 'r');
    $pageText = fread($fh, 25000);
    echo $pageText;
  } else if (strpos($uri, 'config') !== false) {

  } else if (strpos($uri, 'process') !== false) {
    echo $uri;
    echo file_get_contents($uri);
  } else {
    $usersTable = '';
    $connection = new mysqli(MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DATABASE);
    if ($connection->connect_error) {
      die("Connection failed: " . $connection->connect_error);
    }
    $result = $connection->query("SELECT username, id FROM users");
    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $usersTable .= '<tr><td>' . $row['id'] . '</td><td>' . $row['username'] . '</td><td><a href="edit.php?id=' . $row['id'] . '"<button class="button">Edit</button></a></td><td><a href="delete.php?id=' . $row['id'] . '"<button class="button">Delete</button></a></td></tr>';
      }
    }
    $connection->close();
    renderPage($usersTable);
  }
}

function renderPage($usersTable) {
?>
<html>
  <head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="assets/style.css">
    <title>FiveM WCP - Users</title>
  </head>
  <body>
    <div class="navbar">
      <a class="header">WCP</a>
      <a href="console.php">Console</a>
      <div class="dropdown">
        <a style="float: none;" class="active dropbtn" href="">User Management</a>
        <div class="dropdown-content">
          <a href="new.php">New User</a>
        </div>
      </div> 
      <a href="logout.php" style="float: right;">Logout</a>
    </div>
    <h1 class="center" style="margin: 0 auto;">FiveM WPC - Users</h1>
    <div class="form-control" style="height:75%; width:100%; float:right;">
      <?php
        if (isset($_GET['msg']) && $_GET['msg'] != '') {
          if ($_GET['msg'] == 'noPermission') {
            echo "<p><font color=red>You don't have permission to do that!</font></p>";
          }
          if ($_GET['msg'] == 'unknown') {
            echo "<p><font color=red>An unknown error has occured!</font></p>";
          }
          if ($_GET['msg'] == 'deleteSelf') {
            echo "<p><font color=red>You cannot delete yourself!</font></p>";
          }
          if ($_GET['msg'] == 'deletedUser') {
            echo "<p><font color=green>Successfully deleted user!</font></p>";
          }
        }
        ?>
      <table style="width:100%;height:auto;min-height:100px;margin-top:0px;" id="users">
        <tr>
          <th style="font-weight: 700;">ID</th>
          <th style="font-weight: 700;">Username</th>
          <th style="font-weight: 700;">Edit</th>
          <th style="font-weight: 700;">Delete</th>
        </tr>
        <?php echo $usersTable; ?>
      </table>
    </div>
  </body>
</html>
<?php
}
?>
