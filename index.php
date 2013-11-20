<?php 

	include "Afro.php";

	/**
	 * Both of these examples are simple route handling.
	 * The first is an index page. Nothing exciting happening here.
	 * And the second is another page, access via "/request".
	 */
	get('/', function($Afro) {
		echo "Hello! From test.php";
	});
	get('/request', function($Afro) {
		echo "<pre>".print_r($Afro, TRUE)."</pre>";
	});

	/**
	 * An example of named parameters.
	 * "blog" is not passed through as a parameter because it's part of the route.
	 * In this example, both parameters must be passed through.
	 */
	get('/blog/(\w+)/(\d+)', function($Afro, $catID, $pageID) {
		echo "Retrieving from Blog: Category: {$catID} and Page ID: {$pageID}";
	});

	/**
	 * This route is setup so that parameters MUST be passed through, meaning you
	 * cannot access /countries directly.
	 */
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

				$lookingFor = $Afro->param(1);
				if(isset($testData[$lookingFor])) {
					fputcsv($hFile, $testData[$lookingFor]);
				}
				fclose($hFile);
			});
		}else{
			echo "Countries are only available as a JSON format.";
		}

	});

	/**
	 * This example uses the old way of retrieving parameters, using the param object.
	 * It's still a valid and useful way of doing things, but without named parameters.
	 * Notice that the "name" parameter now lies in index 1, previously that would be 2,
	 * because "hello" is the first parameter.
	 * The first argument that isn't dynamic is now removed.
	 */
	get('/hello/(.*?)', function($Afro) {
		$Afro->format('json', function($Afro) {
			echo json_encode(array('name', $Afro->param(1)));
		});

		if(!$Afro->format)
			echo 'Hello '. $Afro->param(1) . ', it\'s a good day today!';
	});

?>