<?function ILPHP____templates_c_73f9c2bd_index_php_menu_item_4fcbffeb_php(&$ILPHP){?><form class="side-menu-header<?if($ILPHP->first){;?> menu-first<? } ?>" method="post" onclick="return mslide(this, '<?=htmlspecialchars($ILPHP->name);?>');" onsubmit="return mslide(this, '<?=htmlspecialchars($ILPHP->name);?>');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="slide">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('module');?>" value="<?=$ILPHP->module;?>">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('id');?>" value="<?=$ILPHP->args_;?>">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('display');?>" value="<?=$ILPHP->display ? 'no' : 'yes';?>">
 <?=htmlspecialchars($ILPHP->title);?>
</form>
<div class="side-menu-content"<?if(!$ILPHP->display){;?> style="display:none;"<? } ?>>
 <div <?if($ILPHP->classes){;?>class="<?=htmlspecialchars($ILPHP->classes);?>" <? } ?>id="IM_MENU_<?=htmlspecialchars($ILPHP->name);?>">
 <?=$ILPHP->content;?>
 </div>
</div>
<?}?>