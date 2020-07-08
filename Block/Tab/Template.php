<?php
namespace Swissup\Easytabs\Block\Tab;

class Template extends \Magento\Framework\View\Element\Template
{
    protected function _prepareLayout()
    {
        $block = $this->getLayout()->createBlock(
            $this->getWidgetBlock(), $this->getNameInLayout() . '_tab'
        );
        if ($block instanceof \Magento\Framework\View\Element\Template) {
            $this->setChild($this->getNameInLayout() . '_tab', $block);
        }
        return parent::_prepareLayout();
    }
    protected function _toHtml()
    {
        $block = $this->getTabBlock();
        if ($block instanceof \Magento\Framework\View\Element\Template) {
            return $block->setTemplate($this->getWidgetTemplate())
                ->toHtml();
        }
        return '';
    }
    public function getTabBlock()
    {
        return $this->getChildBlock($this->getNameInLayout() . '_tab');
    }
}
