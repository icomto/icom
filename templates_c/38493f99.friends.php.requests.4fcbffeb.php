<?function ILPHP____templates_c_38493f99_friends_php_requests_4fcbffeb_php(&$ILPHP){?><div class="user-friends">
 <?if(!$ILPHP->requests->num_rows){;?>
 <p class="info"><?=htmlspecialchars(lang::_LS_get_define('36006dfe'))?></p>
 <?}else{;
 $ILPHP->while_i=0;while($ILPHP->i = $ILPHP->requests->fetch_assoc()){$ILPHP->while_i++;?>
 <div class="clear" style="border-bottom:1px #777 solid;margin-bottom:5px;">
 <div class="friend-box">
 <a href="/<?=LANG;?>/users/<?=$ILPHP->i['user_id'];?>-<?=urlenc($ILPHP->i['nick']);?>/">
 <div><img class="avatar" src="<?if($ILPHP->i['avatar']){;echo htmlspecialchars(get_avatar_url($ILPHP->i['avatar']));}else{;echo STATIC_CONTENT_DOMAIN;?>/img/no_avatar.jpg<? } ?>" alt=""></div>
 <?=htmlspecialchars($ILPHP->i['nick']);?>
 </a>
 </div>
 <div id="UPFRT<?=$ILPHP->i['user_id'];?>" style="float:right;margin:auto 0;">
 <form method="post" action="<?=$ILPHP->url;?>" onsubmit="return iC(this);">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('requests', 'friend_id');?>" value="<?=$ILPHP->i['user_id'];?>">
 <input type="hidden" class="friend-status" name="<?=$ILPHP->IMODULE_POST_VAR('requests', 'status');?>" value="">
 <p><button type="submit" value="accept" class="info" onclick="$(this.form).find('.friend-status').attr('value', this.value);" style="padding:4px;width:150px;font-weight:bold;"><?=htmlspecialchars(lang::_LS_get_define('2a5df5c5'))?></button></p>
 <p><button type="submit" value="reject" class="error" onclick="$(this.form).find('.friend-status').attr('value', this.value);" style="padding:4px;width:150px;"><?=htmlspecialchars(lang::_LS_get_define('cba411c0'))?></button></p>
 </form>
 </div>
 </div>
 <? } ?>
 <div class="clear"></div>
 <? } ?>
</div>
<?}?>