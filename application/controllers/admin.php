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
		// echo json_encode($view_data);
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

	private function _get_api_list(){
		$controller_arr = array();

		/* Open your_project/application/controllers directory */
		if ($handle = opendir(self::CONTROLLER_DIR)) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != ".." && strpos($entry, ".php") && strpos(self::CONTROLLER_DIR . $entry, "admin.php") == false) {
					$controller_name = explode('.php', $entry)[0];
					$api_str = $this->_get_controller_source($controller_name);

					$controller_arr[$controller_name] = array();
					
					preg_match_all('/function (?P<method_name>[^_]\w+)\s{0,}\(/', $api_str, $method_list);
					foreach($method_list['method_name'] as $method_value){
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
			$api_str = 'function' . $api_str;
			preg_match('/function (?P<method_name>[^_]\w+)/', $api_str, $api_method);
			
			if (count($api_method)){
				/* Get API URL Parameter */
				$api_split_str = explode('{', $api_str);
				preg_match_all('/\$(\w+)/', $api_split_str[0], $api_url_parameter);
				
				/* Get API Parameter(Form data) */
				preg_match_all('/\$this\s{0,}->\s{0,}(?:input\s{0,}->){0,}\s{0,}(get|post|put|delete)\s{0,}\(\s{0,}\'(\w+)\'\s{0,}\)/', $api_str, $api_paramter);

				/* Get API Custom header */
				preg_match_all('/\$this\s{0,}->\s{0,}input\s{0,}->\s{0,}get_request_header\s{0,}\(\s{0,}\'(\S+)\'\s{0,}\,{0,}/', $api_str, $api_header);

				array_push($api_list['item'], array(
					'method_name' => preg_replace('/_(get|post|put|delete)/', '', $api_method['method_name']),
					'url_parameter' => $api_url_parameter[1],
					'parameter' => $api_paramter[2],
					'header' => $api_header[1],
					'call_type' => count($api_paramter[1]) ? strtoupper($api_paramter[1][0]) : 'GET'
				));
			}
		}

		if ($external_call_flag == true)
	    	echo json_encode($api_list);
	    else
	    	return $api_list;
	}
}

?>
















