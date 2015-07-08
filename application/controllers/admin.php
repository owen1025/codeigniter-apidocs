<?php

class Admin extends CI_Controller{
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
		            	$fr = fread($fp, filesize($dir . '/' . $entry));
						
		            	foreach (explode("function", $fr) as $value) {
		            		$function_arr = array();
		            		// echo $value . '</br>';

		            		// Get api method name
		            		preg_match('/(\w+)\s{0,}\(/', $value, $method_name, PREG_OFFSET_CAPTURE);
		            		$function_arr['method'] = $method_name[1][0];

		            		// Get api url parameter
		            		$function_arr['url_parameter'] = array();
							preg_match("/\((\s{0,}\\\$(\w+)[\s]{0,}\,{0,}[\s]{0,})+\)\{/", $value, $url_parameter, PREG_OFFSET_CAPTURE);
							if (count($url_parameter)){
								foreach (explode(',', $url_parameter[0][0]) as $url_parameter_name) {
									array_push($function_arr['url_parameter'], preg_replace('/\(|\)|\{|\$/', '', $url_parameter_name));
								}
							}

							$function_arr['parameter'] = array();
							preg_match('/(get|post|put|delete)$/', $function_arr['method'], $rest_check);
							// $parameter_pattern = sprintf('/\$this\s{0,}\-\>\s{0,}%s(get|post|put|delete)\s{0,}(\(\s{0,}\'\s{0,}(\w+)\s{0,}\'\s{0,}\))+/', !count($rest_check) ? 'input\s{0,}\-\>\s{0,}' : '');
							$parameter_pattern = sprintf('/(?:\$this\s{0,}\-\>\s{0,}%s(?:get|post|put|delete)\s{0,}\(\s{0,}\'(\w+)\'\s{0,}\))/', !count($rest_check) ? 'input\s{0,}\-\>\s{0,}' : '');
							preg_match_all($parameter_pattern, $value, $parameter);
							if(count($parameter)){
								foreach ($parameter[1] as $parameter_value) {
									// echo $parameter_value . '</br>';		
									array_push($function_arr['parameter'], $parameter_value);
								}
							}
							// print_r($parameter);
							// echo $value . '</br>';
							// echo $parameter_pattern . '</br>';

		            		array_push($controller_list, $function_arr);
		            	}

		            	fclose($fp);
		            }
		        }
		    }
		    closedir($handle);

		    echo json_encode($controller_list);
		}
	}

	public function test(){
		$this->_get_api_list ();
	}
}

?>