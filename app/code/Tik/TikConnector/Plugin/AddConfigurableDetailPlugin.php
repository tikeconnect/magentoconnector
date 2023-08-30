<?php
namespace Tik\TikConnector\Plugin;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductSearchResultsInterface;

class AddConfigurableDetailPlugin
{
    protected $detailFactory;

    protected $configurableType;

    public function __construct(
        \Tik\TikConnector\Model\Product\Type\Configurable\DetailsFactory $detailFactory,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableType
    ){
        $this->detailFactory = $detailFactory;
        $this->configurableType = $configurableType;
    }

    public function afterGet(
        ProductRepositoryInterface $subject,
        ProductInterface $entity
    ) {
        if($entity->getTypeId() == 'configurable'){
            $details = $this->createConfigurableDetail($entity);
            $extensionAttributes = $entity->getExtensionAttributes();
            $extensionAttributes->setConfigurableProductDetails($details);
            $entity->setExtensionAttributes($extensionAttributes);
        }

        return $entity;
    }

    public function afterGetList(
        ProductRepositoryInterface $subject,
        ProductSearchResultsInterface $searchResults
    ) : ProductSearchResultsInterface {
        $products = [];
        foreach ($searchResults->getItems() as $entity) {
            if($entity->getTypeId() == 'configurable'){
                $details = $this->createConfigurableDetail($entity);
                $extensionAttributes = $entity->getExtensionAttributes();
                $extensionAttributes->setConfigurableProductDetails($details);
                $entity->setExtensionAttributes($extensionAttributes);
            }

            $products[] = $entity;
        }
        $searchResults->setItems($products);
        return $searchResults;
    }

    public function createConfigurableDetail($product)
    {
        $configurableProductOptions = $this->configurableType->getUsedProductAttributes($product);
        $childProducts = $this->configurableType->getUsedProducts($product);
        $configurableDetails = [];
        foreach ($childProducts as $child) {
            foreach ($configurableProductOptions as $configurableProductOption) {
                $attributeCode = $configurableProductOption->getAttributeCode();
                $attributeId = $configurableProductOption->getAttributeId();
                $detailObject = $this->detailFactory->create()
                                        ->setChildId($child->getId())
                                        ->setChildSku($child->getSku())
                                        ->setChildPrice($child->getFinalPrice())
                                        ->setChildStock(100)
                                        ->setAttributeId($attributeId)
                                        ->setAttributeLabel($child->getAttributeText($attributeCode));
                $configurableDetails[] = $detailObject;
            }
        }
        
        return $configurableDetails;
    }
}