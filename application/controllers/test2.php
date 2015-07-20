<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Program extends REST_Controller{
	private function test1(){
		echo 123;
	}

	public function index_get(){
		$this->load->view('program.html');
	}

	public function series_get($test, $t_dk){
		$this->response($this->series->get($this->get('series_id')));
	}

	private function test2(){
		echo 234;
	}

	public function series_list($asfd, $adfljk_123, $_adfs){
		$this->response($this->series->contents_list($this->get('series_id'),
			$this->input->get('order')));
	}

	public function test3 ( $asfdlkj, $adfs){
		echo $this->get('1234');
		echo $this -> input -> get('test');
	}
}

?>
