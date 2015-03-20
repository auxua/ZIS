<?php


	//Hard-coded, because only one user
	$logpass = '$6$rounds=5000$mysaltistsillyfo$J3rVYfihkowu.xgVqACWWZXM9s6wazKt7VGPcGb5HnIf5qFZWAngC/EfMDi/9AspZ3nAJXQckp.2nqWhMk2aJ/';
    
    
    
    
    //var_dump($_POST);
	
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      session_start();

      $username = $_POST['username'];
      $password = $_POST['password'];

      $hostname = $_SERVER['HTTP_HOST'];
      $path = dirname($_SERVER['PHP_SELF']);
	  
	  $passInput = crypt($password,'$6$rounds=5000$mysaltistsillyfo$');

      // check credentials
      if ($username == 'admin' && $logpass == $passInput) {
       $_SESSION['granted'] = true;

       // Redirect
       if ($_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.1') {
        if (php_sapi_name() == 'cgi') {
         header('Status: 303 See Other');
         }
        else {
         header('HTTP/1.1 303 See Other');
         }
        }

       header('Location: http://'.$hostname.($path == '/' ? '' : $path).'/index.php');
       exit;
       } else {
			$falseLogin = true;   
	   }
      }

?>

<!DOCTYPE html>
<html>
  <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>ZIS Administrator Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <!-- Le styles -->
  <link href="../bootstrap/css/bootstrap.css" rel="stylesheet" />
  <style type="text/css">
      body {
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
      }

      .form-signin {
        max-width: 300px;
        padding: 19px 29px 29px;
        margin: 0 auto 20px;
        background-color: #fff;
        border: 1px solid #e5e5e5;
        -webkit-border-radius: 5px;
           -moz-border-radius: 5px;
                border-radius: 5px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
           -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                box-shadow: 0 1px 2px rgba(0,0,0,.05);
      }
      .form-signin .form-signin-heading,
      .form-signin .checkbox {
        margin-bottom: 10px;
      }
      .form-signin input[type="text"],
      .form-signin input[type="password"] {
        font-size: 16px;
        height: auto;
        margin-bottom: 15px;
        padding: 7px 9px;
      }

    </style>
  <link href="../bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
  <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
  <!--[if lt IE 9]>
      <script src="../../Third Party Source Code/bootstrap/js/html5shiv.js"></script>
    <![endif]-->
  <!-- Fav and touch icons -->
  <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../bootstrap/ico/apple-touch-icon-144-precomposed.png" />
  <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../bootstrap/ico/apple-touch-icon-114-precomposed.png" />
  <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../bootstrap/ico/apple-touch-icon-72-precomposed.png" />
  <link rel="apple-touch-icon-precomposed" href="../bootstrap/ico/apple-touch-icon-57-precomposed.png" />
  <link rel="shortcut icon" href="../bootstrap/ico/favicon.png" />
  <script type="text/javascript" src="../ScriptLibrary/jquery-latest.pack.js"></script>
  <script type="text/javascript" src="../bootstrap/js/bootstrap.js"></script>
  </head>

  <body>

    <div class="container">

      <form class="form-signin" action="login.php" method="post" >
        <h2 class="form-signin-heading">Bitte anmelden:</h2>
        <input type="text" class="input-block-level" placeholder="User Name" name="username" />
        <input type="password" class="input-block-level" placeholder="Password" name="password" />
        <label class="checkbox">
          <input type="checkbox" value="remember-me" /> Remember me
        </label>
        <button class="btn btn-large btn-primary" type="submit">Sign in</button>
        <?php
			if (isset($falseLogin)) {
				?>
                	<br /><span style="color:#D30508">Falsche Daten</span>
                <?php
			}
		?>
      </form>

    </div> <!-- /container -->

  </body>
</html>
