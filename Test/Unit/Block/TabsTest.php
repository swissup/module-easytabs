<?php

declare(strict_types=1);

namespace Swissup\Easytabs\Test\Unit\Block;

use Magento\Framework\Data\Collection\AbstractDb;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Swissup\Easytabs\Block\Tabs;

/**
 * prepareTabsData() must not build the markup of an ajax tab: the layout
 * templates throw it away and the browser fetches it again over /easytabs/.
 * The tab may still be dropped as empty, so a block that can count its items
 * answers from the count, and anything else keeps rendering as before.
 */
class TabsTest extends TestCase
{
    /**
     * @var Tabs|MockObject
     */
    private $block;

    protected function setUp(): void
    {
        $this->block = $this->getMockBuilder(Tabs::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getTabs', 'getChildBlock', 'getChildHtml', 'getTabTitle'])
            ->getMock();

        $this->block->method('getTabTitle')->willReturnCallback(fn ($tab) => $tab['title']);
    }

    /**
     * @param array<string, mixed> $tab
     */
    private function expectTabs(array $tab): void
    {
        $this->block->method('getTabs')->willReturn([$tab + [
            'id' => '1',
            'alias' => 'related',
            'title' => 'Related Products',
            'is_ajax' => true,
            'can_listen_product' => false,
        ]]);
    }

    private function blockWithItems($items): object
    {
        return new class ($items) {
            /** @var mixed */
            private $items;

            public function __construct($items)
            {
                $this->items = $items;
            }

            public function getItems()
            {
                return $this->items;
            }
        };
    }

    private function collectionOfSize(int $size): AbstractDb
    {
        $collection = $this->createMock(AbstractDb::class);
        $collection->method('getSize')->willReturn($size);

        return $collection;
    }

    public function testAjaxTabWithItemsIsKeptWithoutRendering(): void
    {
        $this->expectTabs([]);
        $this->block->method('getChildBlock')->willReturn($this->blockWithItems(['a', 'b']));
        $this->block->expects($this->never())->method('getChildHtml');

        $tabs = $this->block->prepareTabsData();

        $this->assertCount(1, $tabs);
        $this->assertSame('', $tabs[0]['child_html']);
        $this->assertSame('Related Products', $tabs[0]['title']);
    }

    public function testAjaxTabWithEmptyCollectionIsDropped(): void
    {
        $this->expectTabs([]);
        $this->block->method('getChildBlock')->willReturn($this->blockWithItems($this->collectionOfSize(0)));
        $this->block->expects($this->never())->method('getChildHtml');

        $this->assertSame([], $this->block->prepareTabsData());
    }

    public function testAjaxTabWithNonEmptyCollectionIsKept(): void
    {
        $this->expectTabs([]);
        $this->block->method('getChildBlock')->willReturn($this->blockWithItems($this->collectionOfSize(3)));
        $this->block->expects($this->never())->method('getChildHtml');

        $this->assertCount(1, $this->block->prepareTabsData());
    }

    public function testAjaxTabThatCannotCountItemsStillRenders(): void
    {
        $this->expectTabs([]);
        $this->block->method('getChildBlock')->willReturn(new \stdClass());
        $this->block->expects($this->once())
            ->method('getChildHtml')
            ->willReturn('<p>cms content</p>');

        $tabs = $this->block->prepareTabsData();

        $this->assertCount(1, $tabs);
        $this->assertSame('<p>cms content</p>', $tabs[0]['child_html']);
    }

    public function testAjaxTabThatRendersNothingIsDropped(): void
    {
        $this->expectTabs([]);
        $this->block->method('getChildBlock')->willReturn(new \stdClass());
        $this->block->method('getChildHtml')->willReturn('   ');

        $this->assertSame([], $this->block->prepareTabsData());
    }

    public function testNonAjaxTabIsRendered(): void
    {
        $this->expectTabs(['is_ajax' => false]);
        $this->block->expects($this->once())
            ->method('getChildHtml')
            ->willReturn('<div class="block related">items</div>');

        $tabs = $this->block->prepareTabsData();

        $this->assertSame('<div class="block related">items</div>', $tabs[0]['child_html']);
    }

    public function testAjaxTabIsRenderedWhenCallerCannotLoadItLater(): void
    {
        $this->expectTabs([]);
        $this->block->expects($this->once())
            ->method('getChildHtml')
            ->willReturn('<div class="block related">items</div>');

        $tabs = $this->block->prepareTabsData(true);

        $this->assertSame('<div class="block related">items</div>', $tabs[0]['child_html']);
    }
}
