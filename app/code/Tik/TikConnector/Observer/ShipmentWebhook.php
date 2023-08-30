<?php

namespace Tik\TikConnector\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;

/**
 * Class SourceDeductionProcessor
 */
class ShipmentWebhook implements ObserverInterface
{
    protected $storeManager;

    protected $helper;

    protected $productRepository;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Tik\TikConnector\Helper\Data $helper
    ) {
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->helper = $helper;
    }

    /**
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/shipmentwebhook.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);

        try {
            /** @var \Magento\Sales\Model\Order\Shipment $shipment */
            $tracking = $observer->getEvent()->getTrack();
            if ($tracking->getOrigData('entity_id')) {
                $logger->info('Tracking Edit');
                $logger->info('tracking origin id: ' . $tracking->getOrigData('entity_id'));
                return;
            }
            $logger->info('New Tracking');
            $shipment = $tracking->getShipment();
            $order = $shipment->getOrder();
            $trackingNumber = $tracking->getTrackNumber();
            $carrierTitle = $tracking->getTitle();
            $isPartiallyShipped = $this->isPartiallyShipped($order);
            $shipmentItemData = $this->prepareShipmentData($shipment);
            $webhookData = [
                'topic' => $isPartiallyShipped ? 'orders/partially_fulfilled' : 'orders/fulfilled',
                'store_url' => $this->storeManager->getStore()->getBaseUrl(),
                'order_id' => $order->getId(),
                'fulfillments' => [
                    [
                        'tracking_number' => $trackingNumber,
                        'tracking_company' => $carrierTitle,
                        'line_items' => $shipmentItemData
                    ]
                ]
            ];

            $logger->info('Shipment Webhook Data: ' . json_encode($webhookData));

            $this->helper->sendDataToConnector($webhookData);
        } catch (\Exception $e) {
            $logger->info($e->getMessage());
        } 
    }

    private function prepareShipmentData($shipment){
        $items = $shipment->getAllItems();
        $itemData = [];

        if(!empty($items)){
            foreach ($items as $item) {
                $sku = $item->getSku();
                $name = $item->getName();
                if(!$item->getName() && $item->getSku()){
                    try {
                        $product = $this->productRepository->get($item->getSku());
                        $name = $product->getName();
                    } catch (\Exception $e) {
                        
                    }
                }
                $itemData[] = [
                    'sku' => $item->getSku(),
                    'title' => $name
                ];
            }
        }

        return $itemData;
    }

    private function isPartiallyShipped($order){
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/shipmentwebhook.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        
        $isPartiallyShipped = false;
        if($order){
            $shipmentCollection = $order->getShipmentsCollection();
            $logger->info('Order Shipment Count: ' . $shipmentCollection->count());
            if($shipmentCollection && $shipmentCollection->count() > 1){
                $isPartiallyShipped = true;
            }
            else{
                foreach ($order->getAllItems() as $item) {
                    if ($item->getQtyToShip() > 0 && !$item->getIsVirtual() &&
                        !$item->getLockedDoShip() && $item->getQtyRefunded() != $item->getQtyOrdered()) {
                        $logger->info('Unshipped Item: ' . $item->getId());
                        $logger->info('Item Qty to Ship: ' . $item->getQtyToShip());
                        $isPartiallyShipped = true;
                    }
                }
            }
        }

        return $isPartiallyShipped;
    }
}
