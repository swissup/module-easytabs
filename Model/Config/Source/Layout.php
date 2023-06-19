<?php
namespace Swissup\Easytabs\Model\Config\Source;

class Layout implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = ['label' => '', 'value' => ''];
        $availableOptions = [
            'collapsed' => __('Collapsed tabs (traditional layout)'),
            'expanded' => __('Expanded tabs'),
            'expanded-with-toolbar' => __('Expanded tabs and sticky titles'),
            'accordion' => __('Accordion'),
            'default' => __('Magento tabs (accordion on mobile, collapsed on desktop)')
        ];

        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }

        return $options;
    }
}
