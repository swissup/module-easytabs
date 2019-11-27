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
     * @var \Magento\Rule\Block\Conditions
     */
    protected $conditions;

    /**
     * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
     */
    protected $rendererFieldset;

    /**
     * @param \Magento\Backend\Block\Template\Context              $context
     * @param \Magento\Framework\Registry                          $registry
     * @param \Magento\Framework\Data\FormFactory                  $formFactory
     * @param \Magento\Store\Model\System\Store                    $systemStore
     * @param \Swissup\Easytabs\Model\TabsFactory                  $tabsOptionsFactory
     * @param \Magento\Rule\Block\Conditions                       $conditions
     * @param \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset
     * @param array                                                $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Swissup\Easytabs\Model\TabsFactory $tabsOptionsFactory,
        \Magento\Rule\Block\Conditions $conditions,
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset,
        array $data = []
    ) {
        $this->systemStore = $systemStore;
        $this->tabsOptionsFactory = $tabsOptionsFactory;
        $this->conditions = $conditions;
        $this->rendererFieldset = $rendererFieldset;
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
        } else {
            // set new tab enabled by default
            $model->setStatus(1);
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

        $fieldset->addField(
            'is_ajax',
            'select',
            [
                'name' => 'is_ajax',
                'label' => __('Load content with ajax'),
                'title' => __('Load content with ajax'),
                'options' => [1 => __('Yes'), 0 => __('No')],
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

        // add hidden field `widget_tab`
        // '0' - product tab
        // '1' - widget tab
        // values set in layout for parent block
        $fieldset->addField(
            'widget_tab',
            'hidden',
            [
                'name' => 'widget_tab',
                'after_element_html' => $this->getSpinnerHtml()
            ]
        );
        $model->setWidgetTab($this->getParentBlock()->getWidgetTab());

        $this->_addConditionsFieldset($form, $model);

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Add fieldset with conditions to form
     *
     * @param \Magento\Framework\Data\Form      $form
     * @param \Magento\Rule\Model\AbstractModel $model
     * @param string                            $fieldsetId
     * @param string                            $formName
     */
    protected function _addConditionsFieldset(
        \Magento\Framework\Data\Form $form,
        \Magento\Rule\Model\AbstractModel $model,
        $fieldsetId = 'conditions_fieldset',
        $formName = 'easytabs_index_edit'
    ) {
        $conditionsFieldSetId = $model->getConditionsFieldSetId($formName);
        $newChildUrl = $this->getUrl(
            'easytabs/index/newConditionHtml/form/' . $conditionsFieldSetId,
            ['form_namespace' => $formName]
        );

        $renderer = $this->rendererFieldset->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')
            ->setNewChildUrl($newChildUrl)
            ->setFieldSetId($conditionsFieldSetId);

        $fieldset = $form->addFieldset(
            $fieldsetId,
            [
                'legend' => __('Conditions')
            ]
        )->setRenderer($renderer);

        $fieldset->addField(
            'condition_notice',
            'label',
            [
                'label' => __('Don\'t add any condition when tab is always visible.'),
                'name' => 'condition_notice'
            ]
        );

        $fieldset->addField(
            'conditions',
            'text',
            [
                'name' => 'conditions',
                'label' => __('Conditions'),
                'title' => __('Conditions'),
                'required' => true,
                'data-form-part' => $formName
            ]
        )
            ->setRule($model)
            ->setRenderer($this->conditions);
    }

    /**
     * Get Easy Tabs block types as array
     * @return Array
     */
    protected function _getBlockTypes()
    {
        $allowedWidgetTabs = [
            'easytabs_cms',
            'easytabs_template',
            'easytabs_html',
            'easytabs_product_review'
        ];
        $showProductTabs = !$this->getParentBlock()->getWidgetTab();
        $tabs = $this->tabsOptionsFactory->create()->getTabsArray();
        $types = [];
        foreach ($tabs as $tab) {
            if (in_array($tab['code'], $allowedWidgetTabs) || $showProductTabs) {
                $types[$tab['type']] = $tab['name'];
            }
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
            $allowed = $this->_getBlockTypes();
            foreach ($allWidgets as $widget) {
                if (array_key_exists($widget['type'], $allowed)) {
                    $result[] = $widget;
                }
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

    /**
     * HTML for spinner loader.
     *
     * @return string
     */
    protected function getSpinnerHtml()
    {
        return '<div class="entry-edit form-inline" style="width: 50%; height: 60px; position: relative">'
            . '<div data-role="spinner" class="admin__data-grid-loading-mask">'
            . '<div class="spinner">'
            . '<span></span><span></span><span></span><span></span>'
            . '<span></span><span></span><span></span><span></span>'
            . '</div>'
            . '</div></div>';
    }
}
