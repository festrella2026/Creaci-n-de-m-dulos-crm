<?php
/* Smarty version 4.5.5, created on 2026-04-25 20:30:23
  from '/var/www/html/vtigercrm/layouts/v7/modules/Users/FPLogin.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_69ed245f78d863_83244103',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '81f306ca085c22a36d7f9b7d281861bdf7beb42b' => 
    array (
      0 => '/var/www/html/vtigercrm/layouts/v7/modules/Users/FPLogin.tpl',
      1 => 1752039682,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69ed245f78d863_83244103 (Smarty_Internal_Template $_smarty_tpl) {
?>

<?php if ($_smarty_tpl->tpl_vars['ERROR']->value) {?>
	<?php echo $_smarty_tpl->tpl_vars['MESSAGE']->value;?>

<?php } else { ?>
	<h4>Loading .... </h4>
	<form class="form-horizontal" name="login" id="login" method="post" action="../../../index.php?module=Users&action=Login">
		<input type=hidden name="username" value="<?php echo $_smarty_tpl->tpl_vars['USERNAME']->value;?>
" >
		<input type=hidden name="password" value="<?php echo $_smarty_tpl->tpl_vars['PASSWORD']->value;?>
" >
	</form>
	<?php echo '<script'; ?>
 type="text/javascript">
		function autoLogin () {
			var form = document.getElementById("login");
			form.submit();
		}
		window.onload = autoLogin;
	<?php echo '</script'; ?>
>
<?php }
}
}
