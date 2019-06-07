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
    protected $Preserver;

    /**
     * Server Request
     *
     * @var \Cake\Http\ServerRequest
     */
    protected $Request;

    /**
     * Response
     *
     * @var \Cake\Http\Response
     */
    protected $Response;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        $this->Request = new ServerRequest();
        $this->Response = $this->getMockBuilder(Response::class)
            ->setMethods(['stop'])
            ->getMock();

        $controller = new Controller($this->Request, $this->Response);

        $this->Preserver = new QueryParamPreserverComponent($controller->components());
    }

    /**
     * @inheritDoc
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * testPreserve
     *
     * @return void
     */
    public function testPreserve()
    {
        $request = $this->Preserver->getController()->getRequest()->withQueryParams([
            'first' => 1,
            'second' => '2nd'
        ]);
        $this->Preserver->getController()->setRequest($request);
        $this->Preserver->preserve();

        $result = $this->Preserver->getController()->getRequest()->getSession()->read();

        $expected = [
                '/' => [
                        'first' => (int) 1,
                        'second' => '2nd'
                ]
        ];

        $this->assertEquals($expected, $result);
    }
}
