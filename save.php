<?php
	$conn = new mysqli("localhost", "root", "", "tugas_sk");
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}

	//get data
	$vertexs = $_POST['vertex'];
	$edges = $_POST['edge'];

	if(count($vertexs) > 0 && count($edges) > 0){
		$insert building dulu
		//$conn->query("insert into buildings (name) values ('change this')");
		//$building_id = $conn->insert_id;

		$vertexs_id = [];
	
		foreach($vertexs as $v){
			$conn->query("insert into vertexs (building_id, name, x, y, floor, type) values ({$building_id}, '".
						  $v['name'] . "', ". $v['x'] .", ". $v['y'] .", 1, 1)");
			$vertexs_id[$v['id']] = ['id'=>$conn->insert_id, 'obj'=>$v];
		}

		foreach($edges as $e){
			$v1 = $vertexs_id[$e['from']];
			$v2 = $vertexs_id[$e['to']];
			$weight = sqrt(pow($v1['obj']['x']-$v2['obj']['x'], 2) + pow($v1['obj']['y']-$v2['obj']['y'], 2));
			$conn->query("insert into edges (vertex1, vertex2, weight) values (".
						  $v1['id'] . ", ". $v2['id'] .", {$weight})");
		}
	}

	$conn->close();

	echo 'success';