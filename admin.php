<?php
//if submitted, then insert it into file..
if (!empty($_POST['titel'])) {
	
	//verify password!
	// TODO: do something that is not hardcoded
	$pass = "TupperWurst";
	
	if ($_POST['password'] != $pass) {
		$status = "Fehler, falsches Passwort!";	
	} else {
	
		$line = "";
		$line .= $_POST['titel']."#|#";
		$line .= trim(preg_replace('/\s+/', ' ', nl2br($_POST['text'])))."#|#";
		$day = jddayofweek ( cal_to_jd(CAL_GREGORIAN, date("m"),date("d"), date("Y")) , 2 ); 
		$line .= $day.", ".date("H:i")."\n";
		
		//pre-append to file
		$line .= file_get_contents('news.txt');
		file_put_contents('news.txt', $line);
		
		$status = "Beitrag hinzugefügt";
	}
	
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="ZIS - Zentrales Informations System der ZKK">
    <meta name="author" content="Arno Schmetz">

    <title>ZIS - Zentrales Informations System der ZKK - Adminbereich</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/2-col-portfolio.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">

                <a class="navbar-brand" href="#">Zentrales Informations System der ZKK - Adminbereich</a>
            </div>
           <!-- Collect the nav links, forms, and other content for toggling -->
<!--             <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li> </li>
                    
                    <li> </li>
                </ul>
-->                
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>

    <!-- Page Content -->
    <div class="container">

        <!-- Page Header -->
        <div class="row">
            <div class="col-lg-12">
            <?php if (!empty($status)) print "<h2>".$status."</h2>"; ?>
            
              <form class="form-horizontal" action="admin.php" method="post" enctype="application/x-www-form-urlencoded">
<fieldset>

<!-- Form Name -->
<legend>Neue Meldung eintragen</legend>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="titel">Meldungstitel</label>  
  <div class="col-md-5">
  <input id="titel" name="titel" type="text" placeholder="AK Foo fällt aus" class="form-control input-md" required="">
    
  </div>
</div>

<!-- Textarea -->
<div class="form-group">
  <label class="col-md-4 control-label" for="text">Meldungstext</label>
  <div class="col-md-4">                     
    <textarea class="form-control" id="text" name="text">Der AK fällt aus, weil das Wetter dagegen ist.</textarea>
  </div>
</div>

<!-- Password input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="password">Kennwort</label>
  <div class="col-md-5">
    <input id="password" name="password" type="password" placeholder="" class="form-control input-md" required="">
    
  </div>
</div>

<!-- Button -->
<div class="form-group">
  <label class="col-md-4 control-label" for="senden"> </label>
  <div class="col-md-4">
    <button id="senden" name="senden" class="btn btn-primary" type="submit">Eintragen</button>
  </div>
</div>

</fieldset>
</form>


            </div>
        </div>
        <!-- /.row -->



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

    </div>
    <!-- /.container -->


</body>

</html>
