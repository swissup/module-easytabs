<?php
namespace Swissup\Easytabs\Block\Tab\Product;

class Review extends \Magento\Review\Block\Product\Review
{
    protected $reviewFormBlockMap =
    [
        'default' => 'Magento\Review\Block\Form',
        'checkout_cart_configure' => 'Magento\Review\Block\Form\Configure',
        'wishlist_index_configure' => 'Magento\Review\Block\Form\Configure'
    ];

    protected function _prepareLayout()
    {
        $currentAction = $this->getRequest()->getFullActionName();
        $formBlock = array_key_exists($currentAction, $this->reviewFormBlockMap) ?
            $this->reviewFormBlockMap[$currentAction] :
            $this->reviewFormBlockMap['default'];

        $layout = $this->getLayout();
        $blockName = 'easytab.product.' . $this->getNameInLayout();
        $reviewForm = $layout->createBlock(
            $formBlock,
            $blockName,
            ['data' =>
                ['jsLayout' =>
                    ['components' =>
                        ['review-form' =>
                            ['component' => 'Magento_Review/js/view/review']
                        ]
                    ]
                ]
            ]
        );
        if ($reviewForm) {
            $this->setChild('review_form', $reviewForm);
            $containerName = $blockName . '.fields.before';
            $layout->addContainer($containerName, 'Review Form Fields Before');
            $layout->setChild($blockName, $containerName, 'form_fields_before');
        }

        return parent::_prepareLayout();
    }

    public function getTabTitle()
    {
        $this->setTabTitle();

        return $this->getTitle();
    }
}
