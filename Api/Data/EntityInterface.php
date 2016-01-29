<?php
namespace Swissup\Easytabs\Api\Data;

interface EntityInterface
{
    const TAB_ID = 'tab_id';
    const TITLE = 'title';
    const ALIAS = 'alias';
    const BLOCK_TYPE = 'block_type';
    const BLOCK = 'block';
    const SORT_ORDER = 'sort_order';
    const STATUS = 'status';
    const WIDGET_TEMPLATE = 'widget_template';
    const WIDGET_UNSET = 'widget_unset';
    const WIDGET_IDENTIFIER = 'widget_identifier';
    const WIDGET_BLOCK = 'widget_block';
    const WIDGET_CONTENT = 'widget_content';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * Get tab_id
     *
     * return int
     */
    public function getTabId();

    /**
     * Get title
     *
     * return string
     */
    public function getTitle();

    /**
     * Get alias
     *
     * return string
     */
    public function getAlias();

    /**
     * Get block_type
     *
     * return string
     */
    public function getBlockType();

    /**
     * Get block
     *
     * return string
     */
    public function getBlock();

    /**
     * Get sort_order
     *
     * return int
     */
    public function getSortOrder();

    /**
     * Get status
     *
     * return int
     */
    public function getStatus();

    /**
     * Get widget_template
     *
     * return string
     */
    public function getWidgetTemplate();

    /**
     * Get widget_unset
     *
     * return string
     */
    public function getWidgetUnset();

    /**
     * Get widget_identifier
     *
     * return string
     */
    public function getWidgetIdentifier();

    /**
     * Get widget_block
     *
     * return string
     */
    public function getWidgetBlock();

    /**
     * Get widget_content
     *
     * return string
     */
    public function getWidgetContent();

    /**
     * Get created_at
     *
     * return string
     */
    public function getCreatedAt();

    /**
     * Get updated_at
     *
     * return string
     */
    public function getUpdatedAt();


    /**
     * Set tab_id
     *
     * @param int $tabId
     * return \Swissup\Easytabs\Api\Data\EntityInterface
     */
    public function setTabId($tabId);

    /**
     * Set title
     *
     * @param string $title
     * return \Swissup\Easytabs\Api\Data\EntityInterface
     */
    public function setTitle($title);

    /**
     * Set alias
     *
     * @param string $alias
     * return \Swissup\Easytabs\Api\Data\EntityInterface
     */
    public function setAlias($alias);

    /**
     * Set block_type
     *
     * @param string $blockType
     * return \Swissup\Easytabs\Api\Data\EntityInterface
     */
    public function setBlockType($blockType);

    /**
     * Set block
     *
     * @param string $block
     * return \Swissup\Easytabs\Api\Data\EntityInterface
     */
    public function setBlock($block);

    /**
     * Set sort_order
     *
     * @param int $sortOrder
     * return \Swissup\Easytabs\Api\Data\EntityInterface
     */
    public function setSortOrder($sortOrder);

    /**
     * Set status
     *
     * @param int $status
     * return \Swissup\Easytabs\Api\Data\EntityInterface
     */
    public function setStatus($status);

    /**
     * Set widget_template
     *
     * @param string $widgetTemplate
     * return \Swissup\Easytabs\Api\Data\EntityInterface
     */
    public function setWidgetTemplate($widgetTemplate);

    /**
     * Set widget_unset
     *
     * @param string $widgetUnset
     * return \Swissup\Easytabs\Api\Data\EntityInterface
     */
    public function setWidgetUnset($widgetUnset);

    /**
     * Set widget_identifier
     *
     * @param string $widgetIdentifier
     * return \Swissup\Easytabs\Api\Data\EntityInterface
     */
    public function setWidgetIdentifier($widgetIdentifier);

    /**
     * Set widget_block
     *
     * @param string $widgetBlock
     * return \Swissup\Easytabs\Api\Data\EntityInterface
     */
    public function setWidgetBlock($widgetBlock);

    /**
     * Set widget_content
     *
     * @param string $widgetContent
     * return \Swissup\Easytabs\Api\Data\EntityInterface
     */
    public function setWidgetContent($widgetContent);

    /**
     * Set created_at
     *
     * @param string $createdAt
     * return \Swissup\Easytabs\Api\Data\EntityInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Set updated_at
     *
     * @param string $updatedAt
     * return \Swissup\Easytabs\Api\Data\EntityInterface
     */
    public function setUpdatedAt($updatedAt);
}
