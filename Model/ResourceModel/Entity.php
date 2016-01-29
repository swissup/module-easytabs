<?php
namespace Swissup\Easytabs\Model\Resource;
/**
 * Easytabs Entity mysql resource
 */
class Entity extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('swissup_easytabs_entity', 'tab_id');
    }

}
