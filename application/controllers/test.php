<?php

class Test extends CI_Controller{
	function __construct(){
		parent::__construct();
	}

	function dafslkj($t1, $t2){
		echo $this->input->get('dasf');
	}

	function adsf( $t1, $t2,$t3 ){
		echo $this->input->post ( 'dasf');
	}

	function adfskl(){
		if (true){
			echo $this-> input->post('adflkj');
		}
	}
}

?>