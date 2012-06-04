<?function ILPHP____templates_c_1a86327f_wiki_php_system_messages_4fcbffeb_php(&$ILPHP){?><?foreach($ILPHP->errors as $ILPHP->ewi){;?>
<div class="error">
 <?switch($ILPHP->ewi){;
 default:;echo htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('be1030f4'),$ILPHP->ewi));break;
 case 'ARTICLE_NOT_CHANGED':;echo htmlspecialchars(lang::_LS_get_define('97d149a0'));break;
 case 'NO_ARTICLES':;echo htmlspecialchars(lang::_LS_get_define('26a5dc5f'));break;
 case 'NO_CATEGORYS':;echo htmlspecialchars(lang::_LS_get_define('04d1c742'));break;
 case 'PAGES_INVALID_TYPE':;echo htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('89cc83d5'),$ILPHP->type));break;
 case 'HISTORY_NOT_SET':;echo htmlspecialchars(lang::_LS_get_define('ce909b36'));break;
 case 'HISTORY_NOT_FOUND':;echo htmlspecialchars(lang::_LS_get_define('d977edd1'));break;
 case 'HISTORY_SET_ACCESS_DENIED':;echo htmlspecialchars(lang::_LS_get_define('275b2cdf'));break;
 case 'PAGE_RENAME_ALREADY_EXISTS':;echo lang::_handle_template_string(lang::_LS_get_define('80e40d20'),$ILPHP->newname);break;
 case 'PAGE_RENAME_SAME_NAME':;echo lang::_LS_get_define('817c0bbe');break;
 } ?>
</div>
<? } ;
foreach($ILPHP->warnings as $ILPHP->ewi){;?>
<div class="warning">
 <?switch($ILPHP->ewi){;
 default:;echo htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('396d320c'),$ILPHP->ewi));break;
 case 'PAGE_EDIT_LOCKED_ACCESS_DENIED':;echo htmlspecialchars(lang::_LS_get_define('b36db634'));break;
 case 'HAS_OPEN_TICKETS':;echo lang::_handle_template_string(lang::_LS_get_define('2ea7c46a'),htmlspecialchars(LANG),htmlspecialchars(wiki_urlencode($ILPHP->this->wiki)));break;
 } ?>
</div>
<? } ;
foreach($ILPHP->infos as $ILPHP->ewi){;?>
<div class="info">
 <?switch($ILPHP->ewi){;
 default:;echo htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('863d01bf'),$ILPHP->ewi));break;
 case 'PAGE_ADDED_WAITING_FOR_PUBLISH':;echo lang::_LS_get_define('0ba2797b');break;
 case 'NO_LATEST_CHANGES_FOUND':;echo htmlspecialchars(lang::_LS_get_define('79bb0166'));break;
 case 'NO_HISTORY_ACTIVATED':;echo htmlspecialchars(lang::_LS_get_define('d5050d64'));break;
 case 'ARTICLE_FOR_RELEASE':;echo lang::_handle_template_string(lang::_LS_get_define('5a370362'),LANG,LANG);break;
 case 'ARTICLE_FOR_TITLE':;echo lang::_handle_template_string(lang::_LS_get_define('e93f6547'),LANG,LANG);break;
 case 'TICKET_CREATED':;echo htmlspecialchars(lang::_LS_get_define('cefe653a'));break;
 case 'TICKET_CLOSED':;echo htmlspecialchars(lang::_LS_get_define('36f6cff8'));break;
 case 'TICKET_OPENED':;echo htmlspecialchars(lang::_LS_get_define('d5c42ba8'));break;
 } ?>
</div>
<? } ?>
<?}?>