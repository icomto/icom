<?function ILPHP____templates_c_04db7353_forum_php_sections_4fcbffeb_php(&$ILPHP){?><?$ILPHP->childs = $ILPHP->query_child_sections($ILPHP->section['id']);
if($ILPHP->childs->num_rows){;
$ILPHP->child_index = 0;
if($ILPHP->sections->num_rows > 1){;?>
<div class="root-section-header">
 <a href="/<?=LANG;?>/forum/<?=$ILPHP->section['section_id'];?>-<?=urlenc($ILPHP->section['name']);?>/"><?=htmlspecialchars($ILPHP->section['name']);?></a>
</div>
<? } ?>
<table class="forum-sections" border="1">
 <tr class="head">
 <th class="section-name"><?=htmlspecialchars(lang::_LS_get_define('18792b1b'))?></th>
 <th><?=htmlspecialchars(lang::_LS_get_define('bac4fee8'))?></th>
 </tr>
 <?while($ILPHP->i = $ILPHP->childs->fetch_assoc()){;
 $ILPHP->child_index++;
 $ILPHP->lastthread = $ILPHP->i;?>
 <tr class="forum-row">
 <td class="section-name">
 <h2><a href="/<?=LANG;?>/forum/<?=$ILPHP->i['section_id'];?>-<?=urlenc($ILPHP->i['section_name']);?>/"><?=htmlspecialchars($ILPHP->i['section_name']);?></a></h2>
 <?if($ILPHP->i['section_description']){;echo ubbcode::compile($ILPHP->i['section_description']);?><br><? } ;
 if($ILPHP->i['childs']){;?>
 <div class="sub-sections">
 <ul>
 <?for($ILPHP->j = 0; $ILPHP->j < $ILPHP->i['childs'] and $ILPHP->k = $ILPHP->childs->fetch_assoc(); $ILPHP->j++){;
 if($ILPHP->k['section_parent'] == $ILPHP->section['id']){;
 $ILPHP->childs->data_seek($ILPHP->child_index);
 break;
 } ;
 $ILPHP->child_index++;
 $ILPHP->i['section_num_threads'] += $ILPHP->k['section_num_threads'];
 $ILPHP->i['section_num_posts'] += $ILPHP->k['section_num_posts'];
 if($ILPHP->lastthread['lastpost_time'] < $ILPHP->k['lastpost_time']){;$ILPHP->lastthread = $ILPHP->k; } ;
 if($ILPHP->k['section_parent'] == $ILPHP->i['section_id']){;?>
 <li style="margin-right:1%;width:31%;float:left;padding-left:0;"><a href="/<?=LANG;?>/forum/<?=$ILPHP->k['section_id'];?>-<?=urlenc($ILPHP->k['section_name']);?>/"><?=htmlspecialchars($ILPHP->k['section_name']);?></a></li>
 <? } ;
 } ?>
 </ul>
 <div class="clear" style="height:10px;"></div>
 </div>
 <? } ?>
 <div class="section-stats"><?if($ILPHP->i['section_num_threads'] == 1){;echo htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('ce262365'),number_format($ILPHP->i['section_num_threads'],''?'':0,',','.')));}else{;echo htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('50350a95'),number_format($ILPHP->i['section_num_threads'],''?'':0,',','.'))); } ?>, <?if($ILPHP->i['section_num_posts'] == 1){;echo htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('a09b0aec'),number_format($ILPHP->i['section_num_posts'],''?'':0,',','.')));}else{;echo htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('5a5ef317'),number_format($ILPHP->i['section_num_posts'],''?'':0,',','.'))); } ?></div>
 </td>
 <td>
 <?if($ILPHP->lastthread['lastthread_id']){;
 if($ILPHP->is_multilang){;echo get_sitelang_flag2($ILPHP->lastthread['lastthread_lang_de'], $ILPHP->lastthread['lastthread_lang_en']);?> <? } ;
 if($ILPHP->lastthread['lastthread_state'] == 'moved'){;echo htmlspecialchars(lang::_LS_get_define('cb1d3a4b'))?> <? } ?>
 <a href="/<?=LANG;?>/thread/<?=$ILPHP->lastthread['lastthread_id'];?>-<?=urlenc($ILPHP->lastthread['firstpost_name']);?>/"><?=htmlspecialchars(truncate($ILPHP->lastthread['firstpost_name'], $ILPHP->lastthread['lastthread_state'] == "moved" ? (DISPLAY_COMMUNITY_ELEMENTS ? 20 : 35) : (DISPLAY_COMMUNITY_ELEMENTS ? 28 : 45)));?></a><br>
 von <?=user($ILPHP->lastthread['lastpost_uid'])->html(-1);?><br>
 <a href="/<?=LANG;?>/thread/<?=$ILPHP->lastthread['lastthread_id'];?>-<?=urlenc($ILPHP->lastthread['firstpost_name']);?>/<?if($ILPHP->lastthread['lastthread_num_posts'] > FORUM_THREAD_NUM_POSTS_PER_SITE){;?>page/<?=urlencode(floor($ILPHP->lastthread['lastthread_num_posts']/FORUM_THREAD_NUM_POSTS_PER_SITE));?>/<? } ?>"><?=timeago($ILPHP->lastthread['lastpost_time']);?></a>
 <?}else{;?>
 &nbsp;
 <? } ?>
 </td>
 </tr>
 <? } ?>
</table>
<? } ?>
<?}?>