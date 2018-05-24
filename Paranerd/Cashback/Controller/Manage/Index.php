<?php

namespace Paranerd\Cashback\Controller\Manage;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
		Context $context,
		PageFactory $resultPageFactory,
		\Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
	) {
		$this->resultPageFactory = $resultPageFactory;
		$this->currentCustomer = $currentCustomer;
		parent::__construct($context);
    }

    /**
     * Customer order history
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute() {
		if ($this->getRequest()->isPost()) {
			$post = (array) $this->getRequest()->getPost();

			if (array_key_exists("auszahlungsart", $post) && ($post['auszahlungsart'] == "gutschein" || array_key_exists("betrag", $post))) {
				if ($post['betrag'] > $this->currentCustomer->getCustomer()->getCashback()) {
					$this->messageManager->addErrorMessage("Das auszuzahlende Guthaben übersteigt Ihren derzeitigen Kontostand");
				}
				else if ($post['betrag'] < 25) {
					$this->messageManager->addErrorMessage("Eine Auszahlung des Guthabens ist erst ab 25€ möglich");
				}
				else {
					// Subtract from cashback
					$this->currentCustomer->getCustomer()->changeCashback(-$post['betrag']);

					// Send mail to employee
					$this->send_mail($post['auszahlungsart'], $post['betrag']);

					// Display the succes form validation message
					$this->messageManager->addSuccessMessage('Ein Mitarbeiter wird sich in Kürze mit Ihnen in Verbindung setzen');
				}
			}
			else {
				$this->messageManager->addErrorMessage('Es ist ein Fehler aufgetreten');
			}

			// Redirect to your form page (or anywhere you want...)
			$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
			$resultRedirect->setUrl($this->_url->getUrl('customer/account'));

			return $resultRedirect;
		}

		$this->_view->loadLayout();

		/** @var \Magento\Framework\View\Result\Page $resultPage */
		$resultPage = $this->resultPageFactory->create();
		$resultPage->getConfig()->getTitle()->set(__('Cashback'));

		$block = $resultPage->getLayout()->getBlock('customer.account.link.back');
		if ($block) {
			$block->setRefererUrl($this->_redirect->getRefererUrl());
			$block->setActive('customer/account/edit');
		}
		return $resultPage;
    }

	private function send_mail($auszahlungsart, $betrag) {
		$customer = $this->currentCustomer->getCustomer();

		$recipient = "info@cato-service.de";
		$subject = "Auszahlungsauftrag";
		$text = "Der Kunde " . $customer->getFirstname() . " " . $customer->getLastname() . " mit der ID " . $customer->getId() . " würde gern " . $betrag . "€ von seinem Guthaben ausgezahlt bekommen.\n";
		$text .= "Auszahlungsart: " . ucfirst($auszahlungsart);

		$headers   = array();
		$headers[] = "MIME-Version: 1.0";
		$headers[] = "Content-type: text/plain; charset=utf-8";
		$headers[] = "From: Info <info@cato-service.de>";
		$headers[] = "Reply-To: Info <info@cato-service.de>";
		$headers[] = "Subject: Auszahlungsauftrag";
		$headers[] = "X-Mailer: PHP/" . phpversion();

		mail($recipient, $subject, $text, implode("\r\n",$headers));
	}
}