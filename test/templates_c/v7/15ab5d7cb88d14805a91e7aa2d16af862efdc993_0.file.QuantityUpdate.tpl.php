<?php
/* Smarty version 4.5.5, created on 2026-03-25 21:08:36
  from '/var/www/html/vtigercrm/layouts/v7/modules/Products/QuantityUpdate.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_69c44ed4c78357_08582008',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '15ab5d7cb88d14805a91e7aa2d16af862efdc993' => 
    array (
      0 => '/var/www/html/vtigercrm/layouts/v7/modules/Products/QuantityUpdate.tpl',
      1 => 1752039682,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69c44ed4c78357_08582008 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div id="quantityUpdateContainer" class="modal-dialog modal-sm">
	<div class="modal-content">
		<?php ob_start();
echo vtranslate('LBL_EDIT_QUANTITY',$_smarty_tpl->tpl_vars['MODULE']->value);
$_prefixVariable1 = ob_get_clean();
$_smarty_tpl->_subTemplateRender(call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'vtemplate_path' ][ 0 ], array( "ModalHeader.tpl",$_smarty_tpl->tpl_vars['MODULE']->value )), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('TITLE'=>$_prefixVariable1), 0, true);
?>
		<form class="form-horizontal" id="quantityUpdate" method="post" action="index.php">
			<input type="hidden" name="module" value="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
" />
			<input type="hidden" name="action" value="RelationAjax" />
			<input type="hidden" name="src_record" value="<?php echo $_smarty_tpl->tpl_vars['RECORD_ID']->value;?>
" />
			<input type="hidden" name="relid" value="<?php echo $_smarty_tpl->tpl_vars['REL_ID']->value;?>
"/> 
			<div class="modal-body">
				<div class="row">
					<span class="col-lg-6"><label for="quantityEdit" class="pull-right" style="margin-top: 5px;"><?php echo vtranslate('LBL_EDIT_QUANTITY',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</label></span>
					<span class="col-lg-6">
						<input id="quantityEdit" data-rule-positiveExcludingZero=true data-rule-positive=true class="form-control" type="text" name="quantity" value="<?php echo $_smarty_tpl->tpl_vars['CURRENT_QTY']->value;?>
">
					</span>
				</div>
			</div>
			<?php $_smarty_tpl->_subTemplateRender(call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'vtemplate_path' ][ 0 ], array( 'ModalFooter.tpl',$_smarty_tpl->tpl_vars['MODULE']->value )), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
		</form>	
	</div>
</div><?php }
}
