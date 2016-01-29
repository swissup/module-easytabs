<?php
namespace Swissup\Easytabs\Model\Config\Source;

class Status implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Swissup\Easytabs\Model\Entity
     */
    protected $entity;

    /**
     * Constructor
     *
     * @param \Swissup\Easytabs\Model\Entity $entity
     */
    public function __construct(\Swissup\Easytabs\Model\Entity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = ['label' => '', 'value' => ''];
        $availableOptions = $this->entity->getAvailableStatuses();
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
