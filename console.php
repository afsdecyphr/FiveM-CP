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
    renderPage(CONFIG_LOGFILE);
  }
}

?>
<?php
function renderPage($log_file) {
?>
  <html>
    <head>
  	 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
      <link rel="stylesheet" type="text/css" href="assets/style.css">
  		<title>FiveM WCP - Console</title>
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
        $(document).ready(function() {
          var firstLoad = 1

           function loadLogs(){
             $.get({
               url: 'config.php?getStatus'
             }).then(function(data) {
               if (data == 1) {
                 $('#serverStatus').addClass("online");
                 $('#serverStatus').removeClass("offline");
                 $('#serverStatus').html('Online');
               }
               if (data == 0) {
                 $('#serverStatus').addClass("offline");
                 $('#serverStatus').removeClass("online");
                 $('#serverStatus').html('Offline');
               }
             });
             var bottom = $('#content').prop('scrollHeight')
             $.get({
               url: 'config.php?getLog'
             }).then(function(data) {
               if (firstLoad) {
                 $('#content').animate({ scrollTop: scrollHeight }, 1000);
                 var heightAfter = Math.round($('#content').scrollTop() + $('#content').height());
                 var split = data.replace('/\n/g', '<br />');
                 $('#content').html(split);
                 var scrollHeight = $('#content').prop('scrollHeight');
                 if (heightAfter == bottom) {
                   $('#content').animate({ scrollTop: scrollHeight }, 1000);
                 }
                 firstLoad = 0;
               }

                 $('#content').animate({ scrollTop: scrollHeight }, 1000);
                 var heightAfter = Math.round($('#content').scrollTop() + $('#content').height());
                 var split = data.replace('/\n/g', '<br />');
                 if (!elementContainsSelection(document.getElementById('content'))) {
                   $('#input').attr("placeholder", "Command...");
                   $('#content').html(split);
                 } else {
                   $('#input').attr("placeholder", "Text selection in progress, refreshing paused. Click here to resume refreshing.");
                 }
                 var scrollHeight = $('#content').prop('scrollHeight');
                 if (heightAfter == bottom) {
                   $('#content').animate({ scrollTop: scrollHeight }, 1000);
                 }
               setTimeout(loadLogs, 1000);
             });
           }
           setTimeout(loadLogs, 10);

           $('#input').keyup(function(e) {
             if (e.keyCode === 13) {
               var command = $('#input').val()
               $('#input').val('')
               $.get({
                 url: 'process.php?data=' + command
               }).then(function(data) {

               });
             }
           })
        })

        function isOrContains(node, container) {
            while (node) {
                if (node === container) {
                    return true;
                }
                node = node.parentNode;
            }
            return false;
        }

        function elementContainsSelection(el) {
            var sel;
            if (window.getSelection) {
                sel = window.getSelection();
                if (sel.rangeCount > 0) {
                    for (var i = 0; i < sel.rangeCount; ++i) {
                        if (!isOrContains(sel.getRangeAt(i).commonAncestorContainer, el)) {
                            return false;
                        }
                    }
                    return true;
                }
            } else if ( (sel = document.selection) && sel.type != "Control") {
                return isOrContains(sel.createRange().parentElement(), el);
            }
            return false;
        }
      </script>
    </head>
  	<body>
        <div class="navbar">
          <a class="header">WCP</a>
          <a href="console.php" class="active">Console</a>
          <div class="dropdown">
            <a style="float: none;" class="dropbtn" href="users.php">User Management</a>
            <div class="dropdown-content">
              <a href="new.php">New User</a>
            </div>
          </div>
          <a href="logout.php" style="float: right;">Logout</a>
        </div>
      <h1 class="center" style="margin: 0 auto;">FiveM WPC - Console</h1>
      <div class="form-control" style="height:75%; width:calc(75% - 6px);float:left;">
        <div style="width:100%;height:calc(100% - 80px);min-height:100px;margin-top:0px;overflow-y:scroll;" id="content" class="form-control" contenteditable="false">
        </div>
        <input type="text" id="input" class="form-control" style="width:100%; margin:0px; height: 34px; margin-top:6px;" placeholder="Command..." ></input>

      </div>
      <div class="form-control" style="height:75%; width:calc(25%); float:right; margin-left:6px;">
        <div style="width:100%;height:80px;margin-top:0px;" id="stats" class="form-control">
          <p style="display:inline-block; margin-bottom:5px; margin-top:0px;">Server Status: </p> <p id="serverStatus" style="display:inline-block; margin-top:0px; margin-bottom:5px;"></p>
          <p style="display:block; margin-top:5px;">Player Count: </p> <p id="playerCount" style="display:inline-block; margin-top:5px;"></p>
        </div>
      </div>
  	</body>
  </html>
<?php
}
?>
