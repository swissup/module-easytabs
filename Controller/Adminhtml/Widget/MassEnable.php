<?php
namespace Swissup\Easytabs\Controller\Adminhtml\Widget;

use Swissup\Easytabs\Controller\Adminhtml\Index\MassEnable as IndexMassEnable;

/**
 * Class MassEnable
 */
class MassEnable extends IndexMassEnable
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
