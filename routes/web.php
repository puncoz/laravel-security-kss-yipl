<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/** @var Router $router */

$router->get(
    "/",
    function () {
        return view('welcome');
    }
);

$router->get('/projects', 'ProjectsController@index')->name('projects');
$router->get('/projects/{project_id}', 'ProjectsController@show')->name('projects.show');

$router->get('/contact-us', 'MessagesController@form')->name('contact-us');
$router->post('/contact-us', 'MessagesController@store')->name('contact-us.store');

Auth::routes();

$router->group(
    [
        'middleware' => 'auth',
    ],
    function () use ($router) {
        $router->get('/home', 'HomeController@index')->name('home');
        $router->get('/profile', 'ProfileController@index')->name('profile');
        $router->get('/profile/edit', 'ProfileController@edit')->name('profile.edit');
        $router->post('/profile/edit', 'ProfileController@update')->name('profile.update');

        $router->get('/messages', 'MessagesController@index')->name('messages');
        $router->get('/messages/{message_id}', 'MessagesController@show')->name('messages.show');
    }
);

