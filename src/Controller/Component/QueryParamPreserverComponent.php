<?php
namespace Psa\QueryParamPreserver\Controller\Component;

use Cake\Controller\Component;

/**
 * QueryParamPreserverComponent
 *
 * @copyright 2016 PSA Publishers Ltd.
 * @license MIT
 */
class QueryParamPreserverComponent extends Component
{

    /**
     * Default Config
     *
     * @var array
     */
    public $_defaultConfig = [
        'autoApply' => true,
        'actions' => [],
        'ignoreParams' => [],
        'disablePreserveWithParam' => 'preserve'
    ];

    /**
     * Checks if the query params should be preserved for the current action.
     *
     * @return bool
     */
    public function actionCheck()
    {
        $request = $this->getController()->getRequest();

        return in_array(
            $request->getParam('action'),
            $this->getConfig('actions')
        );
    }

    /**
     * Preserves the current query params
     *
     * @return void
     */
    public function preserve()
    {
        $request = $this->getController()->getRequest();
        $query = $request->getQueryParams();

        if ($query) {
            $ignoreParams = $this->getConfig('ignoreParams');
            if (!empty($ignoreParams)) {
                foreach ($ignoreParams as $param) {
                    if (isset($query[$param])) {
                        unset($query[$param]);
                    }
                }
            }

            $request->getSession()->write(
                $this->_hashKey(),
                $query
            );
        }
    }

    /**
     * Builds the hash key for the current call
     *
     * @return string Hash key
     */
    protected function _hashKey()
    {
        return $this->getController()->getRequest()->getUri()->getPath();
    }

    /**
     * Applies the preserved query params
     *
     * @return \Cake\Http\Response|null
     */
    public function apply()
    {
        $request = $this->getController()->getRequest();
        $key = $this->_hashKey();

        if (empty($request->getQuery()) && $request->getSession()->check($key)) {
            if (!empty($request->getSession()->read($key))) {
                return $this->getController()->redirect(
                    $key . '?' . http_build_query($request->getSession()->read($key))
                );
            }
        }
    }

    /**
     * beforeFilter callback
     *
     * @return \Cake\Http\Response|null
     */
    public function beforeFilter()
    {
        if ($this->getConfig('autoApply') && $this->actionCheck()) {
            return $this->_autoApply();
        }
    }

    /**
     * Automatically applies the preserved query params
     *
     * Called in the beforeFilter() method
     *
     * @return \Cake\Http\Response|null
     */
    protected function _autoApply()
    {
        $request = $this->getController()->getRequest();
        $params = $request->getQueryParams();
        $ignoreParam = $this->getConfig('disablePreserveWithParam');

        if (isset($params[$ignoreParam])) {
            unset($params[$ignoreParam]);

            $request->getSession()->delete($this->_hashKey());

            return $this->getController()->redirect($this->_hashKey());
        }

        return $this->apply();
    }

    /**
     * beforeRender callback
     *
     * @return void
     */
    public function beforeRender()
    {
        if ($this->getConfig('autoApply') && $this->actionCheck()) {
            $this->preserve();
        }
    }
}
