<?function ILPHP____templates_c_98b1d8fb_news_php_display_4fcbffeb_php(&$ILPHP){?><div class="module-item">
 <h1>
 <?=bookmark_engine::icon($ILPHP->url, 'news', $ILPHP->news['news_id']);
 echo $ILPHP->im_way_html();?>
 </h1>
 <div class="module-content module-news-article">
 <h2><a href="<?=htmlspecialchars($ILPHP->url);?>"><?=htmlspecialchars($ILPHP->news['name']);?></a><?if(is_newswriter()){;?> (<a href="<?=htmlspecialchars($ILPHP->url);?>edit/"><?=htmlspecialchars(lang::_LS_get_define('b8e319fd'))?></a>)<? } ?></h2>
 <div class="news-article user-entry">
 <p class="introduce">
 <?if($ILPHP->news['cover']){;?><img src="<?=htmlspecialchars($ILPHP->news['cover']);?>"><? } ;
 echo ubbcode::compile($ILPHP->news['introduce_content']);?>
 </p>
 <br>
 <p>
 <?=ubbcode::compile($ILPHP->news['content']);?>
 </p>
 <div class="clear"></div>
 <?if($ILPHP->news['source_text'] or $ILPHP->news['source_image'] or $ILPHP->news['source_video']){;?>
 <p class="news-sources">
 <?if($ILPHP->news['source_text']){;?>Text-Quellen: <?=implode(', ', array_map('ubbcode::compile', array_map('trim', explode("\n", str_replace("\r", '', $ILPHP->news['source_text'])))));?><br><? } ;
 if($ILPHP->news['source_image']){;?>Bild-Quellen: <?=implode(', ', array_map('ubbcode::compile', array_map('trim', explode("\n", str_replace("\r", '', $ILPHP->news['source_image'])))));?><br><? } ;
 if($ILPHP->news['source_video']){;?>Video-Quellen: <?=implode(', ', array_map('ubbcode::compile', array_map('trim', explode("\n", str_replace("\r", '', $ILPHP->news['source_video'])))));?><br><? } ?>
 </p>
 <? } ;
 if($ILPHP->news['tags']){;?>
 <p class="news-tags">
 Tags:
 <?$ILPHP->num = count($ILPHP->news['tags']);
 $ILPHP->foreach_tag=0;foreach($ILPHP->news['tags'] as $ILPHP->tag){$ILPHP->foreach_tag++;?>
 <a href="/<?=LANG;?>/news/overview/tag/<?=urlencode($ILPHP->tag);?>/"><?=htmlspecialchars($ILPHP->tag);?></a><?if($ILPHP->foreach_tag < $ILPHP->num){;?>, <? } ;
 } ?>
 </p>
 <? } ?>
 <div class="clear"></div>
 </div>
 <div class="news-replys">
 <?if($ILPHP->news['thread_id'] and $ILPHP->posts->num_rows){;?>
 <h3><a href="/<?=LANG;?>/thread/<?=$ILPHP->news['thread_id'];?>-<?=urlenc($ILPHP->news['name']);?>/"><?=htmlspecialchars(number_format($ILPHP->num_posts_total,''?'':0,',','.'));?> Reaktionen im Forum</a></h3>
 <?while($ILPHP->i = $ILPHP->posts->fetch_assoc()){;?>
 <table class="user-message">
 <tr>
 <td class="user-message-infos">
 <?=user($ILPHP->i['user_id'])->html();?><br>
 <?=timeago($ILPHP->i['timeadded']);?>
 </td>
 <td class="user-message-text user-entry">
 <?=ubbcode::add_smileys(ubbcode::compile(truncate($ILPHP->i['content'], 250, ' [...]'), 580));?>
 </td>
 </tr>
 </table>
 <? } ?>
 <h3><a href="/<?=LANG;?>/thread/<?=$ILPHP->news['thread_id'];?>-<?=urlenc($ILPHP->news['name']);?>/"><?=htmlspecialchars(lang::_LS_get_define('8f8d7c34'))?></a></h3>
 <?}else{;?>
 <h3><a href="/<?=LANG;?>/thread/<?=$ILPHP->news['thread_id'];?>-<?=urlenc($ILPHP->news['name']);?>/"><?=htmlspecialchars(lang::_LS_get_define('2a80f87b'))?></a></h3>
 <? } ?>
 </div>
 </div>
 <div class="module-footer"></div>
</div>
<?}?>