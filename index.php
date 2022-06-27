<?php
require("Dijkstra.php");
$g = new Graph();
$string = file_get_contents('baru1.json');
$json = json_decode($string, true);

foreach ($json as $key => $val) {
	// echo "$key\n";
	$name = $json[$key]['obj']['name'];
	$type = $json[$key]['obj']['type'];
	$edge = $json[$key]['edges'];

	//echo "$key. $name ($type)";
	//echo "<br>";
	
	foreach($edge as $edgeKey => $edgeVal){
		$edgeId = $edge[$edgeKey]['id'];
		$vertex1 = $edge[$edgeKey]['vertex1'];
		$vertex2 = $edge[$edgeKey]['vertex2'];
		$weight = $edge[$edgeKey]['weight'];
		
		$g->addedge(strval($vertex1),strval($vertex2),$weight);
		
		//echo "$vertex1 - $vertex2 - $weight - $name";
		//echo "$g";
		//echo "<br>";
	}
	
}

list($distances, $prev) = $g->paths_from("181");
$path = $g->paths_to($prev, "170");
print("<pre>".print_r($path,true)."</pre>");

list($distances, $prev) = $g->paths_from("181");
$path = $g->paths_to($prev, "186");print("<pre>".print_r($path,true)."</pre>");

list($distances, $prev) = $g->paths_from("181");
$path = $g->paths_to($prev, "201");
print("<pre>".print_r($path,true)."</pre>");

list($distances, $prev) = $g->paths_from("181");
$path = $g->paths_to($prev, "210");
print("<pre>".print_r($path,true)."</pre>");

list($distances, $prev) = $g->paths_from("181");
$path = $g->paths_to($prev, "221");
print("<pre>".print_r($path,true)."</pre>");

list($distances, $prev) = $g->paths_from("181");
$path = $g->paths_to($prev,"225");
print("<pre>".print_r($path,true)."</pre>");

?>

