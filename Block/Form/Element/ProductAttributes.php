<?php

namespace Swissup\Easytabs\Block\Form\Element;

use Magento\Framework\View\Element\Template;
use Swissup\Easytabs\Model\Product\Attribute\Collection;

class ProductAttributes extends Renderer\UiSelect
{
    /**
     * @param Template\Context $context
     * @param Collection       $attributeCollection
     * @param array            $data
     */
    public function __construct(
        Template\Context $context,
        Collection $attributeCollection,
        array $data = []
    ) {
        $data['options'] = $attributeCollection->toOptionArray();
        parent::__construct($context, $data);
    }
}
