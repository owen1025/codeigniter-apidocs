<?php

class Admin extends CI_Controller{
	const CONTROLLER_DIR = './application/controllers';

	public function __construct(){
		parent::__construct();
	}

	public function index(){
		$this->load->view('admin.html');
	}

	public function get_api_list(){
		$file_arr = array();

		/* Open your_project/application/controllers directory */
		if ($handle = opendir(self::CONTROLLER_DIR)) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != ".." && strpos($entry, ".php") && strpos(self::CONTROLLER_DIR . $entry, "admin.php") == false) {
					if ($fp = fopen(self::CONTROLLER_DIR . '/' . $entry, "r")){
						/* Get your_controller/api Method name */
						$api_str = fread($fp, filesize(self::CONTROLLER_DIR . '/' . $entry));
						preg_match_all('/public function (?P<method_name>\w+)/', $api_str, $method_list);
						$file_arr[str_replace('.php', '', $entry)] = $method_list['method_name'];
					}
				}
			}
			closedir($handle);
		}

		echo json_encode($file_arr);
	}

	public function get_api_detail($file_name){
		$api_list = array();

	    if ($fp = fopen(self::CONTROLLER_DIR . '/' . $file_name . '.php', "r")){
	    	foreach(explode('function', fread($fp, filesize(self::CONTROLLER_DIR . '/' . $file_name . '.php'))) as $api_str){
	    		/* Get API Method name */
	    		preg_match('/\s{0,}(?P<method_name>\w+)\s{0,}\((?P<url_parameter>.*)\)\s{0,}\{(?P<body>.*)\}/s', $api_str, $api_meta_data);
	    		
	    		if (count($api_meta_data)){
	    			/* Get API URL Parameter */
					$url_parameter_list = array();
					foreach (explode(',', preg_replace('/\$/', '', $api_meta_data['url_parameter'])) as $url_parameter_str) {
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
	    				'call_type' => count($parameter_list) ? count($parameter_list[1]) ? $parameter_list[1][0] : null : null,
	    				'header' => $header_list[1]
	    			);
	    			if (count($api_item['parameter'])){
	    				array_push($api_list, $api_item);
	    			}
	    		}
	    	}
	    	fclose($fp);
	    }
	    
	    echo json_encode($api_list);
	}
}

?>
















