<?function ILPHP____templates_c_98b1d8fb_news_php_edit_4fcbffeb_php(&$ILPHP){?><div class="module-item">
 <h1>
 <?=$ILPHP->im_way_html();?>
 </h1>
 <div class="module-content module-news-article">
 <form method="post" onsubmit="return iC(this, '~.module-item');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="edit">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('news_id');?>" value="<?=$ILPHP->news['news_id'];?>">
 
 <p><strong>Titel:</strong> <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('name');?>" value="<?=htmlspecialchars($ILPHP->news['name']);?>" size="70"></p>
 <p><strong>Cover:</strong> <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('cover');?>" value="<?=htmlspecialchars($ILPHP->news['cover']);?>" size="70"> (Direktlink angeben!)</p>
 <p><strong>Tags:</strong> <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('tags');?>" value="<?=htmlspecialchars($ILPHP->news['tags']);?>" size="70"> (durch Komma getrennte Liste)</p>
 <p>
 <strong>Status:</strong>
 <select name="<?=$ILPHP->IMODULE_POST_VAR('status');?>">
 <option value="edit"<?if($ILPHP->news['status'] == 'open'){;?> selected="selected"<? } ?>><?=htmlspecialchars(lang::_LS_get_define('0b63d10a'))?></option>
 <option value="public"<?if($ILPHP->news['status'] == 'public'){;?> selected="selected"<? } ?>><?=htmlspecialchars(lang::_LS_get_define('219faa1c'))?></option>
 </select>
 </p>
 <p>
 <strong>News Pushen (zum Beispiel wenn ein wichtiges Update gemacht worden ist):</strong>
 <input type="checkbox" name="<?=$ILPHP->IMODULE_POST_VAR('push_it');?>">
 </p>
 <p>
 <strong>Zugeh&ouml;riger Forenthread:</strong>
 <?if($ILPHP->news['thread_id']){;?><a href="/<?=LANG;?>/thread/<?=$ILPHP->news['thread_id'];?>-<?=urlenc($ILPHP->news['name']);?>/">klick</a>
 <?}else{;?>wird beim ver&ouml;ffentlichen automatisch erstellt
 <? } ?>
 </p>
 <p>
 <strong>Einleitungstext:</strong><br>
 <textarea name="<?=$ILPHP->IMODULE_POST_VAR('introduce_content');?>" class="bbcodeedit" style="width:100%;height:150px;"><?=htmlspecialchars($ILPHP->news['introduce_content']);?></textarea>
 </p>
 <p>
 <strong>Text:</strong><br>
 <textarea name="<?=$ILPHP->IMODULE_POST_VAR('content');?>" class="bbcodeedit" style="width:100%;height:400px;"><?=htmlspecialchars($ILPHP->news['content']);?></textarea>
 </p>
 <p>
 <strong>Textquellen:</strong> (BB-Code im Format <strong>[url=http://link.zur.quelle/]Name der Quelle[/url]</strong> - eine Quelle pro Zeile)<br>
 <textarea name="<?=$ILPHP->IMODULE_POST_VAR('source_text');?>" style="width:100%;height:50px;"><?=htmlspecialchars($ILPHP->news['source_text']);?></textarea>
 </p>
 <p>
 <strong>Bildquellen:</strong> (BB-Code im Format <strong>[url=http://link.zur.quelle/]Name der Quelle[/url]</strong> - eine Quelle pro Zeile)<br>
 <textarea name="<?=$ILPHP->IMODULE_POST_VAR('source_image');?>" style="width:100%;height:50px;"><?=htmlspecialchars($ILPHP->news['source_image']);?></textarea>
 </p>
 <p>
 <strong>Videoquellen:</strong> (BB-Code im Format <strong>[url=http://link.zur.quelle/]Name der Quelle[/url]</strong> - eine Quelle pro Zeile)<br>
 <textarea name="<?=$ILPHP->IMODULE_POST_VAR('source_video');?>" style="width:100%;height:50px;"><?=htmlspecialchars($ILPHP->news['source_video']);?></textarea>
 </p>
 <p><button type="submit" class="big-button center"><?=htmlspecialchars(lang::_LS_get_define('76f4b80c'))?></button></p>
 </form>
 </div>
 <div class="module-footer"></div>
</div>
<?}?>