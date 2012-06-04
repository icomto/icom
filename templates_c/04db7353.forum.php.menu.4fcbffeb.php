<?function ILPHP____templates_c_04db7353_forum_php_menu_4fcbffeb_php(&$ILPHP){?><?if($ILPHP->threads->num_rows){;?>
<div class="menu-forum-threads">
 <?while($ILPHP->i = $ILPHP->threads->fetch_assoc()){;
 $ILPHP->i['pages'] = calculate_pages($ILPHP->i['thread_num_posts'], FORUM_THREAD_NUM_POSTS_PER_SITE);?>
 <a class="<?if($ILPHP->i['thread_lang_de']){;?> lang-de<? } ;if($ILPHP->i['thread_lang_en']){;?> lang-en<? } ?>" <?if($ILPHP->i['pages'] > 1 or $ILPHP->is_multilang or strlen($ILPHP->i['firstpost_name']) > 26){;?> data-dd="ildd.menuForum" data-dd-body="&lt;h4&gt;<?if($ILPHP->is_multilang){;echo htmlspecialchars(get_sitelang_flag2($ILPHP->i['thread_lang_de'], $ILPHP->i['thread_lang_en']));?> <? } ;echo htmlspecialchars($ILPHP->i['firstpost_name']);?>&lt;h4&gt;<?if($ILPHP->i['pages'] > 1){;echo htmlspecialchars(lang::_LS_get_define('bac19910'))?>&lt;span class=&quot;pages&quot;&gt;<?=htmlspecialchars(create_pages(0, $ILPHP->i['pages'] - 1, '/'.LANG.'/thread/'.$ILPHP->i['thread_id'].'-'.urlenc($ILPHP->i['firstpost_name']).'/page/%s/', false));?>&lt;/span&gt;<? } ?>" <? } ?>class="thread" href="/<?=LANG;?>/thread/<?=$ILPHP->i['thread_id'];?>-<?=urlenc($ILPHP->i['firstpost_name']);?>/<?if($ILPHP->i['pages'] > 1){;?>page/<?=urlencode($ILPHP->i['pages']);?>/<? } ?>"><?=htmlspecialchars(truncate($ILPHP->i['firstpost_name'],26,'...',true));?></a>
 <? } ?>
</div>
<? } ?>
<div class="MenuForumFooter">
 <p class="all-entries">
 <?if($ILPHP->namespace == 'def'){;
 if($ILPHP->threads->num_rows){;?><a href="/<?=LANG;?>/forum/latest/def/"><?=htmlspecialchars(lang::_LS_get_define('1c613748'))?></a><? } ?>
 <a href="/<?=LANG;?>/forum/"><?=htmlspecialchars(lang::_LS_get_define('c5aee43c'))?></a>
 <?}elseif($ILPHP->namespace == 'news'){;
 if($ILPHP->threads->num_rows){;?><a href="/<?=LANG;?>/forum/latest/news/"><?=htmlspecialchars(lang::_LS_get_define('1c613748'))?></a><? } ?>
 <a href="/<?=LANG;?>/forum/182-<?=htmlspecialchars(lang::_LS_get_define('b715eff4'))?>/"><?=htmlspecialchars(lang::_LS_get_define('6af2120f'))?></a>
 <?}elseif($ILPHP->namespace == 'team'){;
 if($ILPHP->threads->num_rows){;?><a href="/<?=LANG;?>/forum/latest/team/"><?=htmlspecialchars(lang::_LS_get_define('1c613748'))?></a><? } ?>
 <a href="/<?=LANG;?>/forum/1-<?=htmlspecialchars(lang::_LS_get_define('5e1539f0'))?>/"><?=htmlspecialchars(lang::_LS_get_define('0b63be12'))?></a>
 <? } ?>
 </p>
</div>
<?}?>