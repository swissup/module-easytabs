<?php

namespace Swissup\Easytabs\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Block extends Column
{
    private $settings;

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            $item[$this->getData('name')] = $this->prepareItem($item);
        }

        return $dataSource;
    }

    /**
     * @param  array  $item
     * @return string
     */
    protected function prepareItem(array $item)
    {
        $setting = $this->getSettings($item[$this->getData('name')]);

        $block = $item[$this->getData('name')];

        if (isset($setting['label']) && isset($setting['css'])) {
            return "<span class=\"grid-severity-notice\" style=\"{$setting['css']}\">{$setting['label']}</span>";
        }

        return '<span class="grid-severity-notice">'
            . $item[$this->getData('name')]
            . '</span>';
    }

    /**
     * Get render settings.
     *
     * @param  string $key
     * @return array
     */
    private function getSettings($key)
    {
        if ($this->settings == null) {
            $this->settings = [];

            if (isset($this->_data['render_settings'])) {
                $render = $this->_data['render_settings'];
                $source = $render['source'];
                foreach ($source->toOptionArray() as $item) {
                    $this->settings[$item['value']] = [
                        'label' => $item['label'],
                        'css' => isset($render['css'][$item['value']]) ?
                            $render['css'][$item['value']] :
                            ''
                    ];
                }
            }
        }

        return isset($this->settings[$key]) ? $this->settings[$key] : [];
    }
}
