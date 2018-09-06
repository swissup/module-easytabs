<?php
namespace Swissup\Easytabs\Controller\Adminhtml\Index;

/**
 * Class MassEnable
 */
class MassEnable extends \Swissup\Easytabs\Controller\Adminhtml\AbstractMassStatus
{
    const SUCCESS_MESSAGE = 'A total of %1 record(s) have been enabled.';

    /**
     * Tab enable status
     *
     * @var int
     */
    protected $status = \Swissup\Easytabs\Model\Entity::STATUS_ENABLED;
}
