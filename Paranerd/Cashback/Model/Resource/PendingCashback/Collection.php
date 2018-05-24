<?php
namespace Paranerd\Cashback\Model\Resource\PendingCashback;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
    $this->_init(
        'Paranerd\Cashback\Model\PendingCashback',
        'Paranerd\Cashback\Model\Resource\PendingCashback'
    );

    }
}