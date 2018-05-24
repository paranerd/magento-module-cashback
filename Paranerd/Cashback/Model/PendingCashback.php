<?php
namespace Paranerd\Cashback\Model;
use Magento\Framework\Model\AbstractModel;

class PendingCashback extends AbstractModel
{
	/**
	 * Define resource model
	 */
	 protected function _construct() {
		$this->_init('Paranerd\Cashback\Model\Resource\PendingCashback');
	}
}