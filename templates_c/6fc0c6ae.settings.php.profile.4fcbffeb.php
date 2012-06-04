<?function ILPHP____templates_c_6fc0c6ae_settings_php_profile_4fcbffeb_php_post_responses(&$ILPHP){?>
 <?if($ILPHP->info){;?>
 <p class="info">
 <?switch($ILPHP->info){;
 default:;echo htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('863d01bf'),$ILPHP->info));break;
 case "EMAIL_CHANGED":;echo htmlspecialchars(lang::_LS_get_define('7fef0108'));break;
 case "AVATAR_CHANGED":;echo htmlspecialchars(lang::_LS_get_define('e7902f53'));break;
 case "AVATAR_DELETED":;echo htmlspecialchars(lang::_LS_get_define('64995d83'));break;
 case "SIGNATURE_CHANGED":;echo htmlspecialchars(lang::_LS_get_define('03885964'));break;
 case "PASSWORD_CHANGED":;
 echo lang::_LS_get_define('48ec7185')?>
 <script type="text/javascript">setTimeout(function(){location='/login/';},3500);</script>
 <?break;
 case "MYSPACE_NAME_CHANGED":;echo htmlspecialchars(lang::_LS_get_define('2c9529c3'));break;
 case "MYSPACE_BACKGROUND_CHANGED":;echo htmlspecialchars(lang::_LS_get_define('9ab9630f'));break;
 case "MYSPACE_CHANGED":;echo htmlspecialchars(lang::_LS_get_define('dc198b7c'));break;
 } ?>
 </p>
 <? } ;
 if($ILPHP->error){;?>
 <p class="error">
 <?switch($ILPHP->error){;
 default:;echo htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('be1030f4'),$ILPHP->error));break;
 case "INVALID_EMAIL":;echo htmlspecialchars(lang::_LS_get_define('759adeff'));break;
 case "INVALID_PASSWORD":;echo htmlspecialchars(lang::_LS_get_define('24101f0f'));break;
 case "PASSWORTS_NOT_MATCH":;echo htmlspecialchars(lang::_LS_get_define('ed1402e7'));break;
 case "NEED_PASSWORD_TO_CHANGE_PASSWORD":;echo htmlspecialchars(lang::_LS_get_define('c8a9200c'));break;
 case "AVATAR_DOWNLOAD_FAILED":;echo htmlspecialchars(lang::_LS_get_define('4e589427'));break;
 case "AVATAR_SAVE_FAILED":;echo htmlspecialchars(lang::_LS_get_define('f7ada4a7'));break;
 case "AVATAR_FILESIZE_TOO_BIG":;echo htmlspecialchars(lang::_LS_get_define('90883857'));break;
 case "AVATAR_INVALID_SIZE":;echo htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('77974c32'),AVATAR_MAX_WIDTH,AVATAR_MAX_HEIGHT));break;
 case "SIGNATURE_TOO_BIG":;echo htmlspecialchars(lang::_LS_get_define('e912a779'));break;
 case "NEED_AT_LEAST_ONE_LANGUAGE":;echo htmlspecialchars(lang::_LS_get_define('47e47b65'));break;
 } ?>
 </p>
 <? } ?>
 <?}?><?function ILPHP____templates_c_6fc0c6ae_settings_php_profile_4fcbffeb_php_myspace(&$ILPHP){?>
 <form method="post" onsubmit="return iC(this);">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="myspace">
 <div class="SEP"><div><div></div><h4><?=htmlspecialchars(lang::_LS_get_define('1629cce7'))?></h4><div></div></div></div>
 <?$ILPHP->info = @$ILPHP->info['myspace'];$ILPHP->error = @$ILPHP->error['myspace'];?><?ILPHP____templates_c_6fc0c6ae_settings_php_profile_4fcbffeb_php_post_responses($ILPHP);?>
 <p>
 <?=lang::_LS_get_define('f7a4977b')?>
 </p>
 <p class="info">
 <?=htmlspecialchars(lang::_LS_get_define('20c9c8a9'))?><br>
 <a href="/<?=LANG;?>/theme/apple_white/settings/user/"><?=htmlspecialchars(lang::_LS_get_define('91df4541'))?></a><br>
 <a href="/<?=LANG;?>/theme/apple_black/settings/user/"><?=htmlspecialchars(lang::_LS_get_define('360a048e'))?></a>
 </p>
 <table>
 <tr>
 <td><?=htmlspecialchars(lang::_LS_get_define('dbe7dee0'))?></td>
 <td><input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('name');?>" value="<?=htmlspecialchars($ILPHP->user['myspace_name']);?>"></td>
 </tr>
 <tr>
 <td data-tooltip="<?=lang::_LS_get_define('8d5c2630')?>">
 <?=htmlspecialchars(lang::_LS_get_define('0708d8b7'))?>
 </td>
 <td valign="top">
 <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('background');?>" value="<?=htmlspecialchars($ILPHP->user['myspace_background']);?>">
 </td>
 </tr>
 </table>
 <textarea class="bbcodeedit user-space" name="<?=$ILPHP->IMODULE_POST_VAR('content');?>"><?=htmlspecialchars($ILPHP->user['myspace']);?></textarea><br>
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('76f4b80c'))?></button>
 </form>
 <?}?><?function ILPHP____templates_c_6fc0c6ae_settings_php_profile_4fcbffeb_php_signature(&$ILPHP){?>
 <form method="post" onsubmit="return iC(this);">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="signature">
 <div class="SEP"><div><div></div><h4><?=htmlspecialchars(lang::_LS_get_define('d12a6f71'))?></h4><div></div></div></div>
 <?$ILPHP->info = @$ILPHP->info['signature'];$ILPHP->error = @$ILPHP->error['signature'];?><?ILPHP____templates_c_6fc0c6ae_settings_php_profile_4fcbffeb_php_post_responses($ILPHP);?>
 <p class="info">
 <?=lang::_LS_get_define('b603de5a')?>
 </p>
 <textarea class="bbcodeedit user-signature" name="<?=$ILPHP->IMODULE_POST_VAR('signature');?>"><?=htmlspecialchars($ILPHP->user['signature']);?></textarea><br>
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('76f4b80c'))?></button>
 </form>
 <?}?><?function ILPHP____templates_c_6fc0c6ae_settings_php_profile_4fcbffeb_php_forum(&$ILPHP){?>
 <form method="post" onsubmit="return iC(this);">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="forum">
 <div class="SEP"><div><div></div><h4><?=htmlspecialchars(lang::_LS_get_define('f44edb9b'))?></h4><div></div></div></div>
 <?$ILPHP->info = @$ILPHP->info['forum'];$ILPHP->error = @$ILPHP->error['forum'];?><?ILPHP____templates_c_6fc0c6ae_settings_php_profile_4fcbffeb_php_post_responses($ILPHP);?>
 <table>
 <tr>
 <td><label for="UserSettingsDisplaySignatures"><?=htmlspecialchars(lang::_LS_get_define('c8f22f82'))?></label></td>
 <td><input type="checkbox" id="UserSettingsDisplaySignatures" name="<?=$ILPHP->IMODULE_POST_VAR('display_signatures');?>"<?if($ILPHP->user['display_signatures']){;?> checked="checked"<? } ?> style="width:auto;"></td>
 </tr>
 <tr>
 <?$ILPHP->avatar = get_avatar_url($ILPHP->user['avatar']);
 if($ILPHP->avatar == STATIC_CONTENT_DOMAIN.'/img/no_avatar.jpg'){;$ILPHP->avatar = ''; } ?>
 <td><?=htmlspecialchars(lang::_LS_get_define('ec66ef5a'))?></td>
 <td><input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('avatar');?>" value="<?=htmlspecialchars($ILPHP->avatar);?>" data-tooltip="<?=htmlspecialchars(lang::_LS_get_define('9a338a84'))?>"></td>
 </tr>
 <tr>
 <td>&nbsp;</td>
 <td><button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('76f4b80c'))?></button></td>
 </tr>
 </table>
 </form>
 <?}?><?function ILPHP____templates_c_6fc0c6ae_settings_php_profile_4fcbffeb_php_emails_allowed(&$ILPHP){?>
 <form method="post" onsubmit="return iC(this);">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="emails_allowed">
 <div class="SEP"><div><div></div><h4><?=htmlspecialchars(lang::_LS_get_define('0266e49d'))?></h4><div></div></div></div>
 <?$ILPHP->info = @$ILPHP->info['emails_allowed'];$ILPHP->error = @$ILPHP->error['emails_allowed'];?><?ILPHP____templates_c_6fc0c6ae_settings_php_profile_4fcbffeb_php_post_responses($ILPHP);?>
 <table>
 <tr>
 <td><?=htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('9fa760ff'),SITE_NAME))?></td>
 <td><input type="checkbox" id="UserSettingsEmailAllowed" name="<?=$ILPHP->IMODULE_POST_VAR('allow');?>"<?if($ILPHP->user['emails_allowed']){;?> checked="checked"<? } ?> style="width:auto;"><label for="UserSettingsEmailAllowed"> <?=htmlspecialchars(lang::_LS_get_define('c0e1b8be'))?></label></td>
 </tr>
 <tr>
 <td>&nbsp;</td>
 <td><button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('76f4b80c'))?></button></td>
 </tr>
 </table>
 </form>
 <?}?><?function ILPHP____templates_c_6fc0c6ae_settings_php_profile_4fcbffeb_php_languages(&$ILPHP){?>
 <form method="post" onsubmit="return iC(this);">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="languages">
 <div class="SEP"><div><div></div><h4><?=htmlspecialchars(lang::_LS_get_define('40a82cd2'))?></h4><div></div></div></div>
 <?$ILPHP->info = @$ILPHP->info['languages'];$ILPHP->error = @$ILPHP->error['languages'];?><?ILPHP____templates_c_6fc0c6ae_settings_php_profile_4fcbffeb_php_post_responses($ILPHP);?>
 <table>
 <tr>
 <td><?=htmlspecialchars(lang::_LS_get_define('af1774cf'))?></td>
 <td>
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('save');?>" value="1">
 <input type="checkbox" id="UserSettingsLanguagesDE" name="<?=$ILPHP->IMODULE_POST_VAR('de');?>"<?if(in_array('de', $ILPHP->user['languages'])){;?> checked="checked"<? } ?> style="width:auto;"><label for="UserSettingsLanguagesDE"> <?=htmlspecialchars(lang::_LS_get_define('e650da0f'))?></label><br>
 <input type="checkbox" id="UserSettingsLanguagesEN" name="<?=$ILPHP->IMODULE_POST_VAR('en');?>"<?if(in_array('en', $ILPHP->user['languages'])){;?> checked="checked"<? } ?> style="width:auto;"><label for="UserSettingsLanguagesEN"> <?=htmlspecialchars(lang::_LS_get_define('7b83c6b1'))?></label>
 </td>
 </tr>
 <tr>
 <td>&nbsp;</td>
 <td><button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('76f4b80c'))?></button></td>
 </tr>
 </table>
 </form>
 <?}?><?function ILPHP____templates_c_6fc0c6ae_settings_php_profile_4fcbffeb_php_password(&$ILPHP){?>
 <form method="post" onsubmit="return iC(this);">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="password">
 <div class="SEP"><div><div></div><h4><?=htmlspecialchars(lang::_LS_get_define('c66be2aa'))?></h4><div></div></div></div>
 <?$ILPHP->info = @$ILPHP->info['password'];$ILPHP->error = @$ILPHP->error['password'];?><?ILPHP____templates_c_6fc0c6ae_settings_php_profile_4fcbffeb_php_post_responses($ILPHP);?>
 <table>
 <?if(!$ILPHP->password_recover){;?>
 <tr>
 <td><?=htmlspecialchars(lang::_LS_get_define('48f0549c'))?></td>
 <td><input type="password" name="<?=$ILPHP->IMODULE_POST_VAR('old');?>"></td>
 </tr>
 <? } ?>
 <tr>
 <td><?=htmlspecialchars(lang::_LS_get_define('1e08767c'))?></td>
 <td><input type="password" name="<?=$ILPHP->IMODULE_POST_VAR('new');?>"></td>
 </tr>
 <tr>
 <td><?=htmlspecialchars(lang::_LS_get_define('405c2294'))?></td>
 <td><input type="password" name="<?=$ILPHP->IMODULE_POST_VAR('new2');?>"></td>
 </tr>
 <tr>
 <td>&nbsp;</td>
 <td><button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('76f4b80c'))?></button></td>
 </tr>
 </table>
 </form>
 <?}?><?function ILPHP____templates_c_6fc0c6ae_settings_php_profile_4fcbffeb_php_email(&$ILPHP){?>
 <form method="post" onsubmit="return iC(this);">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="email">
 <div class="SEP"><div><div></div><h4><?=htmlspecialchars(lang::_LS_get_define('b121230c'))?></h4><div></div></div></div>
 <?$ILPHP->info = @$ILPHP->info['email'];$ILPHP->error = @$ILPHP->error['email'];?><?ILPHP____templates_c_6fc0c6ae_settings_php_profile_4fcbffeb_php_post_responses($ILPHP);?>
 <table>
 <tr>
 <td><?=htmlspecialchars(lang::_LS_get_define('71ecd14b'))?></td>
 <td><input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('email');?>" value="<?=htmlspecialchars($ILPHP->user['email']);?>"></td>
 </tr>
 <tr data-tooltip="<?=htmlspecialchars(lang::_LS_get_define('8d7fe5ee'))?>">
 <td><?=htmlspecialchars(lang::_LS_get_define('3f9e61d8'))?></td>
 <td><input type="password" name="<?=$ILPHP->IMODULE_POST_VAR('pass');?>"></td>
 </tr>
 <tr>
 <td>&nbsp;</td>
 <td><button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('76f4b80c'))?></button></td>
 </tr>
 </table>
 </form>
 <?}?><?function ILPHP____templates_c_6fc0c6ae_settings_php_profile_4fcbffeb_php(&$ILPHP){?><div class="module-user-settings">
 
 
 <?if(!IS_LOGGED_IN){;?><p class="error"><?=htmlspecialchars(lang::_LS_get_define('7bc3584f'))?></p>
 <?}else{;?>
 
 <?ILPHP____templates_c_6fc0c6ae_settings_php_profile_4fcbffeb_php_email($ILPHP)?>
 
 <?ILPHP____templates_c_6fc0c6ae_settings_php_profile_4fcbffeb_php_password($ILPHP)?>
 
 <?ILPHP____templates_c_6fc0c6ae_settings_php_profile_4fcbffeb_php_languages($ILPHP)?>
 
 <?ILPHP____templates_c_6fc0c6ae_settings_php_profile_4fcbffeb_php_emails_allowed($ILPHP)?>
 
 <?ILPHP____templates_c_6fc0c6ae_settings_php_profile_4fcbffeb_php_forum($ILPHP)?>
 
 <?ILPHP____templates_c_6fc0c6ae_settings_php_profile_4fcbffeb_php_signature($ILPHP)?>
 
 <?ILPHP____templates_c_6fc0c6ae_settings_php_profile_4fcbffeb_php_myspace($ILPHP)?>
 <? } ?>
</div>
<?}?>