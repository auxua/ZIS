<?php	
	
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
	
function tweetIt($post){
	
	//exceptions
	if (!is_string($post)) {
		return false;
	}
	if ( (strlen($post)<6) || (strlen($post)>140) ) {
		return false;
	}
	
	//Tweet it
	// require codebird
	require_once('codebird.php');
	require('../config.php');
 
	\Codebird\Codebird::setConsumerKey($twitter_consumerKey, $twitter_consumerSecret);
	$cb = \Codebird\Codebird::getInstance();
	$cb->setToken($oauth_token, $oauth_tokenSecret);
 
	$params = array(
	  'status' => $post,
	);
	$reply = $cb->statuses_update($params);
	
	return true;
}

function tweetPost($title,$link) {
	$title = trim($title);
	$link = trim($link);
	$fullText = 'Neuer Blogeintrag: "'.$title.'" - '.$link;
	if (strlen($fullText)>137) {
		$over = strlen($fullTest)-137;
		if ($over > strlen($title)) { return "Fehler: Link viel zu lang"; }
		$title = substr($title,0,(strlen($title)-$over-3))."...";
		$fullText = 'Neuer Blogeintrag: "'.$title.'" - '.$link;
		$erfolg = tweetIt($fullText);
		if ($erfolg) { return "Erfolg: Titel wurde verkuerzt, Tweet gesendet"; }
		return "Fehler: Titel wurde verkuerzt, aber Tweet wurde nciht abgesetzt"; 
	}
	$erfolg = tweetIt($fullText);
	if ($erfolg) { return "Erfolg: Titel wurde nicht verkuerzt, Tweet gesendet"; }
	return "Fehler: Titel wurde nicht verkuerzt, aber Tweet wurde nciht abgesetzt"; 
	
}

print "test";

tweetIt("Dies ist ein Test-Tweet... Einfach nciht beachten...");

print " finished";
?>