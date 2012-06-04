<?function ILPHP____templates_c_98b1d8fb_news_php_overview_4fcbffeb_php(&$ILPHP){?><div class="module-item">
 <h1>
 <?=$ILPHP->im_way_html();?>
 </h1>
 <div class="module-content module-news-overview">
 <?if($ILPHP->page == 1 and is_newswriter()){;?>
 <p>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC(this, '~.module-item');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="new_acticle">
 <?=htmlspecialchars(lang::_LS_get_define('93236e2b'))?>
 <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('name');?>" size="70">
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('bf712da5'))?></button>
 </form>
 </p>
 <? } ;
 if($ILPHP->news->num_rows == 0){;?>
 <p class="info">
 <?=htmlspecialchars(lang::_LS_get_define('6a980c99'))?>
 </p>
 <?}else{;
 if($ILPHP->num_pages > 1){;?><div class="pages"><?=$ILPHP->im_pages_html();?></div><? } ;
 $ILPHP->while_i=0;while($ILPHP->i = $ILPHP->news->fetch_assoc()){$ILPHP->while_i++;;
 $ILPHP->i['cover'] = image::querylinks($ILPHP->i['cover']);
 $ILPHP->i['tags'] = array_map('trim', explode_arr_list($ILPHP->i['tags']));?>
 <div class="news-article">
 <h2><a href="/<?=LANG;?>/news/<?=$ILPHP->i['news_id'];?>-<?=urlenc($ILPHP->i['name']);?>/"><?=htmlspecialchars($ILPHP->i['name']);?></a></h2>
 <?=user($ILPHP->i['user_id'])->html(-1);?> am <?=default_time_format($ILPHP->i['lastupdate']);if($ILPHP->i['thread_id']){;?> - <?=htmlspecialchars(number_format($ILPHP->i['num_replys'],''?'':0,',','.'));?> Reaktionen im <a href="/<?=LANG;?>/thread/<?=$ILPHP->i['thread_id'];?>-<?=urlenc($ILPHP->i['name']);?>/">Forum</a><? } ?>
 <p class="introduce user-entry">
 <?if($ILPHP->i['cover']){;?><a href="/<?=LANG;?>/news/<?=$ILPHP->i['news_id'];?>-<?=urlenc($ILPHP->i['name']);?>/"><img src="<?=htmlspecialchars($ILPHP->i['cover']['1']);?>"></a><? } ;
 echo ubbcode::compile($ILPHP->i['introduce_content']);?>
 </p>
 <a href="/<?=LANG;?>/news/<?=$ILPHP->i['news_id'];?>-<?=urlenc($ILPHP->i['name']);?>/" class="info" style="font-weight:normal;"><?=htmlspecialchars(lang::_LS_get_define('8dd113ee'))?></a>
 <?if($ILPHP->i['tags']){;?>
 <p>
 Tags:
 <?$ILPHP->num = count($ILPHP->i['tags']);
 $ILPHP->foreach_tag=0;foreach($ILPHP->i['tags'] as $ILPHP->tag){$ILPHP->foreach_tag++;?>
 <a href="/<?=LANG;?>/news/overview/tag/<?=urlencode($ILPHP->tag);?>/"><?=htmlspecialchars($ILPHP->tag);?></a><?if($ILPHP->foreach_tag < $ILPHP->num){;?>, <? } ;
 } ?>
 </p>
 <? } ?>
 <div class="clear"></div>
 </div>
 <? } ;
 if($ILPHP->num_pages > 1){;?><div class="pages"><?=$ILPHP->im_pages_html();?></div><? } ;
 } ?>
 </div>
 <div class="module-footer"></div>
</div>
<?}?>