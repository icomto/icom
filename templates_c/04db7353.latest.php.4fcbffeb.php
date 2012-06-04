<?function ILPHP____templates_c_04db7353_latest_php_4fcbffeb_php(&$ILPHP){?><div class="module-item">
 <h1><?=$ILPHP->im_way_html();?></h1>
 <div class="module-content">
 <?while($ILPHP->i = $ILPHP->threads->fetch_assoc()){;
 $ILPHP->i['pages'] = calculate_pages($ILPHP->i['thread_num_posts'], FORUM_THREAD_NUM_POSTS_PER_SITE);?>
 <a href="/<?=LANG;?>/thread/<?=$ILPHP->i['thread_id'];?>-<?=urlenc($ILPHP->i['firstpost_name']);?>/<?if($ILPHP->i['pages']>1){;?>page/<?=urlencode($ILPHP->i['pages']);?>/<? } ?>"<?if(strlen($ILPHP->i['firstpost_name']) > 80){;?> title="<?=htmlspecialchars($ILPHP->i['firstpost_name']);?>"<? } ?>><?if($ILPHP->is_multilang){;echo get_sitelang_flag2($ILPHP->i['thread_lang_de'], $ILPHP->i['thread_lang_en']);}else{;?><img src="<?=STATIC_CONTENT_DOMAIN;?>/img/dot.png" alt=""><? } ?> <?=htmlspecialchars(truncate($ILPHP->i['firstpost_name'], 80, '...', true));?></a><br>
 <? } ?>
 </div>
 <div class="module-footer"></div>
</div>
<?}?>