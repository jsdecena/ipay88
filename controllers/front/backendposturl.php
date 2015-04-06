<?php
/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */

class iPay88BackEndPostUrlModuleFrontController extends ModuleFrontController
{
	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{		
		$this->display_column_left = false;
		parent::initContent();		
	}

	public function process()
	{
		//RESPONSE SIG CONTAINS ADDITIONAL PAYMENT ID [3RD PARAM] ( 1 FOR MASTERCARD/ VISA ) AND THE LAST PARAMETER WHICH IS THE STATUS CODE
		$response_sig = $this->iPay88_signature(
			Configuration::get('MKEY') . //MERCHANT KEY
			Configuration::get('MCODE') . //MERCHANT CODE
			"1" . // 1 FOR MASTERCARD / VISA 
			$_REQUEST['RefNo']. //REF
			str_replace(".", "", $_REQUEST['Amount']) . //TOTAL AMOUNT WITHOUT DOT
			$this->context->currency->iso_code . //CURRENCY CODE
			$_REQUEST['Status'] //STATUS FROM iPay88
		);

		$this->_logToFile(_LOG_DIR_.'/backendpost-'. date("Y-m-d").'.log', $response_sig);
		$this->_logToFile(_LOG_DIR_.'/backendpost-'. date("Y-m-d").'.log', $_REQUEST['Signature']);

		//IF THE SIGNATURE I KNOW AND THE IPAY88 MATCH
		if ( $response_sig == $_REQUEST['Signature'] ) :
			
			//CHECK IF THE ORDER IS SUCCESSFUL
			if ( $_REQUEST['Status'] == "1") :

				$cart = $this->context->cart;
				if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active)
					Tools::redirect('index.php?controller=order&step=1');

				// Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
				$authorized = false;
				foreach (Module::getPaymentModules() as $module)
					if ($module['name'] == 'ipay88')
					{
						$authorized = true;
						break;
					}
				if (!$authorized)
					die($this->module->l('This payment method is not available.', 'validation'));

				$customer = new Customer($cart->id_customer);
				if (!Validate::isLoadedObject($customer))
					Tools::redirect('index.php?controller=order&step=1');

				$currency = $this->context->currency;
				$total = (float)$cart->getOrderTotal(true, Cart::BOTH);

				$this->module->validateOrder($cart->id, Configuration::get('PS_OS_PAYMENT'), $total, $this->module->displayName, NULL, null, (int)$currency->id, false, $customer->secure_key);	
			else:
				$this->_logToFile(_LOG_DIR_.'/backendpost.log', 'Sorry, processing your order is unsuccessful due to an error. Please contact our support team.');
			endif;

			//===== THIS IS FOR IPAY88 ====== //
			echo "RECEIVEOK";
			$this->_logToFile(_LOG_DIR_.'/backendpost.log', 'RECEIVEOK');
			die();
			//===== THIS IS FOR IPAY88 ====== //
		else:
			echo "RECEIVEOK";
			$this->_logToFile(_LOG_DIR_.'/backendpost.log', 'Generated signature and Requested signature mismatch.');
			die('Generated signature and Requested signature mismatch.');
		endif;		
	}

	private function _logToFile($filename, $msg)
	{
		$fd = fopen($filename, "a");
		$str = "[" . date("Y/m/d h:i:s", time()) . "] " . $msg;
		fwrite($fd, $msg . "\n");
		fclose($fd);
	}
}