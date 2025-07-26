<?php
namespace Swissup\Easytabs\Block\Tab\Product;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\ButtonLockManager;

class Review extends \Magento\Review\Block\Product\Review
{
    /**
     * @var array
     */
    protected $reviewFormBlockMap =
    [
        'default' => 'Magento\Review\Block\Form',
        'checkout_cart_configure' => 'Magento\Review\Block\Form\Configure',
        'wishlist_index_configure' => 'Magento\Review\Block\Form\Configure'
    ];

    protected function _prepareLayout()
    {
        if (!$this->getProductId()) {
            return parent::_prepareLayout();
        }

        $currentAction = $this->getRequest()->getFullActionName();
        $formBlock = array_key_exists($currentAction, $this->reviewFormBlockMap) ?
            $this->reviewFormBlockMap[$currentAction] :
            $this->reviewFormBlockMap['default'];

        $layout = $this->getLayout();
        $blockName = 'easytab.product.' . $this->getNameInLayout();
        $blockArgs = [
            'data' => [
                'jsLayout' => [
                    'components' => [
                        'review-form' => [
                            'component' => 'Magento_Review/js/view/review',
                        ]
                    ]
                ]
            ]
        ];

        if (class_exists(ButtonLockManager::class)) {
            $blockArgs['data']['button_lock_manager'] = ObjectManager::getInstance()
                ->get(ButtonLockManager::class);
        }

        $reviewForm = $layout->createBlock($formBlock, $blockName, $blockArgs);
        if ($reviewForm) {
            $this->setChild('review_form', $reviewForm);
            $containerName = $blockName . '.fields.before';
            $layout->addContainer($containerName, 'Review Form Fields Before');
            $layout->setChild($blockName, $containerName, 'form_fields_before');
            $blocksToAssign = [
                'msp-recaptcha', // MSP Recaptcha block name in Magento 2.3.x
                'recaptcha' // Recaptcha block name in Magento 2.4.x
            ];
            $availableBlocks = array_filter($blocksToAssign, [$layout, 'hasElement']);
            array_walk($availableBlocks, function ($blockName) use ($layout, $containerName) {
                $layout->setChild($containerName, $blockName, $blockName);
            });
        }
        $this->_eventManager->dispatch(
            'swissup_easytabs_product_review_prepare_layout_after',
            ['block' => $this, 'layout' => $this->getLayout()]
        );

        return parent::_prepareLayout();
    }

    public function getTabTitle()
    {
        $this->setTabTitle();

        return $this->getTitle();
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if (!$this->getProductId()) {
            return '';
        }

        return parent::_toHtml();
    }
}
