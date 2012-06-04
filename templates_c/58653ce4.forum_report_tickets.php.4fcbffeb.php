<?function ILPHP____templates_c_58653ce4_forum_report_tickets_php_4fcbffeb_php(&$ILPHP){?><div class="module-item">
 <h1>
 <?=$ILPHP->im_way_html();?>
 </h1>
 <div class="module-content">
 <?if($ILPHP->posts->num_rows == 0){;?>
 <p class="info"><?=htmlspecialchars(lang::_LS_get_define('27bf0500'))?></p>
 <?}else{;?>
 <ul>
 <?while($ILPHP->i = $ILPHP->posts->fetch_assoc()){;
 $ILPHP->num = $ILPHP->post_num($ILPHP->i['thread'], $ILPHP->i['id']);?>
 <li><?if($ILPHP->is_multilang){;echo get_sitelang_flag($ILPHP->i['lang']);?> <? } ?><a href="/<?=LANG;?>/thread/<?=$ILPHP->i['thread'];?>-<?=urlenc($ILPHP->i['name']);?>/page/<?=calculate_pages($ILPHP->num, FORUM_THREAD_NUM_POSTS_PER_SITE);?>/#post<?=$ILPHP->num;?>"><?=htmlspecialchars($ILPHP->i['name']);?></a></li>
 <? } ?>
 </ul>
 <? } ?>
 </div>
 <div class="module-footer"></div>
</div>
<?}?>