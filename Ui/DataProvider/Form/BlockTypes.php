<?php

namespace Swissup\Easytabs\Ui\DataProvider\Form;

use Magento\Framework\Data\OptionSourceInterface;
use Swissup\Easytabs\Model\TabsFactory;

class BlockTypes implements OptionSourceInterface
{
    /**
     * @var TabsFactory
     */
    protected $tabsFactory;

    /**
     * @param TabsFactory $tabsFactory [description]
     */
    public function __construct(
        TabsFactory $tabsFactory
    ) {
        $this->tabsFactory = $tabsFactory;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $tabs = $this->tabsFactory->create()->getTabsArray();
        foreach ($tabs as $tab) {
            $options[] = [
                'value' => $tab['type'],
                'label' => $tab['name']
            ];
        }

        return $options;
    }
}
