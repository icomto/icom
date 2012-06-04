<?function ILPHP____templates_c_73f9c2bd_chat_php_head_data_4fcbffea_php_needed_points(&$ILPHP){?>
 <li class="user-chat-setting" id="Chat<?=$ILPHP->data['id'];?>needed_points">
 <strong><?=htmlspecialchars(lang::_LS_get_define('ba9eef2f'))?></strong><br>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC(this, '~li');" style="text-align:left;">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="needed_points">
 Von <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('points_from');?>" size="10" value="<?=$ILPHP->data['points_from'];?>" style="text-align:right;">
 bis <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('points_to');?>" size="10" value="<?=$ILPHP->data['points_to'];?>" style="text-align:right;">
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('adc43853'))?></button><br>
 <p class="info">Wenn man hier zum Beispiel nur &quot;von&quot; angibt heisst das das es bis oben keine Grenze gibt. Gibt man bei beiden Feldern nichts ein wird diese Funktion deaktiviert.</p>
 </form>
 </li>
 <?}?><?function ILPHP____templates_c_73f9c2bd_chat_php_head_data_4fcbffea_php_groups(&$ILPHP){?>
 <li class="user-chat-setting" id="Chat<?=$ILPHP->data['id'];?>groups">
 <strong><?=htmlspecialchars(lang::_LS_get_define('91a3e632'))?></strong><br>
 <?if($ILPHP->available_groups->num_rows){;?>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC(this, '~li');" style="text-align:left;">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="add_group">
 <select name="<?=$ILPHP->IMODULE_POST_VAR('group_id');?>" title="<?=htmlspecialchars(lang::_LS_get_define('c688260d'))?>">
 <?while($ILPHP->i = $ILPHP->available_groups->fetch_assoc()){;?>
 <option value="<?=$ILPHP->i['id'];?>"><?=htmlspecialchars($ILPHP->i['name']);?></option>
 <? } ?>
 </select>
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('5912af1b'))?></button>
 </form>
 <? } ;
 while($ILPHP->i = $ILPHP->groups->fetch_assoc()){;?>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC(this, '~li');" style="text-align:left;display:inline;">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="remove_group">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('group_id');?>" value="<?=$ILPHP->i['id'];?>">
 <button type="submit" class="button" title="<?=htmlspecialchars(lang::_LS_get_define('e3d6ca6f'))?>"><?=htmlspecialchars($ILPHP->i['name']);?></button>
 </form>
 <? } ?>
 </li>
 <?}?><?function ILPHP____templates_c_73f9c2bd_chat_php_head_data_4fcbffea_php_banned_users(&$ILPHP){?>
 <li class="user-chat-setting" id="Chat<?=$ILPHP->data['id'];?>banned_users">
 <strong><?=htmlspecialchars(lang::_LS_get_define('fbf707be'))?></strong><br>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC(this, '~li');" style="text-align:left;">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="add_banned_user">
 <input type="text" size="65" name="<?=$ILPHP->IMODULE_POST_VAR('link');?>" title="<?=htmlspecialchars(lang::_LS_get_define('edd9da3e'))?>">
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('5912af1b'))?></button>
 </form>
 <?while($ILPHP->i = $ILPHP->banned_users->fetch_assoc()){;?>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC(this, '~li');" style="text-align:left;display:inline;">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="remove_banned_user">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('user_id');?>" value="<?=$ILPHP->i['id'];?>">
 <button type="submit" class="button" title="<?=htmlspecialchars(lang::_LS_get_define('3ac8a681'))?>"><?=htmlspecialchars($ILPHP->i['nick']);?></button>
 </form>
 <? } ?>
 </li>
 <?}?><?function ILPHP____templates_c_73f9c2bd_chat_php_head_data_4fcbffea_php_users(&$ILPHP){?>
 <li class="user-chat-setting" id="Chat<?=$ILPHP->data['id'];?>users">
 <strong><?=htmlspecialchars(lang::_LS_get_define('6babd2ab'))?></strong><br>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC(this, '~li');" style="text-align:left;">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="add_user">
 <input type="text" size="65" name="<?=$ILPHP->IMODULE_POST_VAR('link');?>" title="<?=htmlspecialchars(lang::_LS_get_define('edd9da3e'))?>">
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('5912af1b'))?></button><br>
 <input type="checkbox" name="<?=$ILPHP->IMODULE_POST_VAR('send_pn');?>" checked="checked" id="UserChat<?=$ILPHP->data['id'];?>SendPN"><label for="UserChat<?=$ILPHP->data['id'];?>SendPN"> <?=htmlspecialchars(lang::_LS_get_define('0fcf1b39'))?></label>
 </form>
 <?while($ILPHP->i = $ILPHP->users->fetch_assoc()){;?>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC(this, '~li');" style="text-align:left;display:inline;">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="remove_user">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('user_id');?>" value="<?=$ILPHP->i['id'];?>">
 <button type="submit" class="button" title="<?=htmlspecialchars(lang::_LS_get_define('3ac8a681'))?>"><?=htmlspecialchars($ILPHP->i['nick']);?></button>
 </form>
 <? } ?>
 </li>
 <?}?><?function ILPHP____templates_c_73f9c2bd_chat_php_head_data_4fcbffea_php_admin_groups(&$ILPHP){?>
 <li class="user-chat-setting" id="Chat<?=$ILPHP->data['id'];?>admin_groups">
 <strong><?=htmlspecialchars(lang::_LS_get_define('d2d54dc5'))?></strong><br>
 <?if($ILPHP->available_admin_groups->num_rows){;?>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC(this, '~li');" style="text-align:left;">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="add_admin_group">
 <select name="<?=$ILPHP->IMODULE_POST_VAR('group_id');?>" title="<?=htmlspecialchars(lang::_LS_get_define('e4f1555a'))?>">
 <?while($ILPHP->i = $ILPHP->available_admin_groups->fetch_assoc()){;?>
 <option value="<?=$ILPHP->i['id'];?>"><?=htmlspecialchars($ILPHP->i['name']);?></option>
 <? } ?>
 </select>
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('5912af1b'))?></button>
 </form>
 <? } ;
 while($ILPHP->i = $ILPHP->admin_groups->fetch_assoc()){;?>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC(this, '~li');" style="text-align:left;display:inline;">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="remove_admin_group">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('group_id');?>" value="<?=$ILPHP->i['id'];?>">
 <button type="submit" class="button" title="<?=htmlspecialchars(lang::_LS_get_define('e3d6ca6f'))?>"><?=htmlspecialchars($ILPHP->i['name']);?></button>
 </form>
 <? } ?>
 </li>
 <?}?><?function ILPHP____templates_c_73f9c2bd_chat_php_head_data_4fcbffea_php_admins(&$ILPHP){?>
 <li class="user-chat-setting" id="Chat<?=$ILPHP->data['id'];?>admins">
 <strong><?=htmlspecialchars(lang::_LS_get_define('ee2674d8'))?></strong><br>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC(this, '~li');" style="text-align:left;">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="add_admin">
 <input type="text" size="65" name="<?=$ILPHP->IMODULE_POST_VAR('link');?>" title="<?=htmlspecialchars(lang::_LS_get_define('edd9da3e'))?>">
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('5912af1b'))?></button>
 </form>
 <?while($ILPHP->i = $ILPHP->admins->fetch_assoc()){;?>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC(this, '~li');" style="text-align:left;display:inline;">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="remove_admin">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('user_id');?>" value="<?=$ILPHP->i['id'];?>">
 <button type="submit" class="button" title="<?=htmlspecialchars(lang::_LS_get_define('e7fa7bd6'))?>"><?=htmlspecialchars($ILPHP->i['nick']);?></button>
 </form>
 <? } ?>
 </li>
 <?}?><?function ILPHP____templates_c_73f9c2bd_chat_php_head_data_4fcbffea_php_change_place(&$ILPHP){?>
 <li class="user-chat-setting" id="Chat<?=$ILPHP->data['id'];?>change_place">
 <strong><?=htmlspecialchars(lang::_LS_get_define('06d9f582'))?></strong><br>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC(this, '~li');" style="text-align:left;">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="change_place">
 <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('place');?>" size="10" value="<?=$ILPHP->data['place'];?>" style="text-align:right;">
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('76f4b80c'))?></button>
 <p class="info">
 Der Standartplatz ist 12345.<br>
 Wenn ein Chat immer auf Platz 1 sein soll hier 1 eingeben (solange noch kein anderer Chat den Platz hat).<br>
 Am besten einfach immer in 100er schritten Arbeiten. Also 1. Chat bekommt 100, der 2. 200, der 3. 300.<br>
 So kann man immer ohne gro&szlig;e &auml;nderungen nen Chat dazwischenschieben.
 </p>
 </form>
 </li>
 <?}?><?function ILPHP____templates_c_73f9c2bd_chat_php_head_data_4fcbffea_php_change_input_box(&$ILPHP){?>
 <li class="user-chat-setting" id="Chat<?=$ILPHP->data['id'];?>change_input_box">
 <strong><?=htmlspecialchars(lang::_LS_get_define('e80d42a2'))?></strong><br>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC(this, '~li');" style="text-align:left;">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="change_input_box">
 <select name="<?=$ILPHP->IMODULE_POST_VAR('input_box');?>">
 <option value="textarea"<?if($ILPHP->data['input_box'] == 'textarea'){;?> selected="selected"<? } ?>><?=htmlspecialchars(lang::_LS_get_define('b67cb655'))?></option>
 <option value="input"<?if($ILPHP->data['input_box'] == 'input'){;?> selected="selected"<? } ?>><?=htmlspecialchars(lang::_LS_get_define('4a3960c1'))?></option>
 </select> <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('adc43853'))?></button>
 </form>
 </li>
 <?}?><?function ILPHP____templates_c_73f9c2bd_chat_php_head_data_4fcbffea_php_change_status(&$ILPHP){?>
 <li class="user-chat-setting" id="Chat<?=$ILPHP->data['id'];?>change_status">
 <strong><?=htmlspecialchars(lang::_LS_get_define('d0c7fc8a'))?></strong><br>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC_confirm('<?=htmlspecialchars(lang::_LS_get_define('28c26efd'))?>', this, '~li');" style="text-align:left;">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="change_status">
 <select name="<?=$ILPHP->IMODULE_POST_VAR('status');?>">
 <option value="open"<?if($ILPHP->data['status'] == 'open'){;?> selected="selected"<? } ?>><?=htmlspecialchars(lang::_LS_get_define('f7dcddd2'))?></option>
 <option value="closed"<?if($ILPHP->data['status'] == 'closed'){;?> selected="selected"<? } ?>><?=htmlspecialchars(lang::_LS_get_define('29e42a21'))?></option>
 <option value="deleted"<?if($ILPHP->data['status'] == 'deleted'){;?> selected="selected"<? } ?>><?=htmlspecialchars(lang::_LS_get_define('8f17659a'))?></option>
 </select> <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('adc43853'))?></button>
 </form>
 </li>
 <?}?><?function ILPHP____templates_c_73f9c2bd_chat_php_head_data_4fcbffea_php_change_lang(&$ILPHP){?>
 <li class="user-chat-setting" id="Chat<?=$ILPHP->data['id'];?>change_lang">
 <strong><?=htmlspecialchars(lang::_LS_get_define('369db633'))?></strong><br>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC(this, '~li');" style="text-align:left;">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="change_lang">
 <select name="<?=$ILPHP->IMODULE_POST_VAR('lang');?>">
 <?foreach($ILPHP->LANGUAGE_PRIORITY as $ILPHP->lang){;?>
 <option value="<?=htmlspecialchars($ILPHP->lang);?>"<?if($ILPHP->lang == $ILPHP->data['lang']){;?> selected="selected"<? } ?>><?=htmlspecialchars(get_sitelang_string($ILPHP->lang));?></option>
 <? } ?>
 </select> <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('adc43853'))?></button>
 </form>
 </li>
 <?}?><?function ILPHP____templates_c_73f9c2bd_chat_php_head_data_4fcbffea_php_change_default_text(&$ILPHP){?>
 <li class="user-chat-setting" id="Chat<?=$ILPHP->data['id'];?>change_default_text">
 <strong><?=htmlspecialchars(lang::_LS_get_define('d5635dfb'))?></strong><br>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC(this, '~li');" style="text-align:left;">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="change_default_text">
 <input type="text" size="65" name="<?=$ILPHP->IMODULE_POST_VAR('default_text');?>" value="<?=htmlspecialchars($ILPHP->data['default_text']);?>">
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('adc43853'))?></button>
 </form>
 </li>
 <?}?><?function ILPHP____templates_c_73f9c2bd_chat_php_head_data_4fcbffea_php(&$ILPHP){?><?if($ILPHP->data['is_admin']){;?>
<p>
 <div class="SEP first"><div><div></div><h3><?=htmlspecialchars(lang::_LS_get_define('7b06011a'))?></h3><div></div></div></div>
 <?if($ILPHP->display_settings){;?><a href="<?=htmlspecialchars($ILPHP->url);?>">Einstellungen ausblenden</a>
 <?}else{;?><a href="<?=htmlspecialchars($ILPHP->url);?>settings/">Einstellungen einblenden</a>
 <? } ;
 if(m_chat_global::is_ultra_admin()){;?><br>Erstellt von <?=user($ILPHP->data['creator'])->html(-1); } ?>
</p>
<?if($ILPHP->num_admins <= 1 and !$ILPHP->num_allowed_users and !$ILPHP->stats_allowed_groups->num_rows and !$ILPHP->data['points_from'] and !$ILPHP->data['points_to']){;?>
<p class="error">
 Du hast noch keine Berechtigungen verteilt. Momentan kannst nur Du diesen Chat sehen.
</p>
<? } ;

if($ILPHP->display_settings){;?>
<style>li.user-chat-setting { margin-bottom:8px; }</style>
<div class="SEP"><div><div></div><h3><?=htmlspecialchars(lang::_LS_get_define('b1e44042'))?></h3><div></div></div></div>
<ul>
 <li class="user-chat-setting">
 <strong><?=htmlspecialchars(lang::_LS_get_define('d0be3b5b'))?></strong><br>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC(this, '~li');" style="text-align:left;">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="change_name">
 <input type="text" size="65" name="<?=$ILPHP->IMODULE_POST_VAR('name');?>" value="<?=htmlspecialchars($ILPHP->data['name']);?>">
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('37af944d'))?></button>
 </form>
 </li>

 <?ILPHP____templates_c_73f9c2bd_chat_php_head_data_4fcbffea_php_change_default_text($ILPHP)?>

 <?if($ILPHP->possible_categorys->num_rows > 1 or $ILPHP->possible_sub_categorys->num_rows > 1){;?>
 <li class="user-chat-setting">
 <strong><?=htmlspecialchars(lang::_LS_get_define('0a539882'))?></strong><br>
 <?if($ILPHP->possible_categorys->num_rows > 1){;?>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC(this, '~li');" style="text-align:left;">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="change_category">
 <select name="<?=$ILPHP->IMODULE_POST_VAR('category_id');?>">
 <?while($ILPHP->i = $ILPHP->possible_categorys->fetch_assoc()){;?>
 <option value="<?=$ILPHP->i['id'];?>"<?if($ILPHP->i['id'] == $ILPHP->data['category_id']){;?> selected="selected"<? } ?>><?=htmlspecialchars($ILPHP->i['name']);?></option>
 <? } ?>
 </select>
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('76f4b80c'))?></button>
 </form>
 <? } ?>
 </li>
 <?if($ILPHP->data['has_sub_categorys'] and $ILPHP->possible_sub_categorys->num_rows > 1){;?>
 <li class="user-chat-setting">
 <strong><?=htmlspecialchars(lang::_LS_get_define('893f21dd'))?></strong><br>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC(this, '~li');" style="text-align:left;">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="change_sub_category">
 <select name="<?=$ILPHP->IMODULE_POST_VAR('sub_category_id');?>">
 <?while($ILPHP->i = $ILPHP->possible_sub_categorys->fetch_assoc()){;?>
 <option value="<?=$ILPHP->i['id'];?>"<?if($ILPHP->i['id'] == $ILPHP->data['sub_category_id']){;?> selected="selected"<? } ?>><?=htmlspecialchars($ILPHP->i['name']);?></option>
 <? } ?>
 </select>
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('76f4b80c'))?></button>
 </form>
 </p>
 <? } ;
 } ?>
 
 <?ILPHP____templates_c_73f9c2bd_chat_php_head_data_4fcbffea_php_change_lang($ILPHP)?>
 
 <?ILPHP____templates_c_73f9c2bd_chat_php_head_data_4fcbffea_php_change_status($ILPHP)?>
 
 <?ILPHP____templates_c_73f9c2bd_chat_php_head_data_4fcbffea_php_change_input_box($ILPHP)?>

 <?if(m_chat_global::is_admin()){;?>
 <?ILPHP____templates_c_73f9c2bd_chat_php_head_data_4fcbffea_php_change_place($ILPHP)?>
 <? } ;

 if($ILPHP->data['allow_ubb']){;?>
 <li class="user-chat-setting">
 <strong><?=htmlspecialchars(lang::_LS_get_define('fdff6eac'))?></strong><br>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC(this, '~li');" style="text-align:left;">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="change_content_ubb">
 <textarea name="<?=$ILPHP->IMODULE_POST_VAR('content_ubb');?>" class="bbcodeedit"><?=htmlspecialchars($ILPHP->data['content_ubb']);?></textarea>
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('76f4b80c'))?></button>
 </form>
 </li>
 <? } ;

 if($ILPHP->data['allow_html']){;?>
 <li class="user-chat-setting">
 <strong><?=htmlspecialchars(lang::_LS_get_define('59e1c787'))?></strong><br>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC(this, '~li');" style="text-align:left;">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="change_content_html">
 <textarea name="<?=$ILPHP->IMODULE_POST_VAR('content_html');?>" style="width:100%;height:100px;"><?=htmlspecialchars($ILPHP->data['content_html']);?></textarea>
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('76f4b80c'))?></button>
 </form>
 <ul class="info" style="margin-top:5px;">
 <li class="info">
 Um den Inhalt zu Zentrieren nutzt entweder den &quot;center&quot;-Tag: <pre>&lt;center&gt;HIER DER ZENTRIERTE HTML CODE&lt;/center&gt;</pre>
 Oder diesen Code: <pre>&lt;div class=&quot;center&quot;&gt;HIER DER ZENTRIERTE HTML CODE&lt;/div&gt;</pre>
 </li>
 <li class="info">
 Um einen Seperator zu machen (das Teil wo z.B. &quot;Chat anpassen&quot; steht) nutzt diesen Code: <pre>&lt;div class=&quot;SEP&quot;&gt;&lt;div&gt;&lt;div&gt;&lt;/div&gt;&lt;h3&gt;HIER DIE &Uuml;BERSCHRIFT&lt;/h3&gt;&lt;div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;</pre>
 </li>
 </ul>
 </li>
 <? } ?>
</ul>

<div class="SEP"><div><div></div><h3><?=htmlspecialchars(lang::_LS_get_define('66b86083'))?></h3><div></div></div></div>
<ul>
 <li class="user-chat-setting">
 <strong><?=htmlspecialchars(lang::_LS_get_define('8c3a9945'))?></strong><br>
 <ul>
 <li>Wo ein Benutzer verlangt wird muss der Link zu seinem Profil eingetragen werden.</li>
 <li>L&ouml;schen kann man einen Benutzer oder eine Gruppe indem man auf den Button mit dem jeweiligen Namen dr&uuml;ckt.</li>
 </ul>
 </li>
 
 <?ILPHP____templates_c_73f9c2bd_chat_php_head_data_4fcbffea_php_admins($ILPHP)?>

 <?if($ILPHP->available_admin_groups->num_rows or $ILPHP->admin_groups->num_rows){;?>
 <?ILPHP____templates_c_73f9c2bd_chat_php_head_data_4fcbffea_php_admin_groups($ILPHP)?>
 <? } ?>
 
 <?ILPHP____templates_c_73f9c2bd_chat_php_head_data_4fcbffea_php_users($ILPHP)?>

 <?ILPHP____templates_c_73f9c2bd_chat_php_head_data_4fcbffea_php_banned_users($ILPHP)?>

 <?if($ILPHP->available_groups->num_rows or $ILPHP->groups->num_rows){;?>
 <?ILPHP____templates_c_73f9c2bd_chat_php_head_data_4fcbffea_php_groups($ILPHP)?>
 <? } ?>

 <?ILPHP____templates_c_73f9c2bd_chat_php_head_data_4fcbffea_php_needed_points($ILPHP)?>
</ul>
<? } ;

if(!$ILPHP->data['is_admin'] and (!$ILPHP->data['allow_html'] or !$ILPHP->data['content_html']) and (!$ILPHP->data['allow_ubb'] or !$ILPHP->data['content_ubb'])){;?>
<div class="SEP"><div><div></div><h3><?=htmlspecialchars(lang::_LS_get_define('b40d5bf7'))?></h3><div></div></div></div>
<? } ;
 } ;

if(($ILPHP->data['allow_html'] and $ILPHP->data['content_html']) or ($ILPHP->data['allow_ubb'] and $ILPHP->data['content_ubb'])){;?>
<div class="SEP<?if(!$ILPHP->data['is_admin']){;?> first<? } ?>"><div><div></div><h3><?=htmlspecialchars(lang::_LS_get_define('6f5aa60a'))?></h3><div></div></div></div>
<div id="ModuleChat<?=$ILPHP->data['id'];?>Stats">
 <?$ILPHP->ilphp_display('chat.php.head_data.stats.ilp', -1, "", true);?>
</div>
<? } ;
if($ILPHP->data['allow_ubb'] and $ILPHP->data['content_ubb']){;?><p><?=ubbcode::add_smileys(ubbcode::compile($ILPHP->data['content_ubb'], 622));?></p><? } ;
if($ILPHP->data['allow_html'] and $ILPHP->data['content_html']){;?><p><?=$ILPHP->data['content_html'];?></p><? } ;
if($ILPHP->data['is_admin'] or ($ILPHP->data['allow_html'] and $ILPHP->data['content_html']) or ($ILPHP->data['allow_ubb'] and $ILPHP->data['content_ubb'])){;?>
<div class="SEP" style="margin-top:5px;"><div><div></div><h3><?=htmlspecialchars(lang::_LS_get_define('b40d5bf7'))?></h3><div></div></div></div>
<? } ;
if((!$ILPHP->data['allow_ubb'] or !$ILPHP->data['content_ubb']) and (!$ILPHP->data['allow_html'] or !$ILPHP->data['content_html'])){;?>
<div id="ModuleChat<?=$ILPHP->data['id'];?>Stats">
 <?$ILPHP->ilphp_display('chat.php.head_data.stats.ilp', -1, "", true);?>
</div>
<? } ?>
<?}?>