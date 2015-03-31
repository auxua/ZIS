<?php
     session_start();

     $hostname = $_SERVER['HTTP_HOST'];
     $path = dirname($_SERVER['PHP_SELF']);

     if (!isset($_SESSION['granted']) || !$_SESSION['granted']) {
      //header('Location: http://'.$hostname.($path == '/' ? '' : $path).'/login.php');
	  header('Location: http://'.$hostname."/news/admin/".'login.php');
      exit;
      }
?>