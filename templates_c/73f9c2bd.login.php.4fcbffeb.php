<?function ILPHP____templates_c_73f9c2bd_login_php_4fcbffeb_php(&$ILPHP){?><div class="module-item">
 <h1><?=$ILPHP->im_way_html();?></h1>
 <div class="module-content module-login">
 <?if(IS_LOGGED_IN){;?>
 <p class="error"><?=htmlspecialchars(lang::_LS_get_define('14f4c5a0'))?></p>
 <?}else{;
 if($ILPHP->state !== 'password_changed' and !REGISTER_CLOSED){;?>
 <a href="/<?=LANG;?>/register/" class="info">
 <?=lang::_LS_get_define('c54f0628')?>
 </a>
 <? } ;
 if($ILPHP->state === 'failed'){;?>
 <p class="error"><?=htmlspecialchars(lang::_LS_get_define('feb57ce4'))?></p>
 <?}elseif($ILPHP->state === 'not_validated'){;?>
 <p class="error">
 <?=lang::_LS_get_define('dce1299b')?>
 <p>
 <?}elseif($ILPHP->state === 'deleted'){;?>
 <p class="error"><?=htmlspecialchars(lang::_LS_get_define('9c614869'))?></p>
 <?}elseif($ILPHP->state === 'password_changed'){;?>
 <p class="info">
 <?=lang::_LS_get_define('55d1c25f')?>
 </p>
 <? } ?>
 <form method="post" onsubmit="return iC(this, '~.module-item');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="login"></td>
 <table>
 <tr>
 <td><?=htmlspecialchars(lang::_LS_get_define('b62623c4'))?></td>
 <td><input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('nick');?>"></td>
 </tr>
 <tr>
 <td><?=htmlspecialchars(lang::_LS_get_define('3f9e61d8'))?></td>
 <td><input type="password" name="<?=$ILPHP->IMODULE_POST_VAR('pass');?>"></td>
 </tr>
 </table>
 <button type="submit" class="big-button"><?=htmlspecialchars(lang::_LS_get_define('b7c2ad40'))?></button>
 </form>
 <p>
 <a href="/<?=LANG;?>/password_lost/"><?=htmlspecialchars(lang::_LS_get_define('f13f57cd'))?></a>
 </p>
 <? } ?>
 </div>
 <div class="module-footer"></div>
</div>
<?}?>