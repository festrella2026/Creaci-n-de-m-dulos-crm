<?php
/* Smarty version 4.5.5, created on 2026-03-25 18:02:00
  from '/var/www/html/vtigercrm/layouts/v7/modules/Vtiger/uitypes/SalutationDetailView.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_69c42318799867_00206584',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a7aaee3e13b26b5f08bf265133b19865ddb2b7c7' => 
    array (
      0 => '/var/www/html/vtigercrm/layouts/v7/modules/Vtiger/uitypes/SalutationDetailView.tpl',
      1 => 1752039682,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69c42318799867_00206584 (Smarty_Internal_Template $_smarty_tpl) {
echo $_smarty_tpl->tpl_vars['RECORD']->value->getDisplayValue('salutationtype');?>


<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getDisplayValue($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('fieldvalue'),$_smarty_tpl->tpl_vars['RECORD']->value->getId(),$_smarty_tpl->tpl_vars['RECORD']->value);
}
}
