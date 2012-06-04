<?function ILPHP____templates_c_71d4bd6a_radio_php_page_4fcbffeb_php(&$ILPHP){?><?$ILPHP->ilphp_display('~/page_header.ilp', -1, "", true);?>
<table style="width:auto;">
 <tr>
 <td>
 <div class="side-menu side-menu-helper <?=$ILPHP->THEME['side_menu'];?>" style="margin:5px 0 0 5px;">
 <div class="side-menu-header menu-first"><?=$ILPHP->im_way_html();?></div>
 <div class="side-menu-content">
 <div class="menu-radio" id="IM_MENU_radio">
 <?$ILPHP->ilphp_display('radio.php.module.ilp', -1, "", true);?>
 </div>
 </div>
 <div class="side-menu-footer"></div>
 </div>
 </td>
 <?$ILPHP->has_chat = false;
 if($ILPHP->channel['chat_id']){;
 $ILPHP->CHAT = iengine::GET('chat', ['chat_id' => $ILPHP->channel['chat_id']]);
 $ILPHP->CONTENT = $ILPHP->CHAT->RUN('MENU');
 if($ILPHP->CONTENT){;
 $ILPHP->has_chat = true;?>
 <td>
 <div class="side-menu side-menu-helper <?=$ILPHP->THEME['side_menu'];?>" style="margin:5px 5px 0 0;">
 <div class="side-menu-header menu-first"><?if($ILPHP->CHAT->chat_name){;echo htmlspecialchars($ILPHP->CHAT->chat_name);}else{;echo htmlspecialchars(lang::_LS_get_define('b40d5bf7')); } ?></div>
 <div class="side-menu-content">
 <div class="menu-chat" id="IM_MENU_chat_<?=$ILPHP->channel['chat_id'];?>">
 <?=$ILPHP->CONTENT;?>
 </div>
 </div>
 <div class="side-menu-footer"></div>
 </div>
 </td>
 <? } ;
 } ;
 if($ILPHP->has_chat){;?>
 <script>window.innerWidth=365;</script>
 <script>document.body.clientWidth=365;</script>
 <?}else{;?>
 <script>window.innerWidth=195;</script>
 <script>document.body.clientWidth=195;</script>
 <? } ?>
 </tr>
</table>
<?$ILPHP->ilphp_display('~/page_footer.ilp', -1, "", true);?>
<?}?>