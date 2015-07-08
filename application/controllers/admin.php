<?php

class Admin extends CI_Controller{
	const CONTROLLER_DIR = './application/controllers';

	public function __construct(){
		parent::__construct();
	}

	public function index(){
		$this->load->view('admin.html');
	}

	private function _get_api_list(){
		$controller_list = array();
		$dir = './application/controllers';

		// Open cotroller directory
		if ($handle = opendir($dir)) {
		    while (false !== ($entry = readdir($handle))) {
		        if ($entry != "." && $entry != ".." && strpos($entry, ".php") && strpos($dir . $entry, "admin.php") == false) {

		            if ($fp = fopen($dir . '/' . $entry, "r")){
		            	$api_list = array();

		            	foreach(explode('function', fread($fp, filesize($dir . '/' . $entry))) as $api_str){
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
            				
		            			$api_item = array(
		            				'method_name' => $api_meta_data['method_name'],
		            				'url_parameter' => $url_parameter_list,
		            				'parameter' => count($parameter_list) ? $parameter_list[2] : null,
		            				'call_type' => count($parameter_list) ? count($parameter_list[1]) ? $parameter_list[1][0] : null : null
		            			);
		            			if (count($api_item['parameter'])){
		            				array_push($api_list, $api_item);
		            			}
		            		}
		            	}
		            	fclose($fp);
		            }
		        }
		    }
		    echo json_encode($api_list);
		    closedir($handle);
		}
	}

	public function get_file_list(){
		$file_arr = array();

		if ($handle = opendir(self::CONTROLLER_DIR)) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != ".." && strpos($entry, ".php") && strpos(self::CONTROLLER_DIR . $entry, "admin.php") == false) {
					array_push($file_arr, str_replace('.php', '', $entry));
				}
			}
		}

		echo json_encode($file_arr);
	}
}

?>
















