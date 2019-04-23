<?php
	include "settings.php";
	require 'vendor/autoload.php';
	
	class elastic_search_php5_rest_client {
		
		private $m_client = null;
		
		public function __construct() {
			global $USERNAME, $USERPASSWORD, $URL;

			$this->m_client = new \GuzzleHttp\Client( [
				'headers' => [ 'Content-Type' => 'application/json' ]
				]
			);
			
			try { 
				$response = $this->m_client->request('GET', $URL, [
					'auth' => [$USERNAME, $USERPASSWORD], 
					'verify' => false
					]);
				if( $response->getStatusCode() == 200 ) {
					echo "Connection successful\n";
				}
				// echo $response->getBody(); # '{"id": 1420053, "name": "guzzle", ...}'
			} catch(GuzzleHttp\Exception\ClientException $e) {
				$response = $e->getResponse();
				echo "Connection failed\n" . $response->getStatusCode() . "\n" . $response->getReasonPhrase(); 
			}
		}
		
		public function create_index($index) {
			global $USERNAME, $USERPASSWORD, $URL;
			$retval = "";
			$url = $URL . "/$index?pretty";
			
			try {
				$response = $this->m_client->request('PUT', $url, [
					'auth' => [$USERNAME, $USERPASSWORD], 
					'verify' => false
					]);
				if( $response->getStatusCode() == 200 ) {
					echo "Connection successful\n";
					echo $response->getBody(); 
				}
			} catch(GuzzleHttp\Exception\ClientException $e) {
				$response = $e->getResponse();
				echo "Create Index Method failed\n" . $response->getStatusCode() . "\n" . $response->getReasonPhrase(); 
			}
		}
		
		public function list_indices() {
			global $USERNAME, $USERPASSWORD, $URL;
			$retval = "";
			$url = $URL . "/_cat/indices";
			
			try {
				$response = $this->m_client->request('GET', $url, [
					'auth' => [$USERNAME, $USERPASSWORD], 
					'verify' => false
					]);
				if( $response->getStatusCode() == 200 ) {
					echo "Connection successful\n";
					echo $response->getBody(); 
				}
			} catch(GuzzleHttp\Exception\ClientException $e) {
				$response = $e->getResponse();
				echo "List Indices Method failed\n" . $response->getStatusCode() . "\n" . $response->getReasonPhrase(); 
			}
		}
		
		public function push_data($index, $json_string) {
			global $USERNAME, $USERPASSWORD, $URL;
			$retval = "";
			$url = $URL . "/$index/_doc";
			
			try {
				$response = $this->m_client->request('POST', $url, [
					'auth' => [$USERNAME, $USERPASSWORD], 
					'verify' => false,
					'body' => $json_string,
					
					]);
				if( $response->getStatusCode() == 200 || $response->getStatusCode() == 201 ) {
					echo "Connection successful\n";
					echo $response->getBody(); 
				}
			} catch(GuzzleHttp\Exception\ClientException $e) {
				$response = $e->getResponse();
				echo "Push Data Method failed\n" . $response->getStatusCode() . "\n" . $response->getReasonPhrase(); 
				
			}
		}
		
		public function import_sample_data($index) {
			//Data is stored in the attached sample.csv file
			$row = 0;
			
			if (($handle = fopen("sample.csv", "r")) !== FALSE) {
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					$row++;
					
					if( $row > 1 ) {
						$page 		 = $data[0];
						$pageviews 	 = $data[1];
						$unique 	 = $data[2];
						$averagetime = $data[3];
						$bouncerate  = $data[4];
						
						$jsonstring = array ("page" => $page, 'pageviews' => $pageviews, 'unique' => $unique, 'averagetime' => $averagetime, 'bouncerate' => $bouncerate );
						
						$jsonstring = json_encode($jsonstring);
						$this->push_data($index, $jsonstring);
						
						echo "$row \n"; 
					}
				}
				fclose($handle);
			}
		}
		
		public function search_range($index, $search_term, $lessthan, $greaterthan) {
			global $USERNAME, $USERPASSWORD, $URL;
			$retval = "";
			$url = $URL . "/$index/_doc/_search/?pretty=true";
			
			$query = array( "query" => array ( "range" => array ( $search_term => array( "gte" => $greaterthan, "lte" => $lessthan ))));
			
			try {
				$response = $this->m_client->request('GET', $url, [
					'auth' => [$USERNAME, $USERPASSWORD], 
					'verify' => false,
					'body'   => json_encode($query)
					]);
				if( $response->getStatusCode() == 200 ) {
					echo "Connection successful\n";
					echo $response->getBody(); 
				}
			} catch(GuzzleHttp\Exception\ClientException $e) {
				$response = $e->getResponse();
				echo "Search Range Method failed\n" . $response->getStatusCode() . "\n" . $response->getReasonPhrase(); 
			}
		}
		
		public function search_data($index, $search_term, $search_value) {
			global $USERNAME, $USERPASSWORD, $URL;
			$retval = "";
			$url = $URL . "/$index/_doc/_search/?pretty=true";
			$query = array( "query" => array ( "match" => array ( $search_term => $search_value )));
			
			try {
				$response = $this->m_client->request('GET', $url, [
					'auth' => [$USERNAME, $USERPASSWORD], 
					'verify' => false,
					'body'   => json_encode($query)
					]);
				if( $response->getStatusCode() == 200 ) {
					echo "Connection successful\n";
					echo $response->getBody(); 
				}
			} catch(GuzzleHttp\Exception\ClientException $e) {
				$response = $e->getResponse();
				echo "Search Data Method failed\n" . $response->getStatusCode() . "\n" . $response->getReasonPhrase(); 
			}
		}
		
		public function get_data_item($index, $id) {
			global $USERNAME, $USERPASSWORD, $URL;
			$retval = "";
			$url = $URL . "/$index/_doc/$id";
			
			try {
				$response = $this->m_client->request('GET', $url, [
					'auth' => [$USERNAME, $USERPASSWORD], 
					'verify' => false
					]);
				if( $response->getStatusCode() == 200 ) {
					echo "Connection successful\n";
					echo $response->getBody(); 
				}
			} catch(GuzzleHttp\Exception\ClientException $e) {
				$response = $e->getResponse();
				echo "Get Data Item Method failed\n" . $response->getStatusCode() . "\n" . $response->getReasonPhrase(); 
			}
		}
	}
	
	
	
	$index = "nginx_logs";
	
	$esprc = new elastic_search_php5_rest_client; 
	
	
	// $esprc->create_index($index);
	
	// $esprc->list_indices();
	
	// $esprc->import_sample_data($index);
	
	// $esprc->get_data_item($index, "IRHiSGoBbLLKDSaFoZnp");
	
	// $esprc->search_data($index, "pageviews", "1655");
	$esprc->search_range($index, "pageviews", "1300", "1200");
?>