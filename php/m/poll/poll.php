<?php

class m_poll extends imodule {
	use ilphp_trait;
	use im_way;
	
	public $poll = NULL;
	public $state = 0;
	
	public function __construct() {
		parent::__construct(__DIR__);
	}
	
	protected function INIT(&$args) {
		$poll_id = (int)$args['poll'];
		$this->poll = db()->query("
			SELECT
				id, creator, groups, status, question,
				".(IS_LOGGED_IN ? "(SELECT 1 FROM user_poll_votes b WHERE b.poll_id='$poll_id' AND b.user_id='".USER_ID."' LIMIT 1)" : "1")." AS has_voted
			FROM user_polls
			WHERE id='$poll_id' AND ".m_poll_global::build_where()."
			LIMIT 1")->fetch_assoc();
		if(!$this->poll) throw new iexception('403_404', $this);
		
		$this->url = '/'.LANG.'/poll/'.$this->poll['id'].'-'.urlenc($this->poll['question']).'/';
		
		$this->is_admin = (IS_LOGGED_IN ? (m_poll_global::ultra_admin() ? true : ($this->poll['creator'] == USER_ID ? true : false)) : false);
		$this->has_voted = $this->poll['has_voted'];
		
		$answers = db()->query("
			SELECT a.id AS id, a.answer AS answer, COUNT(b.id) AS votes
			FROM user_poll_answers a
			LEFT JOIN user_poll_votes b ON b.answer_id=a.id
			WHERE a.poll_id='".$this->poll['id']."'
			GROUP BY a.id
			ORDER BY ".($this->has_voted ? "votes DESC" : "a.id"));
		
		$this->num_answers = $answers->num_rows;
		$this->answers = [];
		
		$this->num_votes = 0;
		$this->best_num_votes = 0;
		
		while($i = $answers->fetch_assoc()) {
			$this->answers[] = $i;
			$this->num_votes += $i['votes'];
			if($this->best_num_votes < $i['votes']) $this->best_num_votes = $i['votes'];
		}
		
		if(($this->state = cache_L1::get('poll_state_'.$poll_id)) === false) {
			$this->state = crc32(print_r([
				$this->poll,
				$this->answers,
				$this->num_votes
			], true));
			cache_L1::set('poll_state_'.$this->poll['id'], 60, $this->state);
		}
	}
	
	
	
	protected function POST_poll_vote(&$args) {
		if(!IS_LOGGED_IN or $this->has_voted or $this->poll['status'] != 'open') return;
		$answer_id = (int)$args['answer_id'];
		if($answer_id and db()->query("SELECT 1 FROM user_poll_answers WHERE id='$answer_id' AND poll_id='".$this->poll['id']."'")->num_rows) {
			db()->query("INSERT INTO user_poll_votes SET user_id='".USER_ID."', poll_id='".$this->poll['id']."', answer_id='$answer_id'");
			$this->has_voted = true;
		}
		cache_L1::del('poll_state_'.$this->poll['id']);
		$this->INIT($args);
		if(IS_AJAX) G::$json_data['e']['.poll-item-'.$this->poll['id']] = $this->ITEM($args);
	}
	
	
	
	protected function POST_change_question(&$args) {
		if(!$this->poll or !$this->is_admin or $this->best_num_votes) return;
		if(!($question = $args['question'])) return;
		db()->query("UPDATE user_polls SET question='".es($question)."' WHERE id='".$this->poll['id']."' LIMIT 1");
		cache_L1::del('poll_state_'.$this->poll['id']);
		page_redir($this->url.'settings/');
	}
	protected function POST_add_answer(&$args) {
		if(!$this->poll or !$this->is_admin or $this->best_num_votes) return;
		if(!($answer = $args['answer'])) return;
		db()->query("INSERT INTO user_poll_answers SET poll_id='".$this->poll['id']."', answer='".es($answer)."'");
		cache_L1::del('poll_state_'.$this->poll['id']);
		page_redir($this->url.'settings/');
	}
	protected function POST_change_answer(&$args) {
		if(!$this->poll or !$this->is_admin or $this->best_num_votes) return;
		if(!($answer = $args['answer'])) return;
		if(!($answer_id = (int)$args['answer_id'])) return;
		db()->query("UPDATE user_poll_answers SET answer='".es($answer)."' WHERE id='".es($answer_id)."' AND poll_id='".$this->poll['id']."'");
		cache_L1::del('poll_state_'.$this->poll['id']);
		page_redir($this->url.'settings/');
	}
	protected function POST_delete_answer(&$args) {
		if(!$this->poll or !$this->is_admin or $this->best_num_votes) return;
		if(!($answer_id = (int)$args['answer_id'])) return;
		db()->query("DELETE FROM user_poll_answers WHERE id='".es($answer_id)."' AND poll_id='".$this->poll['id']."'");
		cache_L1::del('poll_state_'.$this->poll['id']);
		page_redir($this->url.'settings/');
	}
	protected function POST_change_status(&$args) {
		if(!$this->poll or !$this->is_admin) return;
		if(!($status = $args['status'])) return;
		if(!in_array($status, array('open', 'closed', 'deleted'))) return;
		if($status == 'open' and $this->num_answers < 2) return;
		db()->query("UPDATE user_polls SET status='".es($status)."' WHERE id='".$this->poll['id']."' LIMIT 1");
		if($status == 'deleted') page_redir('/'.LANG.'/community/polls/');
		cache_L1::del('poll_state_'.$this->poll['id']);
		page_redir($this->url.'settings/');
	}
	protected function POST_add_group(&$args) {
		if(!$this->poll or !$this->is_admin) return;
		$group_id = preg_replace('~[^\d]~', '', @$args['group_id']);
		if($group_id == '') return;
		$group_id = (int)$group_id;
		$groups = explode_arr_list($this->poll['groups']);
		if(in_array($group_id, $groups)) return;
		if(db()->query("SELECT 1 FROM groups WHERE id='$group_id' LIMIT 1")->num_rows == 0) return;
		$groups[] = $group_id;
		$groups = implode_arr_list($groups);
		db()->query("UPDATE user_polls SET groups='".es($groups)."' WHERE id='".$this->poll['id']."' LIMIT 1");
		cache_L1::del('poll_state_'.$this->poll['id']);
		page_redir($this->url.'settings/');
	}
	protected function POST_remove_group(&$args) {
		if(!$this->poll or !$this->is_admin) return;
		$group_id = preg_replace('~[^\d]~', '', @$args['group_id']);
		if($group_id == '') return;
		$group_id = (int)$group_id;
		$groups = explode_arr_list($this->poll['groups']);
		if(!in_array($group_id, $groups)) return;
		$groups = implode_arr_list(remove_arr_value($groups, $group_id));
		db()->query("UPDATE user_polls SET groups='".es($groups)."' WHERE id='".$this->poll['id']."' LIMIT 1");
		cache_L1::del('poll_state_'.$this->poll['id']);
		page_redir($this->url.'settings/');
	}
	
	
	protected function MODULE(&$args) {
		$this->display_settings = (isset($args['settings']) ? true : false);
		
		if($this->display_settings) {
			if(!$this->is_admin) page_redir($this->url);
			$this->groups = db()->query("SELECT id, ".LQ('name_LL')." AS name FROM groups WHERE id!=".BANNED_GROUPID." AND ".($this->poll['groups'] != '' ? "id IN (".$this->poll['groups'].")" : "0")." AND ".LQ('name_LL')." NOT LIKE '\\_%' ORDER BY ".LQ('name_LL'));
			$this->available_groups = db()->query("SELECT id, ".LQ('name_LL')." AS name FROM groups WHERE ".LQ('name_LL')." NOT LIKE '\\_%' AND id!=".BANNED_GROUPID.($this->poll['groups'] != '' ? " AND id NOT IN (".$this->poll['groups'].")" : "")." ORDER BY ".LQ('name_LL'));
		}
		
		$this->way[] = [LS('Umfragen'), '/'.LANG.'/community/polls/'];
		$this->way[] = [$this->poll['question'], $this->url];
		if($this->display_settings) $this->way[] = [LS('Einstellungen'), $this->url.'settings/'];
		
		return $this->ilphp_fetch('poll.php.ilp');
	}
	
	
	private function generate_state_id() {
		$this->state = crc32(implode(',', [
			$this->num_answers,
			$this->num_votes,
			$this->poll['groups'],
			$this->poll['status'],
			$this->poll['question']
		]));
	}
	
	protected function ITEM(&$args) {
		try {
			if(!$this->poll) $this->INIT($args);
			$this->imodule_args[$this->poll['id']] = $this->state;
			return $this->ilphp_fetch('poll.php.item.ilp');
		}
		catch(iexception $e) {
			if($e->msg == '403_404') return '<span class="error">'.LS('Die Umfrage wurde nicht gefunden oder Du hast keine Berechtigung sie zu sehen.').'</span>';
			else throw $e;
		}
	}
	
	
	protected function IDLE(&$idle) {
		foreach($idle as $poll_id=>$state) {
			if(!(int)$poll_id) continue;
			if(($this->state = cache_L1::get('poll_state_'.$poll_id)) == $state) {
				$this->imodule_args[$poll_id] = $state;
				continue;
			}
			$args = ['poll' => $poll_id, 'state' => $state];
			$this->poll = NULL;
			G::$json_data['e']['.poll-item-'.$poll_id] = $this->ITEM($args);
		}
	}
}

?>
