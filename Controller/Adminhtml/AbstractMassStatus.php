<?php
namespace Swissup\Easytabs\Controller\Adminhtml;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Swissup\Easytabs\Model\ResourceModel\Entity\CollectionFactory;

/**
 * Class AbstractMassStatus
 */
class AbstractMassStatus extends \Magento\Backend\App\Action
{
    /**
     * Admin resource
     */
    const ADMIN_RESOURCE = 'Swissup_Easytabs::easytabs_status';

    /**
     * Field id
     */
    const ID_FIELD = 'tab_id';

    /**
     * Redirect url
     */
    const REDIRECT_URL = '*/*/';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $tabsCollectionFactory;

    /**
     * Item status
     *
     * @var int
     */
    protected $status = 1;

    /**
     * Tab type: 0 - product, 1 - widget
     *
     * @var int
     */
    protected $type = 0;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory)
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        if ($this->type == 1) {
            $collection->addWidgetTabFilter();
        } else {
            $collection->addProductTabFilter();
        }

        foreach ($collection as $item) {
            $item->setStatus($this->status);
            $item->save();
        }

        $this->messageManager->addSuccess(__(static::SUCCESS_MESSAGE, $collection->getSize()));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath(self::REDIRECT_URL);
    }
}
