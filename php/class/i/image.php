<?php

/*

alter table user_bookmarks change thing_id thing_id bigint not null;
alter table user_bookmarks change thing thing enum('thread','wiki','news','i_set','i_image') not null;

protected $attrSources = [
	'class' => 'i__source',
	'table' => 'i_image_sources',
	'content_table' => 'i_sources',
	'content_id_field' => 'source_id'
];
drop table if exists i_logs;
create table i_logs (
	log_id int unsigned not null primary key auto_increment,
	t varchar(15) not null,
	content_id bigint not null,
	attr_id bigint not null,
	user_id int unsigned not null,
	action enum('add', 'remove') not null,
	args text not null,
	ctime timestamp not null default current_timestamp
);

drop table if exists i_images;
create table i_images (
	image_id bigint not null primary key,
	ext varchar(5) not null,
	size int unsigned not null,
	width int unsigned not null,
	height int unsigned not null,
	has_large tinyint(1) not null default 0,
	has_default tinyint(1) not null default 0,
	has_medium tinyint(1) not null default 0,
	has_thumb tinyint(1) not null default 0,
	has_mini tinyint(1) not null default 0,
	has_icon tinyint(1) not null default 0,
	status enum ('ok', 'blacklisted') not null default 'ok',
	hits bigint not null default 0,
	ctime timestamp not null default current_timestamp
);


create table i_tags (
	tag_id bigint not null primary key,
	name varchar(100) not null,
	ctime timestamp not null default current_timestamp
);


create table i_image_tags (
	image_id bigint not null,
	tag_id bigint not null,
	primary key (image_id, tag_id),
	user_id int unsigned not null,
	ctime timestamp not null default current_timestamp
);


create table i_sets (
	set_id bigint not null primary key,
	user_id int unsigned not null, key user_id (user_id),
	name varchar(100) not null,
	content varchar(2000) not null
	ctime timestamp not null default current_timestamp
);

drop table if exists i_set_images;
create table i_set_images (
	set_id bigint not null,
	image_id bigint not null,
	primary key (set_id, image_id),
	user_id int unsigned not null,
	content varchar(2000) not null,
	ctime timestamp not null default current_timestamp
);

create table i_set_tags (
	set_id bigint not null,
	tag_id bigint not null,
	primary key (set_id, tag_id),
	user_id int unsigned not null,
	ctime timestamp not null default current_timestamp
);


drop table if exists i_sources;
create table i_sources (
	source_id bigint not null primary key,
	url varchar(1000) not null,
	name varchar(500) not null,
	ctime timestamp not null default current_timestamp
);

create table i_image_sources (
	image_id bigint not null,
	source_id bigint not null,
	primary key (image_id, source_id),
	user_id int unsigned not null,
	ctime timestamp not null default current_timestamp
);


create table i_image_comments (
	comment_id bigint not null primary key auto_increment,
	image_id bigint not null, key image_id (image_id),
	ctime timestamp not null default current_timestamp,
	user_id int unsigned not null,
	message varchar(4000) not null
);

create table i_set_comments (
	comment_id bigint not null primary key auto_increment,
	set_id bigint not null, key set_id (set_id),
	ctime timestamp not null default current_timestamp,
	user_id int unsigned not null,
	message varchar(4000) not null
);
*/


class i__image extends ArrayClass2 {
	protected $table = 'i_images';
	protected $id_field = 'image_id';

	private $tags = null;
	private $sources = null;
	private $comments = null;
	private $sets = null;
	
	private static $dir_images = 's/img/i';
	private static $dir_thumbs = 's/img/t';

	private static $sizes = [
		'large' => [1500, 1500],
		'default' => [1024, 1024],
		'medium' => [600, 600],
		'thumb' => [240, 240],
		'mini' => [120, 120],
		'icon' => [60, 60],
	];

	public function __construct($data = NULL) {
		$this->sql_set_fields = ['status'] + array_map(function($k) { return 'has_'.$k; }, array_keys(self::$sizes));
		parent::__construct($data);

		$this->ap = self::initAP($this->id, null);
	}
	
	
	public static function initAP($item_id = null, $owner = null, $permission = null) {
		$ap = new AccessPolicy('i_images', $item_id, $owner, $permission);
		$ap->isAdminCallback = function() { return has_privilege('forum_admin'); };
		$ap->isModCallback = function() { return has_privilege('forum_mod'); };
		#$ap->users = ACCESS_POLICY_MOD | ACCESS_POLICY_BANNED;
		#$ap->groups = ACCESS_POLICY_MOD | ACCESS_POLICY_BANNED;
		$ap->owner_privilege = ACCESS_POLICY_ADMIN;
		$ap->default_privilege = (IS_LOGGED_IN ? ACCESS_POLICY_WRITE : ACCESS_POLICY_READ);
		return $ap;
	}


	public function offsetGet($k) {
		return parent::offsetGet($k);
	}

	public function offsetSet($k, $v) {
		return parent::offsetSet($k, $v);
	}

	public static function insert(&$source, &$data, $ext) {
		$id = i__i::hash($data);

		$dir = substr($id, -3);
		while(strlen($dir) < 3) $dir .= '0';

		$dir = CONFIG_DIRNAME.'/../'.self::$dir_images.'/'.self::_dir($id, $ext);

		if(!file_exists(dirname($dir))) {
			mkdir(dirname($dir), 0777, true);
		}
		file_put_contents($dir, $data);

		list($width, $height) = getimagesize($dir);
		if(!$width or !$height) {
			if(file_exists($dir)) unlink($dir);
			throw new Exception('Save failed');
		}

		db()->query("INSERT IGNORE INTO i_images SET image_id='".$id."', ext='".es($ext)."', width='$width', height='$height', size='".strlen($data)."'");

		$image = new self($id);
		$image->addSource($source);
		return $image;
	}


	public static function handleUpload(&$args) {
		if(!empty($args['url'])) {
			if(preg_match('~([^\?&=/]+\.(jpe?g|gif|png|bmp))([\?#].*)?$~i', $args['url'], $out)) $name = $out[1];
			else $name = 'noname.jpg';

			$source = i__source::insert($args['url'], $name);

			$data = file_get_contents($source->url);
		}
		elseif(!empty($args['file'])) {
			$name = preg_replace('~^.*[/\\\]~', '', $args['file']['name']);
			$source = i__source::insert('file://'.$args['file']['name'], $name);

			$ext = strtolower(substr(strtolower(strrchr($source->name, '.')), 1));
			if($ext != 'jpg' and $ext != 'gif' and $ext != 'png') $ext = 'jpg';

			$data = file_get_contents($args['file']['tmp_name']);
		}
		else {
			return null;
		}

		$ext = strtolower(substr(strtolower(strrchr($source->name, '.')), 1));
		if($ext != 'jpg' and $ext != 'gif' and $ext != 'png') $ext = 'jpg';

		try {
			if(!$data) {
				throw new Exception('Download failed');
			}
			$image = self::insert($source, $data, $ext);
		}
		catch(Exception $e) {
			$source->deleteIfEmpty();
			return $e->getMessage();
		}

		i__tag::insert_string($args['tags'], [[$image, [null]]]);

		return $image;
	}






	private static function _dir($image_id, $ext, $postfix = '') {
		$dir = substr($image_id, -3);
		while(strlen($dir) < 3) $dir .= '0';
		return $dir.'/'.$image_id.($postfix ? '_'.$postfix : '').'.'.$ext;
	}
	public function dir() {
		return self::_dir($this->image_id, $this->ext);
	}

	public function imageDir() {
		return self::$dir_images.'/'.self::_dir($this->image_id, $this->ext);
	}
	public function thumbDir($size_name) {
		return self::$dir_thumbs.'/'.self::_dir($this->image_id, 'jpg', $size_name);
	}

	public function getDisplayLink($size_name = 'default') {
		return 'http://'.SITE_DOMAIN.'/'.$this->getDisplayLink_($size_name);
	}
	public function getDisplayLink_($size_name = 'default') {
		$cache_id = 'class/image/'.$this->id.'/'.$size_name.'/'.$size_name;
		if(($link = cache_L1::get($cache_id)) !== false) {
			return $link;
		}
		$path = '';
		if($size_name) {
			$size = self::$sizes[$size_name];
			if($this->width > $size[0] or $this->height > $size[1]) {
				if(!$this->hasThumb($size_name)) {
					$this->createThumb($size_name);
				}
				$path = $this->thumbDir($size_name);
			}
		}
		if(!$path) $path = $this->imageDir();
		cache_L1::set('class/image/'.$this->id.'/'.$size_name.'/'.$size_name, 60, $path);
		return $path;
	}


	public function getLink() {
		return '/'.LANG.'/i/image/'.$this->id.'/';
	}
	public function getLinkHtml() {
		return '<a href="'.htmlspecialchars($this->getLink()).'">'.htmlspecialchars($this->id).'</a>';
	}



	protected $attrSources = [
		'class' => 'i__source',
		'table' => 'i_image_sources',
		'content_table' => 'i_sources',
		'content_id_field' => 'source_id',
		'log' => true
	];
	public function addSource($attr) {
		if(!$this->ap->allowWrite()) throw new Exception('403');
		return parent::addAttr($this->sources, $this->attrSources, $attr, ['user_id' => IS_LOGGED_IN ? USER_ID : 0]);
	}
	public function removeSource($attr) {
		if(!$this->ap->isAdmin()) throw new Exception('403');
		return parent::removeAttr($this->sources, $this->attrSources, $attr);
	}
	public function getSources() {
		return parent::getAttrs($this->sources, $this->attrSources, 'ctime');
	}
	public function countSources() {
		return parent::countAttrs($this->sources, $this->attrSources);
	}

	protected $attrTags = [
		'class' => 'i__tag',
		'table' => 'i_image_tags',
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
	public function getTags() {
		return parent::getAttrs($this->tags, $this->attrTags, 'name');
	}
	public function countTags() {
		return parent::countAttrs($this->tags, $this->attrTags);
	}

	public function renderTags() {
		return i__tag::renderTagList($this->getTags(), 'i', $this->getLink(), $this->ap->allowWrite(), $this->ap->isMod());
	}

	protected $attrSets = [
		'class' => 'i__set',
		'table' => 'i_set_images',
		'content_table' => 'i_sets',
		'content_id_field' => 'set_id',
		'log' => true
	];
	public function getSets() {
		return parent::getAttrs($this->sets, $this->attrSets, 'b.ctime', 'b.*, a.ctime, a.user_id, a.content');
	}

	protected $attrComments = [
		'class' => 'i__comment',
		'table' => 'i_image_comments',
		'content_table' => 'i_comments',
		'content_id_field' => 'comment_id'
	];
	public function countComments() {
		return parent::countAttrs($this->comments, $this->attrComments);
	}



	public function createThumb($size_name, $quality = 80) {
		if(!$size_name) return;

		$size = self::$sizes[$size_name];
		$max_w = $size[0];
		$max_h = $size[1];

		$in = CONFIG_DIRNAME.'/../'.$this->imageDir();
		$out = CONFIG_DIRNAME.'/../'.$this->thumbDir($size_name);

		if(!file_exists(dirname($out))) {
			mkdir(dirname($out), 0777, true);
		}

		list($w, $h) = getimagesize($in);

		if($w < $max_w and $h < $max_h)
			return;

		switch($this->ext) {
		default: $src = imagecreatefromjpeg($in); break;
		case 'gif'; $src = imagecreatefromgif($in); break;
		case 'png': $src = imagecreatefrompng($in); break;
		case 'bmp': $src = imagecreatefrombmp($in); break;
		}

		if(!$src) {
			trigger_error('Image('.$this->id.'): createThumb failed', E_USER_NOTICE);
			return true;
		}

		$nw = $w;
		$nh = $h;
		if($nh > $max_h) {
			$nw = ($w/$h)*$max_h;
			$nh = $max_h;
		}
		if($nw > $max_w) {//width is more important
			$nw = $max_w;
			$nh = ($h/$w)*$max_w;
		}
		$temp = imagecreatetruecolor($nw, $nh);
		imagecopyresampled($temp, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
		imagejpeg($temp, $out, $quality);
		imagedestroy($src);
		imagedestroy($temp);

		$this->setHasThumb($size_name, true);
	}
	public function deleteThumb($size_name) {
		if(file_exists(CONFIG_DIRNAME.'/'.$this->thumbDir($size_name))) {
			unlink(CONFIG_DIRNAME.'/'.$this->thumbDir($size_name));
		}
		$this->setHasThumb($size_name, false);
	}

	public function hasThumb($size_name) {
		$var = 'has_'.$size_name;
		return $this->$var;
	}
	public function setHasThumb($size_name, $has) {
		$var = 'has_'.$size_name;
		$this->$var = $has;
	}
}

?>
