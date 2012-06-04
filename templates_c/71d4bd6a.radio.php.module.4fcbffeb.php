<?function ILPHP____templates_c_71d4bd6a_radio_php_module_4fcbffeb_php(&$ILPHP){?><div class="menu-radio-tabs side-menu-tabs" id="radio_tabs">
 <?=$ILPHP->tabs();?>
</div>
<div class="clear"></div>
<div id="radio_stats">
 <?=$ILPHP->stats();?>
</div>
<div class="menu-radio-player"> 
 <embed src="<?=STATIC_CONTENT_DOMAIN;?>/swf/dewplayer-stream.swf?mp3=http://<?=$ILPHP->channel['host'];?>:<?=$ILPHP->channel['port'];?>/;&amp;autostart=1&amp;nopointer=1&amp;ext=.mp3&amp;volume=25" wmode="transparent" quality="high" bgcolor="#ffffff" width="135" height="50" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer">
</div>
<br>
<div class="menu-radio-links">
 <a href="/<?=LANG;?>/radio/<?=htmlspecialchars($ILPHP->channel['channel']);?>/playlist/pls/" target="_blank"><img src="<?=STATIC_CONTENT_DOMAIN;?>/img/p.gif" class="p menu-radio-icon-winamp"></a><a href="/<?=LANG;?>/radio/<?=htmlspecialchars($ILPHP->channel['channel']);?>/playlist/pls/" target="_blank"><img src="<?=STATIC_CONTENT_DOMAIN;?>/img/p.gif" class="p menu-radio-icon-vlc"></a><a href="/<?=LANG;?>/radio/<?=htmlspecialchars($ILPHP->channel['channel']);?>/playlist/wmp/" target="_blank"><img src="<?=STATIC_CONTENT_DOMAIN;?>/img/p.gif" class="p menu-radio-icon-wmp"></a>
</div>
<?}?>