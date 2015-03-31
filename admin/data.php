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
    // search backwards starting from haystack length characters from the end
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
	$day = jddayofweek ( cal_to_jd(CAL_GREGORIAN, date("m"),date("d"), date("Y")) , 2 ); 
	$line .= $day.", ".date("H:i")."\n";
	
	// pre-append to file
	$line .= file_get_contents('../news.txt');
	file_put_contents('../news.txt', $line);
		
	print "Beitrag hinzugefügt";
	

	
	if ($tweet) {
		tweetIt("Aktuelle Ankündigung: ".$title." #zkk15");
		print "<br />News getweetet...";
	}
}

// provides a table of all news and the otion to delete entries
function news_table() {
	// Quick and Dirty
	// Get File as String
	$newsfile = file_get_contents('../news.txt');
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
	$newsfile = file_get_contents('../news.txt');
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
		$re = "/\\{\\{Ak Spalte 430\\n\\| name=(.+\\n)*\\}\\}/"; 
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
			$title = substr($value,$toffset,strpos($value,"\n",$toffset+7)-$toffset);
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
		
		print "fertig!<br />";
		
		$zkknum = count ($ZKKAKs);
		print "gemeinsame AKs gefunden: ".$zkknum."<br />";
		print "schreibe externe AKs...";
		file_put_contents('../ak-zkk', trim($zkkline));
		print "fertig!";
		
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
		
		print "fertig!<br />";
		$zkknum = count ($ZKKAKs);
		print "gemeinsame AKs gefunden: ".$zkknum."<br />";
		print "schreibe externe AKs...";
		file_put_contents('../ak-zkk', trim($zkkline));
		print "fertig!";
		print "<p />Importe ZaPF-AKs: ".$num;
}


function import_koma() {

		print "Hole Komapedia... ";
		
		// For better parsing, use the edit-view of the wiki
		// Debugging: use old version, no entries on the new page!
		$url = "http://die-koma.org/komapedia/koma:74_berlin-aks?do=edit";
		//$url = "http://die-koma.org/komapedia/koma:76_aachen-aks?do=edit";
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
		
		print "fertig!<br />";
		$zkknum = count ($ZKKAKs);
		print "gemeinsame AKs gefunden: ".$zkknum."<br />";
		print "schreibe externe AKs...";
		file_put_contents('../ak-zkk', trim($zkkline));
		print "fertig!";
		print "<p />Importe KoMa-AKs: ".$num;
		
		//var_dump($AKs);
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
				break;
			case "Freitag":
				$ch['fr'] = ' checked="checked" ';
				break;
			case "Samstag":
				$ch['sa'] = ' checked="checked" ';
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
}

// creates a plan file for zapf, based on the submitted form
function aktable_zapfadd($arg) {
	$line = aktable_genericadd($arg);
	if ($line =="") return;
	file_put_contents('../aklist-zapf', trim($line));	
}

// creates a plan file for koma, based on the submitted form
function aktable_komaadd($arg) {
	$line = aktable_genericadd($arg);
	if ($line =="") return;
	file_put_contents('../aklist-koma', trim($line));	
}

// creates a plan file for zkk, based on the submitted form
function aktable_zkkadd($arg) {
	$line = aktable_genericadd($arg);
	if ($line =="") return;
	file_put_contents('../aklist-zkk', trim($line));	
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

function show_zkkplan() {
	show_plan('aklist-zkk');	
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
	print '<h1>Die ZKK App</h1>
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
Ihr könnt die App für iPhone/iPad, Android und WindowsPhone aus dem jeweiligen App Store herunterladen (sucht nach "ZKK").
      
      Zusätzlich können Android-Nutzer die App manuell von <a href="zkk.apk">hier</a> installieren.
      
      Bei Fragen und Problemen meldet euch gerne auch bei Arno (auX).
      
      Die App ist natürlich OpenSource. Der Quelltext ist auf <a href="https://github.com/auxua/ZKK-App">Github</a> zu finden.
</p>
<p>
	Screenshots können unter Umständen von der aktuellen Version abweichen: <br> <br>
    <img src="app1.PNG" class="img-rounded" width="250px"> &nbsp; <img src="app2.PNG" class="img-rounded" width="250px"> &nbsp; <img src="app3.png" class="img-rounded" width="250px"> &nbsp; <img src="app4.png" class="img-rounded" width="250px"></p><p><br><img src="app5.PNG" class="img-rounded" width="1000px">
</p>';	
}

?>