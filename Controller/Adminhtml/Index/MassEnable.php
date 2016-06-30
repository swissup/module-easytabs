<?php
namespace Swissup\Easytabs\Controller\Adminhtml\Index;

use Swissup\Easytabs\Controller\Adminhtml\AbstractMassStatus;

/**
 * Class MassEnable
 */
class MassEnable extends AbstractMassStatus
{
    /**
     * Field id
     */
    const ID_FIELD = 'tab_id';

    /**
     * Admin resource
     */
    const ADMIN_RESOURCE = 'Swissup_Easytabs::status';

    /**
     * Resource collection
     *
     * @var string
     */
    protected $collection = 'Swissup\Easytabs\Model\ResourceModel\Entity\Collection';

    /**
     * Easytabs model
     *
     * @var string
     */
    protected $model = 'Swissup\Easytabs\Model\Entity';

    /**
     * Tab enable status
     *
     * @var boolean
     */
    protected $status = 1;
}
