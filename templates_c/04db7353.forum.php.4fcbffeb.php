<?function ILPHP____templates_c_04db7353_forum_php_4fcbffeb_php(&$ILPHP){?><div class="module-item">
 <h1>
 <img class="dropdown-icon" src="<?=STATIC_CONTENT_DOMAIN;?>/img/p.gif" alt="" data-dd="ildd.def" data-dd-item-1="&lt;a href=&quot;/<?=LANG;?>/forum/0/search/&quot;&gt;<?=htmlspecialchars(lang::_LS_get_define('c2a8de01'))?>&lt;/a&gt;">
 <?=$ILPHP->im_way_html();?>
 </h1>
 <div class="module-content forum forum-overview">
 <?if($ILPHP->action == 'new'){;
 
 $ILPHP->ilphp_display('forum.php.new.ilp', -1, "", true);;
 
 }else{;
 
 if($ILPHP->section_id and $ILPHP->section['allow_content']){;
 if($ILPHP->section['is_mod'] and $ILPHP->action == 'content_edit'){;?>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC(this, '~.module-item');" class="center">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="content_save">
 <textarea class="bbcodeedit" name="<?=$ILPHP->IMODULE_POST_VAR('content');?>"><?=htmlspecialchars($ILPHP->section['content']);?></textarea>
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('76f4b80c'))?></button>
 </form>
 <?}elseif($ILPHP->section['content']){;?>
 <p><?=ubbcode::add_smileys(ubbcode::compile($ILPHP->section['content'], DISPLAY_COMMUNITY_ELEMENTS ? 491 : 616));?></p>
 <? } ;
 } ;
 
 if($ILPHP->section_id){;
 if($ILPHP->section['allow_threads']){;
 echo $ILPHP->query_child_threads();
 if($ILPHP->mods and $ILPHP->mods->num_rows){;?>
 <div class="section-mods">
 <?=htmlspecialchars(lang::_LS_get_define('1abd3b4b'));
 $ILPHP->while_i=0;while($ILPHP->mod = $ILPHP->mods->fetch_assoc()){$ILPHP->while_i++;;
 echo user($ILPHP->mod['id'])->html(-1);if($ILPHP->while_i < $ILPHP->mods->num_rows){;?>, <? } ;
 } ?>
 </div>
 <? } ;
 } ;
 $ILPHP->ilphp_display('forum.php.sections.ilp', -1, "", true);;
 if($ILPHP->section['allow_threads']){;
 $ILPHP->ilphp_display('forum.php.threads.ilp', -1, "", true);;
 } ;
 }else{;
 
 while($ILPHP->section = $ILPHP->sections->fetch_assoc()){;
 $ILPHP->ilphp_display('forum.php.sections.ilp', -1, "", true);;
 } ;
 } ;
 
 if($ILPHP->section_id and $ILPHP->section['allow_content'] and $ILPHP->section['is_mod'] and $ILPHP->action != 'content_edit'){;?>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>#0" onsubmit="return iC(this, '~.module-item');" class="center">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="content_edit">
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('99e55781'))?></button>
 </form>
 <? } ;
 
 if(has_privilege('forum_admin') and $ILPHP->section_id){;?>
 <div style="clear:both;"></div>
 <div id="forum<?=$ILPHP->section['id'];?>" style="border:2px black solid;">
 <?$ILPHP->M = iengine::GET(['admin', 'forum']);
 $ILPHP->M->parent = $ILPHP->section_id;
 echo $ILPHP->M->row($ILPHP->section);?>
 </div>
 <? } ;
 } ?>
 </div>
 <div class="module-footer"></div>
</div>
<?}?>