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

?>
<?php
function renderPage($username, $permissions) {
?>
  <html>
    <head>
  	 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
      <link rel="stylesheet" type="text/css" href="assets/style.css">
  		<title>FiveM WCP - Users</title>
      <style>
        html {
          width: 100%;
          height: 100%;
          background-color: #d8d6d6;
          background-image: none;
          background-repeat: no-repeat;
          background-attachment: fixed;
          background-position: center;
        }
        #submitBtn {
          border-color:#13ff13;
        }
        a:link {
          text-decoration: none;
        }
        a:visited {
          text-decoration: none;
        }
        a:hover {
          text-decoration: none;
        }

        a:active {
          text-decoration: none;
        }
        .square {
          background: #11141c;
          width: 25%;
          height: 34px;
          display: inline;
        }
        .square:hover {
          background: #ccc;
          width: 25%;
          height: 34px;
          display: inline;
        }
        .status {
          background: #11141c;
          border: 1px solid red;
        }
        .status:hover {
          background: red;
          border: 1px solid #ccc;
          color: black;
        }
        html,
body {
  height: 100%;
  margin: 0
}

.box {
  display: flex;
  flex-flow: column;
  height: 100%;
}

.box .row {
  margin-left: 6px;
  margin-right: 6px;
}

.box .row.header {
  flex: 0 1 auto;
  /* The above is shorthand for:
  flex-grow: 0,
  flex-shrink: 1,
  flex-basis: auto
  */
}

.box .row.content {
  flex: 1 1 auto;
}

.box .row.footer {
  flex: 0 1 40px;
  margin-bottom:6px;
}
.online {
  color: green;
}
.offline {
  color: red;
}
      </style>
    	<script>
        
      </script>
    </head>
  	<body>
  	</body>
  </html>
<?php
}
?>
