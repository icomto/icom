<?php

class m_error_fsk18 extends imodule {
    use ilphp_trait;
    use im_way;

    public function __construct(&$user = NULL) {
        parent::__construct(__DIR__);
    }
    protected function MODULE(&$args) {
        header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
        $this->way[] = [LS('Alters&uuml;berpr&uuml;fung'), ''];
        $this->im_way_title();
        return $this->ilphp_fetch('fsk18.php.ilp');
    }
}

?>
