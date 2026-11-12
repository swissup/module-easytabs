<?php

declare(strict_types=1);

namespace Swissup\Easytabs\Test\Unit\Installer\Command;

/**
 * Stands in for the tab model the installer saves. setStores() is a DataObject
 * magic method, so a strict mock of the model cannot stub it.
 */
class EntityStub
{
    /**
     * @var array<string, mixed>
     */
    public $data = [];

    /**
     * @var array<int, int>
     */
    public $stores = [];

    /**
     * @param array<string, mixed> $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param array<int, int> $stores
     * @return $this
     */
    public function setStores($stores)
    {
        $this->stores = (array) $stores;

        return $this;
    }

    /**
     * @return $this
     */
    public function save()
    {
        return $this;
    }
}
