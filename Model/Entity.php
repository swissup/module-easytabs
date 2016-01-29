<?php
namespace Swissup\Easytabs\Model;

use Swissup\Easytabs\Api\Data\EntityInterface;
use Magento\Framework\DataObject\IdentityInterface;

class Entity extends \Magento\Framework\Model\AbstractModel
    implements EntityInterface, IdentityInterface
{
    /**
     * Tab's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    /**
     * cache tag
     */
    const CACHE_TAG = 'easytabs_entity';

    /**
     * @var string
     */
    protected $_cacheTag = 'easytabs_entity';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'easytabs_entity';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Swissup\Easytabs\Model\ResourceModel\Entity');
    }

    /**
     * Prepare tabs statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [
            self::STATUS_ENABLED => __('Enabled'),
            self::STATUS_DISABLED => __('Disabled')
        ];
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get tab_id
     *
     * return int
     */
    public function getTabId()
    {
        return $this->getData(self::TAB_ID);
    }

    /**
     * Get title
     *
     * return string
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * Get alias
     *
     * return string
     */
    public function getAlias()
    {
        return $this->getData(self::ALIAS);
    }

    /**
     * Get block_type
     *
     * return string
     */
    public function getBlockType()
    {
        return $this->getData(self::BLOCK_TYPE);
    }

    /**
     * Get block
     *
     * return string
     */
    public function getBlock()
    {
        return $this->getData(self::BLOCK);
    }

    /**
     * Get sort_order
     *
     * return int
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * Get status
     *
     * return int
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Get widget_template
     *
     * return string
     */
    public function getWidgetTemplate()
    {
        return $this->getData(self::WIDGET_TEMPLATE);
    }

    /**
     * Get widget_unset
     *
     * return string
     */
    public function getWidgetUnset()
    {
        return $this->getData(self::WIDGET_UNSET);
    }

    /**
     * Get widget_identifier
     *
     * return string
     */
    public function getWidgetIdentifier()
    {
        return $this->getData(self::WIDGET_IDENTIFIER);
    }

    /**
     * Get widget_block
     *
     * return string
     */
    public function getWidgetBlock()
    {
        return $this->getData(self::WIDGET_BLOCK);
    }

    /**
     * Get widget_content
     *
     * return string
     */
    public function getWidgetContent()
    {
        return $this->getData(self::WIDGET_CONTENT);
    }

    /**
     * Get created_at
     *
     * return string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Get updated_at
     *
     * return string
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * Set tab_id
     *
     * @param int $tabId
     * return \Swissup\Easytabs\Api\Data\EntityInterface
     */
    public function setTabId($tabId)
    {
        return $this->setData(self::TAB_ID, $tabId);
    }

    /**
     * Set title
     *
     * @param string $title
     * return \Swissup\Easytabs\Api\Data\EntityInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Set alias
     *
     * @param string $alias
     * return \Swissup\Easytabs\Api\Data\EntityInterface
     */
    public function setAlias($alias)
    {
        return $this->setData(self::ALIAS, $alias);
    }

    /**
     * Set block_type
     *
     * @param string $blockType
     * return \Swissup\Easytabs\Api\Data\EntityInterface
     */
    public function setBlockType($blockType)
    {
        return $this->setData(self::BLOCK_TYPE, $blockType);
    }

    /**
     * Set block
     *
     * @param string $block
     * return \Swissup\Easytabs\Api\Data\EntityInterface
     */
    public function setBlock($block)
    {
        return $this->setData(self::BLOCK, $block);
    }

    /**
     * Set sort_order
     *
     * @param int $sortOrder
     * return \Swissup\Easytabs\Api\Data\EntityInterface
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * Set status
     *
     * @param int $status
     * return \Swissup\Easytabs\Api\Data\EntityInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Set widget_template
     *
     * @param string $widgetTemplate
     * return \Swissup\Easytabs\Api\Data\EntityInterface
     */
    public function setWidgetTemplate($widgetTemplate)
    {
        return $this->setData(self::WIDGET_TEMPLATE, $widgetTemplate);
    }

    /**
     * Set widget_unset
     *
     * @param string $widgetUnset
     * return \Swissup\Easytabs\Api\Data\EntityInterface
     */
    public function setWidgetUnset($widgetUnset)
    {
        return $this->setData(self::WIDGET_UNSET, $widgetUnset);
    }

    /**
     * Set widget_identifier
     *
     * @param string $widgetIdentifier
     * return \Swissup\Easytabs\Api\Data\EntityInterface
     */
    public function setWidgetIdentifier($widgetIdentifier)
    {
        return $this->setData(self::WIDGET_IDENTIFIER, $widgetIdentifier);
    }

    /**
     * Set widget_block
     *
     * @param string $widgetBlock
     * return \Swissup\Easytabs\Api\Data\EntityInterface
     */
    public function setWidgetBlock($widgetBlock)
    {
        return $this->setData(self::WIDGET_BLOCK, $widgetBlock);
    }

    /**
     * Set widget_content
     *
     * @param string $widgetContent
     * return \Swissup\Easytabs\Api\Data\EntityInterface
     */
    public function setWidgetContent($widgetContent)
    {
        return $this->setData(self::WIDGET_CONTENT, $widgetContent);
    }

    /**
     * Set created_at
     *
     * @param string $createdAt
     * return \Swissup\Easytabs\Api\Data\EntityInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Set updated_at
     *
     * @param string $updatedAt
     * return \Swissup\Easytabs\Api\Data\EntityInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * Receive page store ids
     *
     * @return int[]
     */
    public function getStores()
    {
        return $this->hasData('stores') ? $this->getData('stores') : $this->getData('store_id');
    }
}
