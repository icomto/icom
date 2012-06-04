<?function ILPHP____templates_c_38493f99_bookmarks_php_wiki_4fcbffeb_php(&$ILPHP){?><?if(!$ILPHP->wiki->num_rows){;?><p class="error"><?=htmlspecialchars(lang::_LS_get_define('0a2d805f'))?></p>
<?}else{;
while($ILPHP->i = $ILPHP->wiki->fetch_assoc()){;?>
<div style="margin:3px 0 3px 0;">
 <div style="clear:both;float:left;margin-top:-3px;"><?=bookmark_engine::icon($ILPHP->url, 'wiki', $ILPHP->i['id']);?></div>
 <a href="/<?=$ILPHP->i['lang'];?>/wiki/<?=wiki_urlencode($ILPHP->i['name']);?>/"><?=htmlspecialchars($ILPHP->i['name']);?></a>
</div>
<? } ;
 } ?>
<?}?>