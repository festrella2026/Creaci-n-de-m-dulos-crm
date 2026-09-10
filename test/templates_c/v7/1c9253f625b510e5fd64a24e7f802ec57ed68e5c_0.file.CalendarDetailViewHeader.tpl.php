<?php
/* Smarty version 4.5.5, created on 2026-03-31 14:20:30
  from '/var/www/html/vtigercrm/layouts/v7/modules/Users/CalendarDetailViewHeader.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_69cbd82e92e1f2_32336783',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1c9253f625b510e5fd64a24e7f802ec57ed68e5c' => 
    array (
      0 => '/var/www/html/vtigercrm/layouts/v7/modules/Users/CalendarDetailViewHeader.tpl',
      1 => 1752039682,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69cbd82e92e1f2_32336783 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_assignInScope('MODULE_NAME', $_smarty_tpl->tpl_vars['MODULE_MODEL']->value->get('name'));?><input id="recordId" type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['RECORD']->value->getId();?>
" /><div class="detailViewContainer"><div class="detailViewTitle" id="prefPageHeader"></div><div class="detailViewInfo userPreferences row-fluid"><div class="details col-xs-12"><?php }
}
