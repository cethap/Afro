<?php 

	include "Afro.php";

	get('/', function($Afro) {
		echo "Hello! From test.php";
	});

	get('/request', function($Afro) {
		echo "<pre>".print_r($Afro, TRUE)."</pre>";
	});

	get('/blog/(\w+)/(\d+)', function($Afro, $catID, $pageID) {
		echo "Retrieving from blog: Category: {$catID} and Page ID: {$pageID}";
	});

	get('/countries/(.*?)', function($Afro) {
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
				$lookingFor = strtolower(basename($Afro->params[0], '.json'));
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

	get('/hello/(\w+)', function($Afro, $userName) {
		$Afro->format('json', function($Afro) use ($userName) {
			echo json_encode(array('name', $userName));
		});

		if(!$Afro->format) {
			echo 'Hello '. $userName . ', it\'s a good day today!';
		}
	});

?>