<?php
namespace Paranerd\Cashback\Setup;
use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * Customer setup factory
     *
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    private $customerSetupFactory;
    /**
     * Init
     *
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(\Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory) {
        $this->customerSetupFactory = $customerSetupFactory;
    }
    /**
     * Installs DB schema for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
		$installer = $setup;
		$installer->startSetup();
		$customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
		$entityTypeId = $customerSetup->getEntityTypeId(\Magento\Customer\Model\Customer::ENTITY);

		$customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, "cashback",  array(
			"type"     => "static",
			"backend"  => "",
			"label"    => "Cashback",
			"input"    => "text",
			"source"   => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
			"visible"  => true,
			"required" => false,
			"default" => 0,
			"frontend" => "",
			"unique"     => false,
			"note"       => ""
		));

		$used_in_forms[]="adminhtml_customer";
		$used_in_forms[]="checkout_register";
		$used_in_forms[]="customer_account_create";
		$used_in_forms[]="customer_account_edit";

		$cashback = $customerSetup->getEavConfig()->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'cashback');
		$cashback->setData("used_in_forms", $used_in_forms)
			->setData("is_used_for_customer_segment", true)
			->setData("is_system", 0)
			->setData("is_user_defined", 1)
			->setData("is_visible", 1)
			->setData("is_used_in_grid", 1)
			->setData("is_visible_in_grid", 1)
			->setData("is_filterable_in_grid", 1)
			->setData("is_searchable_in_grid", 1)
			->setData("sort_order", 100);
		$cashback->save();

		$installer->endSetup();
	}
}
