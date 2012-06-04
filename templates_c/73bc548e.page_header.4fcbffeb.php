<?function ILPHP____templates_c_73bc548e_page_header_4fcbffeb_php(&$ILPHP){?><!DOCTYPE HTML>
<html lang="<?=LANG;?>">
 <head>
 <title><?=$ILPHP->SITE_TITLE;?></title>
 <meta HTTP-EQUIV="Content-Type" Content="text/html; charset=utf-8">
 <meta name="Robots" content="index,follow">
 <meta name="Keywords" content="<?=htmlspecialchars($ILPHP->META_KEYWORDS);?>">
 <meta name="Description" content="<?=htmlspecialchars($ILPHP->META_DESCRIPTION);?>">
 <meta name="Language" content="<?=LANG;?>">
 <meta name="Content-Language" content="<?=LANG;?>">
 <meta name="google-site-verification" content="OFr1L3oIwgrOvX5MeMLfvMpUYR_X8Mb3UrcAOWqU0R8">
 <link rel="icon" type="image/ico" href="/favicon.ico?1">
 <link rel="shortcut icon" type="image/gif" href="/favicon.gif?1">
 <?foreach($ILPHP->THEME_INI['THEME_CSS'] as $ILPHP->css){;?>
 <link rel="stylesheet" type="text/css" href="<?=STATIC_CONTENT_DOMAIN;?>/themes/<?=htmlspecialchars($ILPHP->css);?>?ver=<?=htmlspecialchars(RELEASE_VERSION);?>-<?=htmlspecialchars(THEME_VERSION);?>">
 <? } ?>
 
 <script src="<?=STATIC_CONTENT_DOMAIN;?>/lib/jquery/jquery-1.7.2.min.js"></script>
 <?if(LOAD_JAVASCRIPT_MINIFIED){;?>
 <script src="<?=STATIC_CONTENT_DOMAIN;?>/lib/vt-<?=file_get_contents('../lib/vt-hash');?>.js"></script>
 <?}else{;?>
 <script type="text/javascript" src="<?=STATIC_CONTENT_DOMAIN;?>/lib/jquery/superfish.js"></script>
 <script type="text/javascript" src="<?=STATIC_CONTENT_DOMAIN;?>/lib/jquery/jSize.js"></script>
 <script type="text/javascript" src="<?=STATIC_CONTENT_DOMAIN;?>/lib/jquery/jquery.tooltip.js"></script>
 <script type="text/javascript" src="<?=STATIC_CONTENT_DOMAIN;?>/lib/lightbox-0.5/jquery.lightbox-0.5.min.js"></script>
 <script type="text/javascript" src="<?=STATIC_CONTENT_DOMAIN;?>/lib/markitup/jquery.markitup.js"></script>
 <script type="text/javascript" src="<?=STATIC_CONTENT_DOMAIN;?>/lib/markitup/bbcode.js"></script>
 <script type="text/javascript" src="<?=STATIC_CONTENT_DOMAIN;?>/lib/markitup/wiki.js"></script>
 <script type="text/javascript" src="<?=STATIC_CONTENT_DOMAIN;?>/lib/engine.js"></script>
 <? } ?>
 
 <link rel="stylesheet" type="text/css" href="<?=STATIC_CONTENT_DOMAIN;?>/lib/lightbox-0.5/style.css?ver=<?=htmlspecialchars(RELEASE_VERSION);?>-<?=htmlspecialchars(THEME_VERSION);?>">
 <link rel="alternate" type="application/rss+xml" title="<?=htmlspecialchars(lang::_LS_get_define('15d5bed5'))?>" href="/<?=LANG;?>/rss/">
 <style>
 .form-inline {
 display:inline;
 padding:0;
 margin:0;
 }
 .button-inline {
 border:none;
 background:none;
 padding:0 !important;
 margin:0 !important;
 border-radius:0;
 -moz-border-radius:0;
 -webkit-border-radius:0;
 -khtml-border-radius:0;
 }
 .SEP a {
 font-size:inherit;
 }
 </style>
 </head>
 <body class="page-background <?=$ILPHP->THEME['background'];?> <?=$ILPHP->THEME['tooltip'];?> user-logged-<?if(IS_LOGGED_IN){;?>in<?}else{;?>out<? } ?> sitelang-<?=LANG;?>">
 <script>if(self.location!=top.location)top.location=self.location;</script>
 <script>
 var ajaxUpdateInterval=<?if(IS_LOGGED_IN){;?>1*1000<?}else{;?>20*1000<? } ?>;
 var reportAjaxError=function(e){
 <?if(has_privilege('developer')){;?>
 $('#AjaxLoadErrorText').html(e);
 $('#AjaxLoadError').show();
 <? } ?>
 };
 var ilrtd={current_language:'<?=LANG;?>'};
 var ajaxUpdateIntervalHandle = setInterval('ajaxUpdate();', 1000);
 </script>
 <script>var LANG_TIME=<?=json_encode($ILPHP->LANG_TIME);?>;</script>
 
 <?if(has_privilege('developer')){;?>
 <div id="AjaxLoadError" class="ajax-load-error" style="display:none;">
 <a href="" onclick="$(this).parent().hide();return false;">
 <p id="AjaxLoadErrorText"></p>
 <p><?=htmlspecialchars(lang::_LS_get_define('a668230c'))?></p>
 </a>
 </div>
 <? } ?>
 <style>
 .ajax-loader-ic {
 background:#eee url('<?=STATIC_CONTENT_DOMAIN;?>/img/ajax-loader2.gif') center no-repeat;
 opacity:0.7;
 z-index:1000;
 position:absolute;
 border-radius:4px;
 -moz-border-radius:4px;
 -webkit-border-radius:4px;
 -khtml-border-radius:4px;
 box-shadow:0 0 4px #eee;
 -moz-box-shadow:0 0 4px #eee;
 -webkit-box-shadow:0 0 4px #eee;
 -khtml-box-shadow:0 0 4px #eee;
 }
 </style>
 <div style="display:none;">
 <div id="AjaxModuleLoader">
 <div class="module-item">
 <h1><?=htmlspecialchars(lang::_LS_get_define('c2bd448b'))?></h1>
 <div class="module-content ajax-module-loader">
 <img src="<?=STATIC_CONTENT_DOMAIN;?>/img/ajax-loader.gif" alt="<?=htmlspecialchars(lang::_LS_get_define('fbb9ff03'))?>">
 </div>
 <div class="module-footer"></div>
 </div>
 </div>
 <div id="AjaxElementLoader1">
 <div class="ajax-element-loader1">
 <img src="<?=STATIC_CONTENT_DOMAIN;?>/img/loading.gif" alt="" width="24" height="24">
 </div>
 </div>
 <div id="AjaxElementLoader2">
 <div class="ajax-element-loader2">
 <img src="<?=STATIC_CONTENT_DOMAIN;?>/img/ajax-loader.gif" alt="<?=htmlspecialchars(lang::_LS_get_define('fbb9ff03'))?>">
 </div>
 </div>
 </div>
<?}?>