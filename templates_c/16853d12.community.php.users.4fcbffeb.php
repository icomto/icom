<?function ILPHP____templates_c_16853d12_community_php_users_4fcbffeb_php(&$ILPHP){?><style>
.community-users {
 text-align:center;
}
.community-users h2 a {
 font-size:14px;
}
.community-users table {
 margin:0 auto;
 width:50%;
}
.community-users table .groups-of-user {
 width:30%;
}
</style>
<div class="community-users">
 <h2>
 <?if($ILPHP->search){;
 if($ILPHP->group['id'] == 'all'){;?><a href="<?=htmlspecialchars($ILPHP->url);?>"><?=htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('b0e8fad8'),$ILPHP->search))?></a>
 <?}else{;?><a href="<?=htmlspecialchars($ILPHP->url);?>"><?=htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('a3d0776d'),$ILPHP->search,$ILPHP->group['name']))?></a>
 <? } ;
 }else{;
 if($ILPHP->group['id'] == 'all'){;?><a href="<?=htmlspecialchars($ILPHP->url);?>"><?=htmlspecialchars(lang::_LS_get_define('3386cc4a'))?></a>
 <?}else{;?><a href="<?=htmlspecialchars($ILPHP->url);?>"><?=htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('861161af'),$ILPHP->group['name']))?>&quot;</a>
 <? } ;
 } ?>
 </h2>
 <?if($ILPHP->num_pages > 1){;?><div class="pages"><?=$ILPHP->im_pages_html();?></div><? } ;
 if(!$ILPHP->users->num_rows){;?><p class="info"><?=htmlspecialchars(lang::_LS_get_define('938214a1'))?></p>
 <?}else{;?>
 <table border="1">
 <tr>
 <th><?=htmlspecialchars(lang::_LS_get_define('0cfdc80e'))?></th>
 <th class="groups-of-user"><?=htmlspecialchars(lang::_LS_get_define('1cf2ef31'))?></th>
 </tr>
 <?$ILPHP->while_i=0;while($ILPHP->i = $ILPHP->users->fetch_assoc()){$ILPHP->while_i++;?>
 <tr class="row">
 <td><?=user($ILPHP->i['user_id'])->html(-1);?></td>
 <td class="groups-of-user">
 <?$ILPHP->i['groups'] = explode_arr_list($ILPHP->i['groups']);
 ksort($ILPHP->i['groups']);
 $ILPHP->num = count($ILPHP->i['groups']);
 $ILPHP->j = 0;
 foreach($ILPHP->i['groups'] as $ILPHP->g){;if(isset($ILPHP->groups[$ILPHP->g]) and $ILPHP->groups[$ILPHP->g]['public']){;if($ILPHP->j++){;?>, <? } ?><a href="/<?=LANG;?>/community/users/group/<?=$ILPHP->g;?>-<?=urlenc($ILPHP->groups[$ILPHP->g]['name']);?>/"><?=htmlspecialchars($ILPHP->groups[$ILPHP->g]['name']);?></a><? } ;
 } ?>
 </td>
 </tr>
 <? } ?>
 </table>
 <?if($ILPHP->num_pages > 1){;?><div class="pages"><?=$ILPHP->im_pages_html();?></div><? } ;
 } ?>
</div>
<?}?>