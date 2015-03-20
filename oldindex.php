<?php

// Quick and Dirty
// Get File as String
$newsfile = file_get_contents('news.txt');
// split for lines
$newslines = explode("\n",$newsfile);
//number of news
$nnews = count($newslines);

//number of rows needed
$nrows = ceil($nnews/2);

// new line/row needed?
$newline = true;

$output = "";
$rowstart = '<div class="row">';
$rowend = '</div>';

foreach ($newslines as $value) {
	//split the line information
	$parts = explode("#|#",$value);
	if ($newline) {
		$output .= $rowstart;
	}
	$output .= '<div class="col-md-6 portfolio-item">
                <h2>
                    <a href="#">'.$parts[0].'</a>
                    <small>'.$parts[2].'</small>
                </h2>
                <p>'.$parts[1].'</p>
            </div>';
	if ($newline == false) {
		$output .= $rowend;	
	}
	
	$newline = !$newline;
}
if ($newline == false)
	$output .= $rowend;

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="ZIS - Zentrales Informations System der ZKK">
    <meta name="author" content="Arno Schmetz">

    <title>ZIS - Zentrales Informations System der ZKK</title>

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

                <a class="navbar-brand" href="#">ZKK | Zentrales Informations System</a>
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
              <h1 class="page-header">Aktuelle Meldungen <small>&lt;hier der Link oder irgendwas&gt;</small></h1>
            </div>
        </div>
        <!-- /.row -->

<?php
/*
        <!--  Row -->
        <div class="row">
            <div class="col-md-6 portfolio-item">
                <h2>
                    <a href="#">AK Blubb verschoben!</a>
                    <small>30.02.2015</small>
                </h2>
                <p>Der AK Blubb wurde verschoben, weil die AK-Leiterin von einer Mate-Flasche &uuml;berrollt wurde. </p>
            </div>
            <div class="col-md-6 portfolio-item">
                <h2>
                    <a href="#">AK Mate f&auml;llt aus!</a>
                    <small>Mi, 23:22</small>
                </h2>
                <p>Der AK Mate wurde abgesagt, da der Don die Mate-Flasche f&uuml;r einen Ausflug gestohlen hat.</p>
            </div>
<!--            <div class="col-md-6 portfolio-item">
                <a href="#">
                    <img class="img-responsive" src="http://placehold.it/700x400" alt="">
                </a>
                <h2>
                    <a href="#">Bild-News</a>
                </h2>
                <p>Ein Beispiel-Post mit grossem Bild</p>                
            </div>
-->            
        </div>
        <!-- /.row -->

        <!--  Row -->
        <div class="row">
            <div class="col-md-6 portfolio-item">
                <h2>
                    <a href="#">AK Blubb verschoben!</a>
                    <small>30.02.2015</small>
                </h2>
                <p>Der AK Blubb wurde verschoben, weil die AK-Leiterin von einer Mate-Flasche &uuml;berrollt wurde. </p>
            </div>
            <div class="col-md-6 portfolio-item">
                <h2>
                    <a href="#">AK Mate f&auml;llt aus!</a>
                    <small>Mi, 23:22</small>
                </h2>
                <p>Der AK Mate wurde abgesagt, da der Don die Mate-Flasche f&uuml;r einen Ausflug gestohlen hat.</p>
            </div>
<!--            <div class="col-md-6 portfolio-item">
                <a href="#">
                    <img class="img-responsive" src="http://placehold.it/700x400" alt="">
                </a>
                <h2>
                    <a href="#">Bild-News</a>
                </h2>
                <p>Ein Beispiel-Post mit grossem Bild</p>                
            </div>
-->            
        </div>
        <!-- /.row -->
*/
print $output;
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

    </div>
    <!-- /.container -->


</body>

</html>
