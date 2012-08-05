<?php

class i__set extends ArrayClass2 {
	protected $table = 'i_sets';
	protected $id_field = 'set_id';

	private $images = null;
	private $comments = null;
	private $tags = null;
	private $allTags = null;

	private $firstImage = null;

	private static $writeableSets = null;

	public function __construct($data = NULL) {
		$this->sql_set_fields = ['name', 'content', 'atime'];
		parent::__construct($data);

		$this->ap = self::initAP($this->id, $this->user_id);
	}

	public static function initAP($item_id = null, $owner = null, $permission = null) {
		$ap = new AccessPolicy('i_sets', $item_id, $owner, $permission);
		$ap->isAdminCallback = function() { return has_privilege('forum_admin'); };
		$ap->isModCallback = function() { return has_privilege('forum_mod'); };
		#$ap->users = ACCESS_POLICY_MOD | ACCESS_POLICY_BANNED;
		#$ap->groups = ACCESS_POLICY_MOD | ACCESS_POLICY_BANNED;
		$ap->owner_privilege = ACCESS_POLICY_ADMIN;
		$ap->default_privilege = (IS_LOGGED_IN ? ACCESS_POLICY_WRITE : ACCESS_POLICY_READ);
		return $ap;
	}

	public static function insert($name, $content) {
		$user_id = (IS_LOGGED_IN ? USER_ID : 0);
		$id = i__i::hash($name);
		try {
			$set = new self($id);
			throw new Exception('set exists');
		}
		catch(Exception $e) {
		}
		db()->query("INSERT IGNORE INTO i_sets SET set_id='".$id."', name='".es($name)."', content='".es($content)."', user_id='$user_id'");
		return new self($id);
	}

	public static function getWriteableSets() {
		if(self::$writeableSets === null) {
			self::$writeableSets = [];
			$user_id = (IS_LOGGED_IN ? USER_ID : 0);
			$aa = db()->query("SELECT * FROM i_sets WHERE ((".self::initAP()->query('set_id', 'user_id').") & ".ACCESS_POLICY_MOD.") = ".ACCESS_POLICY_MOD);
			while($a = $aa->fetch_assoc()) {
				self::$writeableSets[] = new self($a);
			}
		}
		return self::$writeableSets;
	}

	protected $attrTags = [
		'class' => 'i__tag',
		'table' => 'i_set_tags',
		'content_table' => 'i_tags',
		'content_id_field' => 'tag_id',
		'log' => true
	];
	public function addTag($attr) {
		if(!$this->ap->allowWrite()) throw new Exception('403');
		return parent::addAttr($this->tags, $this->attrTags, $attr, ['user_id' => IS_LOGGED_IN ? USER_ID : 0]);
	}
	public function removeTag($attr) {
		if(!$this->ap->isMod()) throw new Exception('403');
		return parent::removeAttr($this->tags, $this->attrTags, $attr);
	}
	public function removeAllTags($where = []) {
		if(!$this->ap->isAdmin()) throw new Exception('403');
		return parent::removeAllAttrs($this->tags, $this->attrTags, $where);
	}
	public function getTags() {
		return parent::getAttrs($this->tags, $this->attrTags, 'name');
	}
	public function countTags() {
		return parent::countAttrs($this->tags, $this->attrTags);
	}

	public function renderTags() {
		return i__tag::renderTagList($this->getTags(), 'i', $this->getLink(), $this->ap->allowWrite(), $this->ap->isMod());
	}

	protected $attrComments = [
		'class' => 'i__comment',
		'table' => 'i_set_comments',
		'content_table' => 'i_comments',
		'content_id_field' => 'comment_id',
		'log' => true
	];
	public function countComments() {
		return parent::countAttrs($this->comments, $this->attrComments);
	}
	public function removeAllComments() {
		if(!$this->ap->isAdmin()) throw new Exception('403');
		$null = null;
		return parent::removeAllAttrs($null, $this->attrComments, [], true);
	}

	protected $attrImages = [
		'class' => 'i__image',
		'table' => 'i_set_images',
		'content_table' => 'i_images',
		'content_id_field' => 'image_id',
		'log' => true
	];
	public function addImage($image, $content) {
		if(!$this->ap->isMod()) throw new Exception('403');
		return parent::addAttr($this->images, $this->attrImages, $image, ['user_id' => IS_LOGGED_IN ? USER_ID : 0, 'content' => $content]);
	}
	public function removeImage($image) {
		if(!$this->ap->isMod()) throw new Exception('403');
		return parent::removeAttr($this->images, $this->attrImages, $image);
	}
	public function removeAllImages() {
		if(!$this->ap->isAdmin()) throw new Exception('403');
		return parent::removeAllAttrs($this->images, $this->attrImages);
	}
	public function getImages() {
		return parent::getAttrs($this->images, $this->attrImages, 'a.ctime DESC', 'b.*, a.content, a.user_id');
	}
	public function countImages() {
		return parent::countAttrs($this->images, $this->attrImages);
	}

	public function getAllTags() {
		if($this->allTags === null) {
			$this->allTags = $this->getTags();
			foreach($this->getImages() as $image) {
				$this->allTags += $image->getTags();
			}
		}
		return $this->allTags;
	}

	public function removeAllBookmarks() {
		$aa = db()->query("SELECT * FROM user_bookmarks WHERE thing='i_set' AND thing_id='".$this->id."'");
		while($a = $aa->fetch_assoc()) {
			unset($a['thing_id']);
			$this->logAttrAction('user_bookmarks', ['id' => 0], 'remove', $a);
		}
		db()->query("DELETE FROM user_bookmarks WHERE thing='i_set' AND thing_id='".$this->id."'");
	}

	public function remove() {
		if(!$this->ap->isAdmin()) throw new Exception('403');
		
		$this->removeAllImages();
		$this->removeAllTags();
		$this->removeAllComments();
		$this->removeAllBookmarks();

		$a = db()->query("SELECT * FROM i_sets WHERE set_id='".$this->id."' LIMIT 1")->fetch_assoc();
		unset($a['set_id']);
		$this->logAttrAction('i_sets', $this, 'remove', $a);
		db()->query("DELETE FROM i_sets WHERE set_id='".$this->id."' LIMIT 1");
	}

	public function getLink() {
		return '/'.LANG.'/i/set/'.$this->id.'-'.urlenc($this->name).'/';
	}
	public function getLinkHtml() {
		return '<a href="'.htmlspecialchars($this->getLink()).'">'.htmlspecialchars($this->name).'</a>';
	}

	public function getFirstImage() {
		if($this->firstImage === null) {
			$this->firstImage = db()->query("
				SELECT b.*
				FROM i_set_images a
				JOIN i_images b USING (image_id)
				WHERE a.set_id='".$this->id."'
				ORDER BY a.ctime ASC
				LIMIT 1")->fetch_assoc();
			if($this->firstImage) $this->firstImage = new i__image($this->firstImage);
			else $this->firstImage = false;
		}
		return $this->firstImage;
	}
	public function renderFirstImage($size_name = 'thumb', $with_tags = false) { //$with_tags = false|(own|true)|all
		$data = '<a href="'.$this->getLink().'"';
		if($with_tags) {
			$tags = array_map_key('name', $with_tags === 'all' ? $this->getAllTags() : $this->getTags());
			sort($tags);
			$data .= ' title="Tags: '.htmlspecialchars(implode(', ', $tags)).'"';
		}
		$data .= '>';
		$image = $this->getFirstImage();
		if($image) $data .= '<img src="'.$image->getDisplayLink($size_name).'" />';
		$data .= '</a>';
		return $data;
	}
}

?>
