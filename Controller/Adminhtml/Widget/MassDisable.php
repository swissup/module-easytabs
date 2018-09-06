<?php
namespace Swissup\Easytabs\Controller\Adminhtml\Widget;

use Swissup\Easytabs\Controller\Adminhtml\Index\MassDisable as IndexMassDisable;

/**
 * Class MassDisable
 */
class MassDisable extends IndexMassDisable
{
    /**
     * Admin resource
     */
    const ADMIN_RESOURCE = 'Swissup_Easytabs::easytabs_widget_status';

    /**
     * @var string
     */
    protected $type = 1;
}
