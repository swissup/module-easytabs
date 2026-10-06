<?php
declare(strict_types=1);

namespace Swissup\Easytabs\Model\Resolver;

use Swissup\Easytabs\Model\Resolver\DataProvider\Entity as EntityDataProvider;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class Entity implements ResolverInterface
{
    /**
     * @var EntityDataProvider
     */
    private $entityDataProvider;

    /**
     *
     * @param EntityDataProvider $entityDataProvider
     */
    public function __construct(EntityDataProvider $entityDataProvider)
    {
        $this->entityDataProvider = $entityDataProvider;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        if (!isset($args['alias'])) {
            throw new GraphQlInputException(__('"Tab identifier should be specified'));
        }

        $data = [];
        try {
            if (isset($args['alias'])) {
                $extensionAttributes = $context->getExtensionAttributes();
                $data = $this->entityDataProvider->getDataByAlias(
                    (string)$args['alias'],
                    (int)$extensionAttributes->getStore()->getId(),
                    method_exists($extensionAttributes, 'getCustomerGroupId')
                        ? (int)$extensionAttributes->getCustomerGroupId()
                        : 0
                );
            }
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
        return $data;
    }
}
