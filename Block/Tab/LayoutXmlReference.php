<?php

namespace Swissup\Easytabs\Block\Tab;

use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\Element\Text\ListText;

class LayoutXmlReference extends ListText implements BlockInterface
{
    public function setTemplate($childNames)
    {
        $names = explode(',', $childNames);

        foreach ($names as $i => $name) {
            if ($child = $this->getLayout()->getBlock($name)) {
                $this->setChild('easytab.xml.' . $i, $child);
            }
        }

        return $this;
    }
}
