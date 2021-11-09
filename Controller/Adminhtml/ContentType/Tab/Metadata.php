<?php
declare(strict_types=1);

namespace Swissup\Easytabs\Controller\Adminhtml\ContentType\Tab;

use Magento\Framework\Controller\ResultFactory;

class Metadata extends \Magento\Backend\App\AbstractAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Swissup_Easytabs::easytabs';

    /**
     * @var \Swissup\Easytabs\Model\ResourceModel\Entity\CollectionFactory
     */
    private $collectionFactory;

    /**
     * DataProvider constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Swissup\Easytabs\Model\ResourceModel\Entity\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Swissup\Easytabs\Model\ResourceModel\Entity\CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);

        $this->collectionFactory = $collectionFactory;
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        try {
            $collection = $this->collectionFactory->create();
            $items = $collection
                ->addFieldToSelect(['tab_id', 'alias', 'title', 'status'])
                ->addFieldToFilter('alias', ['eq' => $params['alias']])
                ->load();
            $result = $items->getFirstItem()->toArray();
        } catch (\Exception $e) {
            $result = [
                'error' => $e->getMessage(),
                'errorcode' => $e->getCode()
            ];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
