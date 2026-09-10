<?php
/* Smarty version 4.5.5, created on 2026-04-07 20:15:38
  from '/var/www/html/vtigercrm/layouts/v7/modules/Calendar/AddUserCalendar.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_69d565ea45f5a2_78608156',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd9549c969d8f4b76ef6b68ea01c11580b2330c54' => 
    array (
      0 => '/var/www/html/vtigercrm/layouts/v7/modules/Calendar/AddUserCalendar.tpl',
      1 => 1752039682,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69d565ea45f5a2_78608156 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_assignInScope('SHARED_USER_INFO', Zend_Json::encode($_smarty_tpl->tpl_vars['SHAREDUSERS_INFO']->value));
$_smarty_tpl->_assignInScope('CURRENT_USER_ID', $_smarty_tpl->tpl_vars['CURRENTUSER_MODEL']->value->getId());?><div class="modal-dialog modelContainer modal-content"><?php ob_start();
echo vtranslate('LBL_EDITING_CALENDAR_VIEW',$_smarty_tpl->tpl_vars['MODULE']->value);
$_prefixVariable1 = ob_get_clean();
$_smarty_tpl->_assignInScope('HEADER_TITLE', $_prefixVariable1);
$_smarty_tpl->_subTemplateRender(call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'vtemplate_path' ][ 0 ], array( "ModalHeader.tpl",$_smarty_tpl->tpl_vars['MODULE']->value )), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('TITLE'=>$_smarty_tpl->tpl_vars['HEADER_TITLE']->value), 0, true);
?><div class="modal-body"><form class="form-horizontal"><input type="hidden" class="selectedType" value="" /><input type="hidden" class="selectedColor" value="" /><input type="hidden" class="editorMode" value="create" /><div class="form-group addCalendarViewsList"><label class="control-label fieldLabel col-sm-4"><?php echo vtranslate('LBL_SELECT_USER_CALENDAR',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</label><div class="controls fieldValue col-sm-6"><select class="select2" name="usersList" style="min-width: 250px;"><?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['SHAREDUSERS']->value, 'USER_NAME', false, 'USER_ID');
$_smarty_tpl->tpl_vars['USER_NAME']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['USER_ID']->value => $_smarty_tpl->tpl_vars['USER_NAME']->value) {
$_smarty_tpl->tpl_vars['USER_NAME']->do_else = false;
if ($_smarty_tpl->tpl_vars['SHAREDUSERS_INFO']->value[$_smarty_tpl->tpl_vars['USER_ID']->value]['visible'] == '0') {?><option value="<?php echo $_smarty_tpl->tpl_vars['USER_ID']->value;?>
" data-calendar-group="false"><?php echo $_smarty_tpl->tpl_vars['USER_NAME']->value;?>
</option><?php }
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['SHAREDGROUPS']->value, 'GROUP_NAME', false, 'GROUP_ID');
$_smarty_tpl->tpl_vars['GROUP_NAME']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['GROUP_ID']->value => $_smarty_tpl->tpl_vars['GROUP_NAME']->value) {
$_smarty_tpl->tpl_vars['GROUP_NAME']->do_else = false;
if ($_smarty_tpl->tpl_vars['SHAREDUSERS_INFO']->value[$_smarty_tpl->tpl_vars['GROUP_ID']->value]['visible'] == '0') {?><option value="<?php echo $_smarty_tpl->tpl_vars['GROUP_ID']->value;?>
" data-calendar-group="true"><?php echo $_smarty_tpl->tpl_vars['GROUP_NAME']->value;?>
</option><?php }
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?></select></div></div><div class="form-group"><label class="control-label fieldLabel col-sm-4"><?php echo vtranslate('LBL_SELECT_CALENDAR_COLOR',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</label><div class="controls fieldValue col-sm-8"><p class="calendarColorPicker"></p></div></div></form></div><?php $_smarty_tpl->_subTemplateRender(call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'vtemplate_path' ][ 0 ], array( "ModalFooter.tpl",$_smarty_tpl->tpl_vars['MODULE']->value )), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?></div><?php }
}
