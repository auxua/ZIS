<?php
	require('config.php');

if ($debug) {
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
}

	require('admin/data.php');
    
	
	$path_info = $_SERVER['PATH_INFO'];
	if (!startsWith($path_info,"/news/")) {
		$path_info = "/news".$path_info;
	}
	
	//var_dump($path_info);
	
    $site = 'news';
	/*if (isset($_SERVER['PATH_INFO'])) {
		if ($_SERVER['PATH_INFO'] == '/news/news') { $site='news'; }
		else if ($_SERVER['PATH_INFO'] == '/news/zapf') { $site='zapf'; $view = "list"; }
		else if ($_SERVER['PATH_INFO'] == '/news/kif') { $site='kif'; $view = "list"; }
		else if ($_SERVER['PATH_INFO'] == '/news/koma') { $site='koma'; $view = "list"; }
		else if ($_SERVER['PATH_INFO'] == '/news/zkk') { $site='zkk';}
		else if ($_SERVER['PATH_INFO'] == '/news/zapfblock') { $site='zapf'; $view = "block"; }
		else if ($_SERVER['PATH_INFO'] == '/news/kifblock') { $site='kif'; $view = "block"; }
		else if ($_SERVER['PATH_INFO'] == '/news/komablock') { $site='koma'; $view = "block"; }
		else if ($_SERVER['PATH_INFO'] == '/news/app') { $site='koma'; $view = "block"; }
		else if ($_SERVER['PATH_INFO'] == '/news/tagungsheft') { $site='koma'; $view = "block"; }
		else if ($_SERVER['PATH_INFO'] == '/news/zapfgo') { $site='zapfgo';  }
		else if ($_SERVER['PATH_INFO'] == '/news/zapfsatzung') { $site='zapfsatzung';  }
	}*/
	
	
	if ($path_info == '/news') { $site='news'; }
		else if ($path_info == '/news/zapf') { $site='zapf'; $view = "list"; }
		else if ($path_info == '/news/kif') { $site='kif'; $view = "list"; }
		else if ($path_info == '/news/koma') { $site='koma'; $view = "list"; }
		else if ($path_info == '/news/zkk') { $site='zkk';}
		else if ($path_info == '/news/zapfblock') { $site='zapf'; $view = "block"; }
		else if ($path_info == '/news/kifblock') { $site='kif'; $view = "block"; }
		else if ($path_info == '/news/komablock') { $site='koma'; $view = "block"; }
		else if ($path_info == '/news/app') { $site='koma'; $view = "block"; }
		else if ($path_info == '/news/tagungsheft') { $site='koma'; $view = "block"; }
		else if ($path_info == '/news/zapfgo') { $site='zapfgo';  }
		else if ($path_info == '/news/zapfsatzung') { $site='zapfsatzung';  }

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
              <li><a href="/">News</a></li>
              <li><a href="/zapf">AK-Plan ZaPF</a></li>
              <li><a href="/kif">AK-Plan KIF</a></li>
              <li><a href="/koma">AK-Plan KOMA</a></li>
              <li><a href="/zkk">Gemeinsame AKs</a></li>
              <li><a href="/tagungsheft.pdf">Tagungsheft</a></li>
              <li><a href="/engelsystem">Mithelfen!</a></li>
              <li><a href="/zapfgo">GO/Satzung ZaPF</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">
    
    <?php
	if (($site == "zapf") || ($site == "kif") || ($site == "koma"))
	{
		?>
        
        <div class="btn-group">
        	<a href="/<?php print $site; ?>" class="btn btn-info "><i class="icon-white icon-th-list"></i> als Liste</a>
            <a href="/<?php print $site; ?>block" class="btn btn-primary "><i class="icon-white icon-th-large"></i> als Slot-Blöcke</a>
		</div>
        <?php		
		
	} elseif (($site == "zapfgo") || ($site == "zapfsatzung"))
	{
		?>
        
        <div class="btn-group">
        	<a href="/zapfgo" class="btn btn-info "><i class="icon-white icon-list-alt"></i> ZaPF GO</a>
            <a href="/zapfsatzung" class="btn btn-primary "><i class="icon-white icon-list-alt"></i> ZaPF Satzung</a>
		</div>
        <?php		
	}
	
	?>
    
<?php
	switch($site) {
		case "news":
			print "<h1>News</h1>";
			news_show();
			break;
		case "kif":
			print "<h1>KIF AK-Plan</h1>";
			if ($view == "list") {
				show_kifplan(false);
			} else {
				show_kifplan(true);
			}
			break;
		case "zapf":
			print "<h1>ZaPF AK-Plan</h1>";
			if ($view == "list") {
				show_zapfplan(false);
			} else {
				show_zapfplan(true);
			}
			break;
		case "koma":
			print "<h1>KoMa AK-Plan</h1>";
			if ($view == "list") {
				show_komaplan(false);
			} else {
				show_komaplan(true);
			}
			break;
		case "zkk":
			print "<h1>Gemeinsamer AK-Plan</h1>";
			show_zkkplan();
			break;
		case "zapfgo":
			print "<h1>Geschäftsordnung der ZaPF</h1>";
			print "<pre>";
			print file_get_contents_utf8("zapfgo.txt");
			print "</pre>";
			break;
		case "zapfsatzung":
			print "<h1>Satzung der ZaPF</h1>";
			print "<pre>";
			print file_get_contents_utf8("zapfsatzung.txt");
			print "</pre>";
			break;
	}
	

?>



        <hr>

        
        <!-- Footer -->
        <footer>
            <div class="row">
                <div class="col-lg-12">
                    <p>Copyright &copy; Fachschaft I/1 der RWTH Aachen 2015 - <a href="https://zkk.fsmpi.rwth-aachen.de/kontakt.html" >Impressum</a></p>
                </div>
            </div>
            <!-- /.row -->
        </footer>

    </div> <!-- /container -->

  </body>
</html>