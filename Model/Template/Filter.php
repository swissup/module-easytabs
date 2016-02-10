<?php
namespace Swissup\Easytabs\Model\Template;

class Filter extends \Magento\Widget\Model\Template\Filter
{
    protected $_scope;

    public function setScope($scope)
    {
        $this->_scope = $scope;
        return $this;
    }

    public function getScope()
    {
        return $this->_scope;
    }

    public function evalDirective($construction)
    {
        $params = $this->getParameters($construction[2]);

        if (isset($params['scope'])) {
            $scope = $params['scope'];
            $scope = $this->getLayout()->getBlock($scope);
            $this->setScope($scope);
        }

        $scope = $this->getScope();
        if (!$scope || !isset($params['code'])) {
            return '';
        }

        $methods = explode('->', str_replace(array('(', ')'), '', $params['code']));

        foreach ($methods as $method) {
            $callback = array($scope, $method);
            if(!is_callable($callback)) {
                continue;
            }

            try {
                $replacedValue = call_user_func($callback);
            } catch (\Exception $e) {
                throw $e;
            }

            $scope = $replacedValue;
        }

        return (string) $replacedValue;
    }
}
