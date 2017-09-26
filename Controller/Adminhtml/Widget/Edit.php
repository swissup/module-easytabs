<?php
namespace Swissup\Easytabs\Controller\Adminhtml\Widget;

use Swissup\Easytabs\Controller\Adminhtml\Index\Edit as IndexEditAction;
use Magento\Backend\App\Action;

class Edit extends IndexEditAction
{
    /**
     * Admin resource
     */
    const ADMIN_RESOURCE = 'Swissup_Easytabs::easytabs_widget_save';
}
