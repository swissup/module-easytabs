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
            'accordion' => __('Accordion')
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
