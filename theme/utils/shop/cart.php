<?php

namespace MangoPress\Shop;

use Nette;
use Nette\Forms\Form;

require_once __DIR__ . '/../forms.php';

class Payment {
	const BANK_TRANSFER = 'BANK_TRANSFER';
	const CARD = 'CARD';
	const CASH = 'CASH';
}

class Delivery {
	const MANUAL = 'MANUAL';
	const CP = 'CP';
	const PPL = 'PPL';
}

class Cart extends Nette\Object {

	const OPTION_DELIVERY = 'delivery';
	const OPTION_PAYMENT = 'payment';

	private $config = [
		'product_post_type' => 'shop_product',
		'order_post_type' => 'shop_order',
		'price_meta_key' => 'shop_price',
		'allow_note' => true
	];

	private $constraints = [];
	private $session;

	public function __construct($session, $config = array()) {
		$session->start();
		$this->session = $session->getSection('cart');
		$this->config = $config + $this->config;
	}

	function setPaymentConstraints($allowed) {
		$this->constraints[self::OPTION_PAYMENT] = $allowed;
	}

	function setDeliveryConstraints($allowed) {
		$this->constraints[self::OPTION_DELIVERY] = $allowed;
	}

	function getPaymentConstraints() {
		return isset($this->constraints[self::OPTION_PAYMENT]) ? $this->constraints[self::OPTION_PAYMENT] : NULL;
	}

	function getDeliveryConstraints() {
		return isset($this->constraints[self::OPTION_DELIVERY]) ? $this->constraints[self::OPTION_DELIVERY] : NULL;
	}

	function validate($type, $value) {
		if(empty($this->constraints[$type])) {
			return !empty($value);
		}
		if(is_array($this->constraints[$type])) {
			return !(array_search($value, $this->constraints[$type]) === FALSE);
		}
	}

	function setPayment($value) {
		$type = self::OPTION_PAYMENT;
		if(!$this->validate($type, $value)) {
			throw new \Exception("Invalid value `$value` for `$type`.");
		}
		$this->session->options[$type] = $value;
	}

	function setDelivery($value) {
		$type = self::OPTION_DELIVERY;
		if(!$this->validate($type, $value)) {
			throw new \Exception("Invalid value `$value` for `$type`.");
		}
		$this->session->options[$type] = $value;
	}

	function getPayment() {
		return $this->session->options[self::OPTION_PAYMENT];
	}

	function getDelivery() {
		return $this->session->options[self::OPTION_DELIVERY];
	}

	function getItems() {
		return (array) $this->session->items;
	}

	function setProductCount($product, $count = 1, $options = NULL) {
		if($count === 0) {
			unset($this->session->items[(string) $product]);
			return;
		}
		$this->session->items[(string) $product] = $count;
	}

	function add($product, $count = 1, $options = NULL) {
		return $this->setProductCount($product, $count, $options);
	}

	function remove($product) {
		return $this->setProductCount($product, 0);
	}

	function reset() {
		return $this->session->remove();
	}

	function getOptions() {
		return (array) $this->session->options;
	}

	function setOptions($options = array()) {
		$this->session->options = $options;
	}

	function setOption($key, $value) {
		$this->session->options[$key] = $value;
	}

	function getOption($key) {
		return isset($this->session->options[$key]) ? $this->session->options[$key] : NULL;
	}

	function submit() {
		// to-do: validate
		$id = wp_insert_post([
			'post_title' => 'order',
			'post_content' => json_encode($this->session->items),
			'post_type' => $this->config['order_post_type']
		]);
		$this->reset();
	}

	function getItemsCount() {
		return count($this->session->items);
	}

	function getProductPrice($product, $currency = null) {
		$key = $this->config['price_meta_key'];
		if($currency) {
			return (float) get_post_meta($product, $key.'@'.$currency, true);
		}
		return (float) get_post_meta($product, $key, true);
	}

	function getCount() {
		$itemsCount = 0;

		foreach($this->session->items as $id => $count) {
			$itemsCount += $count;
		}

		return $itemsCount;
	}

	function getSum() {
		$itemsSum = 0;

		foreach($this->session->items as $id => $count) {
			$itemsSum += $count * $this->getProductPrice($id);
		}

		return $itemsSum;
	}

	function dump() {
		dump($this->config);
		dump($this->constraints);
		dump('count: ' . $this->getCount());
		dump('items count: ' . $this->getItemsCount());
		dump('sum: ' . $this->getSum());
		dump($this->session->options);
		dump($this->session->items);
	}

	function createForm() {
		$form = new Form;
		$form->addProtection('Detected robot activity.');
		$c = $form->addContainer('frm');

		$deliveryConstraints = $this->getDeliveryConstraints();

		if($deliveryConstraints) {
			$c->addRadiolist(self::OPTION_DELIVERY, self::OPTION_DELIVERY, array_combine($deliveryConstraints, $deliveryConstraints))
				->setRequired()
				->setDefaultValue($this->getDelivery());
		}

		$paymentConstraints = $this->getPaymentConstraints();

		if($paymentConstraints) {
			$c->addRadiolist(self::OPTION_PAYMENT, self::OPTION_PAYMENT, array_combine($paymentConstraints, $paymentConstraints))
				->setRequired()
				->setDefaultValue($this->getPayment());
		}

		$c->addText('delivery_name', 'delivery_name')
			->setRequired();
		$c->addTextarea('delivery_address', 'delivery_address');

		$c->addText('payment_name', 'payment_name');
		$c->addTextarea('payment_address', 'payment_address');

		$c->addText('payment_ic', 'payment_ic');
		$c->addText('payment_dic', 'payment_dic');

		if(!empty($this->config['allow_note'])) {
			$c->addTextarea('note', 'note');
		}

		$c->setDefaults($this->getOptions());

		$c->addSubmit('send', 'Save order');

		if(isFormValid($form, 'submit-order')) {
			$vals = $c->values;

			if($vals[self::OPTION_PAYMENT]) {
				$this->setPayment($vals[self::OPTION_PAYMENT]);
			}

			if($vals[self::OPTION_DELIVERY]) {
				$this->setDelivery($vals[self::OPTION_DELIVERY]);
			}

			$this->setOptions((array) $vals + $this->getOptions());

			wp_redirect('?');
		}

		return $form;
	}
}
