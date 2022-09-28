<?php

namespace Swissup\Easytabs\Block\Adminhtml\Widget;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Template extends \Magento\Framework\View\Element\Template
{
    /**
     * Prepare wysiwyg element HTML
     *
     * @param AbstractElement $element Form Element
     * @return AbstractElement
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        $element->setData('after_element_html', $this->toHtml());

        return $element;
    }
}
