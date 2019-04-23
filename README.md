# Elastic-Search-PHP5-Guzzle
Simple code that demostrates the working of elastic search REST API using PHP 5.6 + and Guzzle Library

Dependencies:

PHP Version 5.6

Guzzle Library (https://github.com/guzzle/guzzle)

Composer

You can install Guzzle using the following json:

{
   "require": {
      "guzzlehttp/guzzle": "~6.0"
   }
}

Installation steps:

1. Ensure composer is installed on your machine
2. Create a new text file and paste above text and save it as composer.json
3. Execute following command on command line. Ensure you have changed directory on command prompt where composer.json is saved:
    composer install 
4. You will see following output after installation:
  C:\Users\Player3\Desktop\trash>composer install
  Loading composer repositories with package information
  Updating dependencies (including require-dev)
  - Installing guzzlehttp/promises (v1.3.1)
    Downloading: 100%

  - Installing ralouphie/getallheaders (2.0.5)
    Downloading: 100%

  - Installing psr/http-message (1.0.1)
    Downloading: 100%

  - Installing guzzlehttp/psr7 (1.5.2)
    Downloading: 100%

  - Installing guzzlehttp/guzzle (6.3.3)
    Downloading: 100%

  guzzlehttp/guzzle suggests installing psr/log (Required for using the Log middleware)
  Writing lock file
  Generating autoload files


Following methods have been integrated:

=> Class constructor - tests if the connection can be established

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
         
=> Create Index - as the name implies creates the index using the supplied name

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
         
=> List Indices - displays the list of existing indices on the server

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
         
=> Push Data - provides a method for pushing data into the selected index

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
         
=> Import Sample Data - provides a method for importing data from the sample file

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
         
=> Search Range - provides a method of searching for the given range of values

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
         
=> Search Data - provides a method for searching the given term with the provided value

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
         
=> Get Data Item - provides a method to get the value of data using provided id

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
      
