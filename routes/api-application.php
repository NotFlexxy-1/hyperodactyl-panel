<?php

use Illuminate\Support\Facades\Route;
use Hyperodactyl\Http\Controllers\Api\Application;

/*
|--------------------------------------------------------------------------
| User Controller Routes
|--------------------------------------------------------------------------
|
| Endpoint: /api/application/users
|
*/

Route::group(['prefix' => '/users'], function () {
    Route::get('/', [Application\Users\UserController::class, 'index'])->name('api.application.users');
    Route::get('/{user:id}', [Application\Users\UserController::class, 'view'])->name('api.application.users.view');
    Route::get('/external/{external_id}', [Application\Users\ExternalUserController::class, 'index'])->name('api.application.users.external');

    Route::post('/', [Application\Users\UserController::class, 'store']);
    Route::patch('/{user:id}', [Application\Users\UserController::class, 'update']);

    Route::delete('/{user:id}', [Application\Users\UserController::class, 'delete']);
});

/*
|--------------------------------------------------------------------------
| Node Controller Routes
|--------------------------------------------------------------------------
|
| Endpoint: /api/application/nodes
|
*/
Route::group(['prefix' => '/nodes'], function () {
    Route::get('/', [Application\Nodes\NodeController::class, 'index'])->name('api.application.nodes');
    Route::get('/deployable', Application\Nodes\NodeDeploymentController::class);
    Route::get('/{node:id}', [Application\Nodes\NodeController::class, 'view'])->name('api.application.nodes.view');
    Route::get('/{node:id}/configuration', Application\Nodes\NodeConfigurationController::class);

    Route::post('/', [Application\Nodes\NodeController::class, 'store']);
    Route::patch('/{node:id}', [Application\Nodes\NodeController::class, 'update'])->name('api.application.nodes.update');

    Route::delete('/{node:id}', [Application\Nodes\NodeController::class, 'delete']);

    Route::group(['prefix' => '/{node:id}/allocations'], function () {
        Route::get('/', [Application\Nodes\AllocationController::class, 'index'])->name('api.application.allocations');
        Route::post('/', [Application\Nodes\AllocationController::class, 'store']);
        Route::delete('/{allocation:id}', [Application\Nodes\AllocationController::class, 'delete'])->name('api.application.allocations.view');
    });
});

/*
|--------------------------------------------------------------------------
| Location Controller Routes
|--------------------------------------------------------------------------
|
| Endpoint: /api/application/locations
|
*/
Route::group(['prefix' => '/locations'], function () {
    Route::get('/', [Application\Locations\LocationController::class, 'index'])->name('api.applications.locations');
    Route::get('/{location:id}', [Application\Locations\LocationController::class, 'view'])->name('api.application.locations.view');

    Route::post('/', [Application\Locations\LocationController::class, 'store']);
    Route::patch('/{location:id}', [Application\Locations\LocationController::class, 'update']);

    Route::delete('/{location:id}', [Application\Locations\LocationController::class, 'delete']);
});

/*
|--------------------------------------------------------------------------
| Server Controller Routes
|--------------------------------------------------------------------------
|
| Endpoint: /api/application/servers
|
*/
Route::group(['prefix' => '/servers'], function () {
    Route::get('/', [Application\Servers\ServerController::class, 'index'])->name('api.application.servers');
    Route::get('/{server:id}', [Application\Servers\ServerController::class, 'view'])->name('api.application.servers.view');
    Route::get('/external/{external_id}', [Application\Servers\ExternalServerController::class, 'index'])->name('api.application.servers.external');

    Route::patch('/{server:id}/details', [Application\Servers\ServerDetailsController::class, 'details'])->name('api.application.servers.details');
    Route::patch('/{server:id}/build', [Application\Servers\ServerDetailsController::class, 'build'])->name('api.application.servers.build');
    Route::patch('/{server:id}/startup', [Application\Servers\StartupController::class, 'index'])->name('api.application.servers.startup');

    Route::post('/', [Application\Servers\ServerController::class, 'store']);
    Route::post('/{server:id}/suspend', [Application\Servers\ServerManagementController::class, 'suspend'])->name('api.application.servers.suspend');
    Route::post('/{server:id}/unsuspend', [Application\Servers\ServerManagementController::class, 'unsuspend'])->name('api.application.servers.unsuspend');
    Route::post('/{server:id}/reinstall', [Application\Servers\ServerManagementController::class, 'reinstall'])->name('api.application.servers.reinstall');

    Route::delete('/{server:id}', [Application\Servers\ServerController::class, 'delete']);
    Route::delete('/{server:id}/{force?}', [Application\Servers\ServerController::class, 'delete']);

    // Database Management Endpoint
    Route::group(['prefix' => '/{server:id}/databases'], function () {
        Route::get('/', [Application\Servers\DatabaseController::class, 'index'])->name('api.application.servers.databases');
        Route::get('/{database:id}', [Application\Servers\DatabaseController::class, 'view'])->name('api.application.servers.databases.view');

        Route::post('/', [Application\Servers\DatabaseController::class, 'store']);
        Route::post('/{database:id}/reset-password', [Application\Servers\DatabaseController::class, 'resetPassword']);

        Route::delete('/{database:id}', [Application\Servers\DatabaseController::class, 'delete']);
    });
});

/*
|--------------------------------------------------------------------------
| Nest Controller Routes
|--------------------------------------------------------------------------
|
| Endpoint: /api/application/nests
|
*/
Route::group(['prefix' => '/nests'], function () {
    Route::get('/', [Application\Nests\NestController::class, 'index'])->name('api.application.nests');
    Route::get('/{nest:id}', [Application\Nests\NestController::class, 'view'])->name('api.application.nests.view');

    // Egg Management Endpoint
    Route::group(['prefix' => '/{nest:id}/eggs'], function () {
        Route::get('/', [Application\Nests\EggController::class, 'index'])->name('api.application.nests.eggs');
        Route::get('/{egg:id}', [Application\Nests\EggController::class, 'view'])->name('api.application.nests.eggs.view');
    });
});

/*
|--------------------------------------------------------------------------
| Branding Controller Routes
|--------------------------------------------------------------------------
|
| Endpoint: /api/application/branding
|
*/
Route::group(['prefix' => '/branding'], function () {
    Route::get('/', [Application\BrandingController::class, 'index'])->name('api.application.branding');
    Route::patch('/', [Application\BrandingController::class, 'update'])->name('api.application.branding.update');
    Route::post('/logo', [Application\BrandingController::class, 'uploadLogo'])->name('api.application.branding.logo');
    Route::post('/favicon', [Application\BrandingController::class, 'uploadFavicon'])->name('api.application.branding.favicon');
});

/*
|--------------------------------------------------------------------------
| Hyper Coin Economy Routes
|--------------------------------------------------------------------------
|
| Endpoint: /api/application/hyper
|
*/
Route::group(['prefix' => '/hyper'], function () {
    Route::get('/balances', [Application\Hyper\UserBalanceController::class, 'index']);
    Route::post('/balances/{user:id}/adjust', [Application\Hyper\UserBalanceController::class, 'adjust']);

    Route::get('/store-items', [Application\Hyper\StoreItemController::class, 'index']);
    Route::post('/store-items', [Application\Hyper\StoreItemController::class, 'store']);
    Route::patch('/store-items/{item:id}', [Application\Hyper\StoreItemController::class, 'update']);
    Route::delete('/store-items/{item:id}', [Application\Hyper\StoreItemController::class, 'delete']);

    Route::get('/achievements', [Application\Hyper\AchievementController::class, 'index']);
    Route::post('/achievements', [Application\Hyper\AchievementController::class, 'store']);
    Route::patch('/achievements/{achievement:id}', [Application\Hyper\AchievementController::class, 'update']);
    Route::delete('/achievements/{achievement:id}', [Application\Hyper\AchievementController::class, 'delete']);
});

/*
|--------------------------------------------------------------------------
| LXC Node Controller Routes
|--------------------------------------------------------------------------
|
| Endpoint: /api/application/lxc/nodes
|
*/
Route::group(['prefix' => '/lxc/nodes'], function () {
    Route::get('/', [Application\Lxc\LxcNodeController::class, 'index'])->name('api.application.lxc-nodes');
    Route::get('/{node:id}', [Application\Lxc\LxcNodeController::class, 'view'])->name('api.application.lxc-nodes.view');

    Route::post('/', [Application\Lxc\LxcNodeController::class, 'store']);

    Route::patch('/{node:id}', [Application\Lxc\LxcNodeController::class, 'update']);

    Route::delete('/{node:id}', [Application\Lxc\LxcNodeController::class, 'delete']);
});

/*
|--------------------------------------------------------------------------
| LXC Container Controller Routes
|--------------------------------------------------------------------------
|
| Endpoint: /api/application/lxc/containers
|
*/
Route::group(['prefix' => '/lxc/containers'], function () {
    Route::get('/', [Application\Lxc\LxcContainerController::class, 'index'])->name('api.application.lxc-containers');
    Route::get('/{container:id}', [Application\Lxc\LxcContainerController::class, 'view'])->name('api.application.lxc-containers.view');

    Route::post('/', [Application\Lxc\LxcContainerController::class, 'store']);

    Route::patch('/{container:id}', [Application\Lxc\LxcContainerController::class, 'update']);

    Route::delete('/{container:id}', [Application\Lxc\LxcContainerController::class, 'delete']);
});
