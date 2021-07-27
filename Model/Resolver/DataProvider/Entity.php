<?php
declare(strict_types=1);

namespace Swissup\Easytabs\Model\Resolver\DataProvider;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\Data\BlockInterface;
use Swissup\Easytabs\Api\Data\EntityInterface;
use Swissup\Easytabs\Api\GetEntityByAliasInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Widget\Model\Template\FilterEmulate;

/**
 * tab entity data provider
 */
class Entity
{
    const CONTENT = 'content';

    /**
     * @var GetEntityByAliasInterface
     */
    private $entityByAlias;

    /**
     * @var FilterEmulate
     */
    private $widgetFilter;

    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @var \Magento\Framework\View\Element\BlockFactory
     */
    private $blockFactory;

    /**
     *
     * @param GetEntityByAliasInterface $entityByAlias
     * @param FilterEmulate $widgetFilter
     * @param BlockRepositoryInterface $blockRepository
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Framework\View\Element\BlockFactory $blockFactory
     */
    public function __construct(
        GetEntityByAliasInterface $entityByAlias,
        FilterEmulate $widgetFilter,
        BlockRepositoryInterface $blockRepository,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\View\Element\BlockFactory $blockFactory
    ) {
        $this->entityByAlias = $entityByAlias;
        $this->widgetFilter = $widgetFilter;
        $this->blockRepository = $blockRepository;
        $this->appState = $appState;
        $this->blockFactory = $blockFactory;
    }

    /**
     * Returns enity data by entity identifier
     *
     * @param string $identifier
     * @param int $storeId
     * @return array
     * @throws NoSuchEntityException
     */
    public function getDataByAlias(string $identifier, int $storeId): array
    {
        $entity = $this->entityByAlias->execute($identifier, $storeId);

        return $this->convertData($entity);
    }

    /**
     * Convert data
     *
     * @param EntityInterface $entity
     * @return array
     * @throws NoSuchEntityException
     */
    private function convertData(EntityInterface $entity)
    {
        if (false === $entity->getStatus()) {
            throw new NoSuchEntityException();
        }
        $tabContentBlock = $entity->getBlock();
        switch ($tabContentBlock) {
                case \Swissup\Easytabs\Block\Tab\Html::class:
                    $renderedContent = $this->widgetFilter
//                        ->setStoreId($storeId)
                        ->filter($entity->getWidgetContent());
                    break;
                case \Swissup\Easytabs\Block\Tab\Cms::class:
                    $blockId = $entity->getWidgetIdentifier();
                    $blockId = is_array($blockId) ? reset($blockId) : $blockId;
                    $blockId = trim($blockId, "\"\'");

                    $blockModel = $this->blockRepository->getById($blockId);

                    $renderedContent = $this->widgetFilter
//                        ->setStoreId($storeId)
                        ->filter($blockModel->getContent());
                    break;
                case \Swissup\Easytabs\Block\Tab\Template::class:
                    $widgetBlock = $entity->getWidgetBlock();
                    $widgetTemplate = $entity->getWidgetTemplate();

                    $renderedContent = '';
                    $blockFactory = $this->blockFactory;
                    $this->appState->emulateAreaCode(
                        \Magento\Framework\App\Area::AREA_FRONTEND,
                        function() use ($blockFactory, $widgetBlock, $widgetTemplate, &$renderedContent) {
                            $widgetBlockInstance = $blockFactory->createBlock($widgetBlock);
                            $renderedContent = $widgetBlockInstance
//                                ->setArea(\Magento\Framework\App\Area::AREA_FRONTEND)
                                ->setTemplate($widgetTemplate)
                                ->toHtml();
                        }
                    );
                    break;
                default:
                    $renderedContent = '';
                    break;
            }

        $data = [
            EntityInterface::TAB_ID => (int) $entity->getTabId(),
            EntityInterface::ALIAS => $entity->getAlias(),
            self::CONTENT => $renderedContent,
        ];
        return $data;
    }
}
