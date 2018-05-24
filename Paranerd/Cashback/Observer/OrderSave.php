<?php

namespace Paranerd\Cashback\Observer;

class OrderSave implements \Magento\Framework\Event\ObserverInterface
{
	public function __construct(
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
		\Paranerd\Cashback\Model\PendingCashbackFactory $cashbackFactory,
		\Magento\Sales\Model\Order $order
	) {
		$this->customerRepositoryInterface = $customerRepositoryInterface;
		$this->cashbackFactory = $cashbackFactory;
		$this->order = $order;
	}

	/**
	 * Set time of order-completion in pending_cashbacks
	 * @param observer
	 */
	public function execute(\Magento\Framework\Event\Observer $observer) {
		$orderId = $observer->getEvent()->getOrder()->getId();
        $order = $this->order->load($orderId);

		if ($order->getStatus() == 'complete') {
			$pendingCashbacks = $this->cashbackFactory->create()->getCollection()
				->addFieldToFilter('order_id', $order->getId());

			foreach ($pendingCashbacks as $cashback) {
				$cashback->setCompletedAt(time());
			}
			$pendingCashbacks->save();
		}
	}
}