<?php
$file_access = 9;
require_once('../includes/check_access.php');
require_once('../config.php');

$uri = $_SERVER['REQUEST_URI'];
$uri = str_replace("/console/", "", $uri);

if (strpos($uri, 'log') !== false) {
      $uri = str_replace("log/", "", $uri);
    $fh = fopen("_private/" . $uri, 'r');

    $pageText = fread($fh, 25000);

    echo str_replace("<br />", "", nl2br($pageText));
}
    

$json = json_decode(file_get_contents("_private/servers.json"));
    
$found = false;
foreach($json->servers as $mydata) {
  if ($mydata->matchName == $uri) {
    $found = true;
    $data = $mydata;
  }
}

if ($found == true) {
  renderPage($data->name, $data->port, $data->rcon, $data->logFile);
  $address = $data->ip;
  $port = $data->port;
  $rcon_password = $data->rcon;
  $log_file = $data->logFile;
}

function gen_uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
        mt_rand( 0, 0xffff ),
        mt_rand( 0, 0x0fff ) | 0x4000,
        mt_rand( 0, 0x3fff ) | 0x8000,
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}
?>

<?php
function renderPage($name, $port, $rcon, $log_file) {
  echo '
  <html>
    <head>
  	 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
      <link rel="stylesheet" type="text/css" href="../../css/' . STYLE . '">
  		<title>' . $name . '  - Console</title>
      <style>
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
      </style>
      ' . "
    	<script>
        $(document).ready(function() {
          var firstLoad = 1
  
           function loadLogs(){
             var bottom = $('#content').prop('scrollHeight')
             $.get({
               url: '" . $log_file . "'
             }).then(function(data) {
               var heightAfter = $('#content').scrollTop() + $('#content').height()
               var split = data.replace(" . '/\n/g' . ", '<br />')
               $('#content').html(split);
               if (firstLoad || bottom === heightAfter){
                 $('#content').animate({ scrollTop: $('#content').prop('scrollHeight') }, 0);
                 firstLoad = 0
               }
               setTimeout(loadLogs, 1000);
             });
           }
           setTimeout(loadLogs, 10);
  
           $('#input').keyup(function(e) {
             if (e.keyCode === 13) {
               var command = $('#input').val()
               $('#input').val('')
               $.post('rocess.php', { data: command }, function(data) {})
             }
           })
        })
      </script>" . '
    </head>
  	<body style="padding:6px;">
      <script>
        document.getElementById("settt").style.left = ((200 - document.getElementById("sett").offsetWidth) * -1);
        document.getElementById("tools_set").style.left = ((200 - document.getElementById("tools").offsetWidth) * -1);
      </script>
      </div>
      <h1 class="center">' . $name . '</h1>
      <div class="form-control" style="height:75%; width:calc(75% - 6px);float:left;">
        <a href=""><button class="btn btn-primary form-control" style="margin-top:0px;margin-bottom:6px; width:auto;display:inline;float:right;border-color:#f4f442;">Restart</button></a>
        <div style="width:100%;height:calc(100% - 80px);min-height:100px;margin-top:0px;overflow-y:scroll;" id="content" class="form-control" contenteditable="false">
        </div>
        <input type="text" id="input" class="form-control" style="width:100%; margin:0px; height: 34px; margin-top:6px;" placeholder="Command..." ></input>
      </div>
      <div class="form-control" style="height:75%; width:calc(25%); float:right; margin-left:6px;"></div>
  	</body>
  </html>';
}
?>