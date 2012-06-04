<?function ILPHP____templates_c_73f9c2bd_report_page_php_4fcbffeb_php(&$ILPHP){?><?$ILPHP->ilphp_display('~/page_header.ilp', -1, "", true);?>
<div class="full-page long-style" style="width:1100px;">
 <div class="module-helper <?=$ILPHP->THEME['module'];?>">
 <div class="module" id="Module" style="width:100%;">
 <div class="module-item" style="margin:5px 0 5px 0;">
 <h1>Seite oder Inhalt melden</h1>
 <div class="module-content">
 <?if($ILPHP->success){;?>
 <p>
 Vielen Danke f&uuml;r die Meldung.<br>
 Wir werden Deine Anfrage so bald wie m&ouml;glich bearbeiten.
 </p>
 <p>
 Du kannst hier auf Dein Ticket zugreifen:<br><a href="http://<?=SITE_DOMAIN;?>/<?=LANG;?>/settings/tickets/ticket_id/<?=$ILPHP->report_id;?>/<?if(!IS_LOGGED_IN){;?>pw/<?=htmlspecialchars($ILPHP->password);?>/<? } ?>">http://<?=SITE_DOMAIN;?>/<?=LANG;?>/settings/tickets/ticket_id/<?=$ILPHP->report_id;?>/<?if(!IS_LOGGED_IN){;?>pw/<?=htmlspecialchars($ILPHP->password);?>/<? } ?></a><br>
 <br>
 <?if(!IS_LOGGED_IN){;?>
 Tip: Als <a href="/<?=LANG;?>/register/">registrierter Benutzer</a> kannst Du direkt &uuml;ber Dein Profil auf Deine Tickets zugreifen.
 <?}else{;?>
 Du kannst Deine Tickets jederzeit unter <a href="/<?=LANG;?>/de/settings/tickets/">Einstellungen -> Tickets einsehen</a>.
 <? } ?>
 </p>
 <?}else{;?>
 <form method="post">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="save">
 <?if(!IS_LOGGED_IN){;?>
 <p>
 Ihr Name:<br>
 <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('name');?>" value="<?=htmlspecialchars(@$ILPHP->args['post']['name']);?>" size="30">
 <?if(@$ILPHP->error['name']){;?><br><span class="error">Du musst deinen Namen angeben.</span><? } ?>
 </p>
 <p>
 Ihre E-Mail Addresse:<br>
 <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('email');?>" value="<?=htmlspecialchars(@$ILPHP->args['post']['email']);?>" size="30">
 <?if(@$ILPHP->error['email']){;?><br><span class="error">Du musst eine g&uuml;tige E-Mail Addresse angeben.</span><? } ?>
 </p>
 <? } ?>
 <p>
 Betreffende URL:<br>
 <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('url');?>" value="<?=htmlspecialchars(@$ILPHP->args['post']['url']);?>" size="75">
 </p>
 <p>
 Art der Meldung:<br>
 <select name="<?=$ILPHP->IMODULE_POST_VAR('class');?>">
 <option value="" style="color:#888;">Bitte w&auml;hlen</option>
 <option value="content"<?if(@$ILPHP->args['post']['class'] == 'content'){;?> selected="selected"<? } ?>>Unangemessener Inhalt (Versto&szlig; gegen den Jugendschutz etc.)</option>
 <option value="abuse"<?if(@$ILPHP->args['post']['class'] == 'abuse'){;?> selected="selected"<? } ?>>Urheberrechtlich gesch&uuml;tzter Inhalt</option>
 <option value="privacy"<?if(@$ILPHP->args['post']['class'] == 'privacy'){;?> selected="selected"<? } ?>>Verletzung von Pers&ouml;nlichkeitsrechten oder den Datenschutz</option>
 <option value="other"<?if(@$ILPHP->args['post']['class'] == 'other'){;?> selected="selected"<? } ?>>Nicht aufgelisteter Grund</option>
 </select>
 <?if(@$ILPHP->error['class']){;?><br><span class="error">Du musst eine Art der Meldung angeben.</span><? } ?>
 </p>
 <p>
 Bitte beschreibe so genau wie m&ouml;glich warum und was Du melden m&ouml;chstest.<br>
 Du kannst auch gerne Screenshots oder weitere Links eintragen.
 <textarea name="<?=$ILPHP->IMODULE_POST_VAR('message');?>" class="bbcodeedit" rows="7"><?=htmlspecialchars(@$ILPHP->args['post']['message']);?></textarea>
 <?if(@$ILPHP->error['message']){;?><br><span class="error">Bitte schreibe uns einen kurzen Text warum Du diese Seite melden willst.</span><? } ?>
 </p>
 <p>
 Gib bitte die folgenden Zeichen ein:<br>
 <img src="/<?=LANG;?>/captcha/report_page?<?=mt_rand();?>"><br>
 <a href="" onclick="document.getElementById('Captcha').src='/<?=LANG;?>/captcha/report_page?'+Math.random();document.getElementById('CaptchaForm').focus();return false;" id="change-image" style="font-weight:normal;font-size:90%;padding-left:35px;">[<?=htmlspecialchars(lang::_LS_get_define('ca5ae91e'))?>]</a><br>
 <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('captcha');?>" style="width:200px;">
 <?if(@$ILPHP->error['captcha']){;?><br><span class="error">Bitte best&auml;tige das Captcha.</span><? } ?>
 </p>
 <p>
 <button type="submit" class="big-button">Abschicken</button>
 </p>
 </form>
 <? } ?>
 </div>
 <div class="module-footer"></div>
 </div>
 </div>
 </div>
</div>
<?$ILPHP->ilphp_display('~/page_footer.ilp', -1, "", true);?>
<?}?>