<?php
namespace Swissup\Easytabs\Model\Config\Source;

class WidgetTabs implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Swissup\Easytabs\Model\ResourceModel\Entity\CollectionFactory
     */
    protected $tabsCollectionFactory;

    /**
     * @param \Swissup\Easytabs\Model\ResourceModel\Entity\CollectionFactory $tabsCollectionFactory
     */
    public function __construct(
        \Swissup\Easytabs\Model\ResourceModel\Entity\CollectionFactory $tabsCollectionFactory
    ) {
        $this->tabsCollectionFactory = $tabsCollectionFactory;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = ['label' => '', 'value' => ''];
        $widgetTabs = $this->tabsCollectionFactory->create()
            ->addStatusFilter(1)
            ->addWidgetTabFilter();

        foreach ($widgetTabs as $tab) {
            $options[$tab->getAlias()] = [
                'label' => $tab->getTitle(),
                'value' => $tab->getAlias(),
            ];
        }

        return array_values($options);
    }
}
