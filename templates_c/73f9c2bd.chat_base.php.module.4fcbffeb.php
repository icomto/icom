<?function ILPHP____templates_c_73f9c2bd_chat_base_php_module_4fcbffeb_php(&$ILPHP){?><div class="module-item" id="Module<?=$ILPHP->html_id;?>">
 <h1><?=$ILPHP->im_way_html();?></h1>
 <div class="module-content module-chat">
 <?if($ILPHP->module_head_data){;?><div id="Module<?=$ILPHP->html_id;?>HeadData"><?=$ILPHP->module_head_data;?></div><? } ;
 if($ILPHP->page == 1 and $ILPHP->type != '_archive'){;
 if($ILPHP->allow_post){;?>
 <span class="error" id="ModuleChat<?=$ILPHP->html_id;?>Error"><?=$ILPHP->error;?></span>
 <form class="<?=$ILPHP->imodule_name;?>_form" method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return chat_base_submit(this, '<?=htmlspecialchars(str_replace("'", "\\'", $ILPHP->default_text));?>', '<?=htmlspecialchars(lang::_LS_get_define('f2e66049'))?>', 'Shouting...');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="new">
 <?if($ILPHP->chat_input_box == 'textarea'){;?><textarea class="bbcodeedit" name="<?=$ILPHP->IMODULE_POST_VAR('message');?>" onfocus="if(this.value=='<?=htmlspecialchars($ILPHP->default_text);?>')this.value='';" onblur="if(this.value=='')this.value='<?=htmlspecialchars($ILPHP->default_text);?>';"><?=htmlspecialchars($ILPHP->default_text);?></textarea>
 <?}else{;?><input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('message');?>" autocomplete="off" onfocus="if(this.value=='<?=htmlspecialchars($ILPHP->default_text);?>')this.value='';" onblur="if(this.value=='')this.value='<?=htmlspecialchars($ILPHP->default_text);?>';" style="width:89%;margin-bottom:3px;">
 <? } ?>
 <button type="submit" class="button"<?if($ILPHP->chat_input_box == 'input'){;?> style="width:10%;margin-bottom:3px;float:right;"<? } ?>><?=htmlspecialchars(lang::_LS_get_define('005e7873'))?></button>
 </form>
 <?}else{;
 if($ILPHP->reason){;?>
 <p class="error"><?=$ILPHP->reason;?></p>
 <? } ;
 } ;
 } ;
 if($ILPHP->num_pages > 1){;?><div class="pages"><?=$ILPHP->im_pages_html(NULL, $ILPHP->url.'page/%s/'.($ILPHP->type == '_archive' ? 'archive/' : ''));?></div>
 <?}else{;?><div style="height:5px;"></div>
 <? } ?>
 <div id="Module<?=$ILPHP->html_id;?>Top"></div>
 <?$ILPHP->last_id = 0;
 if($ILPHP->shouts){;
 while($ILPHP->i = $ILPHP->shouts->fetch_assoc()){;
 $ILPHP->ilphp_display('chat_base.php.row.ilp', -1, "", true);;
 if($ILPHP->last_id < $ILPHP->i['id']){;$ILPHP->last_id = $ILPHP->i['id']; } ;
 } ;
 } ?>
 <div class="thin-line"></div>
 <?if($ILPHP->num_pages > 1){;?><div class="pages"><?=$ILPHP->im_pages_html(NULL, $ILPHP->url.'page/%s/'.($ILPHP->type == '_archive' ? 'archive/' : ''));?></div><? } ;
 if($ILPHP->has_archive){;?>
 <div class="chat-archive">
 <?if($ILPHP->type == "_archive"){;?><a href="<?=htmlspecialchars($ILPHP->url);?>page/1/"><?=htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('f713f7bc'),$ILPHP->title))?></a>
 <?}else{;?><a href="<?=htmlspecialchars($ILPHP->url);?>page/1/archive/"><?=htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('ae09b292'),$ILPHP->title))?></a>
 <? } ?>
 </div>
 <? } ?>
 </div>
 <div class="module-footer"></div>
</div>
<?}?>