<?function ILPHP____templates_c_04db7353_search_php_4fcbffeb_php(&$ILPHP){?><div class="module-item">
 <h1>
 <?=$ILPHP->im_way_html();?>
 </h1>
 <div class="module-content forum forum-search">
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC(this);">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="search">
 <table>
 <tr>
 <td>
 <p>
 <?=htmlspecialchars(lang::_LS_get_define('d4ab7c05'))?><br>
 <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('search');?>" value="<?=htmlspecialchars($ILPHP->term);?>">
 </p>
 <p>
 <?=htmlspecialchars(lang::_LS_get_define('26550e93'))?><br>
 <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('user');?>" value="<?=htmlspecialchars($ILPHP->user);?>">
 </p>
 <p>
 <select name="<?=$ILPHP->IMODULE_POST_VAR('order_by');?>">
 <option value="score"<?if($ILPHP->order_by == 'score'){;?> selected="selected"<? } ?>><?=htmlspecialchars(lang::_LS_get_define('9ee38fc6'))?></option>
 <option value="hits"<?if($ILPHP->order_by == 'hits'){;?> selected="selected"<? } ?>><?=htmlspecialchars(lang::_LS_get_define('71ded759'))?></option>
 <option value="time"<?if($ILPHP->order_by == 'time'){;?> selected="selected"<? } ?>><?=htmlspecialchars(lang::_LS_get_define('89f0deba'))?></option>
 </select>
 </p>
 <p>
 <select name="<?=$ILPHP->IMODULE_POST_VAR('group_by');?>">
 <option value="threads"<?if($ILPHP->group_by == 'threads'){;?> selected="selected"<? } ?>><?=htmlspecialchars(lang::_LS_get_define('0d817b9a'))?></option>
 <option value="posts"<?if($ILPHP->group_by == 'posts'){;?> selected="selected"<? } ?>><?=htmlspecialchars(lang::_LS_get_define('c6da7fec'))?></option>
 </select>
 </p>
 <p>
 <input type="checkbox" id="ModuleForumSearchNames" name="<?=$ILPHP->IMODULE_POST_VAR('names');?>"<?if($ILPHP->names){;?> checked="checked"<? } ?>><label for="ModuleForumSearchNames"> <?=htmlspecialchars(lang::_LS_get_define('d47344d3'))?></label><br>
 <input type="checkbox" id="ModuleForumSearchContent" name="<?=$ILPHP->IMODULE_POST_VAR('content');?>"<?if($ILPHP->content){;?> checked="checked"<? } ?>><label for="ModuleForumSearchContent"> <?=htmlspecialchars(lang::_LS_get_define('95b49eee'))?></label>
 </p>
 </td>
 <td>
 <p>
 <?=htmlspecialchars(lang::_LS_get_define('13c3d4d2'))?><br>
 <select name="<?=$ILPHP->IMODULE_POST_VAR('forum');?>[]" multiple="multiple">
 <option value="0"<?if(count($ILPHP->forum) == 1 and $ILPHP->forum['0'] == 0){;?> selected="selected"<? } ?>><?=htmlspecialchars(lang::_LS_get_define('e2f100b2'))?></option>
 <?$ILPHP->selected = 0;
 while($ILPHP->i = $ILPHP->forum_sections->fetch_assoc()){;
 if($ILPHP->i['selected']){;$ILPHP->selected = $ILPHP->i['level'] + 1;
 }elseif($ILPHP->i['level'] < $ILPHP->selected){;$ILPHP->selected = 0;
 } ?>
 <option value="<?=$ILPHP->i['section_id'];?>"<?if($ILPHP->selected){;?> selected="selected"<? } ?>><?=str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $ILPHP->i['level']);?> <?=htmlspecialchars($ILPHP->i['name']);?></option>
 <? } ?>
 </select>
 </p>
 <p>
 <input type="checkbox" id="ModuleForumSearchSubs" name="<?=$ILPHP->IMODULE_POST_VAR('sub');?>"<?if($ILPHP->search_sub){;?> checked="checked"<? } ?>><label for="ModuleForumSearchSubs"> <?=htmlspecialchars(lang::_LS_get_define('05edd864'))?></label>
 </p>
 </td>
 </tr>
 </table>
 <button type="submit" class="big-button center"><?=htmlspecialchars(lang::_LS_get_define('76f5f934'))?></button>
 </form>
 </div>
 <div class="module-footer"></div>
</div>
<?if(@$ILPHP->results or @$ILPHP->user_not_found_error){;?>
<div class="module-item">
 <h1>
 <?if($ILPHP->term){;?><a href="<?=htmlspecialchars($ILPHP->url);?>"><?=htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('de437767'),$ILPHP->term))?></a><? } ;
 if($ILPHP->term and $ILPHP->user and !@$ILPHP->user_not_found_error){;?> - <? } ;
 if($ILPHP->user and !@$ILPHP->user_not_found_error){;echo lang::_handle_template_string(htmlspecialchars(lang::_LS_get_define('48250d16')),user($ILPHP->user_id)->html(-1)); } ;
 if(@$ILPHP->page > 1){;?> - <a href="<?=htmlspecialchars($ILPHP->url);?>page/<?=$ILPHP->page;?>/"><?=htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('c0e84bde'),$ILPHP->page))?></a><? } ?>
 </h1>
 <div class="module-content forum forum-search-results">
 <?if(@$ILPHP->num_pages > 1){;?><div class="pages"><?=$ILPHP->im_pages_html();?></div><? } ;
 if(@$ILPHP->user_not_found_error){;?><p class="error"><?=htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('57b68cd5'),$ILPHP->user))?></p>
 <?}elseif(!$ILPHP->num_rows){;?><p class="error"><?=htmlspecialchars(lang::_LS_get_define('01377e31'))?></p>
 <?}else{;?>
 <table border="1">
 <?while($ILPHP->i = $ILPHP->results->fetch_assoc()){;
 $ILPHP->i = $ILPHP->search_query_result($ILPHP->i['post_id']);?>
 <tr class="forum-row forum-row-nohover">
 <td class="post-content">
 <h3><?=get_sitelang_flag2($ILPHP->i['lang_de'], $ILPHP->i['lang_en']);?> <a href="/<?=LANG;?>/thread/<?=$ILPHP->i['thread_id'];?>-<?=urlenc($ILPHP->i['post_name']);?>/"><?=htmlspecialchars(truncate($ILPHP->i['post_name'], 80));?></a><?if(!$ILPHP->i['open']){;?><span style="font-weight:normal;"> (<?=htmlspecialchars(lang::_LS_get_define('dd5ffbf5'))?>)</span><? } ?></h3>
 <div class="user-entry">
 <?=ubbcode::add_smileys(ubbcode::compile(truncate(preg_replace('~\r?\n\r?\n~', '\n', $ILPHP->i['post_content']), 450, ' [...]'), 300, 300));?>
 </div>
 </td>
 <td>
 <?=user($ILPHP->i['user_id'])->html(1);?><br>
 <?=timeago($ILPHP->i['post_timeadded']);?>
 </td>
 </tr>
 <? } ?>
 </table>
 <? } ;
 if(@$ILPHP->num_pages > 1){;?><div class="pages"><?=$ILPHP->im_pages_html();?></div><? } ?>
 </div>
 <div class="module-footer"></div>
</div>
<? } ?>
<?}?>