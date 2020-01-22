<?php
namespace Swissup\Easytabs\Block;

use Swissup\Easytabs\Api\Data\EntityInterface;
use Swissup\Easytabs\Model\Entity as TabsModel;
use Swissup\Easytabs\Model\ResourceModel\Entity\Collection as TabsCollection;
use Swissup\Easytabs\Model\ResourceModel\Entity\CollectionFactory as TabsCollectionFactory;

class Tabs extends \Magento\Framework\View\Element\Template
{
    /**
     * {@inheritdocs}
     */
    protected $_template = 'tabs.phtml';

    /**
     * @var Swissup\Easytabs\Model\Template\Filter
     */
    protected $templateFilter;

    /**
     * Array of tabs
     * @var array
     */
    protected $_tabs = [];

    /**
     * Get tabs collection
     * @var \Swissup\Easytabs\Model\ResourceModel\Entity\CollectionFactory
     */
    protected $tabsCollectionFactory;

    /**
     * @var \Magento\Framework\Module\FullModuleList
     */
    protected $fullModuleList;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param TabsCollectionFactory                            $tabsCollectionFactory
     * @param \Swissup\Easytabs\Model\Template\Filter          $templateFilter
     * @param \Magento\Framework\Module\FullModuleList         $fullModuleList
     * @param \Magento\Framework\Module\Manager                $moduleManager
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        TabsCollectionFactory $tabsCollectionFactory,
        \Swissup\Easytabs\Model\Template\Filter $templateFilter,
        \Magento\Framework\Module\FullModuleList $fullModuleList,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->tabsCollectionFactory = $tabsCollectionFactory;
        $this->templateFilter = $templateFilter;
        $this->fullModuleList = $fullModuleList;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $data);
    }

    protected function _getCollection()
    {
        $collection = $this->tabsCollectionFactory->create();
        $storeId = $this->_storeManager->getStore()->getId();
        return $collection
            ->addOrder(EntityInterface::SORT_ORDER, TabsCollection::SORT_ORDER_DESC)
            ->addStatusFilter(TabsModel::STATUS_ENABLED)
            ->addStoreFilter($storeId);
    }

    /**
     * {@inheritdocs}
     */
    protected function _prepareLayout()
    {
        $this->_buildTabs();

        return parent::_prepareLayout();
    }

    /**
     * Build tabs. It does not rebuild existing ones.
     */
    private function _buildTabs() {
        if (!$this->_tabs) {
            foreach ($this->_getCollection() as $tab) {
                $isMatchConditions = $tab->validate($tab);
                if (!$isMatchConditions) {
                    continue;
                }

                $tab->getResource()->unserializeFields($tab);
                $this->addTab(
                    $tab->getAlias(),
                    $tab->getTitle(),
                    $tab->getBlock(),
                    $tab->getWidgetTemplate(),
                    $tab->getData(),
                    $tab->getIsAjax()
                );

                $unsets = (string) $tab->getWidgetUnset();
                $unsets = explode(',', $unsets);
                $layout = $this->getLayout();
                foreach ($unsets as $blockName) {
                    $block = $layout->getBlock($blockName);
                    if ($block) {
                        $layout->unsetElement($blockName);
                    }
                }
            }

            usort($this->_tabs, array($this, '_sort'));
        }
    }

    /**
     * Add tab on product page
     * @param string  $alias
     * @param string  $title
     * @param string  $block
     * @param string  $template
     * @param array   $attributes
     * @param boolean $isAjax
     */
    public function addTab(
        $alias,
        $title,
        $block = false,
        $template = false,
        $attributes = [],
        $isAjax = false
    ) {
        if (!$title || ($block && $block !== 'Swissup\Easytabs\Block\Tab\Html' && !$template)) {
            return false;
        }

        if (!$block) {
            $block = $this->getLayout()->getBlock($alias);
            if (!$block) {
                return false;
            }
        } else {
            if ($attributes['block_arguments']) {
                $args = explode(',', $attributes['block_arguments']);
                unset($attributes['block_arguments']);
                foreach ($args as $arg) {
                    $arg = explode(':', $arg);
                    $attributes[$arg[0]] = $arg[1];
                }
            }

            // when custom block type is set then check if its module enabled
            if (isset($attributes['widget_block'])) {
                $moduleName = $this->extractModuleName($attributes['widget_block']);
                if ($this->fullModuleList->getOne($moduleName) // module with such name exists
                    && !$this->moduleManager->isEnabled($moduleName) // module disabled
                ) {
                    // don't add tab since module disabled
                    return;
                }
            }

            try {
                $block = $this->getLayout()
                    ->createBlock($block, $alias, ['data' => $attributes])
                    ->setTemplate($template);
            } catch (\Exception $e) {
                $this->_logger->critical(
                    "Swissup_Easytabs can't create tab '{$alias}' - {$e->getMessage()}"
                );
                return;
            }
        }

        $tab = [
            'alias' => $alias,
            'title' => $title,
            'is_ajax' => $isAjax
        ];

        if (isset($attributes['sort_order'])) {
            $tab['sort_order'] = $attributes['sort_order'];
        }

        $this->_tabs[] = $tab;

        $this->setChild($alias, $block);
    }

    protected function _sort($tab1, $tab2)
    {
        if (!isset($tab2['sort_order'])) {
            return -1;
        }

        if (!isset($tab1['sort_order'])) {
            return 1;
        }

        if ($tab1['sort_order'] == $tab2['sort_order']) {
            return 0;
        }
        return ($tab1['sort_order'] < $tab2['sort_order']) ? -1 : 1;
    }

    /**
     * @return array
     */
    public function getTabs()
    {
        $this->_buildTabs();
        return $this->_tabs;
    }

    /**
     * Check tab content for anything except html tags and spaces
     *
     * @param  string  $content
     * @return boolean
     */
    public function isEmptyString($content)
    {
        $content = strip_tags(
            $content,
            '<hr><img><iframe><embed><object><video><audio><input><textarea><script><style><link><meta>'
        );
        $content = trim($content);
        return strlen($content) === 0;
    }

    /**
     * @param  array $tab
     * @return string
     */
    public function getTabTitle($tab)
    {
        if (!strstr($tab['title'], '{{') || !strstr($tab['title'], '}}')) {
            return $tab['title'];
        }
        $scope = $this->getChildBlock($tab['alias']);
        if ($scope->getTabBlock()) {
            $scope = $scope->getTabBlock();
        }

        $processor = $this->templateFilter->setScope($scope);

        return $processor->filter($tab['title']);
    }

    public function getInitOptions($json = '{}')
    {
        if (!$json) {
            return '{}';
        }

        $options = json_decode($json, true);
        $options['ajaxContent'] = true; // force ajax content option
        if ($this->isExpanded()) {
            $tabs = $this->getTabs();
            $options['active'] = array_keys($tabs);
            $options['multipleCollapsible'] = true;
            $options['collapsible'] = false;
        }

        $json = json_encode($options, JSON_UNESCAPED_SLASHES);

        return $json;
    }

    /**
     * Prepare tabs data for template
     *
     * @return array
     */
    public function prepareTabsData()
    {
        $tabs = [];
        foreach ($this->getTabs() as $_index => $_tab) {
            if (!($childHtml = $this->getChildHtml($_tab['alias']))
                || $this->isEmptyString($childHtml)) {
                continue;
            }

            $_tab['child_html'] = $childHtml;
            $_tab['title'] = $this->getTabTitle($_tab);
            $tabs[$_index] = $_tab;
        }

        return $tabs;
    }

    /**
     * @param  string $tabAlias
     * @return string
     */
    public function getAjaxUrl($alias)
    {
        $block = $this->getLayout()->getBlock('product.info');
        $product = $block ? $block->getProduct() : false;
        return $this->getUrl(
            'easytabs',
            [
                'id' => $product ? $product->getId() : null,
                'tab' => $alias
            ]
        );
    }

    /**
     * Is tabs layout expanded
     *
     * @return boolean
     */
    public function isExpanded() {
        return $this->getTabsLayout() == 'expanded';
    }
}
