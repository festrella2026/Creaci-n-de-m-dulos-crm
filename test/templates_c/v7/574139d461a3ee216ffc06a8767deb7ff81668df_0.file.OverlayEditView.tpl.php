<?php
/* Smarty version 4.5.5, created on 2026-05-14 14:35:42
  from '/var/www/html/vtigercrm/layouts/v7/modules/Events/OverlayEditView.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_6a05ddbe9c5a96_99797066',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '574139d461a3ee216ffc06a8767deb7ff81668df' => 
    array (
      0 => '/var/www/html/vtigercrm/layouts/v7/modules/Events/OverlayEditView.tpl',
      1 => 1752039682,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6a05ddbe9c5a96_99797066 (Smarty_Internal_Template $_smarty_tpl) {
echo '<script'; ?>
 type="text/javascript" src="<?php echo vresource_url('layouts/v7/modules/Calendar/resources/Edit.js');?>
"><?php echo '</script'; ?>
>
<?php $_smarty_tpl->_subTemplateRender(vtemplate_path('OverlayEditView.tpl','Vtiger'), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
}
