<?php
namespace Swissup\Easytabs\Block;

class WidgetTabs extends Tabs implements \Magento\Widget\Block\BlockInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _getCollection()
    {
        $collection = parent::_getCollection();
        $filterTabs = explode(',', (string)$this->getFilterTabs());
        $filterTabs = array_map('trim', $filterTabs);
        $collection->addFieldToFilter('alias', array('in' => $filterTabs));
        $collection->addWidgetTabFilter();
        return $collection;
    }

    /**
     * @return string
     */
    public function getTabsLayout()
    {
        $layout = $this->getData('tabs_layout');

        return $layout ?: 'collapsed';
    }

    /**
     * {@inheritdoc}
     */
    protected function getJsWidgetOptions()
    {
        $options = parent::getJsWidgetOptions();
        if ($this->isAccordion()) {
            $options[$this->getTabsLayout()]['active'] = $this->getData('active_tabs');
        }

        return $options;
    }
}
