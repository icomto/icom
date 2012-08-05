<?php

/*
CREATE TABLE  ap_users (
    namespace BIGINT NOT NULL,
    item_id BIGINT NOT NULL DEFAULT 0,
    user_id INT UNSIGNED NOT NULL,
    permission TINYINT(1) NOT NULL,
    PRIMARY KEY (namespace, item_id, user_id)
);
CREATE TABLE  ap_groups (
    namespace BIGINT NOT NULL,
    item_id BIGINT NOT NULL DEFAULT 0,
    group_id INT UNSIGNED NOT NULL,
    permission TINYINT(1) NOT NULL,
    PRIMARY KEY (namespace, item_id, group_id)
);
*/

define('ACCESS_POLICY_NONE', 0x00);
define('ACCESS_POLICY_READ', 0x01);
define('ACCESS_POLICY_WRITE', 0x02 | ACCESS_POLICY_READ);
define('ACCESS_POLICY_MOD', 0x04 | ACCESS_POLICY_WRITE);
define('ACCESS_POLICY_BANNED', 0x08);
define('ACCESS_POLICY_ADMIN', 0x10 | ACCESS_POLICY_MOD);
define('ACCESS_POLICY_ALL', ACCESS_POLICY_READ | ACCESS_POLICY_WRITE | ACCESS_POLICY_MOD | ACCESS_POLICY_BANNED | ACCESS_POLICY_ADMIN);

class AccessPolicy {
    //list of permissions allowed to set. ACCESS_POLICY_NONE is for delete and everytime available
    public $users = null;
    public $groups = null;

    //callbacks for static statuses
    public $isAdminCallback = null;
    public $isModCallback = null;

    //the default privilege
    public $default_privilege = ACCESS_POLICY_NONE;

    //privilege the owner becomes
    public $owner_privilege = ACCESS_POLICY_MOD;

    private $namespace = null;
    private $item_id = null;
    private $owner = null;

    private $cache_max_needed_rights = null;
    private $cache_permission = null;
    private $cache_users = null;
    private $cache_groups = null;

    public function __construct($namespace, $item_id = null, $owner = null, $permission = null) {
        $this->namespace = $namespace;
        $this->item_id = $item_id;
        $this->owner = $owner;
        $this->cache_permission = $permission;
    }

    public function query($max_needed_rights, $sql_item_id = null, $sql_owner = null) {
        if(($cb = $this->isAdminCallback) and $cb()) {
            return ACCESS_POLICY_ADMIN;
        }

        if($max_needed_rights <= $this->default_privilege) {
            return $this->default_privilege;
        }

        $q = [];

        $item_query = ["APX.item_id=0"];
        if($sql_item_id !== null) $item_query[] = "APX.item_id=$sql_item_id";
        $item_query = "(".implode(" OR ", $item_query).")";

        if($this->users !== null) {
            $q[] = "
                SELECT COALESCE(MAX(apu.permission), ".ACCESS_POLICY_NONE.") apx_permission
                FROM ap_users apu
                WHERE
                    apu.namespace='".hash64i($this->namespace)."' AND
                    ".str_replace("APX.", "apu.", $item_query)." AND
                    apu.user_id='".USER_ID."'";
        }

        if($this->groups !== null) {
            $q[] = "
                    SELECT COALESCE(MAX(apg.permission), ".ACCESS_POLICY_NONE.") apx_permission
                    FROM ap_groups apg
                    WHERE
                        apg.namespace='".hash64i($this->namespace)."' AND
                        ".str_replace("APX.", "apg.", $item_query)." AND
                        apg.group_id IN (".implode_arr_list(user()->groups).")";
        }

        if(IS_LOGGED_IN and $sql_owner !== null) {
            $q[] = "SELECT IF($sql_owner=".USER_ID.", ".$this->owner_privilege.", ".$this->default_privilege.") apx_permission";
        }

        if(($cb = $this->isModCallback) and $cb()) {
            if($q) {
                $q[] = "SELECT ".ACCESS_POLICY_MOD." apx_permission";
            }
            else {
                return ACCESS_POLICY_MOD;
            }
        }

        if(!(IS_LOGGED_IN and $sql_owner !== null)) {
            if($q) {
                $q[] = "SELECT ".$this->default_privilege." apx_permission";
            }
            else {
                return $this->default_privilege;
            }
        }

        if($q) {
            #return "SELECT MAX(apx_permission) apx_permission FROM (".implode(" UNION ", $q).") apx";
            return "(".implode(") | (", $q).")";
        }

        return $this->default_privilege;
    }

    public function permission($max_needed_rights = ACCESS_POLICY_ALL) {
        if($this->cache_permission === null or $max_needed_rights > $this->cache_max_needed_rights) {
            $permission = $this->query($this->item_id ? "'".es($this->item_id)."'" : null, null);
            if(is_numeric($permission)) {
                $this->cache_permission = $permission;
            }
            else {
                $this->cache_permission = db()->query("SELECT $permission apx_permission")->fetch_assoc()['apx_permission'];
            }
            $this->cache_max_needed_rights = $max_needed_rights;
        }
        return $this->cache_permission;
    }

    private function clearCache() {
        $this->cache_max_needed_rights = null;
        $this->cache_permission = null;
        $this->cache_users = null;
        $this->cache_groups = null;
    }

    private function setPermission($table, $key, $value, $permission) {
        if($permission == ACCESS_POLICY_NONE or $permission == null) return $this->delPermission($table, $key, $value);
        db()->query("
            REPLACE INTO $table
            SET
                namespace='".hash64i($this->namespace)."',
                item_id='".es($this->item_id)."',
                $key='".es($value)."',
                permission='".es($permission)."'");
        if(db()->affected_rows) {
            $this->clear_cache();
            return true;
        }
    }

    private function delPermission($table, $key, $value) {
        db()->query("
            DELETE FROM $table
            WHERE
                namespace='".hash64i($this->namespace)."' AND
                item_id='".es($this->item_id)."' AND
                $key=$value");
        if(db()->affected_rows) {
            $this->clearCache();
            return true;
        }
    }

    public function clearItem() {
        $affected = false;
        foreach(['ap_users', 'ap_groups'] as $table) {
            db()->query("
                DELETE FROM $table
                WHERE
                    namespace='".hash64i($this->namespace)."' AND
                    item_id='".es($this->item_id)."'");
            if(db()->affected_rows) $affected = true;
        }
        if($affected) {
            $this->clearCache();
            return true;
        }
    }

    public function clearNamespace() {
        $affected = false;
        foreach(['ap_users', 'ap_groups'] as $table) {
            db()->query("
                DELETE FROM $table
                WHERE
                    namespace='".hash64i($this->namespace)."'");
            if(db()->affected_rows) $affected = true;
        }
        if($affected) {
            $this->clearCache();
            return true;
        }
    }

    public static function checkBits($field, $bits) {
        return ($field & $bits) == $bits;
    }

    private function get($table, $field) {
        $aa = db()->query("
            SELECT $field, permission
            FROM $table
            WHERE
                namespace='".hash64i($this->namespace)."' AND
                item_id='".es($this->item_id)."'");
        $retval = [];
        while($a = $aa->fetch_assoc()) {
            $retval[] = $a;
        }
        return $retval;
    }

    private function getFilter($arr, $key, $permission = null) {
        if($permission === null) return $arr;
        return array_map(function($v) { return $v[$key]; }, array_filter(function($v) { return self::checkBits($v['permission'], $permission); }, $arr));
    }

    public function getUsers($permission = null) {
        if($users === null) return;
        if($this->cache_users === null) $this->cache_users = $this->get('ap_users', 'user_id');
        return $this->getFilter($this->cache_users, 'user_id', $permission);
    }

    public function getGroups($permission = null) {
        if($users === null) return;
        if($this->cache_groups === null) $this->cache_groups = $this->get('ap_groups', 'group');
        return $this->getFilter($this->cache_groups, 'group_id', $permission);
    }

    public function setPermissionUser($user_id, $permission) {
        if($this->users === null or !self::checkBits($this->users, $permission)) return false;
        return $this->setPermission('ap_users', 'user_id', $user_id, $permission);
    }

    public function setPermissionGroup($group_id, $permission) {
        if($this->groups === null or !self::checkBits($this->groups, $permission)) return false;
        return $this->setPermission('ap_groups', 'group_id', $group_id, $permission);
    }

    public function allowRead() {
        $p = $this->permission();
        return !self::checkBits($p, ACCESS_POLICY_BANNED) and self::checkBits($p, ACCESS_POLICY_READ);
    }

    public function allowWrite() {
        $p = $this->permission();
        return !self::checkBits($p, ACCESS_POLICY_BANNED) and self::checkBits($p, ACCESS_POLICY_WRITE);
    }

    public function isMod() {
        $p = $this->permission();
        return !self::checkBits($p, ACCESS_POLICY_BANNED) and self::checkBits($p, ACCESS_POLICY_MOD);
    }

    public function isAdmin() {
        $p = $this->permission();
        return self::checkBits($p, ACCESS_POLICY_ADMIN);
    }

    public function isBanned() {
        $p = $this->permission();
        return self::checkBits($p, ACCESS_POLICY_BANNED);
    }
}
