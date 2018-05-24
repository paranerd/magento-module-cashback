<?php

namespace Paranerd\Cashback\Observer;

class OrderPlace implements \Magento\Framework\Event\ObserverInterface
{
	public function __construct(
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
		\Paranerd\Cashback\Model\PendingCashbackFactory $db,
		\Magento\Sales\Model\Order $order
	) {
		$this->customerRepositoryInterface = $customerRepositoryInterface;
		$this->cashbackFactory = $db;
		$this->order = $order;
	}

	/**
	 * Create pending cashback
	 * @param observer
	 */
	public function execute(\Magento\Framework\Event\Observer $observer) {
		$orderId = $observer->getEvent()->getOrderIds();
        $order = $this->order->load($orderId);
		$cashbackFactor = 10 / 100; // 10%
		$amount = number_format($order->getBaseSubtotalInclTax() * $cashbackFactor, 2);

		$this->cashbackFactory->create()
			->setData(array(
				'customer_id' => $order->getCustomerId(),
				'order_id' => $order->getId(),
				'amount' => $amount
			))
			->save();
	}
}
