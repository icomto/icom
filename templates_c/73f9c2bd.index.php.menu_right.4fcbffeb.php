<?function ILPHP____templates_c_73f9c2bd_index_php_menu_right_4fcbffeb_php(&$ILPHP){?><?=$ILPHP->add_menu('right', LS('Benutzer'), 'menu-userstats', 'online_users');
echo $ILPHP->add_menu('right', LS('Radio'), 'menu-radio', 'radio');
if(IS_LOGGED_IN){;
echo $ILPHP->add_menu('right', 'MODULE->chat_name', 'menu-chat', 'chat', ['chat_id'=>137]);
 } ;
echo $ILPHP->add_menu('right', LS('Shoutbox'), 'menu-chat', 'shoutbox');?>
<div class="side-menu-footer"></div>
<?}?>