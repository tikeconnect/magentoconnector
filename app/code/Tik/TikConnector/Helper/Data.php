<?php

namespace Tik\TikConnector\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
    }

    public function sendDataToConnector($webhookData){
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/sendwebhook.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);

        $url = "https://staging.tikconnect.com/magento/webhook";
        $header = [
            "Content-Type: application/json"
        ];

        $logger->info('Rquest URL: ' . $url);
        $logger->info('Request body: ' . json_encode($webhookData));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($webhookData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $logger->info('Response: ' . json_encode($response));
    }

}
