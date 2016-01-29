<?php
namespace Swissup\Easytabs\Model\ResourceModel;
/**
 * Easytabs Entity mysql resource
 */
class Entity extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;
    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param string|null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->_date = $date;
        $this->_storeManager = $storeManager;
    }
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('swissup_easytabs_entity', 'tab_id');
    }
    /**
     * Process tab data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Swissup\Easytabs\Model\ResourceModel\Entity
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = ['tab_id = ?' => (int)$object->getId()];
        $this->getConnection()->delete($this->getTable('swissup_easytabs_store'), $condition);
        return parent::_beforeDelete($object);
    }
    /**
     * Perform operations after object save
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();
        $table = $this->getTable('swissup_easytabs_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);
        if ($delete) {
            $where = ['tab_id = ?' => (int)$object->getId(), 'store_id IN (?)' => $delete];
            $this->getConnection()->delete($table, $where);
        }
        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = ['tab_id' => (int)$object->getId(), 'store_id' => (int)$storeId];
            }
            $this->getConnection()->insertMultiple($table, $data);
        }
        return parent::_afterSave($object);
    }
    /**
     * Perform operations after object load
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
            $object->setData('stores', $stores);
        }
        return parent::_afterLoad($object);
    }
    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param \Swissup\Easytabs\Model\Entity $object
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        if ($object->getStoreId()) {
            $stores = [(int)$object->getStoreId(), \Magento\Store\Model\Store::DEFAULT_STORE_ID];
            $select->join(
                ['ses' => $this->getTable('swissup_easytabs_store')],
                $this->getMainTable() . '.tab_id = ses.tab_id',
                ['store_id']
            )->where(
                'ses.store_id in (?)',
                $stores
            )->order(
                'store_id DESC'
            )->limit(
                1
            );
        }
        return $select;
    }
    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($id)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('swissup_easytabs_store'),
            'store_id'
        )->where(
            'tab_id = :tab_id'
        );
        $binds = [':tab_id' => (int)$id];
        return $connection->fetchCol($select, $binds);
    }
}
