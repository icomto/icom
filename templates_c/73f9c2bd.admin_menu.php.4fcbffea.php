<?function ILPHP____templates_c_73f9c2bd_admin_menu_php_4fcbffea_php(&$ILPHP){?><?if(has_privilege('email')){;?><div><a href="/<?=LANG;?>/admin/email/">E-Mail</a></div><? } ;
if(has_privilege('inviter')){;?><div><a href="/<?=LANG;?>/admin/invites/">Invite Codes</a></div><? } ;
if(has_privilege('user_warnings') and (has_privilege('forum_admin') or has_privilege('forum_mod') or has_privilege('forum_super_mod'))){;?><div><a href="/<?=LANG;?>/admin/forum_report_tickets/"><?=htmlspecialchars(lang::_LS_get_define('f294d858'))?></a></div><? } ;
if(has_privilege('radio') or has_privilege('radio_admin')){;?><div><a href="/<?=LANG;?>/admin/radio/">Radio</a></div><? } ;
if(has_privilege('groupmanager')){;?><div><a href="/<?=LANG;?>/admin/groups/">Gruppen</a></div><? } ;
if(has_privilege('usermanager')){;?><div><a href="/<?=LANG;?>/admin/users/">Benutzer</a></div><? } ;
if(has_privilege('forum_admin')){;?><div title="Forum Administration"><a href="/<?=LANG;?>/admin/forum/">Forum</a></div><? } ;
if(has_privilege('report_page')){;?><div><a href="/<?=LANG;?>/admin/report_page/">Gemeldete Seiten</a></div><? } ?>
<?}?>