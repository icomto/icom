<?function ILPHP____templates_c_58653ce4_invites_php_requests_4fcbffeb_php(&$ILPHP){?><?if(!$ILPHP->invite_requests->num_rows){;?>
<center>Es gibt derzeit keine Invitecode Anfragen.</center>
<?}else{;
while($ILPHP->i = $ILPHP->invite_requests->fetch_assoc()){;?>
E-Mail: <em><?=htmlspecialchars($ILPHP->i['email']);?></em> <?=timeago($ILPHP->i['requesttime']);?><br>
<?=ubbcode::compile($ILPHP->i['message']);?>
<center>
 <form method="post" action="<?=$ILPHP->url;?>" onsubmit="return iC(this);">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="request">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('id');?>" value="<?=$ILPHP->i['id'];?>">
 <div><textarea name="<?=$ILPHP->IMODULE_POST_VAR('message');?>" style="width:80%;height:60px;"></textarea></div>
 <select name="<?=$ILPHP->IMODULE_POST_VAR('status');?>">
 <option value="accepted" style="color:green;">Antrag annehmen</option>
 <option value="rejected" style="color:red;">Antrag ablehnen</option>
 </select>
 <input type="submit" class="button" value="Abschicken">
 </form>
</center>
<hr>
<? } ;
 } ?>
<?}?>