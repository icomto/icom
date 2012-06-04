<?function ILPHP____templates_c_1a86327f_wiki_php_4fcbffeb_php(&$ILPHP){?><div class="module-item">
 <script>
 var whist2=function(a){
 whist2c();
 $('.wiki .whist-'+a).addClass('hover');
 };
 var whist2c=function(){
 $('.wiki .history tr').removeClass('hover');
 };
 </script>
 <h1>
 <?if(@$ILPHP->page){;
 echo bookmark_engine::icon('/'.LANG.'/wiki/'.wiki_urlencode($ILPHP->wiki).'/', 'wiki', $ILPHP->page['id']);
 if(IS_LOGGED_IN){;?>
 <img class="dropdown-icon" src="<?=STATIC_CONTENT_DOMAIN;?>/img/p.gif" alt="" data-dd="ildd.def" data-dd-item-1="&lt;a href=&quot;/<?=LANG;?>/wiki/<?=wiki_urlencode($ILPHP->wiki);?>/history/&quot;&gt;<?=htmlspecialchars(lang::_LS_get_define('299be1a0'))?>&lt;/a&gt;"<?if($ILPHP->page['history'] or @$ILPHP->history){;?> data-dd-item-2="&lt;a href=&quot;/<?=LANG;?>/wiki/<?=wiki_urlencode($ILPHP->wiki);?>/edit/<?if(($ILPHP->action == 'MAIN' or $ILPHP->action == 'EDIT') and @$ILPHP->history){;if(is_array($ILPHP->history)){;echo $ILPHP->history['id'];}else{;echo $ILPHP->history; } ?>/<? } ?>&quot;&gt;<?if($ILPHP->page['locked'] and !has_privilege('wiki_mod')){;echo htmlspecialchars(lang::_LS_get_define('7e14276f'));}else{;echo htmlspecialchars(lang::_LS_get_define('b8e319fd')); } ?>"<? } ?>>
 <? } ;
 } ;
 $ILPHP->count = count($ILPHP->way);
 $ILPHP->foreach_i=0;foreach($ILPHP->way as $ILPHP->i){$ILPHP->foreach_i++;;
 if($ILPHP->i['1']){;?> <a href="/<?=LANG;echo $ILPHP->i['1'];?>"><?=htmlspecialchars($ILPHP->i['0']);?></a><?}else{;?> <?=htmlspecialchars($ILPHP->i['0']); } ;if($ILPHP->foreach_i < $ILPHP->count){;?> &raquo;<? } ;
 } ?>
 </h1>
 <div class="module-content">
 <div class="wiki">
 <?if($ILPHP->action == 'MAIN' and !$ILPHP->page){;?>
 <p class="error"><?=htmlspecialchars(lang::_LS_get_define('ad66ccb3'))?></p>
 <ul>
 <li><?=lang::_handle_template_string(lang::_LS_get_define('bf556e31'),htmlspecialchars(LANG),htmlspecialchars(wiki_urlencode($ILPHP->wiki)),htmlspecialchars($ILPHP->wiki))?></li>
 <li><a href="/<?=LANG;?>/wiki/<?=wiki_urlencode($ILPHP->wiki);?>/edit/"><?=htmlspecialchars(lang::_LS_get_define('3a57faca'))?></a> (<a href="/<?=LANG;?>/wiki/Wiki_Artikel_schreiben/"><?=htmlspecialchars(lang::_LS_get_define('e87f3a78'))?></a>).</li>
 </ul>
 <?}else{;
 
 if($ILPHP->action == 'ERROR'){;?>
 <h2 class="firstHeading"><?=htmlspecialchars($ILPHP->wiki);?></h2>
 <?$ILPHP->ilphp_display('wiki.php.system_messages.ilp', -1, "", true);?>
 <div class="error">
 <?=$ILPHP->code;
 switch($ILPHP->code){;
 case 403:;?> - <?=htmlspecialchars(lang::_LS_get_define('4a9420a7'));break;
 case 404:;?> - <?=htmlspecialchars(lang::_LS_get_define('016ec69f'));break;
 } ?>
 </div>
 
 <?}elseif($ILPHP->action == 'MAIN'){;?>
 <h2 class="firstHeading">
 <?=htmlspecialchars($ILPHP->page['name']);
 if($ILPHP->ws->aliases){;?>
 <span class="aliases">
 (<?=htmlspecialchars(lang::_LS_get_define('bdabea5e'));
 $ILPHP->num = count($ILPHP->ws->aliases);
 $ILPHP->foreach_a=0;foreach($ILPHP->ws->aliases as $ILPHP->a){$ILPHP->foreach_a++;?>
 <a href="/<?=LANG;?>/wiki/<?=wiki_urlencode($ILPHP->a);?>/"><?=htmlspecialchars($ILPHP->a);?></a><?if($ILPHP->foreach_a < $ILPHP->num){;?>, <? } ;
 } ?>)
 </span>
 <? } ?>
 </h2>
 <?if(@$ILPHP->history){;?><div class="headingComment"><?=htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('7fc2dd5c'),default_time_format($ILPHP->history['timeadded'])));if($ILPHP->history and $ILPHP->page['history'] == $ILPHP->history['id']){;?> (<?=htmlspecialchars(lang::_LS_get_define('d0683fb4'))?>)<?}elseif(has_privilege('wiki_mod')){;?> (<a href="/<?=LANG;?>/wiki/<?=wiki_urlencode($ILPHP->wiki);?>/history/<?=$ILPHP->history['id'];?>/set/"><?=htmlspecialchars(lang::_LS_get_define('252e0a29'))?></a>)<? } ?></div><? } ;
 $ILPHP->ilphp_display('wiki.php.system_messages.ilp', -1, "", true);;
 echo $ILPHP->ws->output;
 
 }elseif($ILPHP->action == 'HISTORY_OVERVIEW'){;?>
 <h2 class="firstHeading"><?=htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('57713650'),$ILPHP->page['name']))?></h2>
 <?$ILPHP->ilphp_display('wiki.php.system_messages.ilp', -1, "", true);;
 if($ILPHP->tickets->num_rows){;?>
 <h3><?=htmlspecialchars(lang::_LS_get_define('2e764904'))?></h3>
 <ul>
 <?$ILPHP->while_i=0;while($ILPHP->i = $ILPHP->tickets->fetch_assoc()){$ILPHP->while_i++;?>
 <li>
 <h6 style="margin-bottom:0;">
 <?=lang::_handle_template_string(htmlspecialchars(lang::_LS_get_define('aa265cc7')),user($ILPHP->i['opener'])->html(-1))?> <?=timeago($ILPHP->i['timecreated']);
 if($ILPHP->i['closer']){;?>, <?=lang::_handle_template_string(htmlspecialchars(lang::_LS_get_define('3e5d5435')),user($ILPHP->i['closer'])->html(-1))?> <?=timeelapsed(strtotime($ILPHP->i['timecreated']) - strtotime($ILPHP->i['timeclosed']), LS('nach')); } ?>
 </h6>
 <?=htmlspecialchars($ILPHP->i['message']);
 if(has_privilege('wiki_mod')){;?>
 <form method="post" action="/<?=LANG;?>/wiki/<?=wiki_urlencode($ILPHP->this->page['name']);?>/history/" onsubmit="return iC(this, '~.module-item');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('history');?>" value="closed_tickets">
 <?if($ILPHP->i['closer']){;?>
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="reopen_ticket">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('reopen_ticket');?>" value="<?=$ILPHP->i['id'];?>">
 <input type="submit" class="button" value="<?=htmlspecialchars(lang::_LS_get_define('18a2888d'))?>">
 <?}else{;?>
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="close_ticket">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('close_ticket');?>" value="<?=$ILPHP->i['id'];?>">
 <input type="submit" class="button" value="<?=htmlspecialchars(lang::_LS_get_define('4f7b4194'))?>">
 <? } ?>
 </form>
 <? } ?>
 </li>
 <? } ?>
 </ul>
 <?if($ILPHP->has_closed_tickets){;?><ul><li><a href="/<?=LANG;?>/wiki/<?=wiki_urlencode($ILPHP->wiki);?>/history/closed_tickets/"><?=htmlspecialchars(lang::_LS_get_define('97fce5d7'))?></a></li></ul><? } ?>
 <h3><?=htmlspecialchars(lang::_LS_get_define('dcff7446'))?></h3>
 <?}elseif($ILPHP->has_closed_tickets){;?><ul><li><a href="/<?=LANG;?>/wiki/<?=wiki_urlencode($ILPHP->wiki);?>/history/closed_tickets/"><?=htmlspecialchars(lang::_LS_get_define('97fce5d7'))?></a></li></ul>
 <? } ;
 if($ILPHP->num_pages > 1){;?><div class="pages"><?=create_pages($ILPHP->current_page, $ILPHP->num_pages - 1, '/'.LANG.'/wiki/'.wiki_urlencode($ILPHP->page['name']).'/history/page:%s/');?></div><? } ?>
 <table border="1" width="100%" class="history">
 <tr>
 <th width="120" style="text-align:center;"><?=htmlspecialchars(lang::_LS_get_define('b5c29d51'))?></th>
 <th width="110" style="text-align:center;"><?=htmlspecialchars(lang::_LS_get_define('b3ebd9d2'))?></th>
 <th>Grund</th>
 </tr>
 <?while($ILPHP->i = $ILPHP->this->history_changes_fetch($ILPHP->changes)){;?>
 <tr <?if($ILPHP->i['history'] or $ILPHP->i['action'] == "history_activated" or $ILPHP->i['action'] == "article_created"){;?>class="whist-<?=$ILPHP->i['history'];?>" onmouseover="whist2(<?=$ILPHP->i['history'];?>);" onmouseout="whist2c();"<?}else{;?>class="row"<? } ?>>
 <td valign="top" align="center">
 <?$ILPHP->before = false;
 if(has_privilege('wiki_mod')){;
 if($ILPHP->i['history'] == $ILPHP->page['history']){;echo htmlspecialchars(lang::_LS_get_define('d0683fb4'));$ILPHP->before = true;
 }elseif($ILPHP->i['history'] or $ILPHP->i['action'] == 'article_created'){;?><a href="/<?=LANG;?>/wiki/<?=wiki_urlencode($ILPHP->wiki);?>/history/<?=$ILPHP->i['history'];?>/set/"><?=htmlspecialchars(lang::_LS_get_define('d0683fb4'))?></a><?$ILPHP->before = true;
 } ;
 } ;
 
 if($ILPHP->i['action'] == 'content_changed' or ($ILPHP->i['action'] == 'history_activated' and $ILPHP->i['history'])){;
 if($ILPHP->before){;?> | <? } ;
 if($ILPHP->page['history'] != $ILPHP->i['history'] and $ILPHP->page['history'] and $ILPHP->i['history']){;?><a href="/<?=LANG;?>/wiki/<?=wiki_urlencode($ILPHP->wiki);?>/compare/<?=$ILPHP->page['history'];?>/with/<?=$ILPHP->i['history'];?>/"><?=htmlspecialchars(lang::_LS_get_define('dcb4fb48'))?></a>
 <?}else{;echo htmlspecialchars(lang::_LS_get_define('dcb4fb48'));
 } ;
 $ILPHP->before = true;
 } ;
 
 if(has_privilege('wiki_admin')){;if($ILPHP->before){;?> | <? } ?><a href="javascript:iC_confirm('<?=htmlspecialchars(lang::_LS_get_define('28c26efd'))?>', '<?=$ILPHP->IMODULE_POST_VAR('wiki');?>=<?=wiki_urlencode($ILPHP->wiki);?>&<?=$ILPHP->IMODULE_POST_VAR('delete_log');?>=<?=$ILPHP->i['id'];?>', '~.module-item');void(0);">X</a><? } ?>
 </td>
 <td valign="top" align="center">
 <?if($ILPHP->i['history']){;?><a href="/<?=LANG;?>/wiki/<?=wiki_urlencode($ILPHP->wiki);?>/<?if($ILPHP->i['history'] != $ILPHP->page['history']){;?>history/<?=$ILPHP->i['history'];?>/<? } ?>"><?=htmlspecialchars($ILPHP->i['history_timeadded']);?></a><br><? } ;
 echo timeago($ILPHP->i['timeadded']);?>
 </td>
 <td valign="top">
 <?=$ILPHP->this->history_get_reason($ILPHP->i);
 if($ILPHP->i['x']){;?><br><?=$ILPHP->this->history_get_reason($ILPHP->i['x'], strtotime($ILPHP->i['timeadded']) - strtotime($ILPHP->i['x']['timeadded'])); } ?>
 </td>
 </tr>
 <? } ?>
 </table>
 <?if(IS_LOGGED_IN){;?>
 <h3><?=htmlspecialchars(lang::_LS_get_define('533a1b89'))?></h3>
 <form method="post" action="/<?=LANG;?>/wiki/<?=wiki_urlencode($ILPHP->this->page['name']);?>/history/#0" onsubmit="return iC(this, '~.module-item');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="new_ticket">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('history');?>" value="">
 <?=htmlspecialchars(lang::_LS_get_define('70b76282'))?><br>
 <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('ticket');?>" style="width:100%"><br>
 <input type="submit" class="button" value="<?=htmlspecialchars(lang::_LS_get_define('dfc75f6c'))?>">
 </form>
 <? } ;
 if(has_privilege('wiki_mod')){;
 if($ILPHP->unsighted_changes){;?>
 <h3><?=htmlspecialchars(lang::_LS_get_define('21dd3eb8'))?></h3>
 <form method="post" action="/<?=LANG;?>/wiki/<?=wiki_urlencode($ILPHP->wiki);?>/" onsubmit="return iC(this, '~.module-item');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="history_sighted">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('history_sighted');?>" value="1">
 <?=htmlspecialchars(lang::_LS_get_define('eb4e53ea'))?><br>
 <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('reason');?>" style="width:50%"> <input type="submit" class="button" value="<?=htmlspecialchars(lang::_LS_get_define('2bd80707'))?>">
 </form>
 <? } ;
 if($ILPHP->page['locked']){;?>
 <h3><?=htmlspecialchars(lang::_LS_get_define('76c13bc7'))?></h3>
 <form method="post" action="/<?=LANG;?>/wiki/<?=wiki_urlencode($ILPHP->wiki);?>/" onsubmit="return iC(this, '~.module-item');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="unlock_page">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('unlock_page');?>" value="1">
 <?=htmlspecialchars(lang::_LS_get_define('e128afcf'))?><br>
 <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('reason');?>" style="width:50%"> <input type="submit" class="button" value="<?=htmlspecialchars(lang::_LS_get_define('7299171c'))?>">
 </form>
 <?}else{;?>
 <h3><?=htmlspecialchars(lang::_LS_get_define('1df3953b'))?></h3>
 <form method="post" action="/<?=LANG;?>/wiki/<?=wiki_urlencode($ILPHP->wiki);?>/" onsubmit="return iC(this, '~.module-item');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="lock_page">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('lock_page');?>" value="1">
 <?=htmlspecialchars(lang::_LS_get_define('4f3313a3'))?><br>
 <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('reason');?>" style="width:50%"> <input type="submit" class="button" value="<?=htmlspecialchars(lang::_LS_get_define('6cf4d380'))?>">
 </form>
 <? } ;
 } ;
 if(has_privilege('wiki_admin')){;?>
 <h3><?=htmlspecialchars(lang::_LS_get_define('a5f8bc9a'))?></h3>
 <form method="post" action="/<?=LANG;?>/wiki/<?=wiki_urlencode($ILPHP->wiki);?>/rename_page/#0" onsubmit="return iC(this, '~.module-item');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="rename_page">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('rename_page');?>" value="1">
 <?=htmlspecialchars(lang::_LS_get_define('eedd8565'))?><br>
 <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('name');?>" style="width:50%"> <input type="submit" class="button" value="<?=htmlspecialchars(lang::_LS_get_define('37af944d'))?>">
 </form>
 <h3><?=htmlspecialchars(lang::_LS_get_define('09c2fb3c'))?></h3>
 <form method="post" action="/<?=LANG;?>/wiki/<?=wiki_urlencode($ILPHP->wiki);?>/change_language/" onsubmit="return iC(this, '~.module-item');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="change_language">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('change_language');?>" value="">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('lang');?>" value="<?if($ILPHP->page['lang'] == 'de'){;?>en<?}else{;?>de<? } ?>">
 <input type="submit" class="button" value="<?if($ILPHP->page['lang'] == 'de'){;echo htmlspecialchars(lang::_LS_get_define('12ae19b0'));}else{;echo htmlspecialchars(lang::_LS_get_define('9a6bdacb')); } ?>">
 </form>
 <h3><?=htmlspecialchars(lang::_LS_get_define('0cb443db'))?></h3>
 <form method="post" action="/<?=LANG;?>/wiki/<?=wiki_urlencode($ILPHP->wiki);?>/delete_article/" onsubmit="return iC(this, '~.module-item');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="delete_article">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('delete_article');?>" value="">
 <input type="checkbox" name="<?=$ILPHP->IMODULE_POST_VAR('confirm');?>" id="DeleteArticle<?=htmlspecialchars($ILPHP->wiki);?>Confirm"><label for="DeleteArticle<?=htmlspecialchars($ILPHP->wiki);?>Confirm" class="quiet"> <?=htmlspecialchars(lang::_LS_get_define('f14e657d'))?></label><br>
 <input type="submit" class="button" value="<?=htmlspecialchars(lang::_LS_get_define('b99ea249'))?>">
 </form>
 <? } ;
 
 }elseif($ILPHP->action == 'HISTORY_SET'){;?>
 <h2 class="firstHeading"><?=htmlspecialchars(lang::_LS_get_define('3743ab60'))?></h2>
 <form method="post" action="/<?=LANG;?>/wiki/<?=wiki_urlencode($ILPHP->wiki);?>/history/<?=$ILPHP->history;?>/set/" onsubmit="return iC(this, '~.module-item');" style="margin-bottom:5px;">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="set_history">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('history');?>" value="<?=$ILPHP->history;?>">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('set');?>" value="">
 <?=htmlspecialchars(lang::_LS_get_define('7c873dbb'))?>: <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('reason');?>" style="width:50%">
 <input type="submit" class="button" value="<?=htmlspecialchars(lang::_LS_get_define('76f4b80c'))?>">
 </form>
 
 <?}elseif($ILPHP->action == 'ARTICLE_DELETED'){;?>
 <h2 class="firstHeading"><?=htmlspecialchars(lang::_LS_get_define('8a1c1d33'))?></h2>
 <div class="info"><?=htmlspecialchars(lang::_LS_get_define('71f12d15'))?></div>
 </form>
 
 <?}elseif($ILPHP->action == 'RENAME_PAGE'){;?>
 <h2 class="firstHeading"><?=htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('00f1dd42'),$ILPHP->page['name'],$ILPHP->newname))?></h2>
 <?$ILPHP->ilphp_display('wiki.php.system_messages.ilp', -1, "", true);;
 echo htmlspecialchars(lang::_LS_get_define('2c88621c'))?>
 <ul>
 <li><?=lang::_handle_template_string(lang::_LS_get_define('bf556e31'),htmlspecialchars(LANG),htmlspecialchars(wiki_urlencode($ILPHP->newname)),htmlspecialchars($ILPHP->newname))?></li>
 </ul>
 <?if($ILPHP->num){;?>
 <h3>
 <?if($ILPHP->num == 1){;echo htmlspecialchars(lang::_LS_get_define('78ff67f6'));
 }else{;echo htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('4feab680'),number_format($ILPHP->num,''?'':0,',','.')));
 } ?>
 </h3>
 <ul>
 <li><a href="/<?=LANG;?>/wiki/<?=wiki_urlencode($ILPHP->wiki);?>/rename_page/<?=wiki_urlencode($ILPHP->newname);?>/change_links/"><?=htmlspecialchars(lang::_LS_get_define('fc86c5ab'))?></a></li>
 <li><a href="/<?=LANG;?>/wiki/<?=wiki_urlencode($ILPHP->wiki);?>/rename_page/<?=wiki_urlencode($ILPHP->newname);?>"><?=htmlspecialchars(lang::_LS_get_define('e1efc3ae'))?></a></li>
 </ul>
 <?}else{;?>
 <h3><?=htmlspecialchars(lang::_LS_get_define('03964f5d'))?></h3>
 <a href="/<?=LANG;?>/wiki/<?=wiki_urlencode($ILPHP->wiki);?>/rename_page/<?=wiki_urlencode($ILPHP->newname);?>/"><?=htmlspecialchars(lang::_LS_get_define('64e1fecd'))?></a>
 <? } ;
 
 }elseif($ILPHP->action == 'COMPARE'){;?>
 <h2 class="firstHeading"><?=htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('9f01eb97'),$ILPHP->page['name']))?></h2>
 <?$ILPHP->ilphp_display('wiki.php.system_messages.ilp', -1, "", true);?>
 <table style="width:100%">
 <tr>
 <td width="20">&nbsp;</td>
 <td>&nbsp;</td>
 <td width="20">&nbsp;</td>
 <td>&nbsp;</td>
 </tr>
 <tr>
 <td class="diff-blockheader" colspan="2" style="text-align:center;">
 <a href="/<?=LANG;?>/wiki/<?=wiki_urlencode($ILPHP->wiki);?>/history/<?=$ILPHP->compare['id'];?>/"><?=htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('7fc2dd5c'),default_time_format($ILPHP->compare['timeadded'])))?></a> <?if($ILPHP->page['history'] == $ILPHP->compare['id']){;?> (Aktuell Version)<?}elseif(has_privilege('wiki_mod')){;?> (<a href="/<?=LANG;?>/wiki/<?=wiki_urlencode($ILPHP->wiki);?>/history/<?=$ILPHP->compare['id'];?>/set/"><?=htmlspecialchars(lang::_LS_get_define('252e0a29'))?></a>)<? } ?><br>
 <?=lang::_handle_template_string(lang::_LS_get_define('d767ddcc'),user($ILPHP->compare['user'])->html(-1))?>
 </td>
 <td class="diff-blockheader" colspan="2" style="text-align:center;">
 <a href="/<?=LANG;?>/wiki/<?=wiki_urlencode($ILPHP->wiki);?>/history/<?=$ILPHP->with['id'];?>/"><?=htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('7fc2dd5c'),default_time_format($ILPHP->with['timeadded'])))?></a> <?if($ILPHP->page['history'] == $ILPHP->with['id']){;?> (<?=htmlspecialchars(lang::_LS_get_define('ce3679f0'))?>)<?}elseif(has_privilege('wiki_mod')){;?> (<a href="/<?=LANG;?>/wiki/<?=wiki_urlencode($ILPHP->wiki);?>/history/<?=$ILPHP->with['id'];?>/set/"><?=htmlspecialchars(lang::_LS_get_define('252e0a29'))?></a>)<? } ?><br>
 <?=lang::_handle_template_string(lang::_LS_get_define('d767ddcc'),user($ILPHP->with['user'])->html(-1))?>
 </td>
 </tr>
 <?=$ILPHP->diff;?>
 </table>
 
 <?}elseif($ILPHP->action == 'EDIT'){;?>
 <h2 class="firstHeading"><?if($ILPHP->page['locked'] and !has_privilege('wiki_mod')){;echo htmlspecialchars(lang::_LS_get_define('3fbcbac8'));}else{;echo htmlspecialchars(lang::_LS_get_define('b8e319fd')); } ?> <?=htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('8618839e'),$ILPHP->page['name']))?></h2>
 <?if($ILPHP->history){;?><div class="headingComment"><?=htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('7fc2dd5c'),default_time_format($ILPHP->history['timeadded'])))?></div><? } ;
 $ILPHP->ilphp_display('wiki.php.system_messages.ilp', -1, "", true);;
 if($ILPHP->page['locked'] and !has_privilege('wiki_mod')){;?>
 <pre><?=str_replace("\n", '<br>', htmlspecialchars($ILPHP->page['content']));?></pre>
 <?}else{;
 if($ILPHP->page['history'] and $ILPHP->history['id'] == $ILPHP->page['history'] and (!$ILPHP->page['locked'] or has_privilege('wiki_mod'))){;?>
 <div class="info">
 <?=lang::_handle_template_string(lang::_LS_get_define('b4ec15da'),htmlspecialchars(LANG),htmlspecialchars(wiki_urlencode($ILPHP->page['name'])))?>
 </div>
 <? } ?>
 <form method="post" action="/<?=LANG;?>/wiki/<?=wiki_urlencode($ILPHP->wiki);?>/edit/<?if($ILPHP->history){;echo $ILPHP->history['id'];?>/<? } ?>" onsubmit="return iC_confirm('<?=htmlspecialchars(lang::_LS_get_define('28c26efd'))?>', this, '~.module-item');" style="text-align:center;margin-bottom:5px;">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="edit">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('edit');?>" value="<?if($ILPHP->history){;echo $ILPHP->history['id']; } ?>">
 <textarea name="<?=$ILPHP->IMODULE_POST_VAR('content');?>" class="wikiedit" style="width:100%;height:350px;"><?=htmlspecialchars($ILPHP->page['content']);?></textarea>
 <?if($ILPHP->page['id']){;?><p><?=htmlspecialchars(lang::_LS_get_define('48a13af8'))?> <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('reason');?>" style="width:50%;"></p><? } ?>
 <input type="submit" class="big-button" value="<?=htmlspecialchars(lang::_LS_get_define('76f4b80c'))?>">
 </form>
 <h2 class="firstHeading"><?=htmlspecialchars(lang::_LS_get_define('d49e4e67'))?></h2>
 <div id="wiki_preview">
 <?$ILPHP->ws = wikicode::parse($ILPHP->page['name'], $ILPHP->page['content']);
 echo $ILPHP->ws->output;?>
 </div>
 <? } ;
 
 }elseif($ILPHP->action == 'PAGES'){;?>
 <h2 class="firstHeading">
 <?if($ILPHP->type == "Kategorie:" or $ILPHP->type == "Category:"){;echo htmlspecialchars(lang::_LS_get_define('0c8f3295'));
 }elseif($ILPHP->category){;echo htmlspecialchars(lang::_LS_get_define('df965535'));echo htmlspecialchars($ILPHP->category);
 }else{;echo htmlspecialchars(lang::_LS_get_define('76d6d85b'));
 } ?>
 </h2>
 <?$ILPHP->ilphp_display('wiki.php.system_messages.ilp', -1, "", true);;
 if($ILPHP->pages){;
 $ILPHP->col = 1;
 $ILPHP->row = 2;?>
 <div class="pagescol_first">
 <?foreach($ILPHP->pages as $ILPHP->c=>$ILPHP->names){;
 $ILPHP->foreach_name=0;foreach($ILPHP->names as $ILPHP->name){$ILPHP->foreach_name++;;
 
 if($ILPHP->foreach_name == 1){;
 if(count($ILPHP->names) > 3 and $ILPHP->row + 2 + 3 >= $ILPHP->rows_per_col){;$ILPHP->row = $ILPHP->rows_per_col;
 }else{;$ILPHP->row += 2;
 } ;
 } ;
 
 if(++$ILPHP->row >= $ILPHP->rows_per_col and $ILPHP->col < $ILPHP->this->WIKI_SPEZIAL_PAGES_COLS){;?>
 </div>
 <div class="pagescol<?if(++$ILPHP->col == $ILPHP->this->WIKI_SPEZIAL_PAGES_COLS){;?>_last<? } ?>">
 <h3><?=htmlspecialchars($ILPHP->c);?></h3>
 <?$ILPHP->row = 2;
 }elseif($ILPHP->foreach_name == 1){;?>
 <h3><?=htmlspecialchars($ILPHP->c);?></h3>
 <? } ?>
 <a href="/<?=LANG;?>/wiki/<?=$ILPHP->type;echo wiki_urlencode($ILPHP->name);?>/"><?=htmlspecialchars($ILPHP->name);?></a><br>
 <? } ;
 } ?>
 </div>
 <div style="clear:both;"></div>
 <? } ;
 
 }elseif($ILPHP->action == 'ADMIN_LASTEST_CHANGES'){;?>
 <h2 class="firstHeading"><?=htmlspecialchars(lang::_LS_get_define('a3688cdc'))?></h2>
 <?$ILPHP->ilphp_display('wiki.php.system_messages.ilp', -1, "", true);;
 while($ILPHP->i = $ILPHP->pages->fetch_assoc()){;?>
 <h3><a href="/<?=$ILPHP->i['lang'];?>/wiki/<?=wiki_urlencode($ILPHP->i['name']);?>/"><?=htmlspecialchars($ILPHP->i['name']);?></a></h3>
 <?$ILPHP->this->admin_lastest_changes_query_page();?>
 <table border="1" width="100%" class="history">
 <tr>
 <th width="120" style="text-align:center;"><?=htmlspecialchars(lang::_LS_get_define('b5c29d51'))?></th>
 <th width="110" style="text-align:center;"><?=htmlspecialchars(lang::_LS_get_define('b3ebd9d2'))?></th>
 <th><?=htmlspecialchars(lang::_LS_get_define('9af9cbbd'))?></th>
 </tr>
 <?while($ILPHP->j = $ILPHP->this->history_changes_fetch($ILPHP->changes)){;?>
 <tr class="whist-<?=$ILPHP->j['history'];?>" onmouseover="whist2(<?=$ILPHP->j['history'];?>);" onmouseout="whist2c();">
 <td valign="top" align="center">
 <?if(!$ILPHP->j['history']){;?>&nbsp;
 <?}elseif(!$ILPHP->i['history'] or $ILPHP->j['history'] == $ILPHP->i['history']){;echo htmlspecialchars(lang::_LS_get_define('dcb4fb48'));
 }else{;?><a href="/<?=$ILPHP->i['lang'];?>/wiki/<?=wiki_urlencode($ILPHP->i['name']);?>/compare/<?=$ILPHP->i['history'];?>/with/<?=$ILPHP->j['history'];?>/"><?=htmlspecialchars(lang::_LS_get_define('dcb4fb48'))?></a>
 <? } ;
 if(has_privilege('wiki_admin')){;if($ILPHP->j['history']){;?>| <? } ?><a href="javascript:iC_confirm('<?=htmlspecialchars(lang::_LS_get_define('28c26efd'))?>', '<?=$ILPHP->IMODULE_POST_VAR('wiki');?>=<?=wiki_urlencode($ILPHP->i['name']);?>&<?=$ILPHP->IMODULE_POST_VAR('delete_log');?>=<?=$ILPHP->j['id'];?>', '~.module-item');void(0);">X</a><? } ?>
 </td>
 <td valign="top" align="center">
 <?if($ILPHP->j['history']){;?><a href="/<?=$ILPHP->i['lang'];?>/wiki/<?=wiki_urlencode($ILPHP->i['name']);?>/history/<?=$ILPHP->j['history'];?>/"><?=htmlspecialchars($ILPHP->j['history_timeadded']);?></a><br><? } ;
 echo timeago($ILPHP->j['timeadded']);?>
 </td>
 <td valign="top">
 <?=$ILPHP->this->history_get_reason($ILPHP->j);
 if($ILPHP->j['x']){;?><br><?=$ILPHP->this->history_get_reason($ILPHP->j['x'], strtotime($ILPHP->j['timeadded']) - strtotime($ILPHP->j['x']['timeadded'])); } ?>
 </td>
 </tr>
 <? } ?>
 </table>
 <? } ;
 
 }elseif($ILPHP->action == 'ADMIN_UNACTIVATED_ARTICLES'){;?>
 <h2 class="firstHeading"><?=htmlspecialchars(lang::_LS_get_define('db59f08d'))?></h2>
 <?$ILPHP->ilphp_display('wiki.php.system_messages.ilp', -1, "", true);;
 if(!$ILPHP->pages->num_rows){;?>
 <div class="info"><?=htmlspecialchars(lang::_LS_get_define('ea1ce983'))?></div>
 <?}else{;
 while($ILPHP->i = $ILPHP->pages->fetch_assoc()){;?>
 <a href="/<?=$ILPHP->i['lang'];?>/wiki/<?=wiki_urlencode($ILPHP->i['name']);?>/history/"><?=htmlspecialchars($ILPHP->i['name']);?></a> <?=timeago($ILPHP->i['lastchange']);?><br>
 <? } ;
 } ;
 
 }elseif($ILPHP->action == 'SEARCH'){;?>
 </div>
 <div class="wiki">
 <h2 class="firstHeading"><?if($ILPHP->term){;echo htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('23dcfcad'),$ILPHP->term));}else{;echo htmlspecialchars(lang::_LS_get_define('5c2e3184')); } ?></h2>
 <?$ILPHP->ilphp_display('wiki.php.system_messages.ilp', -1, "", true);?>
 <form method="post" action="/<?=LANG;?>/wiki/<?=htmlspecialchars(lang::_LS_get_define('b71bb832'))?>:<?=htmlspecialchars(lang::_LS_get_define('bf69ebf6'))?>/" onsubmit="return iC(this, '~.module-item');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="search">
 <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('q');?>" style="width:50%;" value="<?=htmlspecialchars($ILPHP->term);?>">
 <input type="submit" class="button" value="<?=htmlspecialchars(lang::_LS_get_define('76f5f934'))?>"><br>
 <input type="checkbox" id="wikiSearchDeactivated" name="<?=$ILPHP->IMODULE_POST_VAR('deactivated');?>"<?if($ILPHP->q == 'qd'){;?> checked="checked"<? } ?>><label for="wikiSearchDeactivated" class="quiet"> <?=htmlspecialchars(lang::_LS_get_define('8e667f78'))?></label>
 </form>
 <?if($ILPHP->term){;?>
 <h3><?=htmlspecialchars(lang::_LS_get_define('788c96b5'))?></h3>
 <?if(!$ILPHP->results->num_rows){;echo htmlspecialchars(lang::_LS_get_define('26a5dc5f'));
 }else{;
 if($ILPHP->num_pages > 1){;?><div class="pages"><?=create_pages($ILPHP->page, $ILPHP->num_pages - 1, '/'.LANG.'/wiki/Spezial:Suche/'.$ILPHP->q.'/'.wiki_urlencode($ILPHP->term).'/page/%s/');?></div><? } ;
 while($ILPHP->i = $ILPHP->results->fetch_assoc()){;?>
 <h4>
 <?$ILPHP->this->search_query_aliases();?>
 <a href="/<?=LANG;?>/wiki/<?=wiki_urlencode($ILPHP->i['name']);?>/"<?if(!$ILPHP->i['history']){;?> class="quiet"<? } ?>><?=htmlspecialchars($ILPHP->i['name']);?></a><?if($ILPHP->aliases->num_rows){;?>, <?$ILPHP->while_a=0;while($ILPHP->a = $ILPHP->aliases->fetch_assoc()){$ILPHP->while_a++;?><a href="/<?=LANG;?>/wiki/<?=wiki_urlencode($ILPHP->a['name']);?>/"><?=htmlspecialchars($ILPHP->a['name']);?></a><?if($ILPHP->while_a < $ILPHP->aliases->num_rows){;?>, <? } ; } ; } ?>
 </h4>
 <? } ;
 } ;
 } ;
 
 } ;
 } ?>
 </div>
 </div>
 <div class="module-footer"></div>
</div>
<?}?>