<?php

class m_radio extends imodule {
	use ilphp_trait;
	use im_way;
	
	public function __construct() {
		parent::__construct(__DIR__);
		
		if(!isset(session::$s['m_radio']))
			session::$s['m_radio'] = RADIO_DEFAULT_CHANNEL;
	}
	
	protected function ENGINE(&$args) {
		try {
			$this->url = '/'.LANG.'/radio/';
			$this->way[] = [LS('Radio'), $this->url];
			
			$channel = es($args[$this->imodule_name]);
			
			if(!$channel and session::$s['m_radio']) page_redir($this->url.session::$s['m_radio'].'/');
			if($channel) $this->channel = db()->query("SELECT * FROM radio WHERE channel='".$channel."' LIMIT 1")->fetch_assoc();
			if(!$channel or !$this->channel) {
				if($channel != RADIO_DEFAULT_CHANNEL) {
					session::$s['m_radio'] = RADIO_DEFAULT_CHANNEL;
					page_redir($this->url.session::$s['m_radio'].'/');
				}
				throw new iexception('404', $this);
			}
			session::$s['m_radio'] = $this->channel['channel'];
			
			if(@$args['playlist'])
				$this->playlist($args);
			
			$this->url .= $this->channel['channel'].'/';
			$this->way[] = [$this->channel['channel'], $this->url];
			
			$this->im_way_title();
			
			//default page init
			theme::init($this);
			$this->LANG_TIME =& G::$LANG_TIME;
			
			$this->SITE_TITLE = stripslashes(implode(utf8_encode(' « '), array_reverse(array_merge(array(SITE_NAME), G::$SITE_TITLE))));
			$this->META_KEYWORDS =& G::$META_KEYWORDS;
			$this->META_DESCRIPTION =& G::$META_DESCRIPTION;
			//default page init
			
			$this->imodule_args['page']['state'] = crc32(print_r($this->channel, true));
			
			return $this->ilphp_display('radio.php.page.ilp');
		}
		catch(Exception $e) {
			if($e->getMessage() != '404') throw $e;
			header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
			die('404 Not Found');
		}
	}
	
	public function tabs() {
		$this->imodule_args['page']['channel'] = $this->channel['channel'];
		$this->channels = db()->query("SELECT channel, host, port, online FROM radio ORDER BY channel");
		return $this->ilphp_fetch('radio.php.module.tabs.ilp');
	}
	public function stats() {
		if($this->channel['admins'])
			$this->channel['admins'] = db()->query("SELECT user_id FROM users WHERE user_id IN (".$this->channel['admins'].") ORDER BY nick");
		if($this->channel['guests'])
			$this->channel['guests'] = db()->query("SELECT user_id FROM users WHERE user_id IN (".$this->channel['guests'].") ORDER BY nick");
		return $this->ilphp_fetch('radio.php.module.stats.ilp');
	}
	
	protected function MENU(&$args) {
		$this->imodule_args['menu'] = 1;
		$this->ilphp_init('radio.php.menu.ilp', 10);
		if(($data = cache_L1::get($this->ilphp_cache_file)) !== false) return $data;
		if(($data = $this->ilphp_cache_load()) !== false) {
			cache_L1::set($this->ilphp_cache_file, 5, $data);
			return $data;
		}
		$this->channels = db()->query("SELECT channel, host, port, online FROM radio ORDER BY channel");
		return $this->ilphp_fetch();
	}
	
	protected function IDLE(&$idle) {
		if(!empty($idle['menu']))
			G::$json_data['e']['IM_MENU_radio'] = $this->MENU($idle);
		if(!empty($idle['page'])) {
			$this->channel = db()->query("SELECT * FROM radio WHERE channel='".es($idle['page']['channel'])."' LIMIT 1")->fetch_assoc();
			if($this->channel) {
				G::$json_data['e']['radio_tabs'] = $this->tabs();
				$this->imodule_args['page']['state'] = crc32(print_r($this->channel, true));
				if($this->imodule_args['page']['state'] != $idle['page']['state']) G::$json_data['e']['radio_stats'] = $this->stats();
			}
		}
	}
	
	private function playlist(&$args) {
		switch($args['playlist']) {
		default:
			throw new iexception('404', $this);
			
		case 'pls':
			header("Content-type: application/force-download");
			header("Content-Disposition: attachment; filename=\"iCom_Radio_".$this->channel['channel'].".pls\"");
			die('[playlist]
NumberOfEntries=1
File1=http://'.$this->channel['host'].':'.$this->channel['port'].'/');
			
		case 'm3u':
			header("Content-type: application/force-download");
			header("Content-Disposition: attachment; filename=\"iCom_Radio_".$this->channel['channel'].".m3u\"");
			die('http://'.$this->channel['host'].':'.$this->channel['port'].'/'."\r\n");
			
		case 'wmp':
			header("Content-type: application/force-download");
			header("Content-Disposition: attachment; filename=\"iCom_Radio_".$this->channel['channel'].".asx\"");
			die('<Asx Version = "3.0" >
	<Param Name = "Name" Value = "iCom.to Radio '.$this->channel['channel'].'" />
	<Title >iCom.to</Title>
	<Entry>
		<Param Name = "Location" Value = "Germany" />
		<Param Name = "MediaType" Value = "audio" />
		<Param Name = "type" Value = "broadcast" />
		<Param Name = "Is_Trusted" Value = "false" />
		<Param Name = "Is_Protected" Value = "false" />
		<REF HREF = "http://'.$this->channel['host'].':'.$this->channel['port'].'/" />
	</Entry>
</Asx>');
			
		case 'itunes':
			header("Content-type: application/force-download");
			header("Content-Disposition: attachment; filename=\"iCom_Radio_".$this->channel['channel'].".xml\"");
			die('<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple Computer//DTD PLIST 1.0//EN" 
"http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
	<key>Major Version</key><integer>1</integer>
	<key>Minor Version</key><integer>1</integer>
	<key>Application Version</key><string>4.0</string>
	<key>Tracks</key>
	<dict>
		<key>0028</key>
		<dict>
			<key>Track ID</key><integer>0028</integer>
			<key>Name</key><string>iCom Radio '.$this->channel['channel'].'</string>
			<key>Artist</key><string></string>
			<key>Kind</key><string>MPEG audio stream</string>
			<key>Total Time</key><integer>0</integer>
			<key>Bit Rate</key><integer>128</integer>
			<key>Sample Rate</key><integer>44100</integer>
			<key>Location</key><string>http://'.$this->channel['host'].':'.$this->channel['port'].'/</string>
		</dict>
	</dict>
	<key>Playlists</key>
	<array>
		<dict>
			<key>Name</key><string>iCom Radio '.$this->channel['channel'].'</string>
			<key>All Items</key><true/>
			<key>Playlist Items</key>
			<array>
				<dict>
					<key>Track ID</key><integer>0028</integer>
				</dict>
			</array>
		</dict>
	</array>
</dict>
</plist>');
			
		case 'embed':
			die('<embed src="'.STATIC_CONTENT_DOMAIN.'/swf/dewplayer-stream.swf?mp3=http://'.$this->channel['host'].':'.$this->channel['port'].'/;&amp;autostart=1&amp;nopointer=1&amp;ext=.mp3" wmode="transparent" quality="high" bgcolor="#ffffff" width="135" height="50" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer">');
				
		case 'embed_quicktime':
			die('<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" width="320"height="180" codebase="http://www.apple.com/qtactivex/qtplugin.cab">
	<param name="src" value="http://icom.to/php/radio_playlist.php?channel='.$this->channel['channel'].'&type=m3u">
	<param name="autoplay" value="true">
	<param name="controller" value="false">
	<embed src="http://icom.to/php/radio_playlist.php?channel='.$this->channel['channel'].'&type=m3u" width="320" height="180" autoplay="true" controller="false" pluginspage="http://www.apple.com/quicktime/download/"></embed>
</object>');
		}
	}
}

?>
