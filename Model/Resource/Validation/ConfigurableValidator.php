<?php

namespace BigBridge\ProductImport\Model\Resource\Validation;

use BigBridge\ProductImport\Api\Data\ConfigurableProduct;
use BigBridge\ProductImport\Model\Data\EavAttributeInfo;
use BigBridge\ProductImport\Model\Resource\MetaData;

/**
 * @author Patrick van Bergen
 */
class ConfigurableValidator
{
    /** @var  MetaData */
    protected $metaData;

    public function __construct(
        MetaData $metaData)
    {
        $this->metaData = $metaData;
    }

    /**
     * @param ConfigurableProduct $product
     */
    public function validate(ConfigurableProduct $product)
    {
        $this->validateSuperAttributes($product);
        $this->validateVariants($product);
    }

    /**
     * @param ConfigurableProduct $product
     */
    protected function validateSuperAttributes(ConfigurableProduct $product)
    {
        if ($product->getSuperAttributeCodes() === null) {

            if ($product->id === null) {
                $product->addError("specify the super attributes with setSuperAttrbuteCodes()");
                return;
            }

        } else {

            foreach ($product->getSuperAttributeCodes() as $superAttributeCode) {

                if (!array_key_exists($superAttributeCode, $this->metaData->productEavAttributeInfo)) {
                    $product->addError("attribute does not exist: " . $superAttributeCode);
                } else {
                    $info = $this->metaData->productEavAttributeInfo[$superAttributeCode];
                    if ($info->scope !== EavAttributeInfo::SCOPE_GLOBAL) {
                        $product->addError("attribute does not have global scope: " . $superAttributeCode);
                    }
                    if ($info->frontendInput !== EavAttributeInfo::FRONTEND_SELECT) {
                        $product->addError("attribute input type is not dropdown: " . $superAttributeCode);
                    }
                }
            }
        }
    }

    /**
     * @param ConfigurableProduct $product
     */
    protected function validateVariants(ConfigurableProduct $product)
    {
        if ($product->id === null && $product->getVariantSkus() === null) {
            $product->addError("specify the variants with setVariantSkus()");
        }
    }
}