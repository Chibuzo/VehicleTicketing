<?php
class ApiCaller
{
	//some variables for the object
	private $_app_id;
	private $_app_key;
	private $_api_url;
	
	
	public function __construct($app_id, $app_key, $api_url)
	{
		$this->_app_id = $app_id;
		$this->_app_key = $app_key;
		$this->_api_url = $api_url;
	}
	

	public function sendRequest($request_params)
	{
		//encrypt the request parameters
		$enc_request = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->_app_key, json_encode($request_params), MCRYPT_MODE_ECB));
		
		$params = array();
		$params['enc_request'] = $enc_request;
		$params['app_id'] = $this->_app_id;
		
		//initialize and setup the curl handler
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->_api_url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		//execute the request
		$result = curl_exec($ch);

		//json_decode the result
		$result = json_decode($result);
		//var_dump($result);
		
		//check if we're able to json_decode the result correctly
		if( $result == false || isset($result->success) == false ) {
			throw new Exception('Request was not correct');
		}
		
		//if there was an error in the request, throw an exception
		if( $result->success == false ) {
			throw new Exception($result->errormsg);
		}
		
		//if everything went great, return the data
		return $result->data;
	}
}