<?php
namespace Swissup\Easytabs\Block\Tab;

class Template extends \Magento\Framework\View\Element\Template
{
    /**
     * @return string
     */
    private function getTabBlockName()
    {
        return $this->getNameInLayout() . '_tab';
    }

    protected function _prepareLayout()
    {
        $block = $this->getLayout()->createBlock(
            $this->getWidgetBlock(), $this->getTabBlockName()
        );
        if ($block instanceof \Magento\Framework\View\Element\Template) {
            $this->setChild($this->getTabBlockName(), $block);
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
        return $this->getChildBlock($this->getTabBlockName());
    }
}
