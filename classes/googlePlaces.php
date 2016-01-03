<?php

include_once dirname(__FILE__) . '/core/utility.php';


// wrapper class for calling Google Places API from PHP code
class GooglePlaces
{
	private $key       = 'AIzaSyB9Zbt86U4kbMR534s7_gtQbx-0tMdL0QA';
    private $base_url  = 'https://maps.googleapis.com/maps/api/place';
	public $client = '';
	
	
	public function __construct()
    {
        
    }
	
	
	
	public function checkGooglePlaces($name,$longitude,$latitude) {
		
		// first find location and get place_id
		
		$url = $this->base_url . '/textsearch/json?';
		$url .= 'key=' . $this->key;
		$url .=	'&query=' . urlencode($name);
		$url .=	'&location=' . $latitude . ',' . $longitude;
		$url .= '&radius=1000';
	
		//$pos = $latitude . ',' . $longitude;
		/*$data = array(
			'key'=>$this->key,
			'query'=>$name,
			'location'=>$pos,
			'radius'=>'1000');
		$url .= http_build_query($data);*/
		
		Utility::debug('calling GooglePlaces textsearch method:' . $url,5);
		
		$response = "";
		try {
			$response = $this->queryGoogle($url);
		}
		catch(exception $e)
		{
			Utility::debug('Google Places API call error: ' . $e->getMessage(),3);
		}
		
		// next, use place_id to get details
		if (!isset($response['results'][0])) {
			// didn't get a hit
			Utility::debug('No Google Places entry found for : ' . $name,5);
			return "";
		}
		else {
			$placeid = $response['results'][0]['place_id'];
			$url = $this->base_url . '/details/json?';
			$url .= 'key=' . $this->key;
			$url .=	'&placeid=' . urlencode($placeid);
			
			Utility::debug('calling GooglePlaces details method:' . $url,5);
			
			$response = "none";
			try {
				$response = $this->queryGoogle($url);
			}
			catch(exception $e)
			{
				Utility::debug('Google Places API call error: ' . $e->getMessage(),3);
			}
			
			
			return $response;
		}
		
	}
	
	
	/* Submits request via curl, sets the response, then returns the response */
    private function queryGoogle($url) {

        $response = $this->get($url);
        $response = json_decode($response, true);
        if ($response === null)
            {
                throw new \Exception('The returned JSON was malformed or nonexistent.');
            }
        $this->response = $response;
        return $this->response;
    }
	
	private function get($url) {
        $curl = curl_init($url);
       	curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
        
		Utility::debug('executing url via cUrl . . .',1);
        $response = curl_exec($curl);
		Utility::debug('cUrl complete',1);
		Utility::debug('GooglePlaces called: response=' . $response,5);
        if ($error = curl_error($curl))
        {
        	Utility::debug('cUrl exception:' . $error,3);
            throw new \Exception('CURL Error: ' . $error);
        }
        curl_close($curl);
        return $response;
    }
}
