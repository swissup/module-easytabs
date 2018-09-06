<?php
namespace Swissup\Easytabs\Controller\Adminhtml\Index;

/**
 * Class MassDisable
 */
class MassDisable extends \Swissup\Easytabs\Controller\Adminhtml\AbstractMassStatus
{
    const SUCCESS_MESSAGE = 'A total of %1 record(s) have been disabled.';

    /**
     * Tab disable status
     *
     * @var int
     */
    protected $status = \Swissup\Easytabs\Model\Entity::STATUS_DISABLED;
}
