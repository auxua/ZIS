<?php

/******************************************************
*
* data.php for data management and request interpretation
*
*	If you are not me and really want/need to understand or fix the code,
*	I feel absolutely sorry for you. This used to be a Q&D approach
*	
*	If you managed to achieve your goal with this code and are still sane,
*	I give you a beer or wine when we meet!
*
*******************************************************/


////////////////////////////////////////////////////////
/// Some helper functions and Twitter Integration
////////////////////////////////////////////////////////

function file_get_contents_utf8($fn) {
     $content = file_get_contents($fn);
      return mb_convert_encoding($content, 'UTF-8',
          mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
}


function startsWith($haystack, $needle) {
	
    // search backwards starting from haystack length cfrom the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}
function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}

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

/////////////////////////////////////////////////////////
/// NEWS
/////////////////////////////////////////////////////////


// prints a form for adding a News entry
function news_addform() {
	?>

<form class="form-horizontal" method="post" enctype="application/x-www-form-urlencoded">
  <fieldset>
    
    <!-- Form Name -->
    <legend>Neue Meldung erstellen</legend>
    
    <!-- Text input-->
    <div class="control-group">
      <label class="control-label" for="titel">Medlungstitel</label>
      <div class="controls">
        <input id="titel" name="titel" type="text" placeholder="Der AK Mate wird verschoben" class="input-large" required="">
      </div>
    </div>
    
    <!-- Textarea -->
    <div class="control-group">
      <label class="control-label" for="text">Meldungstext</label>
      <div class="controls">
        <textarea id="text" name="text">AK Mate fällt aus, da das Wetter schlecht ist</textarea>
      </div>
    </div>
    
    <!-- Button -->
    <div class="control-group">
      <label class="control-label" for="submit"></label>
      <div class="controls">
        <button id="submit" name="submit" class="btn btn-success">Eintragen</button>
      </div>
    </div>
  </fieldset>
</form>
<?php
}

// Update the news-Text-File
function news_add($title, $text, $tweet) {
	if (empty($title)) {
		print "Fehler - Keint Titel angegeben!";
		return;
	}
	if (empty($text)) {
		print "Fehler - Keint Text angegeben!";
		return;
	}
		
	$line = "";
	$line .= $title."#|#";
	$line .= trim(preg_replace('/\s+/', ' ', nl2br($text)))."#|#";
	$day = jddayofweek ( cal_to_jd(CAL_GREGORIAN, date("m"),date("d"), date("Y")) , 0 ); 
	// replace number of day by the german name
	$day = str_replace("0", "Sonntag", $day);
	$day = str_replace("1", "Montag", $day);
	$day = str_replace("2", "Dienstag", $day);
	$day = str_replace("3", "Mittwoch", $day);
	$day = str_replace("4", "Donnerstag", $day);
	$day = str_replace("5", "Freitag", $day);
	$day = str_replace("6", "Samstag", $day);
	$line .= $day.", ".date("H:i")."\n";
	
	// pre-append to file
	$line .= file_get_contents_utf8('../news.txt');
	file_put_contents('../news.txt', $line);
		
	print "Beitrag hinzugefügt";
	

	
	if ($tweet) {
		$tweetText = "Aktuelle Ankündigung: ".$title." #ZKK15";
		if (strlen($tweetText) < 140)
		{
			tweetIt("Aktuelle Ankündigung: ".$title." #ZKK15");
		}
		else
		{
			$shortTitle = substr($title,0,100)."...";
			tweetIt("Aktuelle Ankündigung: ".$shortTitle." #ZKK15");
		}
		print "<br />News getweetet...";
	}
}

// provides a table of all news and the otion to delete entries
function news_table() {
	// Quick and Dirty
	// Get File as String
	$newsfile = file_get_contents_utf8('../news.txt');
	// split for lines
	$newslines = explode("\n",$newsfile);
	// number of news
	$nnews = count($newslines);
	
	$output = '<table class="table table-striped table-bordered table-hover"><thead><tr><th>Titel</th><th>Text</th><th>Option</th></tr></thead>';
	$nnumber = 0;	
	// create a table of news/
	foreach ($newslines as $value) {
		// split the line information
		$parts = explode("#|#",$value);
		// add data and then the form to the table
		$output .= "<tr><td>".$parts[0]."</td><td>".$parts[1]."</td><td>";
		$output .= '<form class="form-horizontal" method="post" enctype="application/x-www-form-urlencoded">
<fieldset>
    <button id="submit" name="newsnumber" class="btn btn-danger" value="'.$nnumber.'">Löschen</button>
</fieldset>
</form></td></tr>';

		$nnumber++;
		
	}
	$output .= "</table>";
	print $output;
}

// delete news number $number from newsfile
function news_delete($number) {
	// Quick and Dirty
	// Get File as String
	$newsfile = file_get_contents_utf8('../news.txt');
	// split for lines
	$newslines = explode("\n",$newsfile);
	// number of news
	$nnews = count($newslines);
	
	if ($number > $nnews) return;
	$line ="";
	$nnumber = 0;	
	// walk through
	foreach ($newslines as $value) {
		if ($nnumber != $number) {	$line .= $value; $line .= "\n"; }
		$nnumber++;
		
	}	
	
	file_put_contents('../news.txt', trim($line));
	
	print "News erfolgreich bearbeitet";
}

// shows the news according to the bootstrap-2 container
function news_show() {
	// Quick and Dirty
	// Get File as String
	$newsfile = file_get_contents_utf8('news.txt');
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
		$output .= '<div class="span6">
					<h3>
						<a href="#">'.$parts[0].'</a>
						<small>'.$parts[2].'</small>
					</h3>
					<p>'.$parts[1].'</p>
				</div>';
		if ($newline == false) {
			$output .= $rowend;	
		}
		
		$newline = !$newline;
	}
	if ($newline == false)
		$output .= $rowend;	
		
	print $output;
}


////////////////////////////////////////////////////////
/// Workshop-importing part
////////////////////////////////////////////////////////

function import_kif() {

		print "Hole Kif-Wiki... ";
		
		// For better parsing, use the edit-view of the wiki
		$url = "https://kif.fsinf.de/w/index.php?title=KIF430:Arbeitskreise&action=edit";
		$re = "/{{Ak Spalte 430\\n\\| name=(.+\\n)*\\}\\}/"; 
		$content = file_get_contents_utf8($url);
		
		// get all AK-Spalten
		preg_match_all($re,$content,$matches);
		
		$AKs;
		$ZKKAKs;
		$zkkline="";
		// Number of Workshops
		$num = 0;
		// for file export
		$line = "";
		
		print "fertig!<br />beginne Extraktion... ";
		
		foreach ($matches[0] as $value)
		{
			// Regex every title and contents
			$toffset = strpos($value,"| name=")+7;
			//var_dump($toffset);
			//$title = substr($value,$toffset,strpos($value,"\n",$toffset+7)-$toffset);
			$title = substr($value,$toffset,strpos($value,"\n",$toffset)-$toffset);
			//var_dump(strpos($value,"\n",$toffset+7));
			var_dump($title);
			$coffset = strpos($value,"| beschreibung=")+15;
			$content = substr($value,$coffset,strpos($value,"\n",$coffset+15)-$coffset);
			
			// store in an array
			$ak['title'] = trim($title);
			$ak['content'] = trim($content);
			
			if (startsWith($ak['title'],"(ZKK) "))
			{
				$ak['title'] = substr($ak['title'],6);
				$ZKKAKs[] = $ak;
				$zkkline = $zkkline.$ak['title']."#|#".$ak['content']."\n";
				continue;
			}
			
			//print "<br />Entry: ".$title." - ".$content;
			if ($title != "Titel des Arbeitskreises")
			{
				// not a dummy - add to workshop-list
				$AKs[$num++] = $ak;
				if ($num != 1) $line = $line."\n";
				$line = $line.$ak['title']."#|#".$ak['content'];
			}
		}
		
		print "fertig<br />Beginne Datenexport...";
		//var_dump($AKs);
		

		file_put_contents('../ak-kif', trim($line));
		setVersion("ak-kif",getNow());
		
		print "fertig!<br />";
		/*
		$zkknum = count ($ZKKAKs);
		print "gemeinsame AKs gefunden: ".$zkknum."<br />";
		print "schreibe externe AKs...";
		file_put_contents('../ak-zkk', trim($zkkline));
		setVersion("ak-zkk",getNow());
		print "fertig!";*/
		
		print "<p />Importe KIF-AKs: ".$num;
}

function import_zapf() {

		print "Hole ZaPF-Wiki... ";
		
		// For better parsing, use the edit-view of the wiki
		$url = "https://vmp.ethz.ch/zapfwiki/index.php?title=SoSe15_Arbeitskreise&action=edit";
		$re = "/\\[\\[SoSe15_AK_.*\\]\\]/"; 
		$content = file_get_contents_utf8($url);
		
		
		// get all AK-Spalten
		preg_match_all($re,$content,$matches);
		
		$AKs;
		$ZKKAKs;
		$zkkline="";
		// Number of Workshops
		$num = 0;
		// for file export
		$line = "";
		
		print "fertig!<br />beginne Extraktion... ";
		
		
		foreach ($matches[0] as $value)
		{
			// Regex every title and contents
			$toffset = strpos($value,"|")+1;
			$title = substr($value,$toffset,strpos($value,"]]")-$toffset);
			//$coffset = strpos($value,"| beschreibung=")+15;
			//$content = substr($value,$coffset,strpos($value,"\n",$coffset+15)-$coffset);
			
			$content = $title;
			// store in an array
			$ak['title'] = trim($title);
			$ak['content'] = trim($content);

			if (startsWith($ak['title'],"(ZKK) "))
			{
				$ak['title'] = substr($ak['title'],6);
				$ZKKAKs[] = $ak;
				$zkkline = $zkkline.$ak['title']."#|#".$ak['content']."\n";
				continue;
			}
			
			//print "<br />Entry: ".$title." - ".$content;
			if ($title != "Titel des Arbeitskreises")
			{
				// not a dummy - add to workshop-list
				$AKs[$num++] = $ak;
				if ($num != 1) $line = $line."\n";
				$line = $line.$ak['title']."#|#".$ak['content'];
			}
		}
		
		print "fertig<br />Beginne Datenexport...";

		file_put_contents('../ak-zapf', trim($line));
		setVersion("ak-zapf",getNow());
		
		print "fertig!<br />";
		/*$zkknum = count ($ZKKAKs);
		print "gemeinsame AKs gefunden: ".$zkknum."<br />";
		print "schreibe externe AKs...";
		file_put_contents('../ak-zkk', trim($zkkline));
		setVersion("ak-zkk",getNow());
		print "fertig!";*/
		print "<p />Importe ZaPF-AKs: ".$num;
}


function import_koma() {

		print "Hole Komapedia... ";
		
		// For better parsing, use the edit-view of the wiki
		// Debugging: use old version, no entries on the new page!
		//$url = "http://die-koma.org/komapedia/koma:74_berlin-aks?do=edit";
		$url = "http://die-koma.org/komapedia/koma:76_aachen-aks?do=edit";
		$re = "/\\|\\*\\*.*\\n.*/";  
		$content = file_get_contents_utf8($url);
		
		
		// get all AK-Spalten
		preg_match_all($re,$content,$matches);
		
		$AKs;
		$ZKKAKs;
		$zkkline="";
		// Number of Workshops
		$num = 0;
		// for file export
		$line = "";
		
		print "fertig!<br />beginne Extraktion... ";
		
		
		foreach ($matches[0] as $value)
		{
			// Regex every title and contents
			$toffset = strpos($value,"|**")+3;
			$title = substr($value,$toffset,strpos($value,"**|")-$toffset);
			$coffset = strpos($value,"|",strpos($value,"\n"))+1;
			$content = substr($value,$coffset,strpos($value,"|",$coffset)-$coffset);
			
			// store in an array
			$ak['title'] = trim($title);
			$ak['content'] = trim($content);

			if (startsWith($ak['title'],"(ZKK) "))
			{
				$ak['title'] = substr($ak['title'],6);
				$ZKKAKs[] = $ak;
				$zkkline = $zkkline.$ak['title']."#|#".$ak['content']."\n";
				continue;
			}
			
			//print "<br />Entry: ".$title." - ".$content;
			if ($title != "Titel des Arbeitskreises")
			{
				// not a dummy - add to workshop-list
				$AKs[$num++] = $ak;
				if ($num != 1) $line = $line."\n";
				$line = $line.$ak['title']."#|#".$ak['content'];
			}
		}
		
		print "fertig<br />Beginne Datenexport...";

		file_put_contents('../ak-koma', trim($line));
		setVersion("ak-koma",getNow());
		
		print "fertig!<br />";
		$zkknum = count ($ZKKAKs);
		//print "gemeinsame AKs gefunden: ".$zkknum."<br />";
		//print "schreibe externe AKs...";
		//file_put_contents('../ak-zkk', trim($zkkline));
		//setVersion("ak-zkk",getNow());
		//print "fertig!";
		print "<p />Importe KoMa-AKs: ".$num;
		
		//var_dump($AKs);
}

function import_zkk() {

		print "Hole Kif-Wiki... ";
		
		// For better parsing, use the edit-view of the wiki
		$url = "https://kif.fsinf.de/w/index.php?title=KIF430:Arbeitskreise&action=edit";
		$re = "/{{Ak Spalte 430\\n\\| name=(.+\\n)*\\}\\}/"; 
		$content = file_get_contents_utf8($url);
		
		// get all AK-Spalten
		preg_match_all($re,$content,$matches);
		
		$AKs;
		$ZKKAKs;
		$zkkline="";
		// Number of Workshops
		$num = 0;
		// for file export
		$line = "";
		
		print "fertig!<br />beginne Extraktion... ";
		
		foreach ($matches[0] as $value)
		{
			// Regex every title and contents
			$toffset = strpos($value,"| name=")+7;
			//var_dump($toffset);
			//$title = substr($value,$toffset,strpos($value,"\n",$toffset+7)-$toffset);
			$title = substr($value,$toffset,strpos($value,"\n",$toffset)-$toffset);
			//var_dump(strpos($value,"\n",$toffset+7));
			//var_dump($title);
			$coffset = strpos($value,"| beschreibung=")+15;
			$content = substr($value,$coffset,strpos($value,"\n",$coffset+15)-$coffset);
			
			// store in an array
			$ak['title'] = trim($title);
			$ak['content'] = trim($content);
			
			if (startsWith($ak['title'],"(ZKK) "))
			{
				$ak['title'] = substr($ak['title'],6);
				$ZKKAKs[] = $ak;
				$zkkline = $zkkline.$ak['title']."#|#".$ak['content']."\n";
				continue;
			}
			
			//print "<br />Entry: ".$title." - ".$content;
			if ($title != "Titel des Arbeitskreises")
			{
				// not a dummy - add to workshop-list
				$AKs[$num++] = $ak;
				if ($num != 1) $line = $line."\n";
				$line = $line.$ak['title']."#|#".$ak['content'];
			}
		}
		
		print "fertig<br />Beginne Datenexport...";
		//var_dump($AKs);
		
		///
		/// ZKK-Import: ignore KIF, only ZKK import!
		///

		//file_put_contents('../ak-kif', trim($line));
		//setVersion("ak-kif",getNow());
		
		//print "fertig!<br />";
		
		$zkknum = count ($ZKKAKs);
		print "gemeinsame AKs gefunden: ".$zkknum."<br />";
		print "schreibe externe AKs...";
		file_put_contents('../ak-zkk', trim($zkkline));
		setVersion("ak-zkk",getNow());
		print "fertig!";
		
		//print "<p />Importe KIF-AKs: ".$num;
}

///////////////////////////////////////////////////////////////
/// Workshop-Management
///////////////////////////////////////////////////////////////

// Show Edit-Table for KIF
function aktable_kif() {
	$filesource = '../ak-kif';
	$filetarget = '../aklist-kif';
	aktable_showgeneric($filesource,$filetarget);
}

// Show Edit-Table for ZaPF
function aktable_zapf() {
	$filesource = '../ak-zapf';
	$filetarget = '../aklist-zapf';
	aktable_showgeneric($filesource,$filetarget);
}

// Show Edit-Table for KoMa
function aktable_koma() {
	$filesource = '../ak-koma';
	$filetarget = '../aklist-koma';
	aktable_showgeneric($filesource,$filetarget);
}

// Show Edit-Table for common
function aktable_zkk() {
	$filesource = '../ak-zkk';
	$filetarget = '../aklist-zkk';
	aktable_showgeneric($filesource,$filetarget);
}

// Shows an edit-Table for the workshops of a conference
// takes source and target of the plan as arguments for content
function aktable_showgeneric($a,$b) {
	// Get File as String
	$newsfile = file_get_contents_utf8($a);
	$versiona = getFileVersion($a);
	$versionb = getFileVersion($b);
	print "Letzte Aktualisierung (Import): ".$versiona." <br />Letzte Version (AK-Plan): ".$versionb." <br />";
	// split for lines
	$newslines = explode("\n",$newsfile);
	//number of news
	$nnews = count($newslines);
	$num = 0;
	
	if (file_exists($b)) {
		$planfile = file_get_contents_utf8($b);
	} else {
		$planfile = "";	
	}

	$planlines = explode("\n",$planfile);
	$aklist;
	
	foreach ($planlines as $value)
	{
		$pars = explode("#|#",$value);
		$ak['day'] = $pars[0];
		$ak['time'] = $pars[1];
		$ak['room'] = $pars[3];
		$ak['used'] = false;
		
		$aklist[$pars[2]] = $ak;
	}
	
	$output = '<form method="post" enctype="application/x-www-form-urlencoded"><table class="table table-striped table-bordered table-hover"><thead><tr><th>AK-Name</th><th>Tag</th><th>Uhrzeit</th><th>Raum</th></tr></thead><input type="hidden" name="nnews" value="'.$nnews.'" />';
	
	foreach ($newslines as $value) {
		//split the line information
		$parts = explode("#|#",$value);
		$parts[0] = str_replace('"',"'",$parts[0]);
		
		$output = $output.'<tr><td> '.$parts[0].'<input type="hidden" name="ak-'.$num.'" value="'.$parts[0].'" /></td>';
		$ch['do'] =""; $ch['fr'] =""; $ch['sa'] = "";
		switch ($aklist[$parts[0]]['day']) {
			case "Donnerstag":
				$ch['do'] = ' checked="checked" ';
				$aklist[$parts[0]]['used'] = true;
				break;
			case "Freitag":
				$ch['fr'] = ' checked="checked" ';
				$aklist[$parts[0]]['used'] = true;
				break;
			case "Samstag":
				$ch['sa'] = ' checked="checked" ';
				$aklist[$parts[0]]['used'] = true;
				break;
		}
		$output = $output.'<td><!-- Multiple Radios -->
<div class="control-group">
  <label class="control-label" for="radios">Tag:</label>
  <div class="controls">
    <label class="radio" for="radios-0-'.$num.'">
      <input type="radio" name="radios-'.$num.'" id="radios-0-'.$num.'" value="Donnerstag"'.$ch['do'].'>
      Donnerstag
    </label>
    <label class="radio" for="radios-1-'.$num.'">
      <input type="radio" name="radios-'.$num.'" id="radios-1-'.$num.'" value="Freitag"'.$ch['fr'].'>
      Freitag
    </label>
    <label class="radio" for="radios-2-'.$num.'">
      <input type="radio" name="radios-'.$num.'" id="radios-2-'.$num.'" value="Samstag"'.$ch['sa'].'>
      Samstag
    </label>
  </div>
</div></td>';

		$output = $output.'<td><div class="control-group">
  <label class="control-label" for="uhrzeit">Uhrzeit</label>
  <div class="controls">
    <input id="uhrzeit" name="uhrzeit-'.$num.'" type="text" placeholder="13:00" class="input-small" value="'.($aklist[$parts[0]]['time']).'">
    
  </div>
</div></td>';

		$output = $output.'<td><div class="control-group">
  <label class="control-label" for="raum">Raum</label>
  <div class="controls">
    <input id="raum" name="raum-'.$num.'" type="text" placeholder="" class="input-small" value="'.($aklist[$parts[0]]['room']).'">
    
  </div>
</div></td></tr>';

		$num++;
	}
	
	$output = $output.'</table><div class="control-group">
  <label class="control-label" for="submit"></label>
  <div class="controls">
    <button id="submit" name="submit" class="btn btn-primary">Übernehmen</button>
  </div>
</div></form>';

	foreach ($aklist as $key => $value)
	{
		if ($value['used'] == false)
		{
			$output = $output."<br /> Achtung - nicht mehr vorhanden: ".$key." - bei Speichern des Planes, wird der genannte AK aus dem Plan entfernt";
		}
	}
		
	print $output;		
}

// Taking the data from a form, format it for an output planning file
function aktable_genericadd($arg) {
	$nnews = $arg['nnews'];
	
	$AKs;
	
	$line ="";
	$delim = "#|#";
	// Stores pseudo-Hash-based Ak-names for testing on collisions
	$hashes;
	
	for ($i = 0; $i<$nnews; $i++)
	{
		$ak['name'] = $arg['ak-'.$i];
		$ak['day'] = $arg['radios-'.$i];
		$ak['time'] = $arg['uhrzeit-'.$i];
		$ak['room'] = $arg['raum-'.$i];
		$AKs[] = $ak;
		
		if (empty($ak['day'])) { $ak['day'] = " "; }
		if (empty($ak['time'])) { $ak['time'] = " "; }
		if (empty($ak['room'])) { $ak['room'] = " "; }
		
		$line = $line.$ak['day'].$delim.$ak['time'].$delim.$ak['name'].$delim.$ak['room'];
		
		if (!($i >= ($nnews-1))) { $line = $line."\n"; }
		
		$hash = $ak['day'].$ak['time'].$ak['room'];
		if (!empty($hashes[$hash]))
		{
			// Collission detected!
			print '<h3 style="color:red">Kollision! betroffene AKs: "'.$hashes[$hash]."' und '".$ak['name']."'</h3>\n";
			//var_dump($hashes);
			return "";
		}
		$hashes[$hash] = $ak['name'];
		
	}
	
	//var_dump($AKs);
	
	print "Daten eingetragen!";
	
	return $line;
}

// creates a plan file for kif, based on the submitted form
function aktable_kifadd($arg) {
	$line = aktable_genericadd($arg);
	if ($line =="") return;
	file_put_contents('../aklist-kif', trim($line));
	setVersion("aklist-kif",getNow());	
}

// creates a plan file for zapf, based on the submitted form
function aktable_zapfadd($arg) {
	$line = aktable_genericadd($arg);
	if ($line =="") return;
	file_put_contents('../aklist-zapf', trim($line));	
	setVersion("aklist-zapf",getNow());	
}

// creates a plan file for koma, based on the submitted form
function aktable_komaadd($arg) {
	$line = aktable_genericadd($arg);
	if ($line =="") return;
	file_put_contents('../aklist-koma', trim($line));	
	setVersion("aklist-koma",getNow());	
}

// creates a plan file for zkk, based on the submitted form
function aktable_zkkadd($arg) {
	$line = aktable_genericadd($arg);
	if ($line =="") return;
	file_put_contents('../aklist-zkk', trim($line));	
	setVersion("aklist-zkk",getNow());	
}

function show_kifplan($block) {
	if (!$block) {
		show_plan('aklist-kif');	
	} else {
		show_plan_blockwise('aklist-kif');
	}
}

function show_komaplan($block) {
	if (!$block) {
		show_plan('aklist-koma');	
	} else {
		show_plan_blockwise('aklist-koma');
	}
}

function show_zapfplan($block) {
	if (!$block) {
		show_plan('aklist-zapf');	
	} else {
		show_plan_blockwise('aklist-zapf');
	}
}

function show_zkkplan($block) {
	if (!$block) {
		show_plan('aklist-zkk');	
	} else {
		show_plan_blockwise('aklist-zkk');
	}
}

// Shows the plan of workshops according to the input file
function show_plan($source)
{
	$output = '<table class="table table-striped table-bordered table-hover"><thead<tr><th>AK-Name</th><th>Tag</th><th>Zeit</th><th>Ort</th></tr></thead>';
	
	$planfile;
	
	$planfile = file_get_contents_utf8($source);
	
	
	
	$planlines = explode("\n",$planfile);
	//$aklist;
	
	foreach ($planlines as $value)
	{
		$pars = explode("#|#",$value);
		$ak['day'] = $pars[0];
		$ak['time'] = $pars[1];
		$ak['room'] = $pars[3];
		
		$output = $output.'<tr><td>'.$pars[2].'</td><td>'.$pars[0].'</td><td>'.$pars[1].'</td><td>'.$pars[3].'</td></tr>';		
		//$aklist[$pars[2]] = $ak;
	}
	
	$output = $output."</table>";
	
	print $output;
	$versiona = getFileVersion($source);
	print "Letzte Aktualisierung: ".$versiona." <br />";
}


// Shows the plan of workshops according to the input file in blocks of day/time
function show_plan_blockwise($source)
{
	//$tablestart = '<table class="table table-striped table-bordered table-hover"><thead<tr><th>AK-Name</th><th>Tag</th><th>Zeit</th><th>Ort</th></tr></thead>';
	$tablestart = '<table class="table table-striped table-bordered table-hover"><thead<tr><th>AK-Name</th><th>Ort</th></tr></thead>';
	
	//$output = $tablestart;
	
	$planfile;
	
	$planfile = file_get_contents_utf8($source);
	
	$AKs;
	//$indices;
	
	$planlines = explode("\n",$planfile);
	//$aklist;
	
	foreach ($planlines as $value)
	{
		$pars = explode("#|#",$value);
		$ak['day'] = $pars[0];
		$ak['time'] = $pars[1];
		$ak['room'] = $pars[3];
		$ak['name'] = $pars[2];
		
		$index = $ak['day']." - ".$ak['time'];
		//$indices[] = $index;

		$AKs[$index][] = $ak;
		
		//$output = $output.'<tr><td>'.$pars[2].'</td><td>'.$pars[0].'</td><td>'.$pars[1].'</td><td>'.$pars[3].'</td></tr>';
		//$aklist[$pars[2]] = $ak;
	}
	
	//$output = $output."</table>";
	
	//print $output;
	
	//var_dump($AKs);
	
	// create blockwise
	ksort($AKs);
	
	$rowstart = '<div class="row">';
	$rowend = '</div>';
	$output = "";
	$newrow = true;
	
	foreach ($AKs as $key => $value)
	{
		if ($newrow) {
			$output = $output.$rowstart;
		}
		$output = $output.'<div class="span6">';
		$output = $output."<h2>".$key."</h2>";
		$output = $output.$tablestart;
		
		foreach ($value as $akb)
		{
			$output = $output.'<tr><td>'.$akb['name'].'</td><td>'.$akb['room'].'</td></tr>';
		}
		
		$output = $output."</table>";
		$output = $output.'</div>';
		
		if (!($newrow)) {
			$output = $output.$rowend;
		}
		
		$newrow = !$newrow;
	}
	
	if ($newrow == false) {
		$output = $output.$rowend;	
	}
	
	print $output;
	
	$versiona = getFileVersion($source);
	print "Letzte Aktualisierung: ".$versiona." <br />";
	
}


//////////////////////////////////////////////////////////////
// Room  listings
//////////////////////////////////////////////////////////////

function getRoomPlan($room)
{

	$tablehead = '<table class="table table-striped table-bordered table-hover"><thead<tr><th>Uhrzeit</th><th>AK-Name</th></tr></thead>';
	$output = "";
	
	$plan;
	
	$planfile1 = file_get_contents_utf8("../aklist-zapf"); $conf[0] = " [ZaPF]";
	$planfile2 = file_get_contents_utf8("../aklist-koma"); $conf[1] = " [KoMa]";
	$planfile3 = file_get_contents_utf8("../aklist-kif"); $conf[2] = " [KIF]";
	$planfile4 = file_get_contents_utf8("../aklist-zkk"); $conf[3] = " [ZKK]";
	
	$planlines = explode("\n",$planfile1);
	//$aklist;
	foreach ($planlines as $value)
	{
		$pars = explode("#|#",$value);
		$ak['day'] = $pars[0];
		$ak['time'] = $pars[1];
		$ak['room'] = $pars[3];
		$ak['name'] = $pars[2];
		
		if ($ak['room'] == $room)
		{
			// Store the AK Name and the conference suffix
			$plan[$ak['day']][$ak['time']] = $ak['name'].$conf[0];
		}
		
		//$output = $output.'<tr><td>'.$pars[2].'</td><td>'.$pars[0].'</td><td>'.$pars[1].'</td><td>'.$pars[3].'</td></tr>';		
		//$aklist[$pars[2]] = $ak;
	}

	$planlines = explode("\n",$planfile2);
	//$aklist;

	foreach ($planlines as $value)
	{
		$pars = explode("#|#",$value);
		$ak['day'] = $pars[0];
		$ak['time'] = $pars[1];
		$ak['room'] = $pars[3];
		$ak['name'] = $pars[2];
	
		
		if ($ak['room'] == $room)
		{
			$plan[$ak['day']][$ak['time']] = $ak['name'].$conf[1];
		}
		
		//$output = $output.'<tr><td>'.$pars[2].'</td><td>'.$pars[0].'</td><td>'.$pars[1].'</td><td>'.$pars[3].'</td></tr>';		
		//$aklist[$pars[2]] = $ak;
	}

	$planlines = explode("\n",$planfile3);
	//$aklist;
	
	foreach ($planlines as $value)
	{
		$pars = explode("#|#",$value);
		$ak['day'] = $pars[0];
		$ak['time'] = $pars[1];
		$ak['name'] = $pars[2];
		$ak['room'] = $pars[3];
		
		if ($ak['room'] == $room)
		{
			$plan[$ak['day']][$ak['time']] = $ak['name'].$conf[2];
		}
		
		//$output = $output.'<tr><td>'.$pars[2].'</td><td>'.$pars[0].'</td><td>'.$pars[1].'</td><td>'.$pars[3].'</td></tr>';		
		//$aklist[$pars[2]] = $ak;
	}
	
	$planlines = explode("\n",$planfile4);
	//$aklist;
	
	foreach ($planlines as $value)
	{
		$pars = explode("#|#",$value);
		$ak['day'] = $pars[0];
		$ak['time'] = $pars[1];
		$ak['name'] = $pars[2];
		$ak['room'] = $pars[3];
		
		if ($ak['room'] == $room)
		{
			$plan[$ak['day']][$ak['time']] = $ak['name'].$conf[3];
		}
		
		//$output = $output.'<tr><td>'.$pars[2].'</td><td>'.$pars[0].'</td><td>'.$pars[1].'</td><td>'.$pars[3].'</td></tr>';		
		//$aklist[$pars[2]] = $ak;
	}
	
	if (is_null($plan))
	{
		print "Kein AK in diesem Raum";
		return;
	}
	
	ksort($plan);
	
	foreach ($plan as $key => $value)
	{
		ksort($value);
		$output = $output.'<h3>'.$key.'</h3>';
		$output = $output.$tablehead;
		foreach ($value as $k => $v)
		{
			$output = $output.'<tr><td>'.$k.'</td><td>'.$v.'</td></tr>';		
		}
		//$aklist[$pars[2]] = $ak;
		$output = $output."</table><br />";
	}
	
	
	
	print $output;
}


function show_usedRooms()
{
	
	$tablehead = '<ul>';
	$output = "";
	
	$plan;
	
	$planfile1 = file_get_contents_utf8("../aklist-zapf"); $conf[0] = " [ZaPF]";
	$planfile2 = file_get_contents_utf8("../aklist-koma"); $conf[1] = " [KoMa]";
	$planfile3 = file_get_contents_utf8("../aklist-kif"); $conf[2] = " [KIF]";
	$planfile4 = file_get_contents_utf8("../aklist-zkk"); $conf[3] = " [ZKK]";
	
	$planlines = explode("\n",$planfile1);
	//$aklist;
	foreach ($planlines as $value)
	{
		$pars = explode("#|#",$value);
		$ak['day'] = $pars[0];
		$ak['time'] = $pars[1];
		$ak['room'] = $pars[3];
		$ak['name'] = $pars[2];
		
		$plan[$ak['room']] = true;
		
		//$output = $output.'<tr><td>'.$pars[2].'</td><td>'.$pars[0].'</td><td>'.$pars[1].'</td><td>'.$pars[3].'</td></tr>';		
		//$aklist[$pars[2]] = $ak;
	}

	$planlines = explode("\n",$planfile2);
	//$aklist;

	foreach ($planlines as $value)
	{
		$pars = explode("#|#",$value);
		$ak['day'] = $pars[0];
		$ak['time'] = $pars[1];
		$ak['room'] = $pars[3];
		$ak['name'] = $pars[2];
	
		$plan[$ak['room']] = true;
		
		//$output = $output.'<tr><td>'.$pars[2].'</td><td>'.$pars[0].'</td><td>'.$pars[1].'</td><td>'.$pars[3].'</td></tr>';		
		//$aklist[$pars[2]] = $ak;
	}

	$planlines = explode("\n",$planfile3);
	//$aklist;
	
	foreach ($planlines as $value)
	{
		$pars = explode("#|#",$value);
		$ak['day'] = $pars[0];
		$ak['time'] = $pars[1];
		$ak['name'] = $pars[2];
		$ak['room'] = $pars[3];
		
		$plan[$ak['room']] = true;
		
		//$output = $output.'<tr><td>'.$pars[2].'</td><td>'.$pars[0].'</td><td>'.$pars[1].'</td><td>'.$pars[3].'</td></tr>';		
		//$aklist[$pars[2]] = $ak;
	}
	
	$planlines = explode("\n",$planfile4);
	//$aklist;
	
	foreach ($planlines as $value)
	{
		$pars = explode("#|#",$value);
		$ak['day'] = $pars[0];
		$ak['time'] = $pars[1];
		$ak['name'] = $pars[2];
		$ak['room'] = $pars[3];
		
		$plan[$ak['room']] = true;
		
		//$output = $output.'<tr><td>'.$pars[2].'</td><td>'.$pars[0].'</td><td>'.$pars[1].'</td><td>'.$pars[3].'</td></tr>';		
		//$aklist[$pars[2]] = $ak;
	}
	
	if (is_null($plan))
	{
		print "Keine AK-Räume gefunden";
		return;
	}
	
	ksort($plan);
	
	foreach ($plan as $key => $value)
	{
		$output = $output.'<li>'.$key.'</li>';
	}
	
	
	
	print $output."</ul>";
}

function show_roomform()
{
	print '<form class="form-horizontal" method="post" enctype="application/x-www-form-urlencoded">
<fieldset>

<!-- Form Name -->
<legend>Raumabfrage</legend>

<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="roomname"></label>
  <div class="controls">
    <input id="roomname" name="roomname" type="text" placeholder="Raumname" class="input-small" required="">
    
  </div>
</div>

<!-- Button -->
<div class="control-group">
  <label class="control-label" for="singlebutton"></label>
  <div class="controls">
    <button id="singlebutton" name="singlebutton" class="btn btn-primary">Plan abrufen</button>
  </div>
</div>

</fieldset>
</form>
';
}

function show_app()
{
	?>
    <h1>Die ZKK App</h1>
      <p>Damit ihr auch die Informationen immer bei euch haben könnt, auch wenn kein Internet verfügbar ist, haben wir für euch die ZKK App gebaut.</p>
      <p>Zu den Features gehören (derzeit):</p>
      <p>
      <ul>
      	<li>AK-Listen der Konferenzen</li>
        <li>AK-Pläne der Konferenzen</li>
        <li>Eigener Stundenplan (halb-automatisch)</li>
        <li>Raumfinder</li>
        <li>Ort-Verzeichnis</li>
        <li>Links, Policies, Standards</li>
        <li>...</li>
      </ul>
Ihr könnt die App für iPhone/iPad, Android und WindowsPhone aus dem jeweiligen App Store herunterladen (sucht nach "ZKK" oder nutzt die Links unten).
     
      
      Bei Fragen und Problemen meldet euch gerne auch bei Arno (auX).
      
      Die App ist natürlich OpenSource. Der Quelltext ist auf <a href="https://github.com/auxua/ZKK-App">Github</a> zu finden.
</p>
<p>
<a href="https://play.google.com/store/apps/details?id=com.auxua.zkkapp"  target="_blank">
  <img alt="Android app on Google Play" style="height:60px"
       src="https://developer.android.com/images/brand/de_app_rgb_wo_60.png" />
</a> &nbsp; 
<a href="http://www.windowsphone.com/s?appid=f0f9b8d7-94ef-49ab-8f55-bae7e08e958b"  target="_blank">
  <img alt="App im Windows Phone Store" style="height:60px"
       src="wpstore.png" />
</a> &nbsp; 
<a href="https://itunes.apple.com/de/app/zkk-app/id982931658?l=de&ls=1&mt=8" target="_blank">
  <img alt="iOS App" style="height:60px"
       src="ios.png" />
</a>
</p>
<p>
	Screenshots können unter Umständen von der aktuellen Version abweichen: <br> <br>
    <div class="row">
	<div class="span3">
		<small>iPhone - Markierbare AKs</small><br />
    	<img src="app1.PNG" class="img-rounded" width="250px">
    </div><div class="span3">
	    <small>iPhone - Menü</small><br />
       	<img src="app2.PNG" class="img-rounded" width="250px"> 
    </div><div class="span3">
        <small>Windows Phone - Gemeinschaftsstandards</small><br />
        <img src="app3.png" class="img-rounded" width="250px">
    </div><div class="span3">
        <small>Android - Update</small><br />
        <img src="app4.png" class="img-rounded" width="250px">
    </div></div><p>&nbsp;</p>
    <div class="row">
	<div class="span12">     
        <small>iPad - News</small><br />
        <img src="app5.PNG" class="img-rounded" width="1000px">
    </div>
</p><p>&nbsp;</p>
<?php
}

/////////////////////////////////////////////
// File versioning
/////////////////////////////////////////////


// Get Version of the file $file - $admin describes whether the call comes from admin panel
function getVersion($file,$admin)
{
	$path = "fileversions";
	if ($admin)
	{
		$path = "../".$path;
		$file = substr($file,3);
	}
	
	$vfile = file_get_contents_utf8($path);
	
	$planlines = explode("\n",$vfile);
	//$aklist;
	foreach ($planlines as $value)
	{
		$pars = explode("#|#",$value);
		if ($pars[0] == $file)
		{
			return $pars[1];
		}
	}
	
	return "Unknown";
}

// Sets the Version of the File to the specified state
function setVersion($file,$newdate)
{
	$path = "../fileversions";
	
	$vfile = file_get_contents_utf8($path);
	$output = "";
	$planlines = explode("\n",$vfile);
	//$aklist;
	foreach ($planlines as $value)
	{
		$pars = explode("#|#",$value);
		if ($pars[0] == $file)
		{
			$output = $output."\n".$pars[0]."#|#".$newdate;
		}
		else
		{
			$output = $output."\n".$pars[0]."#|#".$pars[1];
		}
	}
	
	$output = trim($output);
	file_put_contents("../fileversions",$output);
}

// Get a simple representation of the Time
function getNow()
{
	$day = jddayofweek ( cal_to_jd(CAL_GREGORIAN, date("m"),date("d"), date("Y")) , 2 ); 
	$line = $day.", ".date("H:i");
	return $line;
}

// Tries to get the File version and tries detecting prefixes (paths)
function getFileVersion($file)
{
	if (startsWith($file,"../"))
	{
		return getVersion($file,true);
	}
	return getVersion($file,false);
}

// Prints the actual FAQ for the App
function showFAQ()
{
	?>
    <h1>FAQs</h1>
<div class="row">
	<div class="span6">
		<h3>Wie viel kostet die App?</h3>
		<p>Natürlich ist die App umsonst!</p>
	</div>
	<div class="span6">
		<h3>Für welche Systeme ist die App verfügbar?</h3>
		<p>
		<ul><li>Android ab Version 4</li><li>iOS ab Version 7.0</li><li>Windows Phone 8</li></ul><br />
		Möglicherweise läuft die App auch auf älteren Systemen, dies ist aber wenig wahrscheinlich.
		</p>
	</div>
</div>
<div class="row">
	<div class="span6">
		<h3>Welche Daten werden über mich gesammelt?</h3>
		<p>Keine. Ganz einfach.</p>
	</div>
	<div class="span6">
		<h3>Kann ich den Quelltext sehen?</h3>
		<p>Ja! Der Quelltext ist zu finden <a href="https://github.com/auxua/ZKK-App">auf Github</a></p>
	</div>
</div>
<?php
}

//////////////////////////////////////////////////////////////
// Mensa
//////////////////////////////////////////////////////////////

function showMensaForm()
{
	//print "test2";
?>

<form class="form-horizontal" method="post">
<fieldset>

<!-- Form Name -->
<legend>Mensa bewerten</legend>

<!-- Multiple Radios -->
<div class="control-group">
  <label class="control-label" for="radiomensa">Besuchte Mensa</label>
  <div class="controls">
    <label class="radio" for="radiomensa-0">
      <input type="radio" name="radiomensa" id="radiomensa-0" value="Mensa Academica" checked="checked">
      Mensa Academica
    </label>
    <label class="radio" for="radiomensa-1">
      <input type="radio" name="radiomensa" id="radiomensa-1" value="Bistro">
      Bistro
    </label>
    <label class="radio" for="radiomensa-2">
      <input type="radio" name="radiomensa" id="radiomensa-2" value="Ahornstraße">
      Ahornstraße
    </label>
    <label class="radio" for="radiomensa-3">
      <input type="radio" name="radiomensa" id="radiomensa-3" value="Mensa Vita">
      Mensa Vita
    </label>
  </div>
</div>

<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="textessen">Gericht</label>
  <div class="controls">
    <input id="textessen" name="textessen" type="text" placeholder="optional" class="input-xlarge">
  </div>
</div>

<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="texthome">Heimatuni</label>
  <div class="controls">
    <input id="texthome" name="texthome" type="text" placeholder="" class="input-xlarge" required="">
    
  </div>
</div>

<!-- Multiple Radios -->
<div class="control-group">
  <label class="control-label" for="radiorating">Bewertung im Vergleich zu eigenen Mensen</label>
  <div class="controls">
    <label class="radio" for="radiorating-0">
      <input type="radio" name="radiorating" id="radiorating-0" value="Viel besser" checked="checked">
      Viel besser
    </label>
    <label class="radio" for="radiorating-1">
      <input type="radio" name="radiorating" id="radiorating-1" value="Etwas besser">
      Etwas besser
    </label>
    <label class="radio" for="radiorating-2">
      <input type="radio" name="radiorating" id="radiorating-2" value="etwa gleich">
      etwa gleich
    </label>
    <label class="radio" for="radiorating-3">
      <input type="radio" name="radiorating" id="radiorating-3" value="schlechter">
      schlechter
    </label>
    <label class="radio" for="radiorating-4">
      <input type="radio" name="radiorating" id="radiorating-4" value="Viel Schlechter">
      Viel Schlechter
    </label>
  </div>
</div>

<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="textcomment">Kommentar</label>
  <div class="controls">
    <input id="textcomment" name="textcomment" type="text" placeholder="Mensen sind bah" class="input-xlarge">
    
  </div>
</div>

<!-- Button -->
<div class="control-group">
  <label class="control-label" for="submit"></label>
  <div class="controls">
    <button id="submit" name="submit" class="btn btn-success">Absenden</button>
  </div>
</div>

</fieldset>
</form>


<?php	
}

define ("MENSAMUTEX",42);

function addMensaRating()
{
	$mensa = $_POST['radiomensa'];
	if (empty($mensa)) $mensa = "unbekannt";
	$gericht = $_POST['textessen'];
	if (empty($gericht)) $gericht = "irgendwas";
	$uni = $_POST['texthome'];
	if (empty($uni)) $uni = "irgendwo";
	$rating = $_POST['radiorating'];
	if (empty($rating)) $rating = "Viel Schlechter";
	$comment = $_POST['textcomment'];
	if (empty($comment)) $comment = "Kein Kommentar";
	
	// Prevent race conditions by using mutex!
	$mutex = sem_get(MENSAMUTEX);
	sem_acquire($mutex);
	
	// get actual data
	$line = "";
	$line .= $mensa."#|#";
	$line .= trim(preg_replace('/\s+/', ' ', nl2br($gericht)))."#|#";
	$line .= trim(preg_replace('/\s+/', ' ', nl2br($uni)))."#|#";
	$line .= $rating."#|#";
	$line .= trim(preg_replace('/\s+/', ' ', nl2br($comment)))."\n";
	// pre-append to file
	$line .= file_get_contents_utf8('./mensen');
	file_put_contents('./mensen', $line);
	
	sem_release($mutex);	
	
	print "Bewertung eingetragen";
}
?>