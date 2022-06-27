<?php
	function find_route($ver, $edg, $source){
		$indexed_vertex = [];
		$all_apar = [];
		foreach($ver as $c_v){
			$indexed_vertex[$c_v['id']] = [
				'obj'=>$c_v,
				'edges'=>[]
			];
			if($c_v['type'] == 3)
				$all_apar[] = $c_v;
		}

		//sudah bolak balik;
		foreach($edg as $c_e){
			$indexed_vertex[$c_e['vertex1']]['edges'][] = $c_e;
			$revert_edge = $c_e;
			$revert_edge['vertex1'] = $c_e['vertex2'];
			$revert_edge['vertex2'] = $c_e['vertex1'];
			$indexed_vertex[$c_e['vertex2']]['edges'][] = $revert_edge;
		}

		$Q = [];
		$dist = [];
		$prev = [];
		
		foreach($indexed_vertex as $key=>$v){
			$dist[$key] = INF;
			$prev[$key] = null;
			$Q[$key] = $v;
		}
		$dist[$source] = 0;
		
		while(count($Q) > 0){ //mencari 
			$min_distance = INF;
			$min_obj = NULL;
			foreach($Q as $u){
				if($dist[$u['obj']['id']] < $min_distance) {
					$min_distance = $dist[$u['obj']['id']];
					$min_obj = $u;
				}
			}
			unset($Q[$min_obj['obj']['id']]); // remove 
			foreach($min_obj['edges'] as $v){
				if(!isset($Q[$v['vertex2']])) continue;
				$alt = $dist[$min_obj['obj']['id']] + $v['weight'];
				if($alt <  $dist[$v['vertex2']]){
					$dist[$v['vertex2']] = $alt;
					$prev[$v['vertex2']] = $min_obj['obj']['id'];
				}
			}//prev menampung sebelumnya
		}
		$apar_route = [];
		foreach($all_apar as $apar){
			$new_route = [
				'obj' => $apar, 
				'distance' => $dist[$apar['id']],
				'route' => []
				];
			$current_vertex = $apar['id'];
			while($current_vertex != NULL){
				array_unshift($new_route['route'], $indexed_vertex[$current_vertex]['obj']);
				$current_vertex = $prev[$current_vertex];
			}
			
			$apar_route[] = $new_route;
		}
		usort($apar_route, function($a, $b) {return $a['distance'] < $b['distance']?-1:1;});
		return $apar_route;
		//edge sudah di buat 2 arah 
		//header('Content-Type: application/json');
		//echo json_encode($indexed_vertex);

	}
	//url/alert.php?device=1&passwor=8ac76feccd70c175cf769b5d2cd59805
	//http://localhost/Tugas/alert.php?device=1&password=8ac76feccd70c175cf769b5d2cd59805

	//type
	//1 = vertex biasa
	//2 = sensor
	//3 = apar

	$PASS = '8ac76feccd70c175cf769b5d2cd59805';

	$device_id = $_GET['device'];
	$password = $_GET['password'];

	if($PASS != $password){
		echo 'error: Password Salah...!';
		exit;
	}

	//query dari tabel vertex
	$conn = new mysqli("localhost", "root", "", "tugas_sk");
	if ($conn->connect_error) {
	    die("error: Connection failed: " . $conn->connect_error);
	}

	//query vertex
	$result_sensor = $conn->query("select * from vertexs where id = {$device_id} AND type = 2");
	if ($result_sensor->num_rows == 1) {
		$sensor = $result_sensor->fetch_assoc();

		//query semua device yang satu gedung, mau bikin graphnya
		$result_vertex = $conn->query("select * from vertexs where building_id = " . $sensor['building_id']);
		if ($result_sensor->num_rows > 0) {
			$all_vertex = [];
			while($row_vertex = $result_vertex->fetch_assoc())
				$all_vertex[] = $row_vertex;

			//kita udah dapat semua data vertex yang ada di gedung itu
			//load data edges masih dalam 1 arah harus diubah di 2 arah
			$result_edge = $conn->query("select e.* from edges e join vertexs v1 on e.vertex1 = v1.id where v1.building_id = " . $sensor['building_id']);
			if ($result_edge->num_rows > 0) {
				$all_edge = [];
				while($row_edge = $result_edge->fetch_assoc())
					$all_edge[] = $row_edge;
				$all_route = find_route($all_vertex, $all_edge, $device_id);

				//simpan db
				$conn->query("insert into alerts (vertex_id, created, routes, is_solved) values ({$device_id}, now(), '" . json_encode($all_route) . "', 0)");

				echo 'ok';
			}
		
		}

	}else{
		die("Sensor tidak ditemukan / ID bukan sensor");
	}

	$conn->close();
	//berdasarkan device yang memiliki type =2 
	//seperti = http://localhost/skripsi/alert.php?device=181&password=8ac76feccd70c175cf769b5d2cd59805
	//http://localhost/skripsi/alert.php?device=184&password=8ac76feccd70c175cf769b5d2cd59805
