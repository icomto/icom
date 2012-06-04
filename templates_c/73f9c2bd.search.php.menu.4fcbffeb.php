<?function ILPHP____templates_c_73f9c2bd_search_php_menu_4fcbffeb_php(&$ILPHP){?><div class="main-menu-search">
 <form class="main-menu-search-category form-inline" method="post">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="top">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('top');?>" value="for">
 <button <?if($ILPHP->search['top'] === 'for' or !$ILPHP->search['top']){;?> class="selected"<? } ?>><?=htmlspecialchars(lang::_LS_get_define('18792b1b'))?></button>
 </form>
 &nbsp;&nbsp; |&nbsp;&nbsp;
 <form class="main-menu-search-category form-inline" method="post">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="top">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('top');?>" value="wiki">
 <button <?if($ILPHP->search['top'] === 'wiki'){;?> class="selected"<? } ?>><?=htmlspecialchars(lang::_LS_get_define('641e668f'))?></button><?if(IS_LOGGED_IN){;?>
 </form>
 &nbsp;&nbsp; |&nbsp;&nbsp;
 <form class="main-menu-search-category form-inline" method="post">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="top">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('top');?>" value="user">
 <button <?if($ILPHP->search['top'] === 'user'){;?> class="selected"<? } ?>><?=htmlspecialchars(lang::_LS_get_define('6babd2ab'))?></button><? } ?>
 </form>
 <form class="main-menu-search-form" method="post" onsubmit="var t=this;setTimeout(function(){iC(t, '~.main-menu-search');},200);return false;">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="search">
 <input type="hidden" id="MainMenuSearchTop" name="<?=$ILPHP->IMODULE_POST_VAR('p');?>" value="<?=$ILPHP->search['top'];?>">
 <input type="text" id="SearchSuggest" class="search-text" name="<?=$ILPHP->IMODULE_POST_VAR('q');?>" value="<?=htmlspecialchars(lang::_LS_get_define('cdc7bdd7'))?>" autocomplete="off">
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('76f5f934'))?></button>
 </form>
 <script>
 var t = $('.main-menu-search-category');
 t.find('button').bind('click', function() {
 t.find('button').removeClass('selected');
 $(this).addClass('selected');
 $('#MainMenuSearchTop').val($(this).parent().find('input[name$=top]').attr('value'));
 $('#SearchSuggest').focus();
 iC(false, $(this.form).serialize());
 return false;
 });
 $('#SearchSuggest').focus(function(){if(this.value=='<?=htmlspecialchars(lang::_LS_get_define('cdc7bdd7'))?>')this.value='';this.select();}).blur(function(){if(!this.value)this.value='<?=htmlspecialchars(lang::_LS_get_define('cdc7bdd7'))?>';});
 </script>
</div>
<?}?>