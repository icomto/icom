<?function ILPHP____templates_c_73f9c2bd_index_php_menu_left_4fcbffeb_php(&$ILPHP){?><?if(IS_LOGGED_IN){;
echo $ILPHP->add_menu('left', LS('Admin'), 'menu-admin', 'admin_menu');
if(!user()->has_group(USER_GROUPID) or user()->has_group(210)){;
echo $ILPHP->add_menu('left', LS('Teamintern'), 'menu-forum', 'forum', ['namespace' => 'team', 'limit' => 10]);
 } ;
 } ;

echo $ILPHP->add_menu('left', LS('News'), 'menu-forum', 'forum', ['namespace' => 'news', 'limit' => 10]);
echo $ILPHP->add_menu('left', LS('Forum'), 'menu-forum', 'forum', ['namespace' => 'def', 'limit' => 20]);?>

<div class="side-menu-footer"></div>
<?}?>