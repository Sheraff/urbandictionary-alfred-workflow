<?php

require_once('workflows.php');
$w = new Workflows();

//querying API
$query = "lol";
$results = $w->request("http://api.urbandictionary.com/v0/define?term=".urlencode($query));
$results = json_decode($results);

//no result case, outputting like Alfred's "define"
if($results->result_type!="exact"){
	$w->result(
		"ud".$query,
		"http://www.urbandictionary.com/define.php?term=".urlencode($query),
		$query,
		"Open word in urbandictionary.com",
		'icon.png',
		'yes' );
	echo $w->toxml();
	return;
}

//sorting by quality
$results = $results->list;
function ruleOfThumbs($a, $b){
	$a_score = $a->thumbs_up/$a->thumbs_down;
	$b_score = $b->thumbs_up/$b->thumbs_down;
	if($a_score==$b_score) return 0;
	return ($a_score > $b_score) ? -1 : 1;
}
usort($results, ruleOfThumbs);

//outputting results like Alfred's "define"
foreach ($results as $result) {
	$w->result(
		$result->defid,
		$result->permalink,
		$result->word,
		$result->definition,
		'icon.png',
		'yes' );
}
echo $w->toxml();

?>