<?php 

	require('../config.php');

if ($debug) {
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
}
	require('auth.php'); 
	require('data.php');

    
    $site = 'newsadd';
	$subsite = "";
	if (isset($_SERVER['PATH_INFO'])) {
		if ($_SERVER['PATH_INFO'] == '/home') { $site='newsadd'; }
		else if ($_SERVER['PATH_INFO'] == '/newsadd') { $site='newsadd'; }
		else if ($_SERVER['PATH_INFO'] == '/newsdelete') { $site='newsdelete'; }
		else if ($_SERVER['PATH_INFO'] == '/akplanzapf') { $site='zapf';}
		else if ($_SERVER['PATH_INFO'] == '/akplankoma') { $site='koma';}
		else if ($_SERVER['PATH_INFO'] == '/akplankif') { $site='kif';}
		else if ($_SERVER['PATH_INFO'] == '/akplanzkk') { $site='zkk';}
		else if ($_SERVER['PATH_INFO'] == '/importzapf') { $site='zapf'; $subsite = "import"; }
		else if ($_SERVER['PATH_INFO'] == '/importkif') { $site='kif'; $subsite = "import"; }
		else if ($_SERVER['PATH_INFO'] == '/importkoma') { $site='koma'; $subsite = "import"; }
//		else if ($_SERVER['PATH_INFO'] == '/importzkk') { $site='zkk'; $subsite = "import"; }
		else if ($_SERVER['PATH_INFO'] == '/planzapf') { $site='zapf'; $subsite = "plan"; }
		else if ($_SERVER['PATH_INFO'] == '/plankif') { $site='kif'; $subsite = "plan"; }
		else if ($_SERVER['PATH_INFO'] == '/plankoma') { $site='koma'; $subsite = "plan"; }
		else if ($_SERVER['PATH_INFO'] == '/planzkk') { $site='zkk'; $subsite = "plan"; }
		else if ($_SERVER['PATH_INFO'] == '/room') { $site='room'; }
	}
?>

<!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8">
<meta charset="utf-8">
<title>ZIS - Admin panel</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">
<!-- Le styles -->
<link href="../bootstrap/css/bootstrap.css" rel="stylesheet">
<style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>
<link href="../bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
      <script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.6/html5shiv.min.js"></script>
    <![endif]-->
<!-- Fav and touch icons -->
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="../bootstrap/ico/apple-touch-icon-144-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="../bootstrap/ico/apple-touch-icon-114-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="../bootstrap/ico/apple-touch-icon-72-precomposed.png">
<link rel="apple-touch-icon-precomposed" href="../bootstrap/ico/apple-touch-icon-57-precomposed.png">
<link rel="shortcut icon" href="../bootstrap/ico/favicon.png">
<style type="text/css">
</style>
<script type="text/javascript" src="../ScriptLibrary/jquery-latest.pack.js"></script>
<script type="text/javascript" src="../bootstrap/js/bootstrap.js"></script>
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
          <a class="brand" href="/admin/home">ZIS - Admin Panel</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li><a href="/admin/newsadd">Neue News</a></li>
              <li><a href="/admin/newsdelete">News löschen</a></li>
              <li><a href="/admin/akplanzapf">ZaPF AK-Plan</a></li>
              <li><a href="/admin/akplankoma">KoMA AK-Plan</a></li>
              <li><a href="/admin/akplankif">KIF AK-Plan</a></li>
              <li><a href="/admin/akplanzkk">Plan gemeinsame AKs</a></li>
              <li><a href="/admin/room">Raumplan</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
    
    

    <div class="container">

<?php
	if (($site == "kif") || ($site == "koma") || ($site == "zapf"))
	{
		?>
        
        <div class="btn-group">
        	<a href="/admin/import<?php print $site; ?>" class="btn btn-info "><i class="icon-white icon-file"></i> AK-Liste importieren</a>
            <a href="/admin/plan<?php print $site; ?>" class="btn btn-primary "><i class="icon-white icon-calendar"></i> AK Planung</a>
		</div>
        <?php	
	} elseif ($site == "zkk") {
		?>
        <div class="btn-group">
            <a href="/admin/plan<?php print $site; ?>" class="btn btn-primary "><i class="icon-white icon-calendar"></i> AK Planung</a>
		</div>
        <?php	
	}
?>

      
      <?php
	  	switch($site) {
			case "newsadd":
				print "<h1>News Eintragen</h1>";
				if (isset($_POST['titel'])) {
					$titel = $_POST['titel'];
					$text = $_POST['text'];
					news_add($titel,$text,$enable_twitter);
				}
				// provide a form
				news_addform();	
				break;
			case "newsdelete":
				print "<h1>News löschen</h1>";
				if (isset($_POST['newsnumber'])) {
					news_delete($_POST['newsnumber']);
				}
				// provide the table of news
				news_table();
				break;
			case "kif":
				print "<h1>AK-Planung KIF</h1>";
				if ($subsite == "import") { import_kif(); }			
				if ($subsite == "plan") 
				{ 
					if (isset($_POST['submit']))
					{
						aktable_kifadd($_POST);
					}
					aktable_kif(); 
				}
				break;
			case "zapf":
				print "<h1>AK-Planung ZaPF</h1>";
				if ($subsite == "import") { import_zapf(); }	
				if ($subsite == "plan") 
				{ 
					if (isset($_POST['submit']))
					{
						aktable_zapfadd($_POST);
					}
					aktable_zapf(); 
				}		
				break;
			case "koma":
				print "<h1>AK-Planung KoMa</h1>";
				if ($subsite == "import") { import_koma(); }	
				if ($subsite == "plan") 
				{ 
					if (isset($_POST['submit']))
					{
						aktable_komaadd($_POST);
					}
					aktable_koma(); 
				}		
				break;
			case "zkk":
				print "<h1>AK-Planung gemeinsame AKs</h1>";
				if ($subsite == "plan") 
				{ 
					if (isset($_POST['submit']))
					{
						aktable_zkkadd($_POST);
					}
					aktable_zkk(); 
				}
				break;
			case "room":
				print "<h1>Raumplan</h1>";
				if (isset($_POST['roomname'])) 
				{ 
					getRoomPlan($_POST['roomname']);
				}
				else
				{
					show_roomform();
				}
				break;
		}
	  ?>
      

    </div> <!-- /container -->

  </body>
</html>