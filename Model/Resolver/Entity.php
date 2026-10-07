<?php
declare(strict_types=1);

namespace Swissup\Easytabs\Model\Resolver;

use Swissup\Easytabs\Model\Resolver\DataProvider\Entity as EntityDataProvider;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\GroupInterface;
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
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     *
     * @param EntityDataProvider $entityDataProvider
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        EntityDataProvider $entityDataProvider,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->entityDataProvider = $entityDataProvider;
        $this->customerRepository = $customerRepository;
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
                $data = $this->entityDataProvider->getDataByAlias(
                    (string)$args['alias'],
                    (int)$context->getExtensionAttributes()->getStore()->getId(),
                    $this->getCustomerGroupId($context)
                );
            }
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
        return $data;
    }

    /**
     * Customer group of GraphQL caller. Context extension attribute
     * `customer_group_id` is missing in older Magento, so load customer.
     *
     * @param \Magento\GraphQl\Model\Query\ContextInterface $context
     * @return int
     */
    private function getCustomerGroupId($context): int
    {
        if ((int)$context->getUserType() !== UserContextInterface::USER_TYPE_CUSTOMER
            || !$context->getUserId()
        ) {
            return GroupInterface::NOT_LOGGED_IN_ID;
        }

        return (int)$this->customerRepository->getById($context->getUserId())->getGroupId();
    }
}
