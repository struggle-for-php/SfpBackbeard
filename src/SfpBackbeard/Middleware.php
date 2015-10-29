<?php
namespace SfpBackbeard;

use Backbeard\Dispatcher;
use Zend\ServiceManager\ServiceLocatorInterface;

class Middleware
{
    /**
     * @param array $keys;
     */
    private $keys = [
        'routing-factory' => 'routing-factory',
        'view' => 'view',
        'router' => 'router',
    ];
    
    /**
     * @var ServiceLocatorInterface
     */
    private $serviceLocator;

    public function __construct(ServiceLocatorInterface $serviceLocator, $serivceLocatorKeys = array())
    {
        $this->serviceLocator = $serviceLocator;
        $this->keys = array_merge($this->keys, $serivceLocatorKeys);
    }
    
    public function __invoke($req, $res, $next)
    {
        $dispatcher = $this->getDispatcher();
        $dispatchResult = $dispatcher->dispatch($req, $res);
        if ($dispatchResult->isDispatched() !== false) {
            return $dispatchResult->getResponse();
        }
        
        return $next($req, $res);
    }
    
    /**
     * @return \Backbeard\Dispatcher
     */
    protected function getDispatcher()
    {
        $sl = $this->serviceLocator;
        $keys = $this->keys;
        
        $routingFactory = $sl->get($keys['routing-factory']);
        $view = $sl->has($keys['view']) ? $sl->get($keys['view']) : $this->getDefaultView();
        $router = $sl->has($keys['router']) ? $sl->get($keys['router']) : $this->getDefaultRouter();
        
        $dispatcher = new Dispatcher($routingFactory($this->serviceLocator), $view, $router);
        return $dispatcher;    
    }
    
    protected function getDefaultView()
    {
        return new \Backbeard\View(getcwd().'/views');
    }
    
    protected function getDefaultRouter()
    {
        return new \Backbeard\Router(new \FastRoute\RouteParser\Std());
    }
}
