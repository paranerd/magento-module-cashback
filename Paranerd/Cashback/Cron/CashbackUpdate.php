<?php

namespace Paranerd\Cashback\Cron;

class CashbackUpdate
{
	 protected $customerRepositoryInterface;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
		\Paranerd\Cashback\Model\PendingCashbackFactory $cashbackFactory,
		\Magento\Sales\Model\Order $order,
		\Magento\Sales\Model\OrderFactory $orderFactory,
        array $data = []
    ) {
		$this->customerRepositoryInterface = $customerRepositoryInterface;
		$this->cashbackFactory = $cashbackFactory;
		$this->order = $order;
		$this->orderFactory = $orderFactory;
    }

	/**
	 * Called once a day
	 * Grabs all pending cashbacks that are out of cancellation-range
	 * Distributes cashback accordingly and removes pending cashback
	 */
	public function execute() {
		$storno_frist = "2 weeks";
		$range = array('to' => date('Y-m-d', strtotime('-' . $storno_frist)));
		$pendingCashbacks = $this->cashbackFactory->create()->getCollection()
			->addFieldToFilter('created_at', $range)
			->addFieldToFilter('completed_at', array('gt' => 0));

		foreach ($pendingCashbacks as $pc) {
			$customer = $this->customerRepositoryInterface->getById($pc->getCustomerId());
			//if ($customer && $this->isOrderComplete($pc->getOrderId())) {
			if ($customer) {
				$customer->changeCashback($pc->getAmount());

				// Remove this pending cashback
				$delete = $this->cashbackFactory->create()->getCollection()
					->addFieldToFilter('id', $pc->getId())
					//->addFieldToFilter('created_at', $range)
					->walk('delete');
			}
		}
	}

	/**
	 * Check whether the order has been completed
	 * @param int orderId
	 * @return boolean
	 */
	private function isOrderComplete($orderId) {
		/*$orders = $this->orderFactory->create()->getCollection()
			->addFieldToFilter('entity_id', $orderId);

		foreach ($orders as $order) {
			return ($o->getStatus() == 'complete');
		}*/

		$o = $this->order->load($orderId);
		return ($o->getStatus() == 'complete');
	}
}
