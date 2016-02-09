<?php
namespace Swissup\Easytabs\Model;

class Tabs extends \Magento\Widget\Model\Widget
{
    /**
     * @param \Magento\Framework\Escaper $escaper
     * @param \Swissup\Easytabs\Model\Config\Data $dataStorage
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Framework\View\Asset\Source $assetSource
     * @param \Magento\Framework\View\FileSystem $viewFileSystem
     * @param \Magento\Widget\Helper\Conditions $conditionsHelper
     */
    public function __construct(
        \Magento\Framework\Escaper $escaper,
        \Swissup\Easytabs\Model\Config\Data $dataStorage,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\View\Asset\Source $assetSource,
        \Magento\Framework\View\FileSystem $viewFileSystem,
        \Magento\Widget\Helper\Conditions $conditionsHelper
    ) {
        parent::__construct($escaper, $dataStorage, $assetRepo, $assetSource, $viewFileSystem, $conditionsHelper);
    }

    public function getTabsArray($filters = [])
    {
        return parent::getWidgetsArray($filters);
    }
}
