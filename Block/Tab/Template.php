<?php
namespace Swissup\Easytabs\Block\Tab;

use Magento\Framework\DataObject\IdentityInterface;

class Template extends \Magento\Framework\View\Element\Template
     implements IdentityInterface
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
    public function getCount()
    {
        $block = $this->getTabBlock();
        if ($block instanceof \Magento\Framework\View\Element\Template) {
            return $block->getCount();
        }
        return;
    }
    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Swissup\Easytabs\Model\Entity::CACHE_TAG . '_' . $this->getNameInLayout()];
    }
}
