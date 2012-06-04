<?function ILPHP____templates_c_73f9c2bd_online_users_php_menu_4fcbffeb_php(&$ILPHP){?><?=htmlspecialchars(lang::_LS_get_define('1adb4014'))?> <?=$ILPHP->num_registered;?><br>
<?=htmlspecialchars(lang::_LS_get_define('391975cb'))?> <?=$ILPHP->num_guests_online;?><br>
<a href="/<?=LANG;?>/online_users/"><?=htmlspecialchars(lang::_LS_get_define('6db4c538'))?> <?if($ILPHP->num_online_users){;echo $ILPHP->num_online_users;}else{;echo htmlspecialchars(lang::_LS_get_define('42fe71da')); } ?></a>
<?}?>