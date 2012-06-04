<?function ILPHP____templates_c_38493f99_users_php_guestbook_4fcbffeb_php(&$ILPHP){?><div class="module-user-guestbook">
 <?if(!$ILPHP->user){;?><p class="error"><?=htmlspecialchars(lang::_LS_get_define('b1bbc6d9'))?></p>
 <?}elseif(!$ILPHP->allow_see){;?><p class="error"><?=htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('6fb82af3'),$ILPHP->user['nick']))?></p>
 <?}else{;
 if($ILPHP->user['user_id'] == USER_ID){;?>
 <div class="SEP first"><div><div></div><h3><?=htmlspecialchars(lang::_LS_get_define('10ce3a60'))?></h3><div></div></div></div>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>">
 <input type="radio" name="priv_guestbook" value="friends"<?if($ILPHP->user['priv_guestbook'] == 'friends'){;?> checked="checked"<? } ?> id="PrivGuestbookFriends"><label for="PrivGuestbookFriends"><?=htmlspecialchars(lang::_LS_get_define('7575b27f'))?></label><br>
 <input type="radio" name="priv_guestbook" value="users"<?if($ILPHP->user['priv_guestbook'] == 'users'){;?> checked="checked"<? } ?> id="PrivGuestbookUsers"><label for="PrivGuestbookUsers"><?=htmlspecialchars(lang::_LS_get_define('bcd8b831'))?></label><br>
 <input type="radio" name="priv_guestbook" value="public"<?if($ILPHP->user['priv_guestbook'] == 'public'){;?> checked="checked"<? } ?> id="PrivGuestbookPublic"><label for="PrivGuestbookPublic"><?=htmlspecialchars(lang::_LS_get_define('5c57000b'))?></label><br>
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('76f4b80c'))?></button>
 </form>
 <div class="SEP"><div><div></div><h3><?=htmlspecialchars(lang::_LS_get_define('f499cc52'))?></h3><div></div></div></div>
 <? } ;
 if($ILPHP->num_pages > 1){;?><div class="pages"><?=$ILPHP->im_pages_html();?></div><? } ;
 if(!$ILPHP->entries->num_rows){;?><p class="info"><?=htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('59e9ff06'),$ILPHP->user['nick']))?></p>
 <?}else{;
 $ILPHP->while_i=0;while($ILPHP->i = $ILPHP->entries->fetch_assoc()){$ILPHP->while_i++;?>
 <table class="forum-post" border="1">
 <tr>
 <th class="post-infos">
 <?=user($ILPHP->i['user_id'])->html(-1);?><br>
 <?if(user($ILPHP->i['user_id'])->has_groups()){;echo user($ILPHP->i['user_id'])->html_groups(0, ' ', array());?><br><? } ;
 $ILPHP->rank = user($ILPHP->i['user_id'])->html_rank();
 if($ILPHP->rank){;?><span style="<?=htmlspecialchars($ILPHP->rank['css']);?>" data-tooltip="<?=htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('3063efa2'),number_format($ILPHP->i['user_points'],''?'':0,',','.')))?>"><?=htmlspecialchars($ILPHP->rank['de']);?></span><br><? } ;
 if(user($ILPHP->i['user_id'])->has_avatar()){;echo user($ILPHP->i['user_id'])->avatar_html();?><br><? } ?>
 </th>
 <td class="post-content user-entry">
 <?=ubbcode::add_smileys(ubbcode::compile($ILPHP->i['message'], (DISPLAY_COMMUNITY_ELEMENTS ? 491 : 616)));?>
 </td>
 </tr>
 <tr class="post-footer">
 <th>
 <?=timeago($ILPHP->i['timeadded']);?>
 </th>
 <td class="post-controls">
 <?if($ILPHP->user['user_id'] == USER_ID or has_privilege('guestbook_master')){;?>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="if(!confirm('<?=htmlspecialchars(lang::_LS_get_define('28c26efd'))?>'))return false;">
 <input type="hidden" name="delete_guestbook_entrie" value="<?=htmlspecialchars($ILPHP->i['id']);?>">
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('5533445e'))?></button>
 </form>
 <? } ?>
 </td>
 </tr>
 </table>
 <? } ;
 } ;
 if($ILPHP->num_pages > 1){;?><div class="pages"><?=$ILPHP->im_pages_html();?></div><? } ;

 if(IS_LOGGED_IN and $ILPHP->page == 1){;?>
 <div class="guestbook-new-post">
 <div class="SEP"><div><div></div><h3><?=htmlspecialchars(lang::_LS_get_define('5ab2270d'))?></h3><div></div></div></div>
 <?switch($ILPHP->allow_post){;
 default:;?><p class="error"><?=htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('50393731'),$ILPHP->allow_post))?></p><?break;
 case "POST_LIMIT_REACHED":;?><p class="info"><?=htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('cb9b45e9'),$ILPHP->user['nick']))?></p><?break;
 case "ALLOW":;?>
 <div class="guestbook-rules">
 <h3><?=htmlspecialchars(lang::_LS_get_define('5683637c'))?></h3>
 <p><?=htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('e1525ffc'),$ILPHP->user['nick']))?></p>
 <p><?=htmlspecialchars(lang::_LS_get_define('e890af31'))?></p>
 </div>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>">
 <textarea class="bbcodeedit" name="guestbook_message"></textarea>
 <button type="submit" class="big-button lonely-button"><?=htmlspecialchars(lang::_LS_get_define('e864f7e0'))?></button>
 </form>
 <?break;
 } ?>
 </div>
 <? } ;

 } ?>
</div>
<?}?>