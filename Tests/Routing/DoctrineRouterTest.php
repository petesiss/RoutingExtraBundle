<?php

namespace Symfony\Cmf\Bundle\RoutingExtraBundle\Tests\Routing;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Cmf\Component\Routing\RouteRepositoryInterface;
use Symfony\Cmf\Bundle\RoutingExtraBundle\Document\Route;

use Symfony\Cmf\Component\Routing\Test\CmfUnitTestCase;
use Symfony\Cmf\Bundle\RoutingExtraBundle\Routing\DoctrineRouter;

use Symfony\Cmf\Component\Routing\Tests\Routing\DoctrineRouterTest as BaseDoctrineRouterTest;
use Symfony\Cmf\Component\Routing\Tests\Routing\RouteMock;

class DoctrineRouterTest extends BaseDoctrineRouterTest
{
    protected $request;
    protected $attributes;
    protected $container;

    public function setUp()
    {
        parent::setUp();
        $this->request = new RequestMock($this->attributes);
        $this->container = $this->buildMock('Symfony\\Component\\DependencyInjection\\ContainerInterface');
        $this->context = $this->buildMock('Symfony\\Component\\Routing\\RequestContext');

        $this->router = new DoctrineRouter($this->repository);
        $this->router->setContainer($this->container);
        $this->router->addControllerResolver($this->resolver);
    }

    /**
     * @expectedException Symfony\Component\Routing\Exception\RouteNotFoundException
     */
    public function testGenerateInvalidRoute()
    {
        $this->container->expects($this->once())
            ->method('get')
            ->with('request')
            ->will($this->returnValue($this->request)
        );
        parent::testGenerateInvalidRoute();
    }

    public function testGenerateNoRequest()
    {
        $this->container->expects($this->once())
            ->method('get')
            ->with('request')
            ->will($this->returnValue(null)
        );

        $this->contentDocument->expects($this->once())
            ->method('getRoutes')
            ->will($this->returnValue(array(new RouteMock())));

        try {
            $this->router->generate('something', array('content' => $this->contentDocument));
            $this->fail('Expected failure when context is null');
        } catch (\Exception $e) {
            // expected
        }
    }
}

class RequestMock extends \Symfony\Component\HttpFoundation\Request
{
    public function __construct($attributes)
    {
        $this->attributes = $attributes;
    }
}
