<?php

class db extends mysqli {
	public static $configs = array();
	public static $c = array();
	
	public $querys = 0;
	public $time_started = 0;
	public $time_last_query = 0;
	public $last_query_duration = 0;
	public $real_query_time = 0;
	public $timelog = '';
	public $DEBUG = false;
	
	public static function addConnection($id, $host, $user, $pass, $db, $port, $socket) {
		self::$configs[$id] = func_get_args();
	}
	public static function c($id = 0) {
		if(!isset(self::$c[$id]))
			self::$c[$id] = new self(
				self::$configs[$id][1],
				self::$configs[$id][2],
				self::$configs[$id][3],
				self::$configs[$id][4],
				self::$configs[$id][5],
				self::$configs[$id][6]);
		return self::$c[$id];
	}
	
	public function __construct($host, $user, $pass, $db, $port, $socket) {
		#$this->time_started = $this->time_last_query = get_militime();
		$this->connect($host, $user, $pass, $db, $port, $socket);
	}
	public function connect($host = '', $user = '', $pass = '', $db = '', $port = 3306, $socket = '') {
		$this->_host = $host;
		$this->_user = $user;
		$this->_pass = $pass;
		$this->_db = $db;
		$this->_port = $port;
		$this->_socket = $socket;
		$this->reconnect();
	}
	public function reconnect() {
		#$t = get_militime();
		parent::connect($this->_host, $this->_user, $this->_pass, $this->_db, $this->_port, $this->_socket);
		#$this->real_query_time += sub_militime($t, get_militime());
		if(mysqli_connect_errno()) throw new Exception('DB CONNECT ERROR'); #trigger_error('DB CONNECT ERROR', E_USER_ERROR);
		parent::query('SET NAMES UTF8');
	}
	public function direct_query($q, $resultmode = MYSQLI_STORE_RESULT, $retry = 0) {
		#$t = get_militime();
		$rv = parent::query($q, $resultmode);
		#$this->real_query_time += sub_militime($t, get_militime());
		if($this->errno) if($this->__error($q, $retry)) return $this->direct_query($q, $resultmode, $retry + 1);
		return $rv;
	}
	public function query($q, $resultmode = MYSQLI_STORE_RESULT, $retry = 0) {
		$this->__developer($q);
		#$t = get_militime();
		$rv = parent::query($q, $resultmode);
		#$this->real_query_time += sub_militime($t, get_militime());
		if($this->errno) if($this->__error($q, $retry)) return $this->query($q, $resultmode, $retry + 1);
		return $rv;
	}
	public function multi_query($q, $retry = 0) {
		$this->__developer($q);
		#$t = get_militime();
		$rv = parent::multi_query($q);
		#$this->real_query_time += sub_militime($t, get_militime());
		if($this->errno) if($this->__error($q, $retry)) return $this->direct_query($q, $retry + 1);
		return $rv;
	}
	protected function __error(&$q, $retry) {
		$retry_error_numbers = array();
		$retry_error_numbers[] = 1053; //Server shutdown in progress
		$retry_error_numbers[] = 1205; //Lock wait timeout exceeded; try restarting transaction
		$retry_error_numbers[] = 2006; //MySQL server has gone away
		if($retry < 2 and in_array($this->errno, $retry_error_numbers)) {
			parent::close();
			$this->reconnect();
			return true;
		}
		trigger_error('FUCK: '.$this->errno.': '.$this->error.' --- '.$q, E_USER_ERROR);
		die;
	}
	protected function __developer($q) {
		$this->querys++;
		#$this->last_query_duration = sub_militime($this->time_last_query, get_militime());
		#$this->timelog .= $this->last_query_duration."\n\n\n".htmlspecialchars($q)."\n";
		#$this->time_last_query = get_militime();
	}
	public function __destruct() {
		/*if(isset($_GET['agt85go5ieugjoaijg'])) {
			$pt = sub_militime($this->time_started, get_militime());
			echo '<pre style="width:600px;">'.$this->timelog.sub_militime($this->time_last_query, get_militime()).'</pre>'."\n";
			echo '<center><i>'.
				'Seitenaufbau in '.$pt.' Sekunden ('.$this->querys.' querys)'.
				'</i></center><div id="dbgt_dbg"></div>'.
				"\nTIMES:".$pt." \t ".$this->real_query_time."\n";
		}*/
	}
}

?>
