<?php

class m_image extends imodule {
	use ilphp_trait;
	use im_way;

	public $id = NULL;
	public $action = NULL;
	public $error = NULL;

	public function __construct() {
		parent::__construct(__DIR__);
	}

	protected function INIT(&$args) {
		if(!IS_LOGGED_IN) throw new Exception('ACCESS_DENIED');

		$this->url = '/'.LANG.'/image/';
		$this->way[] = [LS('Bilder'), $this->url];

		$this->id = $args[$this->imodule_name];
		switch($this->id) {
		default:
			$this->action = 'view';
			break;
		case '':
			page_redir($this->url.'upload/');
		case 'upload':
			$this->action = 'upload';
			break;
		}
	}

	protected function POST_upload(&$args) {
		if(!empty($args['url'])) {
			$id = image::download($args['url'], false, $this->error);
			if($id and !$this->error)
				page_redir('/'.LANG.'/image/'.$id.'/');
		}
		elseif(!empty($args['file'])) {
			$id = image::insert(file_get_contents($args['file']['tmp_name']), $args['file']['name'], false, $this->error);
			if($id and !$this->error)
				page_redir('/'.LANG.'/image/'.$id.'/');
		}
		return IS_AJAX ? $this->RUN('MODULE') : NULL;
	}

	protected function MODULE(&$args) {
		switch($this->action) {
		case 'view':
			$this->image = db()->query("SELECT * FROM images WHERE id='".es($this->id)."' LIMIT 1")->fetch_assoc();
			if(!$this->image) throw new Exception('404');
			$this->url .= $this->id.'/';
			$this->way[] = [$this->image['name'].'.'.$this->image['ext'], $this->url];
			break;
		case 'upload':
			$this->url .= 'upload/';
			$this->way[] = [LS('Upload'), $this->url];
			break;
		}
		$this->im_way_title();
		return $this->ilphp_fetch('image.php.'.$this->action.'.ilp');
	}
}

?>
