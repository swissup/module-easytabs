<?php
namespace Swissup\Easytabs\Block;

use Swissup\Easytabs\Api\Data\EntityInterface;
use Swissup\Easytabs\Model\Entity as TabsModel;
use Swissup\Easytabs\Model\ResourceModel\Entity\Collection as TabsCollection;

class Tabs extends \Magento\Framework\View\Element\Template
{
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
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Swissup\Easytabs\Model\ResourceModel\Entity\CollectionFactory $tabsCollectionFactory
     * @param \Swissup\Easytabs\Model\Template\Filter $templateFilter
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Swissup\Easytabs\Model\ResourceModel\Entity\CollectionFactory $tabsCollectionFactory,
        \Swissup\Easytabs\Model\Template\Filter $templateFilter,
        array $data = []
    )
    {
        $this->tabsCollectionFactory = $tabsCollectionFactory;
        $this->templateFilter = $templateFilter;
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

    protected function _prepareLayout()
    {
        foreach ($this->_getCollection() as $tab) {
            $this->addTab(
                $tab->getAlias(),
                $tab->getTitle(),
                $tab->getBlock(),
                $tab->getWidgetTemplate(),
                $tab->getData()
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
        return parent::_prepareLayout();
    }

    /**
     * Add tab on product page
     * @param string $alias
     * @param string $title
     * @param string $block
     * @param string $template
     * @param array  $attributes
     */
    public function addTab($alias, $title, $block = false, $template = false, $attributes = array())
    {
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
            $block = $this->getLayout()
                ->createBlock($block, $alias, ['data' => $attributes])
                ->setTemplate($template);
        }

        $tab = array(
            'alias' => $alias,
            'title' => $title
        );

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

    public function getTabs()
    {
        usort($this->_tabs, array($this, '_sort'));
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

    public function getTabTitle($tab)
    {
        if (!strstr($tab['title'], '{{') || !strstr($tab['title'], '}}')) {
            return $tab['title'];
        }
        $scope = $this->getChildBlock($tab['alias']);
        $processor = $this->templateFilter->setScope($scope);

        return $processor->filter($tab['title']);
    }
}
