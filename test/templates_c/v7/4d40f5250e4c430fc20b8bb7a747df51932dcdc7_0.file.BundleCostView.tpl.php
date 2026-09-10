<?php
/* Smarty version 4.5.5, created on 2026-03-25 21:07:32
  from '/var/www/html/vtigercrm/layouts/v7/modules/Products/BundleCostView.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_69c44e9492b5e4_91645628',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '4d40f5250e4c430fc20b8bb7a747df51932dcdc7' => 
    array (
      0 => '/var/www/html/vtigercrm/layouts/v7/modules/Products/BundleCostView.tpl',
      1 => 1752039682,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69c44e9492b5e4_91645628 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class ="col-lg-12" style="margin-top:20px;">
    <?php ob_start();
echo ucfirst(vtranslate('LBL_IN',$_smarty_tpl->tpl_vars['MODULE']->value));
$_prefixVariable1=ob_get_clean();
ob_start();
echo getCurrencyName($_smarty_tpl->tpl_vars['USER_MODEL']->value->get('currency_id'),false);
$_prefixVariable2=ob_get_clean();
ob_start();
echo vtranslate('LBL_PRODUCT_NAME',$_smarty_tpl->tpl_vars['MODULE']->value);
$_prefixVariable3=ob_get_clean();
ob_start();
echo vtranslate('LBL_PRICE_QUANTITY',$_smarty_tpl->tpl_vars['MODULE']->value);
$_prefixVariable4=ob_get_clean();
ob_start();
echo vtranslate('LBL_TOTAL',$_smarty_tpl->tpl_vars['MODULE']->value);
$_prefixVariable5=ob_get_clean();
ob_start();
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['SUB_PRODUCTS_COSTS_INFO']->value, 'SUB_PRODUCT_COST_INFO', false, 'SUB_PRODUCT_ID');
$_smarty_tpl->tpl_vars['SUB_PRODUCT_COST_INFO']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['SUB_PRODUCT_ID']->value => $_smarty_tpl->tpl_vars['SUB_PRODUCT_COST_INFO']->value) {
$_smarty_tpl->tpl_vars['SUB_PRODUCT_COST_INFO']->do_else = false;
echo "<tr><td>";
echo (string)$_smarty_tpl->tpl_vars['SUB_PRODUCT_COST_INFO']->value['productName'];
echo "</td><td>";
echo (string)$_smarty_tpl->tpl_vars['SUB_PRODUCT_COST_INFO']->value['actualPrice'];
echo " X ";
echo (string)$_smarty_tpl->tpl_vars['SUB_PRODUCT_COST_INFO']->value['quantityInBundle'];
echo "</td> <td align='right'> ";
echo (string)$_smarty_tpl->tpl_vars['SUB_PRODUCT_COST_INFO']->value['priceInUserFormat'];
echo "</td></tr>";
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
$_prefixVariable6=ob_get_clean();
ob_start();
echo vtranslate('LBL_BUNDLE_TOTAL_COST',$_smarty_tpl->tpl_vars['MODULE']->value);
$_prefixVariable7=ob_get_clean();
$_smarty_tpl->_assignInScope('CALCULATION_INFO', $_prefixVariable1." <strong>".$_prefixVariable2."</strong><br /><br /> <table class = 'table table-bordered'><thead><th width='40%'>".$_prefixVariable3." </th><th width='35%'>".$_prefixVariable4."</th><th width = 25%>".$_prefixVariable5."</th></thead>".$_prefixVariable6."<tr></table><br /><div class = 'pull-right' style = 'padding-right:5px'><strong>".$_prefixVariable7." : ".((string)$_smarty_tpl->tpl_vars['SUB_PRODUCTS_TOTAL_COST']->value)."</strong></div>");?>
    <label><?php echo vtranslate('LBL_BUNDLE_TOTAL_COST',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 :&nbsp;&nbsp;<a class ="totalCostCalculationInfo" role="button" data-trigger="focus" title = "<?php echo vtranslate('LBL_BUNDLE_TOTAL_COST',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" tabindex="0" data-toggle="popover" data-content="<?php echo $_smarty_tpl->tpl_vars['CALCULATION_INFO']->value;?>
"><span class="subProductsTotalCost"><?php echo $_smarty_tpl->tpl_vars['SUB_PRODUCTS_TOTAL_COST']->value;?>
</span></a>
    </label>
    <?php ob_start();
echo CurrencyField::convertToUserFormat((float)$_smarty_tpl->tpl_vars['PARENT_RECORD']->value->get('unit_price'),'',true,true);
$_prefixVariable8=ob_get_clean();
$_smarty_tpl->_assignInScope('PRODUCT_ACTUAL_PRICE', $_prefixVariable8);?>
    &nbsp;&nbsp;
    <button type="button" id ="updatePrice" class="btn btn-sm btn-dark btn-default"
            <?php if ($_smarty_tpl->tpl_vars['SUB_PRODUCTS_TOTAL_COST']->value == $_smarty_tpl->tpl_vars['PRODUCT_ACTUAL_PRICE']->value && $_smarty_tpl->tpl_vars['USER_MODEL']->value->get('currency_id') == $_smarty_tpl->tpl_vars['PARENT_RECORD']->value->get('currency_id')) {?>
                disabled
            <?php }?>>
        <i class="fa fa-refresh"><strong>&nbsp;&nbsp;<?php echo vtranslate('LBL_UPDATE_BUNDLE_PRICE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</strong></i>
    </button>
</div>
<?php }
}
