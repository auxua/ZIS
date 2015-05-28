<?php
	require('config.php');

if ($debug) {
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
}

	require('admin/data.php');
    
	// The whole $path_info-Stuff is just a fix for the nginx...
	
	//var_dump($_SERVER['PATH_INFO']);
	$path_info = $_SERVER['PATH_INFO'];
	
	if (startsWith($path_info,"//")) {
		$path_info = substr($path_info,1);
	}
	
	
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
	
	$standards = false;
	
	if ($path_info == '/news') { $site='news'; }
		else if ($path_info == '/news/zapf') { $site='zapf'; $view = "list"; }
		else if ($path_info == '/news/kif') { $site='kif'; $view = "list"; }
		else if ($path_info == '/news/koma') { $site='koma'; $view = "list"; }
		else if ($path_info == '/news/zkk') { $site='zkk'; $view = "list"; }
		else if ($path_info == '/news/zapfblock') { $site='zapf'; $view = "block"; }
		else if ($path_info == '/news/kifblock') { $site='kif'; $view = "block"; }
		else if ($path_info == '/news/komablock') { $site='koma'; $view = "block"; }
		else if ($path_info == '/news/zkkblock') { $site='zkk'; $view = "block"; }
		else if ($path_info == '/news/zapfblock2day') { $site='zapf'; $view = "block2day"; }
		else if ($path_info == '/news/kifblock2day') { $site='kif'; $view = "block2day"; }
		else if ($path_info == '/news/komablock2day') { $site='koma'; $view = "block2day"; }
		else if ($path_info == '/news/zkkblock2day') { $site='zkk'; $view = "block2day"; }
		//else if ($path_info == '/news/tagungsheft') { $site='koma'; $view = "block"; }
		else if ($path_info == '/news/zapfgo') { $site='zapfgo';  }
		else if ($path_info == '/news/standallgemein') { $site='standallgemein'; $standards = true;  }
		else if ($path_info == '/news/standtwitter') { $site='standtwitter'; $standards = true;  }
		else if ($path_info == '/news/standfoto') { $site='standfoto'; $standards = true;  }
		else if ($path_info == '/news/standfeuer') { $site='standfeuer'; $standards = true;  }
		else if ($path_info == '/news/app') { $site='app';  }
		//else if ($path_info == '/engelsystem') { $site='engel';  }
		else if ($path_info == '/news/zapfsatzung') { $site='zapfsatzung';  }
		else if ($path_info == '/news/plan') { $site='plan';  }
		else if ($path_info == '/news/mensa') { $site='mensa';  }
		else if ($path_info == '/news/mensaapp') { $site='mensaapp';  }

// if this site is requested by the App, only provide basic information and no HTML
if ($site == 'mensaapp')
{
	//var_dump($_POST);
	addMensaRating();
	exit();
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
        padding-top: 120px; /* 60px to make the container go all the way to the bottom of the topbar */
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
    <!--<div class="navbar navbar-inverse">-->
      <div class="navbar-inner">
        <div class="container">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand" href="/news">ZIS - Zentrales InformationsSystem</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li><a href="/news"><i class="icon-warning-sign icon-white" > </i> News</a></li>
              <li><a href="/news/plan"><i class="icon-th-large icon-white" > </i> Ablaufplan</a></li>
              <li><a href="/news/zapf"><i class="icon-th-large icon-white" > </i> AK-Plan ZaPF</a></li>
              <li><a href="/news/kif"><i class="icon-th-large icon-white" > </i> AK-Plan KIF</a></li>
              <li><a href="/news/koma"><i class="icon-th-large icon-white" > </i> AK-Plan KoMa</a></li>
              <li><a href="/news/zkk"><i class="icon-th-large icon-white" > </i> Gemeinsame AKs</a></li>
              <li><a href="/news/app"><i class="icon-download-alt icon-white" > </i> ZKK App</a></li>
              <li><a href="/news/tagungsheft.pdf"><i class="icon-file icon-white" > </i> Tagungsheft</a></li>
              <li><a href="/engelsystem"><i class="icon-wrench icon-white" > </i> Mithelfen!</a></li>
              <li><a href="/news/zapfgo"><i class="icon-comment icon-white" > </i> GO/Satzung ZaPF</a></li>
              <li><a href="/news/standallgemein"><i class="icon-comment icon-white" > </i> Gemeinschaftsstandards</a></li>
              <li><a href="/news/mensa"><i class="icon-check icon-white" > </i> Mensabewertung</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">
    
    <?php
	if (($site == "zapf") || ($site == "kif") || ($site == "koma") || ($site == "zkk"))
	{
		?>
        <br />
        <div class="btn-group">
        	<a href="/news/<?php print $site; ?>" class="btn btn-info "><i class="icon-white icon-th-list"></i> als Liste</a>
            <a href="/news/<?php print $site; ?>block" class="btn btn-primary "><i class="icon-white icon-th-large"></i> als Slot-Blöcke</a>
            <a href="/news/<?php print $site; ?>block2day" class="btn btn-primary "><i class="icon-white icon-th-large"></i> nur Heute</a>
		</div>
        <?php		
		
	} elseif (($site == "zapfgo") || ($site == "zapfsatzung"))
	{
		?>
        <br />
        <div class="btn-group">
        	<a href="/news/zapfgo" class="btn btn-info "><i class="icon-white icon-list-alt"></i> ZaPF GO</a>
            <a href="/news/zapfsatzung" class="btn btn-primary "><i class="icon-white icon-list-alt"></i> ZaPF Satzung</a>
		</div>
        <?php		
	} elseif ($standards)
	{
		?>
        <br />
        <div class="btn-group">
        	<a href="/news/standallgemein" class="btn btn-info "><i class="icon-white icon-user"></i> Allgemeine Standards</a>
            <a href="/news/standtwitter" class="btn btn-primary "><i class="icon-white icon-retweet"></i> Twitterwall</a>
            <a href="/news/standfoto" class="btn btn-primary "><i class="icon-white icon-camera"></i> Fotoregeln</a>
            <a href="/news/standfeuer" class="btn btn-primary "><i class="icon-white icon-fire"></i> Seelenfeuerwehr</a>
		</div>
        <?php
	}
	
	
	?>
    
<?php

// get todays filter
$filter;
$day = date("d");
if ($day <29) {
	$filter = "Donnerstag";
} elseif ($day == 29) {
	$filter = "Freitag";
} else {
	$filter = "Samstag";
}

	switch($site) {
		case "news":
			print "<h1>News</h1>";
			news_show();
			break;
		case "kif":
			print "<h1>KIF AK-Plan</h1>";
			if ($view == "list") {
				show_kifplan(false);
			} elseif ($view == "block") {
				show_kifplan(true,"");
			} else {
				show_kifplan(true,$filter);
			}
			break;
		case "zapf":
			print "<h1>ZaPF AK-Plan</h1>";
			if ($view == "list") {
				show_zapfplan(false);
			} elseif ($view == "block") {
				show_zapfplan(true,"");
			} else {
				show_zapfplan(true,$filter);
			}
			break;
		case "koma":
			print "<h1>KoMa AK-Plan</h1>";
			if ($view == "list") {
				show_komaplan(false);
			} elseif ($view == "block") {
				show_komaplan(true,"");
			} else {
				show_komaplan(true,$filter);
			}
			break;
		case "zkk":
			print "<h1>Gemeinsamer AK-Plan</h1>";
			if ($view == "list") {
				show_zkkplan(false);	
			} elseif ($view == "block") {
				show_zkkplan(true,"");	
			} else {
				show_zkkplan(true,$filter);
			}
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
		case "app":
			show_app();
			showFAQ();
			break;
		case "engel":
			print "Link kommt...";
			break;
		case "plan":
			print '<h1>Ablaufplan der ZKK</h1><div class="text-center"><img src="https://zkk.fsmpi.rwth-aachen.de/images/ablaufplan.jpg" /></div>';
			break;
		case "standallgemein":
			print "<h1>Allgemeine Standards</h1>";
			print "<pre>";
			print file_get_contents_utf8("standallgemein.txt");
			print "</pre>";
			break;
		case "standtwitter":
			print "<h1>Twitterwall</h1>";
			print "<pre>";
			print file_get_contents_utf8("standtwitter.txt");
			print "</pre>";
			break;
		case "standfoto":
			print "<h1>Fotoregeln</h1>";
			print "<pre>";
			print file_get_contents_utf8("standfoto.txt");
			print "</pre>";
			break;
		case "standfeuer":
			print "<h1>Seelenfeuerwehr</h1>";
			print "<pre>";
			print file_get_contents_utf8("standfeuer.txt");
			print "</pre>";
			break;
		case "mensa":
			print "<h1>Mensabewertung</h1>";
			print "<p>Wir sind neugierig, wie unsere Mensa im vergleich zu euren Mensen abschneidet, da wir gefühlt euch alle beneiden. Helft uns, mit Zahlen unsere Eindrücke zu bestätigen oder zu widerlegen. Vielen Dank!</p>";
			if (isset($_POST['radiorating']))
			{
				// Execute add operation
				//var_dump($_POST);
				addMensaRating();
			}
			showMensaForm();
			break;
	}
	

?>



        <hr>

        
        <!-- Footer -->
        <footer>
            <div class="row">
                <div class="col-lg-12">
                    <p>Copyright &copy; Fachschaft I/1 der RWTH Aachen 2015 - <a href="https://zkk.fsmpi.rwth-aachen.de/kontakt.html" >Impressum</a> - ZIS ist OpenSource bei <a href="https://github.com/auxua/ZIS" target="_blank">Github</a></p>
                </div>
            </div>
            <!-- /.row -->
        </footer>

    </div> <!-- /container -->

  </body>
</html>
