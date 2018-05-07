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
        $reviewForm = $layout->createBlock(
            $formBlock,
            'product.review.form',
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
            $layout->addContainer('product.review.form.fields.before', 'Review Form Fields Before');
            $layout->setChild('product.review.form', 'product.review.form.fields.before', 'form_fields_before');
        }

        return parent::_prepareLayout();
    }

    public function getTabTitle()
    {
        $this->setTabTitle();

        return $this->getTitle();
    }
}
