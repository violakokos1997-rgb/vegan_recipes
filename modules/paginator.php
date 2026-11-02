<?php 
require_once $base_path . 'modules/helpers.php';

class Paginator implements \Iterator{
	public $current_page = 1;
	public $page_count = 1;
	public $first_record_num = 1;

	public $existing_params;
	private $cur = 1;

	function __construct(int $record_count, array $existing_params =[]) {
		
		
		$this->page_count = ceil($record_count /\Settings\RECORDS_ON_PAGE);

		if($this->page_count < 1){
			$this->page_count = 1;
			$page_num = 1;
		}

		$page_num = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
		if($page_num < 1)
			$page_num = 1;
		if($page_num > $this->page_count)
			$page_num = $this->page_count;
		$this->current_page = $page_num;
		$this->first_record_num = ($page_num - 1) * \Settings\RECORDS_ON_PAGE;
		$this->existing_params = $existing_params;
		
	}
	function current(){
		return \Helpers\get_GET_params($this->existing_params, ['page' => $this-> cur]);
	}
	function key(){
		return $this->cur;
	}
	function next(){
		$this->cur++;
	}
	function rewind(){
		$this->cur = 1;
	}
	function valid(){
		return $this->cur >= 1 && $this->cur <= $this->page_count;
	}
}