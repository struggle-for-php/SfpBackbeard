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
        $dispatcher = $this->getDispatcher();
        if ($dispatcher->dispatch($req, $res) !== false) {
            return $dispatcher->getActionResponse();
        }
        
        return $next($req, $res);
    }
    
    protected function getDispatcher()
    {
        $routingFactory = $this->serviceLocator->get('routing-factory');
        $view = $this->serviceLocator->get('view');
        $router = $this->serviceLocator->get('router');
        
        $dispatcher = new Dispatcher($routingFactory($this->serviceLocator), $view, $router);
        return $dispatcher;    
    }    
}
