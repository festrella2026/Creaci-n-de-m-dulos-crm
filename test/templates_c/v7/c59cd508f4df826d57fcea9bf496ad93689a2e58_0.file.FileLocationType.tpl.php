<?php
/* Smarty version 4.5.5, created on 2026-03-30 20:59:33
  from '/var/www/html/vtigercrm/layouts/v7/modules/Vtiger/uitypes/FileLocationType.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_69cae4351a40c3_80820121',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c59cd508f4df826d57fcea9bf496ad93689a2e58' => 
    array (
      0 => '/var/www/html/vtigercrm/layouts/v7/modules/Vtiger/uitypes/FileLocationType.tpl',
      1 => 1752039682,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69cae4351a40c3_80820121 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_assignInScope('FIELD_VALUES', $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getFileLocationType());?><select class="select2" name="<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getFieldName();?>
" <?php if ((isset($_smarty_tpl->tpl_vars['FILE_LOCATION_TYPE']->value)) && ($_smarty_tpl->tpl_vars['FILE_LOCATION_TYPE']->value == 'I' || $_smarty_tpl->tpl_vars['FILE_LOCATION_TYPE']->value == 'E')) {?> disabled <?php }?>><?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['FIELD_VALUES']->value, 'TYPE', false, 'KEY');
$_smarty_tpl->tpl_vars['TYPE']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['KEY']->value => $_smarty_tpl->tpl_vars['TYPE']->value) {
$_smarty_tpl->tpl_vars['TYPE']->do_else = false;
if ((isset($_smarty_tpl->tpl_vars['FILE_LOCATION_TYPE']->value)) && $_smarty_tpl->tpl_vars['FILE_LOCATION_TYPE']->value == 'I') {
$_smarty_tpl->_assignInScope('SELECTED', 'I');
} elseif ((isset($_smarty_tpl->tpl_vars['FILE_LOCATION_TYPE']->value)) && $_smarty_tpl->tpl_vars['FILE_LOCATION_TYPE']->value == 'E') {
$_smarty_tpl->_assignInScope('SELECTED', 'E');
} else {
$_smarty_tpl->_assignInScope('SELECTED', $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue'));
}?><option value="<?php echo $_smarty_tpl->tpl_vars['KEY']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['SELECTED']->value == $_smarty_tpl->tpl_vars['KEY']->value) {?> selected <?php }?>><?php echo vtranslate($_smarty_tpl->tpl_vars['TYPE']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
</option><?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?></select><?php }
}
