<?function ILPHP____templates_c_38493f99_friends_php_friends_4fcbffeb_php(&$ILPHP){?><div class="user-friends">
 <?if(!IS_LOGGED_IN){;?>
 <p class="error"><?=htmlspecialchars(lang::_LS_get_define('5dbc522a'))?></p>
 <?}elseif(!$ILPHP->user){;?>
 <p class="error"><?=htmlspecialchars(lang::_LS_get_define('b1bbc6d9'))?></p>
 <?}elseif(!$ILPHP->friends->num_rows){;?>
 <p class="info"><?=htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('05ea8d2f'),$ILPHP->user['nick']))?></p>
 <?}else{;
 $ILPHP->while_i=0;while($ILPHP->i = $ILPHP->friends->fetch_assoc()){$ILPHP->while_i++;?>
 <div class="friend-box">
 <a href="/<?=LANG;?>/users/<?=$ILPHP->i['user_id'];?>-<?=urlenc($ILPHP->i['nick']);?>/">
 <div><img class="avatar" src="<?if($ILPHP->i['avatar']){;echo htmlspecialchars(get_avatar_url($ILPHP->i['avatar']));}else{;echo STATIC_CONTENT_DOMAIN;?>/img/no_avatar.jpg<? } ?>" alt=""></div>
 <?=htmlspecialchars($ILPHP->i['nick']);?>
 </a>
 </div>
 <? } ?>
 <div class="clear"></div>
 <? } ?>
</div>
<?}?>