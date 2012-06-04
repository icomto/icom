<?function ILPHP____templates_c_16853d12_community_php_groups_4fcbffeb_php(&$ILPHP){?><style>
.community-groups table {
 margin:0 auto;
 width:50%;
}
.community-groups table .num-members {
 text-align:right;
 width:30%;
}
</style>
<div class="community-groups">
 <table border="1">
 <tr>
 <th><?=htmlspecialchars(lang::_LS_get_define('0cfdc80e'))?></th>
 <th class="num-members"><?=htmlspecialchars(lang::_LS_get_define('77476663'))?></th>
 </tr>
 <?while($ILPHP->i = $ILPHP->groups->fetch_assoc()){;?>
 <tr class="row">
 <td><a href="/<?=LANG;?>/community/users/group/<?=$ILPHP->i['id'];?>-<?=urlenc($ILPHP->i['name']);?>/"><img src="<?=STATIC_CONTENT_DOMAIN;?>/img/groups/<?=$ILPHP->i['id'];?>.gif" alt="" width="16" height="12"> <?=htmlspecialchars($ILPHP->i['name']);?></a></td>
 <td class="num-members"><?=htmlspecialchars(number_format($ILPHP->i['num'],''?'':0,',','.'));?></td>
 </tr>
 <? } ?>
 </table>
</div>
<?}?>