<?function ILPHP____templates_c_73f9c2bd_index_php_menu_main_4fcbffeb_php_controls(&$ILPHP){?>
 <?if(IS_LOGGED_IN){;?>
 <a href="/<?=LANG;?>/users/<?=user()->user_id;?>-<?=urlenc(user()->nick);?>/" class="profile" title="<?=htmlspecialchars(lang::_LS_get_define('de786612'))?>"><?=htmlspecialchars(lang::_LS_get_define('bdc29d29'))?><img src="/img/p.gif"></a>
 <span id="MenuPNs"><?=iengine::GET('pn')->RUN('ICON', ['pn'=>'icon']);?></span>
 <a href="/<?=LANG;?>/settings/" class="settings" title="<?=htmlspecialchars(lang::_LS_get_define('b1e44042'))?>"><?=htmlspecialchars(lang::_LS_get_define('b1e44042'))?><img src="/img/p.gif"></a>
 <a href="/<?=LANG;?>/logout/" class="logout" title="<?=htmlspecialchars(lang::_LS_get_define('a17f8282'))?>" style="border:0;"><?=htmlspecialchars(lang::_LS_get_define('a17f8282'))?><img src="/img/p.gif"></a>
 <?}else{;?>
 <a href="/<?=LANG;?>/settings/" class="settings" title="<?=htmlspecialchars(lang::_LS_get_define('b1e44042'))?>"><?=htmlspecialchars(lang::_LS_get_define('b1e44042'))?><img src="/img/p.gif"></a>
 <a href="/<?=LANG;?>/login/" class="login" title="<?=htmlspecialchars(lang::_LS_get_define('f16ffd7e'))?>"><?=htmlspecialchars(lang::_LS_get_define('f16ffd7e'))?><img src="/img/p.gif"></a>
 <a href="/<?=LANG;?>/register/" class="register" title="<?=htmlspecialchars(lang::_LS_get_define('6652d890'))?>"><?=htmlspecialchars(lang::_LS_get_define('6652d890'))?><img src="/img/p.gif"></a>
 <? } ?>
 <?}?><?function ILPHP____templates_c_73f9c2bd_index_php_menu_main_4fcbffeb_php(&$ILPHP){?><div class="main-menu-top">
 <a href="/<?=LANG;?>/" class="main-menu-logo"></a>
 
 <div class="main-menu-language">
 <a href="/<?=LANG;?>/_lang/en<?=htmlspecialchars(rebuild_location());?>" style="position:relative;margin-top:2px;" target="_self" class="language"><img src="<?=STATIC_CONTENT_DOMAIN;?>/img/p.gif" alt="" class="en" style="background:transparent url(/img/sitelang/en.gif) top left no-repeat;"></a>
 <a href="/<?=LANG;?>/_lang/de<?=htmlspecialchars(rebuild_location());?>" style="position:relative;margin-top:2px;" target="_self" class="language"><img src="<?=STATIC_CONTENT_DOMAIN;?>/img/p.gif" alt="" class="de" style="background:transparent url(/img/sitelang/de.gif) top left no-repeat;"></a>
 </div>
 <div class="main-menu-controls">
 <?ILPHP____templates_c_73f9c2bd_index_php_menu_main_4fcbffeb_php_controls($ILPHP)?>
 </div>
 <?=iengine::GET('search')->RUN('MENU');?>
 <div class="clear"></div>
</div>
<?}?>