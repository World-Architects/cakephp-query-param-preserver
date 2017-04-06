<?php
namespace Psa\QueryParamPreserver\Controller\Component;

use Cake\Controller\Component;
use Cake\Routing\Router;

/**
 * QueryParamPreserverComponent
 *
 * @copyright 2016 PSA Publishers Ltd.
 * @license MIT
 */
class QueryParamPreserverComponent extends Component {

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
        $request = $this->getController()->request;
        return in_array($request->action, $this->getConfig('actions'));
    }

    /**
     * Preserves the current query params
     *
     * @return void
     */
    public function preserve()
    {
        $request = $this->getController()->request;
        $query = $request->getQueryParams();
        $ignoreParams = $this->getConfig('ignoreParams');
        if (!empty($ignoreParams)) {
            foreach ($ignoreParams as $param) {
                if (isset($query[$param])) {
                    unset($query[$param]);
                }
            }
        }

        $request->session()->write(
            $this->_hashKey(),
            $query
        );
    }

    /**
     * Builds the hash key for the current call
     *
     * @return string Hash key
     */
    protected function _hashKey()
    {
        $request = $this->getController()->request;
        $string = '';
        if (!empty($request->plugin)) {
            $string .= $request->plugin;
        }

        return $string . $request->controller . '.' . $request->action;
    }

    /**
     * Applies the preserved query params
     *
     * @return \Cake\Network\Response|null
     */
    public function apply()
    {
        $request = $this->getController()->request;
        $key = $this->_hashKey();

        if (empty($request->query) && $request->session()->check($key)) {
            $queryParams = array_merge(
                (array)$request->session()->read($key),
                $request->getQueryParams()
            );

            if ($request->here !== Router::url(['?' => $queryParams])) {
                return $this->_registry->getController()->redirect(['?' => $queryParams]);
            };
        }
    }

    /**
     * beforeFilter callback
     *
     * @return \Cake\Network\Response|null
     */
    public function beforeFilter()
    {
        $request = $this->getController()->request;
        $params = $request->getQueryParams();
        $ignoreParam = $this->getConfig('disablePreserveWithParam');

        if ($this->getConfig('autoApply') && $this->actionCheck()) {
            if (isset($params[$ignoreParam])) {
                unset($params[$ignoreParam]);
                $request->session()->delete($this->_hashKey());
                $this->getController()->request = $request->withQueryParams($params);
                $this->getController()->redirect([
                    '?' => $params
                ]);
            }

            return $this->apply();
        }
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
