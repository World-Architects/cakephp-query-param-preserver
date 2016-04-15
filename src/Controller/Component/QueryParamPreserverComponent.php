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
        'ignoreParams' => []
    ];

    /**
     * Checks if the query params should be preserved for the current action.
     *
     * @return bool
     */
    public function actionCheck()
    {
        return in_array($this->request->action, $this->config('actions'));
    }

    /**
     * Preserves the current query params
     *
     * @return void
     */
    public function preserve()
    {
        $query = $this->request->query;
        $ignoreParams = $this->config('ignoreParams');
        if (!empty($ignoreParams)) {
            foreach ($ignoreParams as $param) {
                if (isset($query[$param])) {
                    unset($query[$param]);
                }
            }
        }
        $this->request->session()->write(
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
        $string = '';
        if (!empty($this->request->plugin)) {
            $string .= $this->request->plugin;
        }
        $string .= $this->request->controller . '.' . $this->request->action;
        return $string;
    }

    /**
     * Applies the preserved query params
     *
     * @return void
     */
    public function apply()
    {
        $key = $this->_hashKey();
        if (empty($this->request->query) && $this->request->session()->check($key)) {
            $this->request->query = array_merge(
                (array)$this->request->session()->read($key),
                $this->request->query
            );
            $request = $this->_registry->getController()->request;
            if ($request->here !== Router::url(['?' => $this->request->query])) {
                $this->_registry->getController()->redirect(['?' => $this->request->query]);
            };
        }
    }

    /**
     * beforeFilter callback
     *
     * @return void
     */
    public function beforeFilter()
    {
        if ($this->config('autoApply') && $this->actionCheck()) {
            $this->apply();
        }
    }

    /**
     * beforeRender callback
     *
     * @return void
     */
    public function beforeRender()
    {
        if ($this->config('autoApply') && $this->actionCheck()) {
            $this->preserve();
        }
    }

}
