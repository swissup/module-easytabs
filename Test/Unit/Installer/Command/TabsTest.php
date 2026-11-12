<?php

declare(strict_types=1);

namespace Swissup\Easytabs\Test\Unit\Installer\Command;

use Magento\Framework\DataObject;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManager;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Swissup\Easytabs\Installer\Command\Tabs;
use Swissup\Easytabs\Model\EntityFactory;
use Swissup\Easytabs\Model\ResourceModel\Entity\Collection;
use Swissup\Easytabs\Model\ResourceModel\Entity\CollectionFactory;

/**
 * is_ajax defaults applied by the Marketplace installer when a theme creates
 * tabs. Product list tabs are lazy loaded unless the definition says otherwise.
 */
class TabsTest extends TestCase
{
    private const RELATED = \Magento\Catalog\Block\Product\ProductList\Related::class;
    private const UPSELL = \Magento\Catalog\Block\Product\ProductList\Upsell::class;
    private const CROSSSELL = \Swissup\Easytabs\Block\Tab\Product\Crosssell::class;
    private const HTML = \Swissup\Easytabs\Block\Tab\Html::class;

    /**
     * @var array<int, EntityStub>
     */
    private $saved = [];

    /**
     * @var Tabs
     */
    private $command;

    protected function setUp(): void
    {
        $this->saved = [];

        $collection = $this->createMock(Collection::class);
        $collection->method('addFieldToFilter')->willReturnSelf();
        $collection->method('addStoreFilter')->willReturnSelf();
        $collection->method('getIterator')->willReturn(new \ArrayIterator([]));

        $collectionFactory = $this->createMock(CollectionFactory::class);
        $collectionFactory->method('create')->willReturn($collection);

        $entityFactory = $this->createMock(EntityFactory::class);
        $entityFactory->method('create')->willReturnCallback(function () {
            return $this->saved[] = new EntityStub();
        });

        $this->command = new Tabs(
            $this->createMock(TimezoneInterface::class),
            $this->createMock(StoreManager::class),
            $entityFactory,
            $collectionFactory
        );
        $this->command->setLogger($this->createMock(LoggerInterface::class));
    }

    /**
     * @param array<int, array<string, mixed>> $definitions
     */
    private function install(array $definitions): void
    {
        $this->command->execute(new DataObject(['params' => $definitions]));

        $this->assertCount(
            count($definitions),
            $this->saved,
            'Installer did not save every tab definition'
        );
    }

    /**
     * @return array<string, array{0: string, 1: int}>
     */
    public static function blockDefaultProvider(): array
    {
        return [
            'related products' => [self::RELATED, 1],
            'upsell products' => [self::UPSELL, 1],
            'cross-sell products' => [self::CROSSSELL, 1],
            'html content' => [self::HTML, 0],
        ];
    }

    /**
     * @dataProvider blockDefaultProvider
     */
    public function testIsAjaxDefaultDependsOnBlock(string $block, int $expected): void
    {
        $this->install([
            ['title' => 'Tab', 'alias' => 'tab', 'block' => $block],
        ]);

        $this->assertSame($expected, $this->saved[0]->data['is_ajax']);
    }

    public function testLeadingBackslashIsIgnored(): void
    {
        $this->install([
            ['title' => 'Related', 'alias' => 'related', 'block' => '\\' . self::RELATED],
        ]);

        $this->assertSame(1, $this->saved[0]->data['is_ajax']);
    }

    public function testDefinitionKeepsItsOwnIsAjax(): void
    {
        $this->install([
            ['title' => 'Related', 'alias' => 'related', 'block' => self::RELATED, 'is_ajax' => 0],
            ['title' => 'Html', 'alias' => 'html', 'block' => self::HTML, 'is_ajax' => 1],
        ]);

        $this->assertSame(0, $this->saved[0]->data['is_ajax']);
        $this->assertSame(1, $this->saved[1]->data['is_ajax']);
    }

    public function testOtherDefaultsAreUnchanged(): void
    {
        $this->install([
            ['title' => 'Related', 'alias' => 'related', 'block' => self::RELATED],
        ]);

        $this->assertSame(1, $this->saved[0]->data['status']);
        $this->assertSame(0, $this->saved[0]->data['widget_tab']);
        $this->assertSame(0, $this->saved[0]->data['can_listen_product']);
    }
}
