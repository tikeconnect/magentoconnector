<?php
namespace Tik\TikConnector\Model\Product\Type\Configurable;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\LocalizedException;

class Details extends \Magento\Framework\Model\AbstractExtensibleModel
{
    const KEY_CHILD_ID = 'id';

    const KEY_CHILD_SKU = 'sku';

    const KEY_CHILD_PRICE = 'price';

    const KEY_CHILD_STOCK = 'stock';

    const KEY_ATTRIBUTE_LABEL = 'attribute_label';

    const KEY_ATTRIBUTE_ID = 'attribute_id';

     /**
     * Get attribute child id
     *
     * @return string
     */
    public function getChildId()
    {
        return $this->getData(self::KEY_CHILD_ID);
    }

    /**
     * Get attribute child id
     *
     * @return $this
     */
    public function setChildId($childId)
    {
        return $this->setData(self::KEY_CHILD_ID, $childId);
    }

     /**
     * Get attribute label
     *
     * @return array
     */
    public function getAttributeLabel()
    {
        return $this->getData(self::KEY_ATTRIBUTE_LABEL);
    }

    /**
     * Get attribute child id
     *
     * @return $this
     */
    public function setAttributeLabel($label)
    {
        return $this->setData(self::KEY_ATTRIBUTE_LABEL, $label);
    }

     /**
     * Get attribute label
     *
     * @return array
     */
    public function getAttributeId()
    {
        return $this->getData(self::KEY_ATTRIBUTE_ID);
    }

    /**
     * Get attribute child id
     *
     * @return $this
     */
    public function setAttributeId($attributeId)
    {
        return $this->setData(self::KEY_ATTRIBUTE_ID, $attributeId);
    }

     /**
     * Get attribute label
     *
     * @return string
     */
    public function getChildSku()
    {
        return $this->getData(self::KEY_CHILD_SKU);
    }

    /**
     * Get attribute child id
     *
     * @return $this
     */
    public function setChildSku($sku)
    {
        return $this->setData(self::KEY_CHILD_SKU, $sku);
    }

     /**
     * Get attribute label
     *
     * @return string
     */
    public function getChildPrice()
    {
        return $this->getData(self::KEY_CHILD_PRICE);
    }

    /**
     * Get attribute child id
     *
     * @return $this
     */
    public function setChildPrice($price)
    {
        return $this->setData(self::KEY_CHILD_PRICE, $price);
    }

    /**
     * Get attribute label
     *
     * @return string
     */
    public function getChildStock()
    {
        return $this->getData(self::KEY_CHILD_STOCK);
    }

    /**
     * Get attribute child id
     *
     * @return $this
     */
    public function setChildStock($stock)
    {
        return $this->setData(self::KEY_CHILD_STOCK, $stock);
    }
}
