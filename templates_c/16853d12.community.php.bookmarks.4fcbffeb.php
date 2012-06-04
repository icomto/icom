<?function ILPHP____templates_c_16853d12_community_php_bookmarks_4fcbffeb_php(&$ILPHP){?><style>
.community-bookmarks {
 text-align:center;
}
.community-bookmarks h2 a {
 font-size:14px;
}
.community-bookmarks table {
 margin:0 auto;
 width:50%;
}
.community-bookmarks table .num-bookmarks {
 text-align:right;
 width:40%;
}
</style>
<div class="community-bookmarks">
 <?if($ILPHP->num_pages > 1){;?><div class="pages"><?=$ILPHP->im_pages_html();?></div><? } ;
 if(!$ILPHP->bookmarks->num_rows){;?><p class="info"><?=htmlspecialchars(lang::_LS_get_define('ead438c4'))?></p>
 <?}else{;?>
 <table border="1">
 <tr>
 <th><?=htmlspecialchars(lang::_LS_get_define('dce7262a'))?></th>
 <th class="num-bookmarks"><?=htmlspecialchars(lang::_LS_get_define('7c51f68c'))?></th>
 </tr>
 <?$ILPHP->while_i=0;while($ILPHP->i = $ILPHP->bookmarks->fetch_assoc()){$ILPHP->while_i++;?>
 <tr class="row">
 <td><?=user($ILPHP->i['user_id'])->html(-1);?></td>
 <td class="num-bookmarks"><?=htmlspecialchars(number_format($ILPHP->i['num'],''?'':0,',','.'));?></td>
 </tr>
 <? } ?>
 </table>
 <?if($ILPHP->num_pages > 1){;?><div class="pages"><?=$ILPHP->im_pages_html();?></div><? } ;
 } ?>
</div>
<?}?>