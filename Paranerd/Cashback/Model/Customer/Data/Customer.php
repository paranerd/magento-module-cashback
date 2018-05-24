<?php

namespace Paranerd\Cashback\Model\Customer\Data;

use \Magento\Framework\Api\AttributeValueFactory;

/**
 * Class Customer
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Customer extends \Magento\Customer\Model\Data\Customer implements \Magento\Customer\Api\Data\CustomerInterface
{
    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $attributeValueFactory
     * @param \Magento\Customer\Api\CustomerMetadataInterface $metadataService
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $attributeValueFactory,
        \Magento\Customer\Api\CustomerMetadataInterface $metadataService,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
		\Magento\Framework\Event\Manager $eventManager,
		\Paranerd\Cashback\Model\PendingCashbackFactory $db,
        $data = []
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->customerFactory = $customerFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
		$this->eventManager = $eventManager;
		$this->cashbackFactory = $db;

        parent::__construct(
			$extensionFactory,
			$attributeValueFactory,
			$metadataService,
			$data
		);
	}

	/**
     * Get Cashback that's ready to be payed out
	 *
     * @return int|null
     */
	public function getCashback() {
		return $this->getCustomAttribute('cashback')->getValue();
	}

	/**
	 * Get total cashback
	 *
	 * @return int|null
	 */
	public function getCashbackTotal() {
		// Get current cashback
		$total = $this->getCustomAttribute('cashback')->getValue();

		// Add all pending cashbacks
		$pendingCashbacks = $this->cashbackFactory->create()->getCollection()
			->addFieldToFilter('customer_id', $this->getId());

		foreach ($pendingCashbacks as $pendingCashback) {
			$total += $pendingCashback->getAmount();
		}

		return $total;
	}

	/**
	* Change cashback amount
	* Gets called by the "CashbackUpdate"-Cron-Job
	* @param int $amount
	* @return void
	*/
	public function changeCashback($amount = 0) {
		$currentCashback = $this->getCustomAttribute('cashback')->getValue();
		$newCashback = $currentCashback + $amount;
		$this->setCustomAttribute('cashback', $newCashback);
		$this->customerRepositoryInterface->save($this);
	}

	/**
	* Add order to pending_cashback table
	*
	* @return void
	*/

	public function distributePendingCashback($order_id, $amount) {
		$this->cashbackFactory->create()->setData(array('customer_id' => $this->getId(), 'order_id' => $order_id, 'amount' => $amount))->save();
	}
}