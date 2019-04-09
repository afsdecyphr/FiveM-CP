<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require('config.php');

if (!CONFIG_SETUPCOMPLETE) {
  renderSetup(setSpecialKey());
} else {
  if (!isset($_SESSION['uuid'])) {
    if (isset($_POST['submit'])) {
      $connection = new mysqli(MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DATABASE);
      if ($connection->connect_error) {
          die("Connection failed: " . $connection->connect_error);
      }
      $username = mysqli_real_escape_string($connection, $_POST["username"]);
      $password = mysqli_real_escape_string($connection, $_POST["password"]);
      $result = $connection->query("SELECT uuid, password, id FROM users WHERE username='$username'");
      if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
              $hashedPassword = $row["password"];
              $uuid = $row["uuid"];
              $id = $row["id"];
          }
          if (password_verify($password, $hashedPassword)) {
              $_SESSION["uuid"] = $uuid;
              $_SESSION["id"] = $id;
              header("Location: console.php");
          } else {
              $error = "Invalid username/password combinationn.";
              renderPage($username, $password, $error);
          }
      } else {
          $error = "Invalid username/password combinationnn." . $username;
          renderPage($username, $password, $error);
      }
      $connection->close();
    } else {
      renderPage("", "", "");
    }
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
      if (!CONFIG_SETUPCOMPLETE) {
        renderSetup(setSpecialKey());
      } else {
        header("Location: console.php");
      }
    }
  }
}

?>
<?php
function renderPage($username, $password, $error) {
?>

<!DOCTYPE html>
<html>
    <head>
        <title>FiveM WCP - Login</title>
        <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>
        <link rel='stylesheet' type='text/css' href='assets/style.css'>
        </style>
    </head>
    <body>
      <h1 class='center'>FiveM WCP - Login</h1>
      <div class='panel center'>
        <form action="" name="form1" method="post">
          <p>Username:</p>
          <input type='text' name='username' placeholder='Username'/>
          <p>Password:</p>
          <input type='password' name='password' placeholder='Password'/>
          <?php
            if ($error != "") {
              echo '<p><font color="red">' . $error . '</font></p>';
            }
          ?>
          <input type="submit" name="submit" value="Login" class='button wide' id='submitBtn'>
        </form>
      </div>
    </body>
</html>

<?php
}
function renderSetup($specialKey) {
?>
<html>
  <head>
    <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>
    <link rel='stylesheet' type='text/css' href='assets/style.css'>
    <title>FiveM WCP - Setup</title>
    <script>
      $(document).ready(function() {
       $('#input').keyup(function(e) {
        if (e.keyCode === 13) {

        }
       })
       $('#submitBtn').click(function(e) {
         $.ajax({
           type: 'POST',
           url: 'config.php',
           data: {
             doSetup: '1',
             rconIp: $('#rconIp').val(),
             rconPort: $('#rconPort').val(),
             rconPassword: $('#rconPassword').val(),
             logFile: $('#logFile').val(),
             mysqlHost: $('#mysqlHost').val(),
             mysqlUsername: $('#mysqlUsername').val(),
             mysqlPassword: $('#mysqlPassword').val(),
             mysqlDatabase: $('#mysqlDatabase').val(),
             rootUser: $('#rootUser').val(),
             rootPassword: $('#rootPassword').val(),
             specialKey: $('#showSpecial').html()
           },
           success: function() {

           }
         })
       })
       $('#showSpecial').click(function(e) {
         $('#showSpecial').html('<?php echo $specialKey; ?>')
       })
      })
    </script>
  </head>
  <body>
    <h1 class='center'>FiveM WCP Setup</h1>
    <div class='panel center'>
      <p>Admin Login Username:</p>
      <input type='text' id='rootUser' placeholder='Admin Login Username'/>
      <p>Admin Login Password:</p>
      <input type='password' id='rootPassword' placeholder='Admin Login Password'/>
      <p>RCON IP:</p>
      <input type='text' id='rconIp' placeholder='RCON IP'/>
      <p>RCON Port:</p>
      <input type='number' id='rconPort' value='30120'/>
      <p>RCON Password:</p>
      <input type='text' id='rconPassword' placeholder='RCON Password'/>
      <p>Log File:</p>
      <input type='text' id='logFile' placeholder='Log File'/>
      <p>MySQL Host:</p>
      <input type='text' id='mysqlHost' placeholder='MySQL Host'/>
      <p>MySQL Username:</p>
      <input type='text' id='mysqlUsername' placeholder='MySQL Username'/>
      <p>MySQL Password:</p>
      <input type='text' id='mysqlPassword' placeholder='MySQL Password'/>
      <p>MySQL Database:</p>
      <input type='text' id='mysqlDatabase' placeholder='MySQL Database'/>
      <p><strong>Write down your special key somewhere secret. It can be used to reset the admin password if you forget it and to change the RCON settings.</strong></p>
      <a id='showSpecial'>Click to show special key.</a>
      <button class='button wide' id='submitBtn'>Next</button>
    </div>
  </body>
</html>
<?php
}
?>
