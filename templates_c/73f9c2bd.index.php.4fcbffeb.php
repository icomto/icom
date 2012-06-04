<?function ILPHP____templates_c_73f9c2bd_index_php_4fcbffeb_php(&$ILPHP){?><?$ILPHP->ilphp_display('~/page_header.ilp', -1, "", true);?>
<div class="full-page <?if(DISPLAY_COMMUNITY_ELEMENTS){;?>short-style<?}else{;?>long-style<? } ?>">
 <div class="main-menu-helper">
 <div class="main-menu-icons-helper <?=$ILPHP->THEME['mm_icons'];?>">
 <div class="main-menu">
 <div class="main-menu-style-helper <?=$ILPHP->THEME['mm_style'];?>">
 <div class="main-menu-logo-helper <?=$ILPHP->THEME['mm_logo'];?>">
 <?$ILPHP->ilphp_display('index.php.menu_main.ilp', -1, "", true);?>
 </div>
 </div>
 <div class="main-menu-places-helper <?=$ILPHP->THEME['mm_places'];?>">
 <style>.main-menu-places a { padding: 0 30px 0 30px; }</style>
 <div class="main-menu-places">
 <a href="/<?=LANG;?>/forum/"><?=htmlspecialchars(lang::_LS_get_define('18792b1b'))?></a> |
 <a href="/<?=LANG;?>/news/"><?=htmlspecialchars(lang::_LS_get_define('b715eff4'))?></a> |
 <a href="/<?=LANG;?>/wiki/"><?=htmlspecialchars(lang::_LS_get_define('641e668f'))?></a> |
 <a href="/<?=LANG;?>/chats/"><?=htmlspecialchars(lang::_LS_get_define('6fd7bc0e'))?></a> |
 <?if(IS_LOGGED_IN){;?><a href="/<?=LANG;?>/community/"><?=htmlspecialchars(lang::_LS_get_define('ccc98692'))?></a> |<? } ?>
 <a href="/<?=LANG;?>/wiki/Portal/"><?=htmlspecialchars(lang::_LS_get_define('396c9199'))?></a> |
 <a href="/<?=LANG;?>/contact/"><?=htmlspecialchars(lang::_LS_get_define('434d5a35'))?></a> |
 <a href="/<?=LANG;?>/report_page/" target="_blank"><?=htmlspecialchars(lang::_LS_get_define('401086f2'))?></a>
 </div>
 </div>
 </div>
 </div>
 </div>
 
 <?if(DISPLAY_COMMUNITY_ELEMENTS){;?>
 <div class="side-menu side-menu-helper side-menu-left <?=$ILPHP->THEME['side_menu'];?>">
 <?$ILPHP->ilphp_display('index.php.menu_left.ilp', -1, "", true);?>
 </div>
 <? } ?>
 <div class="module-helper <?=$ILPHP->THEME['module'];?>">
 <div class="module" id="Module">
 
 
 
 <?if(!HIDE_DIFFERENT_LANG_MESSAGE_BOX and LANG_ENABLED_DIFFERENT_LANG){;?>
 <div class="module-item" id="ModuleDifferentLang">
 <h1><?=htmlspecialchars(lang::_LS_get_define('319e8338'))?></a></h1>
 <div class="module-content" style="text-align:center;">
 <?=htmlspecialchars(lang::_LS_get_define('d7eec410'))?><br>
 <form method="post" action="/<?=LANG;?>/_lang/<?=LANG;echo rebuild_location();?>" style="margin:5px;">
 <button type="submit" class="big-button" style="width:75%;"><?=htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('248328ea'),get_user_language_strings(array(LANG))))?></button>
 </form>
 <form method="post" action="/<?=LANG;?>/_lang/<?=LANG_COOKIE_LANG;echo rebuild_location();?>" style="margin:5px;">
 <button type="submit" class="big-button" style="width:75%;"><?=htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('b6b33502'),get_user_language_strings(array(LANG_COOKIE_LANG))))?></button>
 </form>
 <form method="post" action="/<?=LANG;echo rebuild_location();?>" style="margin:5px;">
 <input type="hidden" name="lang_hide_different_lang_message_box" value="1">
 <button type="submit" class="big-button" style="width:75%;"><?=htmlspecialchars(lang::_LS_get_define('1534ae0a'))?></button>
 </form>
 </div>
 <div class="module-footer"></div>
 </div>
 <?}elseif(IS_LOGGED_IN and !HIDE_CAN_UNDERSTAND_MESSAGE_BOX and !user()->can_understand_one_language(array(LANG))){;?>
 <div class="module-item" id="ModuleDifferentLang">
 <h1><?=htmlspecialchars(lang::_LS_get_define('889025d1'))?></a></h1>
 <div class="module-content" style="text-align:center;">
 <?=lang::_handle_template_string(lang::_LS_get_define('6d7b2f2e'),htmlspecialchars(get_user_language_strings(array(LANG))),htmlspecialchars(get_user_language_strings(NULL)))?><br>
 <form method="post" action="/<?=LANG;echo rebuild_location();?>" style="margin:5px;">
 <input type="hidden" name="lang_add_current_lang_to_profile" value="1">
 <button type="submit" class="big-button" style="width:75%;"><?=htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('1a9a5c07'),get_user_language_strings(array(LANG))))?></button>
 </form>
 <form method="post" action="/<?=LANG;echo rebuild_location();?>" style="margin:5px;">
 <input type="hidden" name="lang_hide_can_understand_message_box" value="1">
 <button type="submit" class="big-button" style="width:75%;"><?=htmlspecialchars(lang::_LS_get_define('1534ae0a'))?></button>
 </form>
 </div>
 <div class="module-footer"></div>
 </div>
 <? } ;
 echo $ILPHP->MODULE_CONTENT;?>
 </div>
 </div>
 <div class="side-menu side-menu-helper side-menu-right <?=$ILPHP->THEME['side_menu'];?>">
 <?if(!DISPLAY_COMMUNITY_ELEMENTS){;$ILPHP->ilphp_display('index.php.menu_left.ilp', -1, "", true);; } ;
 $ILPHP->ilphp_display('index.php.menu_right.ilp', -1, "", true);?>
 </div>
 
 <div class="page-footer">
 <div class="copyright">Copyright &copy; 2008 - 2012 iCom.to Inc. <?=htmlspecialchars(lang::_LS_get_define('1f78b2f8'))?></div>
 <div class="links">
 <a href="/de/wiki/Disclaimer/" class="tos"><?=htmlspecialchars(lang::_LS_get_define('75a04b0f'))?></a><a href="/de/wiki/Disclaimer/" class="tos"><?=htmlspecialchars(lang::_LS_get_define('75a04b0f'))?></a>
 </div>
 <div class="credits">
 Coding by wuff, Content-Management by chucky, Design by J0NES &copy; 2008-2012 iCom.to, Land of the rising sun Holdings, LLC. <?=htmlspecialchars(lang::_LS_get_define('1f78b2f8'))?>.
 </div>
 </div>
</div>
<?$ILPHP->ilphp_display('~/page_footer.ilp', -1, "", true);?>
<?}?>