<?function ILPHP____templates_c_38493f99_bookmarks_php_news_4fcbffeb_php(&$ILPHP){?><?if(!$ILPHP->news->num_rows){;?><p class="error"><?=htmlspecialchars(lang::_LS_get_define('0a2d805f'))?></p>
<?}else{;
while($ILPHP->i = $ILPHP->news->fetch_assoc()){;?>
<div style="margin:3px 0 3px 0;">
 <div style="clear:both;float:left;margin-top:-3px;"><?=bookmark_engine::icon($ILPHP->url, 'news', $ILPHP->i['news_id']);?></div>
 <a href="/<?=LANG;?>/news/<?=$ILPHP->i['news_id'];?>-<?=urlenc($ILPHP->i['name']);?>/"><?=htmlspecialchars($ILPHP->i['name']);?></a>
</div>
<? } ;
 } ?>
<?}?>