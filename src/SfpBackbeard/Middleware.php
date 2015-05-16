<?php
namespace SfpBackbeard;

use Backbeard\Dispatcher;
use Zend\ServiceManager\ServiceLocatorInterface;

class Middleware
{
    private $serviceLocator;

    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;    
    }
    
    public function __invoke($req, $res, $next)
    {
        if ($this->getDispatch()->dispatch($req, $res)) {
            return $res;
        }
        
        return $next($req, $res);
    }
    
    protected function getDispatch()
    {
        $routingFactory = $this->serviceLocator->get('routing-factory');
        $view = $this->serviceLocator->get('view');
        $router = $this->serviceLocator->get('router');
        
        $dispatch = new Dispatcher($routingFactory($this->serviceLocator), $view, $router);
        return $dispatch;    
    }    
}
