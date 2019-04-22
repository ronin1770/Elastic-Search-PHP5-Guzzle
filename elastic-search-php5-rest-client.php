<?php
	include "settings.php";
	require 'vendor/autoload.php';
	
	class elastic_search_php5_rest_client {
		
		public function __construct() {
			
			global $USERNAME, $USERPASSWORD, $URL;

			$client = new \GuzzleHttp\Client();
			
			try { 
				$response = $client->request('GET', $URL, [
					'auth' => [$USERNAME, $USERPASSWORD], 
					'verify' => false
					]);
				echo $response->getStatusCode(); # 200
				echo $response->getBody(); # '{"id": 1420053, "name": "guzzle", ...}'
			} catch(exception $e ) {
				echo $e;
			}

			
		}
	}
	
	$esprc = new elastic_search_php5_rest_client; 
?>