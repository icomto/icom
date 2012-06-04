<?function ILPHP____templates_c_58653ce4_email_php_4fcbffeb_php(&$ILPHP){?><div class="module-item">
 <h1>E-Mail verschicken</h1>
 <div class="module-content">
 <?if($ILPHP->sent){;?>
 <center>
 <br>
 Nachricht Abgeschickt.<br>
 <br>
 </center>
 <? } ?>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC(this);">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="send">
 <i>Absender:: </i> &nbsp; <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('from');?>" value="noreply@icom.to" style="width:220px;"><br>
 <i>Empf&auml;nger: </i> &nbsp; <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('to');?>" value="" style="width:220px;"><br>
 <i>Betreff: </i> &nbsp; <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('topic');?>" value="Nachricht von icom.to" style="width:380px;"><br>
 <center>
 <textarea name="<?=$ILPHP->IMODULE_POST_VAR('message');?>" rows="5" cols="30" style="width:590px;height:150px;font-size:12px;font-family:Arial;"></textarea><br>
 <input type="submit" class="button" value="Abschicken">
 </center>
 </form>
 </div>
 <div class="module-footer"></div>
</div>
<?}?>