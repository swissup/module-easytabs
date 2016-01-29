<?php
namespace Swissup\Easytabs\Block\Adminhtml;

/**
 * Adminhtml easytabs block
 */
class Index extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Swissup_Easytabs';
        $this->_controller = 'adminhtml_index';
        $this->_headerText = __('Easy Tabs');
        $this->_addButtonLabel = __('Add New Tab');
        parent::_construct();
    }
}
