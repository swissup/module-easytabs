<?php
namespace Swissup\Easytabs\Block;

use Magento\Framework\UrlInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Url\EncoderInterface;
use Magento\Store\Model\Store;
use Swissup\Easytabs\Api\Data\EntityInterface;
use Swissup\Easytabs\Model\Entity as TabModel;
use Swissup\Easytabs\Model\ResourceModel\Entity\Collection as TabsCollection;
use Swissup\Easytabs\Model\ResourceModel\Entity\CollectionFactory as TabsCollectionFactory;

class Tabs extends \Magento\Framework\View\Element\Template implements IdentityInterface
{
    /**
     * {@inheritdocs}
     */
    protected $_template = 'tabs.phtml';

    /**
     * @var array
     */
    protected $blockUnsetTemplate = [
        'product.reviews.wrapper'
    ];

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
     * @var array of Blocks
     */
    private $tabBlocks = [];

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
     * @var EncoderInterface
     */
    protected $encoder;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param TabsCollectionFactory                            $tabsCollectionFactory
     * @param \Swissup\Easytabs\Model\Template\Filter          $templateFilter
     * @param \Magento\Framework\Module\FullModuleList         $fullModuleList
     * @param \Magento\Framework\Module\Manager                $moduleManager
     * @param EncoderInterface                                 $encoder
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        TabsCollectionFactory $tabsCollectionFactory,
        \Swissup\Easytabs\Model\Template\Filter $templateFilter,
        \Magento\Framework\Module\FullModuleList $fullModuleList,
        \Magento\Framework\Module\Manager $moduleManager,
        EncoderInterface $encoder,
        array $data = []
    ) {
        $this->tabsCollectionFactory = $tabsCollectionFactory;
        $this->templateFilter = $templateFilter;
        $this->fullModuleList = $fullModuleList;
        $this->moduleManager = $moduleManager;
        $this->encoder = $encoder;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _getCollection()
    {
        $collection = $this->tabsCollectionFactory->create();
        $storeId = $this->_storeManager->getStore()->getId();
        return $collection
            ->addOrder(EntityInterface::SORT_ORDER, TabsCollection::SORT_ORDER_DESC)
            ->addStatusFilter(TabModel::STATUS_ENABLED)
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
            $collection = $this->_getCollection();

            $storeId = $this->_storeManager->getStore()->getId();
            $tabs = [];
            foreach ($collection as $tab) {
                $isMatchConditions = $tab->validate($tab);
                if (!$isMatchConditions) {
                    continue;
                }

                $stores = $tab->getData('store_id');
                $alias = $tab->getAlias();
                $tabs[$alias] = $tabs[$alias] ?? [];
                if (in_array($storeId, $stores)) {
                    $tabs[$alias][(int)$storeId] = $tab;
                } elseif (in_array(Store::DEFAULT_STORE_ID, $stores)) {
                    $tabs[$alias][Store::DEFAULT_STORE_ID] = $tab;
                }
            }

            foreach ($tabs as $tab) {
                // Multiple tabs can have same alias.
                // First try to find tabs specified for this store view.
                // Then use tab from default store view level.
                $tab = $tab[(int)$storeId] ?? ($tab[Store::DEFAULT_STORE_ID] ?? null);
                if (!$tab) {
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
                    if (!$block)
                        continue;

                    if (in_array($blockName, $this->blockUnsetTemplate))
                        // don't unset block it can cause exception; unset template
                        $block->setTemplate('');
                    else
                        $layout->unsetElement($blockName);
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

            if ($this->getLayout()->getBlock($alias)) {
                $this->getLayout()->unsetElement($alias);
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
            'id' => $attributes['tab_id'] ?? '',
            'alias' => $alias,
            'title' => $title,
            'is_ajax' => $isAjax
        ];

        if (isset($attributes['sort_order'])) {
            $tab['sort_order'] = $attributes['sort_order'];
        }

        $this->_tabs[] = $tab;

        $this->tabBlocks[$alias] = $block;
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

    /**
     * Get JSON string with settings for js widget
     *
     * @return string
     */
    public function getInitOptions()
    {
        $options = $this->getJsWidgetOptions();
        $layout = $this->getTabsLayout();
        $json = json_encode($options[$layout] ?? [], JSON_UNESCAPED_SLASHES);

        return $json;
    }

    /**
     * @param $alias
     * @param false $useCache
     * @return string
     */
    private function getChildTabHtml($alias, $useCache = false)
    {
        if (isset($this->tabBlocks[$alias])) {
            /** @var $tabBlock \Magento\Framework\View\Element\AbstractBlockTest */
            $tabBlock = $this->tabBlocks[$alias];
            return $tabBlock->toHtml();
        }

        return $this->getChildHtml($alias, $useCache);
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
            if (!($childHtml = $this->getChildTabHtml($_tab['alias']))
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
        $pathAlias = $this->getRequest()->getAlias(UrlInterface::REWRITE_REQUEST_PATH_ALIAS);

        return $this->getUrl(
            'easytabs',
            [
                'id' => $product ? $product->getId() : null,
                'tab' => $alias,
                'path_alias' => $this->encoder->encode($pathAlias)
            ]
        );
    }

    /**
     * Is tabs layout expanded
     *
     * @return boolean
     */
    public function isExpanded() {
        return $this->getTabsLayout() === 'expanded';
    }

    /**
     * Is tabs layout accordion
     *
     * @return boolean
     */
    public function isAccordion() {
        return $this->getTabsLayout() === 'accordion';
    }

    /**
     * @return array
     */
    protected function getJsWidgetOptions()
    {
        return [
            'collapsed' => [
                'ajaxContent' => true,
                'openedState' => 'active'
            ],
            'expanded' => [
                'ajaxContent' => true,
                'active' => array_keys($this->getTabs()),
                'multipleCollapsible' => true,
                'collapsible' => false,
                'openedState' => 'active'
            ],
            'accordion' => [
                'ajaxContent' => true,
                'active' => [-1],
                'collapsible' => true,
                'openedState' => 'active'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        $result = [];

        foreach ($this->getTabs() as $tab) {
            $result[] = TabModel::CACHE_TAG . '_' . $tab['id'];
        }

        return $result;
    }
}
