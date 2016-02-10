<?php
namespace Swissup\Easytabs\Block\Tab\Product;

class Review extends \Magento\Review\Block\Product\Review
{
    protected function _prepareLayout()
    {
        $layout = $this->getLayout();
        $reviewForm = $layout->createBlock('Magento\Review\Block\Form', 'product.review.form');
        if ($reviewForm) {
            $this->setChild('review_form', $reviewForm);
            $layout->addContainer('product.review.form.fields.before', 'Review Form Fields Before');
            $layout->setChild('product.review.form', 'product.review.form.fields.before', 'form_fields_before');
        }
        return parent::_prepareLayout();
    }
}
