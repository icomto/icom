<?function ILPHP____templates_c_04db7353_forum_php_threads_4fcbffeb_php(&$ILPHP){?><?if($ILPHP->has_posts){;?>
<table class="forum-sections" border="1">
 <tr>
 <th class="thread-topic"><?=htmlspecialchars(lang::_LS_get_define('44265674'))?></th>
 <th class="thread-stats"><?=htmlspecialchars(lang::_LS_get_define('6fe3231d'))?></th>
 <th class="thread-stats"><?=htmlspecialchars(lang::_LS_get_define('78d95a95'))?></th>
 <th><?=htmlspecialchars(lang::_LS_get_define('bac4fee8'))?></th>
 </tr>
 <?foreach($ILPHP->rvs as $ILPHP->rv){;
 while($ILPHP->i = $ILPHP->rv->fetch_assoc()){;?>
 <tr class="forum-row<?if(in_array($ILPHP->i['thread_state'], array('sticky', 'important'))){;?> <?=$ILPHP->i['thread_state']; } ?>">
 <?if($ILPHP->i['thread_state'] == 'moved'){;?>
 <td class="thread-topic">
 <h2><?if($ILPHP->is_multilang){;echo get_sitelang_flag2($ILPHP->i['thread_lang_de'], $ILPHP->i['thread_lang_en']);?> <? } ;echo htmlspecialchars(lang::_LS_get_define('cb1d3a4b'))?> <a href="/<?=LANG;?>/thread/<?=$ILPHP->query_last_editor($ILPHP->i['firstpost_id']);?>-<?=urlenc($ILPHP->i['firstpost_name']);?>/"><?=htmlspecialchars($ILPHP->i['firstpost_name']);?></a></h2>
 <?=lang::_LS_get_define('21a4d4ee')?> <?=user($ILPHP->i['firstpost_user_id'])->html(-1);?> <?=timeago($ILPHP->i['firstpost_time']);?>
 </td>
 <td class="thread-stats">-</td>
 <td class="thread-stats">-</td>
 <td class="thread-lastpost">
 <?if($ILPHP->section['is_mod']){;?>
 <form method="post" action="/<?=LANG;?>/thread/<?=$ILPHP->i['thread_id'];?>-<?=urlenc($ILPHP->i['firstpost_name']);?>/" onsubmit="return iC_confirm('<?=htmlspecialchars(lang::_LS_get_define('28c26efd'))?>', this);">
 <input type="hidden" name="imodules/forum__thread/action" value="delete_thread">
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('5533445e'))?></button>
 </form>
 <?}else{;?>-
 <? } ?>
 </td>
 <?}else{;
 
 $ILPHP->i['thread_num_pages'] = calculate_pages($ILPHP->i['thread_num_posts'], FORUM_THREAD_NUM_POSTS_PER_SITE);?>
 <td class="thread-topic">
 <h2>
 <?if($ILPHP->is_multilang){;echo get_sitelang_flag2($ILPHP->i['thread_lang_de'], $ILPHP->i['thread_lang_en']);?> <? } ;
 if($ILPHP->i['thread_state'] == 'sticky'){;echo htmlspecialchars(lang::_LS_get_define('0a1956a8'))?> <?}elseif($ILPHP->i['thread_state'] == 'important'){;echo htmlspecialchars(lang::_LS_get_define('d4fb3a25'))?> <? } ?>
 <a href="/<?=LANG;?>/thread/<?=$ILPHP->i['thread_id'];?>-<?=urlenc($ILPHP->i['firstpost_name']);?>/"><?=htmlspecialchars($ILPHP->i['firstpost_name']);?></a><?if(!$ILPHP->i['thread_open']){;?> (<?=htmlspecialchars(lang::_LS_get_define('dd5ffbf5'))?>)<? } ?>
 </h2>
 <?if($ILPHP->i['thread_num_pages'] > 1){;?><div class="pages">( <img class="mulitpage" src="<?=STATIC_CONTENT_DOMAIN;?>/img/p.gif" alt="" title="<?=htmlspecialchars(lang::_LS_get_define('52c2cb8a'))?>"><?=create_pages(0, $ILPHP->i['thread_num_pages'] - 1, '/'.LANG.'/thread/'.urlencode($ILPHP->i['thread_id']).'-'.urlenc($ILPHP->i['firstpost_name']).'/page/%s/', false, " &nbsp;", "%s");?> )</div><? } ?>
 Er&ouml;ffnet von <?=user($ILPHP->i['firstpost_user_id'])->html(-1);?> <?=timeago($ILPHP->i['firstpost_time']);?>
 </td>
 <td class="thread-stats"><?=htmlspecialchars(number_format($ILPHP->i['thread_num_posts'],''?'':0,',','.'));?></td>
 <td class="thread-stats"><?=htmlspecialchars(number_format($ILPHP->i['thread_num_hits'],''?'':0,',','.'));?></td>
 <td class="thread-lastpost">
 Letzter Beitrag <a href="/<?=LANG;?>/thread/<?=$ILPHP->i['thread_id'];?>-<?=urlenc($ILPHP->i['firstpost_name']);?>/<?if($ILPHP->i['thread_num_pages'] > 1){;?>page/<?=$ILPHP->i['thread_num_pages'];?>/<? } ?>"><?=timeago($ILPHP->i['lastpost_time']);?></a><br>
 von <?=user($ILPHP->i['lastpost_user_id'])->html(-1);?>
 </td>
 <? } ?>
 </tr>
 <? } ;
 } ?>
</table>

<?}else{;?>
<p class="info">
 <?=htmlspecialchars(lang::_LS_get_define('bddd7cc7'))?>
</p>
<? } ;

if($ILPHP->num_pages or $ILPHP->section['allow_write']){;?>
<div class="section-footer">
 <?if($ILPHP->num_pages){;?><div class="pages"><?=$ILPHP->im_pages_html();?></div><? } ;
 if($ILPHP->section['allow_write']){;?>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>#0" onsubmit="return iC(this, '~.module-item');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="new">
 <button type="submit" class="button new-thread-button">Neuen Thread erstellen</button>
 </form>
 <? } ?>
</div>
<div class="clear"></div>
<? } ?>
<?}?>