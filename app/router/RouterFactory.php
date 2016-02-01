<?php

namespace App;

use Nette;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

/**
 * Router factory.
 */
class RouterFactory
{

    /**
     * @return \Nette\Application\IRouter
     */
    public function createRouter()
    {
        $router = new RouteList();

        $router[] = new Route('index.php', 'Front:Homepage:default', Route::ONE_WAY);

        $router[] = $adminRouter = new RouteList('Admin');
        $adminRouter[] = new Route('admin/<presenter>/<action>[/<id>]', 'Homepage:default');

        $router[] = $apiRouter = new RouteList('Api');
        $apiRouter[] = new Route('api/?presenter=<presenter>&action=<action>[&id=<id>]', 'Homepage:default');

        $router[] = $frontRouter = new RouteList('Front');
        $frontRouter[] = new Route('top', 'Homepage:top');
        $frontRouter[] = new Route('most-commented', 'Homepage:mostcommented');
        $frontRouter[] = new Route('random', 'Homepage:random');
        $frontRouter[] = new Route('search[/<id>]', 'Homepage:search');
        $frontRouter[] = new Route('teacher[/<id>]', 'Homepage:teacher');
        $frontRouter[] = new Route('subject[/<id>]', 'Homepage:subject');
        $frontRouter[] = new Route('tag[/<id>]', 'Homepage:tag');
        $frontRouter[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');

        return $router;
    }

}
