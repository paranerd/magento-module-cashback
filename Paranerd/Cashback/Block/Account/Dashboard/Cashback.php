<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Paranerd\Cashback\Block\Account\Dashboard;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Dashboard Customer Info
 */
class Cashback extends \Magento\Framework\View\Element\Template
{
    /** @var \Magento\Customer\Helper\View */
    protected $_helperView;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var \Magento\Customer\Model\Customer
     */
     protected $customer;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Customer\Helper\View $helperView
     * @param array $data
     */
    public function __construct(
		\Magento\Customer\Model\Customer $customer,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Customer\Helper\View $helperView,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        array $data = []
    ) {
		$this->customer = $customer;
        $this->currentCustomer = $currentCustomer;
        $this->_helperView = $helperView;
        $this->orderCollectionFactory = $orderCollectionFactory;

		parent::__construct($context, $data);

		$this->urlBuilder = $context->getUrlBuilder();
    }

    /**
     * Return the Magento Customer Model for this block
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCustomer() {
        try {
            return $this->currentCustomer->getCustomer();
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

	/**
	 * Retrieve form action
	 *
	 * @return string
	 */
	public function getFormAction() {
			// companymodule is given in routes.xml
			// controller_name is folder name inside controller folder
			// action is php file name inside above controller_name folder

		return $this->urlBuilder->getUrl('cashback/manage');
		// here controller_name is manage, action is contact
	}

    /**
     * @return string
     */
    protected function _toHtml() {
        return $this->currentCustomer->getCustomerId() ? parent::_toHtml() : 'You are not logged in';
    }
}
