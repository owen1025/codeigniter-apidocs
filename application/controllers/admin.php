<?php

class Admin extends CI_Controller{
	const CONTROLLER_DIR = './application/controllers';

	public function __construct(){
		parent::__construct();

		$this->load->helper('url');
	}

	public function index(){
		$api_list = $this->_get_api_list();
		$first_controller = key($api_list);

		$view_data = array(
			'first_controller' => $first_controller,
			'api_list' => $api_list,
			'api_detail' => $this->get_api_detail($first_controller, false)
		);
		
		$this->load->view('admin.html', $view_data);
	}

	private function _get_controller_source($file_name){
		$controller_line = '';
		// Get your source in Controller (all contents)
		if ($fp = fopen(self::CONTROLLER_DIR . '/' . $file_name . '.php', "r")){
	    	while (!feof($fp)) {
	    	   $controller_line .= fgets($fp);
	    	}
	    	fclose($fp);
	    }
	    return $controller_line;
	}

	private function _private_method_check($method_name){
		preg_match('/^_/', $method_name, $private_check);
		/* Method name start a '_', It is a private method. */
		return count($private_check) ? true : false;
	}

	private function _get_api_list(){
		$controller_arr = array();

		/* Open your_project/application/controllers directory */
		if ($handle = opendir(self::CONTROLLER_DIR)) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != ".." && strpos($entry, ".php") && strpos(self::CONTROLLER_DIR . $entry, "admin.php") == false) {
					$controller_name = explode('.php', $entry)[0];
					$api_str = $this->_get_controller_source($controller_name);

					$controller_arr[$controller_name] = array();
					preg_match_all('/public function (?P<method_name>\w+)/', $api_str, $method_list);
					foreach($method_list['method_name'] as $method_value){
						/* Private method check */
						if (!$this->_private_method_check($method_value))
							array_push($controller_arr[$controller_name], $method_value);
					}
				}
			}
			closedir($handle);
		}
		
		return $controller_arr;
	}

	public function get_api_detail($file_name, $external_call_flag = true){
		$current_url = explode('/admin', current_url());

		$api_list = array(
			'base_url' => preg_replace('/admin$/', '', $current_url[0] . '/admin'),
			'item' => array()
		);

    	foreach(explode('function', $this->_get_controller_source($file_name)) as $api_str){
    		/* Get API Method name */
    		preg_match('/\s{0,}(?P<method_name>\w+)\s{0,}\((?P<url_parameter>.*)\)\s{0,}\{(?P<body>.*)\}/s', $api_str, $api_meta_data);
    		
    		if (count($api_meta_data)){
    			/* Private method check */
    			if ($this->_private_method_check($api_meta_data['method_name'])) continue;

    			/* Get API URL Parameter */
				$url_parameter_list = array();
				foreach (explode(',', preg_replace(array('/\$/', '/\s/'), array('', ''), $api_meta_data['url_parameter'])) as $url_parameter_str) {
					if ($url_parameter_str != '')
						array_push($url_parameter_list, $url_parameter_str);
				}
			 
			 	/* Get API Parameter */
				preg_match_all("/\\\$this\s{0,}->\s{0,}(?:input\s{0,}->){0,}\s{0,}(get|post|put|delete)\s{0,}\(\s{0,}'(\w+)\'\s{0,}\)/", $api_meta_data['body'], $parameter_list);

				/* Get API Headers */
				preg_match_all("/\\\$this\s{0,}->\s{0,}input\s{0,}->\s{0,}get_request_header\s{0,}\(\s{0,}\'(\w+)\'\s{0,}\)/", $api_meta_data['body'], $header_list);
			
    			$api_item = array(
    				'method_name' => $api_meta_data['method_name'],
    				'url_parameter' => $url_parameter_list,
    				'parameter' => count($parameter_list) ? $parameter_list[2] : null,
    				'call_type' => count($parameter_list) ? count($parameter_list[1]) ? $parameter_list[1][0] : 'GET' : 'GET',
    				'header' => $header_list[1]
    			);

    			array_push($api_list['item'], $api_item);
    		}
    	}
	    
	    if ($external_call_flag == true)
	    	echo json_encode($api_list);
	    else
	    	return $api_list;
	}
}

?>
















