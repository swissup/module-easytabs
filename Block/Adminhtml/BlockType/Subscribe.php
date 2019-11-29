<?php

namespace Swissup\Easytabs\Block\Adminhtml\BlockType;

use Magento\Framework\View\Element\Template;
use Swissup\Easytabs\Model\TabsFactory;

class Subscribe extends Template
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'subscribe.phtml';

    /**
     * @var TabsFactory
     */
    protected $tabsFactory;

    /**
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        TabsFactory $tabsFactory,
        Template\Context $context,
        array $data = []
    ) {
        $this->tabsFactory = $tabsFactory;
        parent::__construct($context, $data);
    }

    public function getDescriptionsJson()
    {
        $description = [];
        $tabs = $this->tabsFactory->create()->getTabsArray();
        foreach ($tabs as $tab) {
            $description[$tab['type']] = (string)$tab['description'];
        }

        return json_encode($description, JSON_UNESCAPED_SLASHES);
    }
}
