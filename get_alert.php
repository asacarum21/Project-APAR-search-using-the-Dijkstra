<?php
	function output_result($result, $data){
		header('Content-Type: application/json');
		echo json_encode(
			[
				'status' => $result,
				'data' => $data
			]
		);
		exit;
	}

	//query dari tabel vertex
	$conn = new mysqli("localhost", "root", "", "tugas_sk");
	if ($conn->connect_error)
	    output_result(false, "Connection failed: " . $conn->connect_error);

	$result_alert = $conn->query("select * from alerts where is_solved = 0 order by id desc limit 1");
	$alert_row = NULL;
	$alert_data = NULL;
	if ($result_alert->num_rows > 0){
		$alert_row = $result_alert->fetch_assoc();

		//load all vertex
		$result_vertex = $conn->query("select * from vertexs where building_id = (select building_id from vertexs where id = " . $alert_row['vertex_id'] . ")");
		$all_vertex = [];
		if ($result_vertex->num_rows > 0) {
			while($row_vertex = $result_vertex->fetch_assoc())
				$all_vertex[] = $row_vertex;
		}

		$result_edge = $conn->query("select e.* from edges e join vertexs v1 on e.vertex1 = v1.id where v1.building_id = (select building_id from vertexs where id = " . $alert_row['vertex_id'] . ")");
		$all_edge = [];
		if ($result_edge->num_rows > 0) {
			while($row_edge = $result_edge->fetch_assoc())
				$all_edge[] = $row_edge;
		}

		$alert_data = [
			'id' => $alert_row['id'],
			'updated' => date('Y-m-d H:i:s'),
			'routes' => json_decode($alert_row['routes'], TRUE),
			'vertexs' => $all_vertex,
			'edges' => $all_edge
		];
	}

	output_result(true, $alert_data);