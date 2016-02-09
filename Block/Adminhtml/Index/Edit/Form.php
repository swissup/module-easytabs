<?php
namespace Swissup\Easytabs\Block\Adminhtml\Index\Edit;

/**
 * Adminhtml easytab edit form main tab
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;
    /**
     * @var \Swissup\Easytabs\Model\TabsFactory
     */
    protected $tabsOptionsFactory;
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Swissup\Easytabs\Model\TabsFactory $tabsOptionsFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Swissup\Easytabs\Model\TabsFactory $tabsOptionsFactory,
        array $data = []
    ) {
        $this->systemStore = $systemStore;
        $this->tabsOptionsFactory = $tabsOptionsFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('easytab_form');
        $this->setTitle(__('Tab Information'));
    }
    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Swissup\Easytabs\Model\Data $model */
        $model = $this->_coreRegistry->registry('easytab');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Swissup_Easytabs::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
                ]
            ]
        );

        $form->setHtmlIdPrefix('easytab_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Tab Information')]);

        if ($model->getTabId()) {
            $fieldset->addField('tab_id', 'hidden', ['name' => 'tab_id']);
        }

        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Title'),
                'title' => __('title'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'alias',
            'text',
            [
                'name' => 'alias',
                'label' => __('Alias'),
                'title' => __('Alias'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $block = $model->getBlock();
        $blockTypes = $this->_getBlockTypes();
        if (!isset($blockTypes[$block])) {
            $model->setBlock('Swissup\Easytabs\Block\Tab\Html');
        }
        $model->setBlockType($model->getBlock());

        $fieldset->addField(
            'block_type',
            'select',
            [
                'name' => 'block_type',
                'label' => __('Block Type'),
                'title' => __('Block Type'),
                'required' => true,
                'options' => $this->_getBlockTypes(),
                'disabled' => $isElementDisabled,
                'after_element_html' => $this->_getWidgetSelectAfterHtml()
            ]
        );

        $fieldset->addField(
            'block',
            'text',
            [
                'name' => 'block',
                'label' => __('Block'),
                'title' => __('Block'),
                'required' => true,
                'disabled' => true
            ]
        );

        $sortOrder = $model->getSortOrder();
        if (empty($sortOrder)) {
            $model->setSortOrder(0);
        }
        $fieldset->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );

        /* Check is single store mode */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name' => 'stores[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->systemStore->getStoreValuesForForm(false, true),
                    'disabled' => $isElementDisabled
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
    /**
     * Get Easy Tabs block types as array
     * @return Array
     */
    protected function _getBlockTypes()
    {
        $tabs = $this->tabsOptionsFactory->create()->getTabsArray();
        $types = [];
        foreach ($tabs as $tab) {
            $types[$tab['type']] = $tab['name'];
        }
        return $types;
    }
    /**
     * Prepare widgets select after element HTML
     *
     * @return string
     */
    protected function _getWidgetSelectAfterHtml()
    {
        $html = '<p class="nm"><small></small></p>';
        $i = 0;
        foreach ($this->_getAvailableWidgets(true) as $data) {
            $html .= sprintf('<div id="widget-description-%s" class="no-display">%s</div>', $i, $data['description']);
            $i++;
        }
        return $html;
    }
    /**
     * Return array of available widgets based on configuration
     *
     * @param bool $withEmptyElement
     * @return array
     */
    protected function _getAvailableWidgets($withEmptyElement = false)
    {
        if (!$this->hasData('available_widgets')) {
            $result = [];
            $allWidgets = $this->tabsOptionsFactory->create()->getTabsArray();
            foreach ($allWidgets as $widget) {
                $result[] = $widget;
            }
            if ($withEmptyElement) {
                array_unshift($result, ['type' => '', 'name' => __('-- Please Select --'), 'description' => '']);
            }
            $this->setData('available_widgets', $result);
        }

        return $this->_getData('available_widgets');
    }
    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
