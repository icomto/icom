<?function ILPHP____templates_c_73f9c2bd_pn_php_icon_4fcbffeb_php(&$ILPHP){?><a href="/<?=LANG;?>/pns/" class="pns<?if($ILPHP->has_new_pns){;?> blink<? } ?>" title="<?=htmlspecialchars(lang::_LS_get_define('23f0a2b3'))?>"><?=htmlspecialchars(lang::_LS_get_define('a30f74c6'))?><img src="/img/p.gif"></a>
<script>try{if(tupni)clearInterval(tupni);}catch(e){}tupni=null;</script>
<?if($ILPHP->has_new_pns){;?>
<script>var tupni=setInterval(function(){var x=$('#MenuPNs .pns');if(x.hasClass('blink'))x.removeClass('blink');else x.addClass('blink');},500);</script>
<? } ?>
<?}?>