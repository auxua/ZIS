<?php
	require('admin/data.php');
    
    $site = 'news';
	if (isset($_SERVER['PATH_INFO'])) {
		if ($_SERVER['PATH_INFO'] == '/news') { $site='news'; }
		else if ($_SERVER['PATH_INFO'] == '/zapf') { $site='zapf'; }
		else if ($_SERVER['PATH_INFO'] == '/kif') { $site='kif'; }
		else if ($_SERVER['PATH_INFO'] == '/koma') { $site='koma';}
		else if ($_SERVER['PATH_INFO'] == '/zkk') { $site='zkk';}
	}

?> 

<!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8">
<meta charset="utf-8">
<title>Zentrales InformationsSystem ZIS</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">
<!-- Le styles -->
<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
<style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>
<link href="bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
      <script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.6/html5shiv.min.js"></script>
    <![endif]-->
<!-- Fav and touch icons -->
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="bootstrap/ico/apple-touch-icon-144-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="bootstrap/ico/apple-touch-icon-114-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="bootstrap/ico/apple-touch-icon-72-precomposed.png">
<link rel="apple-touch-icon-precomposed" href="bootstrap/ico/apple-touch-icon-57-precomposed.png">
<link rel="shortcut icon" href="bootstrap/ico/favicon.png">
<style type="text/css">
</style>
<script type="text/javascript" src="ScriptLibrary/jquery-latest.pack.js"></script>
<script type="text/javascript" src="bootstrap/js/bootstrap.js"></script>
</head>

  <body cz-shortcut-listen="true">

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand" href="#">ZIS</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li><a href="/news">News</a></li>
              <li><a href="/zapf">AK-Plan ZaPF</a></li>
              <li><a href="/kif">AK-Plan KIF</a></li>
              <li><a href="/koma">AK-Plan KOMA</a></li>
              <li><a href="/zkk">gemeinsame AKs</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">
<?php
	switch($site) {
		case "news":
			print "<h1>News</h1>";
			news_show();
			break;
		case "kif":
			print "<h1>KIF AK-Plan</h1>";
			show_kifplan();
			break;
		case "zapf":
			print "<h1>ZaPF AK-Plan</h1>";
			show_zapfplan();
			break;
		case "koma":
			print "<h1>KoMa AK-Plan</h1>";
			show_komaplan();
			break;
		case "zkk":
			print "<h1>Gemeinsamer AK-Plan</h1>";
			show_zkkplan();
			break;
	}
	

?>



        <hr>

        
        <!-- Footer -->
        <footer>
            <div class="row">
                <div class="col-lg-12">
                    <p>Copyright &copy; Fachschaft I/1 der RWTH Aachen 2015</p>
                </div>
            </div>
            <!-- /.row -->
        </footer>

    </div> <!-- /container -->

  </body>
</html>