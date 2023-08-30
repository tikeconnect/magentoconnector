<?php

namespace Tik\TikConnector\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;

/**
 * Class SourceDeductionProcessor
 */
class OrderCancelWebhook implements ObserverInterface
{
    protected $storeManager;

    protected $helper;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Tik\TikConnector\Helper\Data $helper
    ) {
        $this->storeManager = $storeManager;
        $this->helper = $helper;
    }

    /**
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/ordercancelwebhook.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);

        try {
            /** @var \Magento\Sales\Model\Order\Shipment $shipment */
            $order = $observer->getEvent()->getOrder();
            $webhookData = [
                'topic' => 'orders/cancelled',
                'store_url' => $this->storeManager->getStore()->getBaseUrl(),
                'order_id' => $order->getId(),
                'cancel_reason' => 'customer'
            ];

            $logger->info('Order Cancel Data: ' . json_encode($webhookData));

            $this->helper->sendDataToConnector($webhookData);
        } catch (\Exception $e) {
            $logger->info($e->getMessage());
        } 
    }
}
