<?php

namespace SfpBackbeardTest;

use SfpBackbeard\Middleware;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response;
use Zend\Diactoros\Uri;
use Zend\ServiceManager\ServiceManager;

class MiddlewareTest extends \PHPUnit_Framework_TestCase
{
    public function testMiddlewareReturnDispatchedResponseWhenRoutingMatched()
    {
        $locator = new ServiceManager;
        $locator->setFactory('routing-factory', function(){
            return function () {
                yield '/hello' => function () {
                    return ['name' => 'John'];
                };
            };
        });
        $request = ServerRequestFactory::fromGlobals()->withUri(new Uri('http://example.com/hello'));
        $response = new Response;

        chdir(__DIR__.'/_files');

        $middleware = new Middleware($locator);
        $response = $middleware($request, $response, null);

        $this->assertEquals('hello,John', $response->getBody()->__toString());
    }
}
