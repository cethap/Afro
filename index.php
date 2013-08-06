<?php 

	include "Afro.php";

	get('/', function($Afro) {
		echo "Hello! From test.php";
	});

	get('/request', function($Afro) {
		echo "<pre>".print_r($Afro, TRUE)."</pre>";
	});

	get('/countries(.*?)', function($Afro) {
		$testData = array(
			"mx" => array(
				'iso' => 'MX',
				'fullName' => 'Mexico'
			),
			"jm" => array(
				'iso' => 'JM',
				'fullName' => 'Jamaica'
			)
		);

		if($Afro->format) {
			$Afro->format('json', function($Afro) use ($testData) {
				$lookingFor = strtolower(basename($Afro->params[1], '.json'));
				if(isset($testData[$lookingFor])) {
					echo json_encode($testData[$lookingFor]);
				}else{
					echo json_encode($testData);
				}
			});

			$Afro->format('csv', function($Afro) use ($testData) {
				$hFile = fopen("php://output", "w");
				fputcsv($hFile, array('ISO', 'Country'));

				$lookingFor = $Afro->param(2);
				if(isset($testData[$lookingFor])) {
					fputcsv($hFile, $testData[$lookingFor]);
				}
				fclose($hFile);
			});
		}else{
			echo "Countries are only available as a JSON format.";
		}

	});

	get('/hello/(.*?)', function($Afro) {
		$Afro->format('json', function($Afro) {
			echo json_encode(array('name', $Afro->param(2)));
		});

		if(!$Afro->format)
			echo 'Hello '. $Afro->param(2) . ', it\'s a good day today!';
	});

?>