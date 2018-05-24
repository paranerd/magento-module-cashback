<?php

namespace Paranerd\Cashback\Observer;

class OrderRemove implements \Magento\Framework\Event\ObserverInterface
{
	public function __construct(
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
		\Paranerd\Cashback\Model\PendingCashbackFactory $db
	) {
		$this->customerRepositoryInterface = $customerRepositoryInterface;
		$this->cashbackFactory = $db;
	}

	public function execute(\Magento\Framework\Event\Observer $observer) {
		//$data = $observer->getData('data');
		$order = $observer->getEvent()->getOrder();

		$this->cashbackFactory->create()->getCollection()
			->addFieldToFilter('order_id', $order->getId())
			->walk('delete');
	}
}