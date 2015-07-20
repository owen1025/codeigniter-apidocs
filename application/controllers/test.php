<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller{
	private function _test1(){
		echo 123;
	}

	public function index(){
		echo 123;
	}

	public function series($test, $t_dk){
		echo json_encode(array(
			'test' => $test,
			't_dk' => $t_dk,
			'get_test' => $this->input->get('get_test')
		));
	}

	private function _test2(){
		echo 234;
	}

	public function series_list(){
		echo json_encode(array(
			'test' => $this->input->post('test'),
			'test2' => $this->input->post('test2'),
			'header_test1' => $this-> input -> get_request_header( 'header_test1'),
			'header_test2' => $this-> input -> get_request_header( 'header_test2'),
		));
	}

	public function test3 ( $asfdlkj, $adfs){
		echo $this->get('1234');
		echo $this-> input -> get_request_header( 'header_test3');
		echo $this -> input -> get('test');
	}
}

?>
