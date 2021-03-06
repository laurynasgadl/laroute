<?php

namespace Jojo\Laroute\Routes;

use Illuminate\Routing\Route;
use Illuminate\Routing\RouteCollection;
use Jojo\Laroute\Routes\Exceptions\ZeroRoutesException;
use Illuminate\Support\Arr;

class Collection extends \Illuminate\Support\Collection
{
    public function __construct(RouteCollection $routes, $filter, $namespace)
    {
        $this->items = $this->parseRoutes($routes, $filter, $namespace);
    }

    /**
     * Parse the routes into a jsonable output.
     *
     * @param RouteCollection $routes
     * @param string $filter
     * @param string $namespace
     *
     * @return array
     * @throws ZeroRoutesException
     */
    protected function parseRoutes(RouteCollection $routes, $filter, $namespace)
    {
        $this->guardAgainstZeroRoutes($routes);

        $results = [];

        foreach ($routes as $route) {
            $results[] = $this->getRouteInformation($route, $filter, $namespace);
        }

        return array_values(array_filter($results));
    }

    /**
     * Throw an exception if there aren't any routes to process
     *
     * @param RouteCollection $routes
     *
     * @throws ZeroRoutesException
     */
    protected function guardAgainstZeroRoutes(RouteCollection $routes)
    {
        if (count($routes) < 1) {
            throw new ZeroRoutesException("You don't have any routes!");
        }
    }

    /**
     * Get the route information for a given route.
     *
     * @param $route \Illuminate\Routing\Route
     * @param $filter string
     * @param $namespace string
     *
     * @return array
     */
    protected function getRouteInformation(Route $route, $filter, $namespace)
    {
        $host    = $route->domain();
        $methods = $route->methods();
        $uri     = $route->uri();
        $name    = $route->getName();
        $laroute = Arr::get($route->getAction(), 'laroute', null);

        switch ($filter) {
            case 'all':
                if($laroute === false) return null;
                break;
            case 'only':
                if($laroute !== true) return null;
                break;
        }

        return compact('host', 'methods', 'uri', 'name');
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->items;
    }
}
