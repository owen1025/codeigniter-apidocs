<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Docs extends CI_Controller{
	const CONTROLLER_DIR = './application/controllers';

	public function __construct(){
		parent::__construct();

		$this->load->helper('url');
	}

	public function index(){
		// redirect docs/view
    	echo 	'<script>
					window.location.href = "' . current_url() . '/view"
    		  	</script>';
	}

	public function view($controller_name = null){
		$view_data = array(
			'base_url' => preg_replace('/docs$/', '', explode('/docs', current_url())[0] . '/docs'),
			'api_list' => $this->_get_api_list(),
		);
		$view_data['active_controller'] = $controller_name != null ? $controller_name : key($view_data['api_list']);
		$view_data['api_detail'] = $this->_get_api_detail($view_data['active_controller']);
		// print_r($view_data);
		
		$this->load->view('docs.html', $view_data);
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
				if ($entry != "." && $entry != ".." && strpos($entry, ".php") && !preg_match('/^(?:docs.php)$/i', $entry) && 
					!preg_match('/^(?:welcome.php)$/i', $entry)) {
					
					$controller_name = explode('.php', $entry);
					$controller_name = $controller_name[0];
					$api_str = $this->_get_controller_source($controller_name);

					$controller_arr[$controller_name] = array();
					
					preg_match_all('/function (?P<method_name>[^_]\w+)\s{0,}\(/', $api_str, $method_list);
					foreach($method_list['method_name'] as $method_value){
						array_push($controller_arr[$controller_name], preg_replace('/_(get|post|put|delete)$/', '', $method_value));
					}
				}
			}
			closedir($handle);
		}
		
		return $controller_arr;
	}

	private function _get_api_detail($file_name){
		$current_url = explode('/docs', current_url());
		$api_list = array();

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

				/* Get API Description */
				preg_match('/\/\*{1,}\s{0,}@description(?P<description>.*)\*\//si', $api_str, $api_description);

				/* Check use Codeigniter_restserver */ 
				$rest_method_pattern = '/_(get|post|put|delete)$/';
				$api_method_name = $api_method['method_name'];
				$call_type = null;
				if (preg_match($rest_method_pattern, $api_method['method_name'], $rest_method_parameter)){
					$api_method_name = preg_replace($rest_method_pattern, '', $api_method['method_name']);
					$call_type = strtoupper($rest_method_parameter[1]);
				}
				else{
					$call_type = count($api_paramter[1]) ? strtoupper($api_paramter[1][0]) : 'GET';
				}

				array_push($api_list, array(
					'method_name' => $api_method_name,
					'url_parameter' => $api_url_parameter[1],
					'parameter' => $api_paramter[2],
					'header' => $api_header[1],
					'call_type' => $call_type,
					'description' => count($api_description) ? $api_description['description'] : ''
				));
			}
		}

	    return $api_list;
	}
}

?>
















