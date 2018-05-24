<?php
namespace Paranerd\Cashback\Model\Resource;

class PendingCashback extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	/**
	* Define main table
	*/
	 protected function _construct()
	{
		$this->_init('pending_cashback', 'id');   //here id is the primary key of custom table
	}
}