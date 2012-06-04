<?function ILPHP____templates_c_98b1d8fb_news_php_display_small_4fcbffeb_php(&$ILPHP){?><div class="module-item">
 <h1>
 <?=bookmark_engine::icon($ILPHP->url, 'news', $ILPHP->news['news_id']);
 echo $ILPHP->im_way_html();?>
 </h1>
 <div class="module-content module-news-article">
 <h2><a href="<?=htmlspecialchars($ILPHP->url);?>"><?=htmlspecialchars($ILPHP->news['name']);?></a><?if(is_newswriter()){;?> (<a href="<?=htmlspecialchars($ILPHP->url);?>edit/"><?=htmlspecialchars(lang::_LS_get_define('b8e319fd'))?></a>)<? } ?></h2>
 <div class="news-article user-entry" style="border:0;margin-bottom:0;">
 <p class="introduce">
 <?if($ILPHP->news['cover']){;?><img src="<?=htmlspecialchars($ILPHP->news['cover']);?>"><? } ;
 echo ubbcode::compile($ILPHP->news['introduce_content']);?>
 </p>
 <div class="clear"></div>
 <p><a href="<?=htmlspecialchars($ILPHP->url);?>"><?=htmlspecialchars(lang::_LS_get_define('13aed78e'))?></a></p>
 <?if($ILPHP->news['source_text'] or $ILPHP->news['source_image'] or $ILPHP->news['source_video']){;?>
 <p class="news-sources" style="border:0;">
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
 </div>
 <div class="module-footer"></div>
</div>
<?}?>