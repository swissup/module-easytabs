<?php

namespace Swissup\Easytabs\Ui\DataProvider\Form;

use Swissup\Easytabs\Model\ResourceModel\Entity\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

class TabDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var \Swissup\Easytabs\Model\ProductEdit $item */
        foreach ($items as $item) {
            $item->setStores($item->getStoreId());
            $item->setBlockType($item->getBlock());
            $item->getResource()->unserializeFields($item);
            $this->loadedData[$item->getId()] = $item->getData();
        }

        $data = $this->dataPersistor->get('easytabs_index_edit');
        if (!empty($data)) {
            $item = $this->collection->getNewEmptyItem();
            $item->setData($data);
            $this->loadedData[$item->getId()] = $item->getData();
            $this->dataPersistor->clear('easytabs_index_edit');
        }

        return $this->loadedData;
    }
}
