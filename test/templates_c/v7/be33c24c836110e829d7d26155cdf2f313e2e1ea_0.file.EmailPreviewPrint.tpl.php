<?php
/* Smarty version 4.5.5, created on 2026-05-05 14:55:12
  from '/var/www/html/vtigercrm/layouts/v7/modules/Vtiger/EmailPreviewPrint.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_69fa04d0a0a219_82290463',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'be33c24c836110e829d7d26155cdf2f313e2e1ea' => 
    array (
      0 => '/var/www/html/vtigercrm/layouts/v7/modules/Vtiger/EmailPreviewPrint.tpl',
      1 => 1752039682,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69fa04d0a0a219_82290463 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/var/www/html/vtigercrm/vendor/smarty/smarty/libs/plugins/modifier.replace.php','function'=>'smarty_modifier_replace',),));
?>
<!DOCTYPE><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body onLoad="javascript:window.print()" style="padding:3% 5%;"><?php $_smarty_tpl->_assignInScope('TO_EMAILS', smarty_modifier_replace($_smarty_tpl->tpl_vars['RECORD']->value->get('saved_toid'),']',''));
$_smarty_tpl->_assignInScope('TO_EMAIL', smarty_modifier_replace($_smarty_tpl->tpl_vars['TO_EMAILS']->value,'[',''));
$_smarty_tpl->_assignInScope('TO_EMAIL_VALUE', smarty_modifier_replace($_smarty_tpl->tpl_vars['TO_EMAIL']->value,'&quot;',''));?><span style="position:absolute;right:6%;top:3%;font-family:'Lucida Grande';font-size:15px"><?php echo $_smarty_tpl->tpl_vars['USER_MODEL']->value->get('first_name');?>
 <?php echo $_smarty_tpl->tpl_vars['USER_MODEL']->value->get('last_name');?>
 &lt;<?php echo $_smarty_tpl->tpl_vars['USER_MODEL']->value->get('email1');?>
&gt;</span><hr/><span style="font-family:'Lucida Grande';font-size:15px"><?php echo $_smarty_tpl->tpl_vars['RECORD']->value->get('subject');?>
</span><hr/><div><div style="width:100%;text-align: right;font-family:'Lucida Grande';font-size:15px"><span><?php echo Vtiger_Util_Helper::formatDateTimeIntoDayString($_smarty_tpl->tpl_vars['RECORD']->value->get('createdtime'));?>
</span></div></div><div><div style="float:left;width:10%;text-align: right;font-family:'Lucida Grande';font-size:15px"><?php echo vtranslate('LBL_FROM',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</div><div style="width:90%;text-align: left;font-family:'Lucida Grande';font-size:15px"><span style="margin-left:7%;"><?php echo $_smarty_tpl->tpl_vars['FROM']->value;?>
</span></div><div class="clear-both"></div></div><div><div style="float:left;width:10%;text-align: right;font-family:'Lucida Grande';font-size:15px"><?php echo vtranslate('LBL_TO',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</div><div style="width:90%;text-align: left;font-family:'Lucida Grande';font-size:15px"><span style="margin-left:7%;font-family:'Lucida Grande';font-size:15px"><?php $_smarty_tpl->_assignInScope('TO_EMAILS', call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'implode' ][ 0 ], array( ",",$_smarty_tpl->tpl_vars['TO']->value )));
echo $_smarty_tpl->tpl_vars['TO_EMAILS']->value;?>
</span></div><div class="clear-both"></div></div><?php if (!empty($_smarty_tpl->tpl_vars['CC']->value)) {?><div><div style="float:left;width:10%;text-align: right;font-family:'Lucida Grande';font-size:15px"><?php echo vtranslate('LBL_CC',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</div><div style="width:90%;text-align: left;font-family:'Lucida Grande';font-size:15px"><span style="margin-left:7%;"><?php if (!empty($_smarty_tpl->tpl_vars['CC']->value)) {
echo $_smarty_tpl->tpl_vars['CC']->value;
}?></span></div><div class="clear-both"></div></div><?php }
if (!empty($_smarty_tpl->tpl_vars['BCC']->value)) {?><div><div style="float:left;width:10%;text-align: right;font-family:'Lucida Grande';font-size:15px"><?php echo vtranslate('LBL_BCC',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</div><div style="width:90%;text-align: left;font-family:'Lucida Grande';font-size:15px"><span style="margin-left:7%;"><?php if (!empty($_smarty_tpl->tpl_vars['BCC']->value)) {
echo $_smarty_tpl->tpl_vars['BCC']->value;
}?></span></div><div class="clear-both"></div></div><?php }?><div><div style="float:left;width:10%;text-align: right;font-family:'Lucida Grande';font-size:15px"><?php echo vtranslate('LBL_SUBJECT',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</div><div style="width:90%;text-align: left;font-family:'Lucida Grande';font-size:15px"><span style="margin-left:7%;"><?php echo $_smarty_tpl->tpl_vars['RECORD']->value->get('subject');?>
</span></div><div class="clear-both"></div></div><?php $_smarty_tpl->_assignInScope('ATTACHMENT_DETAILS', $_smarty_tpl->tpl_vars['RECORD']->value->getAttachmentDetails());
if (!empty($_smarty_tpl->tpl_vars['ATTACHMENT_DETAILS']->value)) {?><div><div style="float:left;width:10%;text-align: right;font-family:'Lucida Grande';font-size:15px"><?php echo vtranslate('LBL_ATTACHMENT',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</div><div style="width:90%;text-align: left;font-family:'Lucida Grande';font-size:15px"><span style="margin-left:7%;"><?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['ATTACHMENT_DETAILS']->value, 'ATTACHMENT_DETAIL');
$_smarty_tpl->tpl_vars['ATTACHMENT_DETAIL']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['ATTACHMENT_DETAIL']->value) {
$_smarty_tpl->tpl_vars['ATTACHMENT_DETAIL']->do_else = false;
?><a href="javascript:void(0)"><?php echo $_smarty_tpl->tpl_vars['ATTACHMENT_DETAIL']->value['attachment'];?>
</a>&nbsp;&nbsp;<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?></span></div><div class="clear-both"></div></div><?php }?><div><div style="width:90%;text-align: left;margin-left:7%;font-family:'Lucida Grande';font-size:15px"><span style="margin-left:7%;"><?php echo decode_html($_smarty_tpl->tpl_vars['RECORD']->value->get('description'));?>
</span></div><div class="clear-both"></div></div></body></html>
<?php }
}
