<?php
namespace Psa\QueryParamPreserver\Test\TestCase\Controller\Component;

use Cake\Controller\Controller;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Psa\QueryParamPreserver\Controller\Component\QueryParamPreserverComponent;

/**
 * QueryParamPreserverComponentTest
 *
 * @copyright 2016 PSA Publishers Ltd.
 * @license MIT
 */
class QueryParamPreserverComponentTest extends TestCase
{

    /**
     * Query Param Preserver Component
     *
     * @var \Psa\QueryParamPreserver\Controller\Component\QueryParamPreserverComponent
     */
    public $Preserver;

    /**
     * @inheritdoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $request = new ServerRequest();
        $response = $this->getMockBuilder(Response::class)
            ->setMethods(['stop'])
            ->getMock();

        $controller = new Controller($request, $response);

        $this->Preserver = new QueryParamPreserverComponent($controller->components());
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }
}
