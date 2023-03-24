<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\Auth\RegisterController as AdminRegisterController;
use App\Http\Controllers\Admin\BlogsController;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\CommentsController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TagsController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\User\HomeController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [LoginController::class, 'login'])->name('login.index');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'register'])->name('register.index');
Route::post('/register', [RegisterController::class, 'create'])->name('register');


// Routes for normal users
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/blog', [HomeController::class, 'blog'])->name('blog');
Route::get('/category', [HomeController::class, 'mountCategory'])->name('category');
Route::get('/category/{category?}', [HomeController::class, 'category'])->name('category.category')->where("category", "^[a-zA-Z0-9\/]+");
Route::get('/category/{category}/{blog}', [HomeController::class, 'single'])->name('single')->where("category", "^[a-zA-Z0-9\/]+");

// Routes for pages
Route::get('/pages/archive', function() {
    return view('pages.archives');
})->name('page.archive');
Route::get('/pages/not-found', function() {
    return view('pages.not-found');
})->name('page.not-found');


// administrator login
Route::prefix('/admin')
    ->name('admin.')
    ->controller(AdminLoginController::class)
    ->group(function () {

        Route::get('/login', 'login')->name('login.index');
        Route::post('/login', 'authenticate')->name('login');
        Route::get('/logout', 'logout')->name('logout');
    });

// administrator control
Route::prefix('/admin')
    ->name('admin.')
    ->group(function () {
        // url /admin
        // name admin.

        Route::get('/register', [AdminRegisterController::class, 'register'])->name('register.index');
        Route::post('/register', [AdminRegisterController::class, 'create'])->name('register');

        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

        // users section control
        Route::prefix('/users')
            ->name('users.')
            ->controller(UsersController::class)
            ->group(function () {
                // url /admin/users
                // name admin.users.

                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/store', 'store')->name('store');
                Route::get('/{user}', 'show')->name('show');
                Route::patch('/{user}/make-admin', 'makeAdmin')->name('make-admin');
                Route::patch('/{user}/make-user', 'makeUser')->name('make-user');
                Route::delete('/{user}/destroy', 'delete')->name('destroy');
                Route::get('/{user}/edit', 'edit')->name('edit');
                Route::put('/{user}/update', 'update')->name('update');
            });


        // Blogs section control
        Route::prefix('/blogs')
            ->name('blogs.')
            ->controller(BlogsController::class)
            ->group(function () {
                // url /admin/blogs
                // name admin.blogs.

                Route::get('/', 'index')->name('index');
                Route::get('/trash', 'trashed')->withTrashed()->name('trash');
                Route::get('/create', 'create')->name('create');
                Route::post('/store', 'store')->name('store');
                Route::delete('/{blog}/delete', 'delete')->name('delete');
                Route::get('/{blog}/remove-gallery', 'removeGallery')->name('remove-gallery');
                Route::get('/{blog}/remove-thumbnail', 'removeThumbnail')->name('remove-thumbnail');
                Route::get('/{blog}/edit', 'edit')->name('edit');
                Route::put('/{blog}/update', 'update')->name('update');
                Route::patch('/trash/{blog}/restore', 'restore')->withTrashed()->name('restore');
                Route::patch('/trash/restore', 'restoreAll')->withTrashed()->name('restore-all');
                Route::delete('/trash/{blog}/delete', 'forceDelete')->withTrashed()->name('force-delete');
                Route::delete('/trash/empty', 'emptyTrash')->name('empty-trash');
            });


        // categories section control
        Route::prefix('/categories')
            ->name('categories.')
            ->controller(CategoriesController::class)
            ->group(function () {

                // url /admin/categories
                // name admin.categories.
                Route::get('/create', 'create')->name('create');
                Route::post('/store', 'store')->name('store');
                Route::delete('/{category}/delete', 'delete')->name('delete');
                Route::put('/{category}/update', 'update')->name('update');
                Route::get('/{category}/edit', 'edit')->name('edit');
            });

        // tags section control
        Route::prefix('/tags')
            ->name('tags.')
            ->controller(TagsController::class)
            ->group(function () {

                // url /admin/tags
                // name admin.tags.
                Route::get('/create', 'create')->name('create');
                Route::post('/store', 'store')->name('store');
                Route::get('/{tag}/edit', 'edit')->name('edit');
                Route::put('/{tag}/update', 'update')->name('update');
                Route::delete('/{tag}/delete', 'delete')->name('delete');
            });

        // media section control
        Route::prefix('/media')
            ->name('media.')
            ->controller(MediaController::class)
            ->group(function () {

                // url /admin/media
                // name admin.media.

                Route::get('/index', 'index')->name('index');
                Route::get('/{image}/show', 'show')->name('show');
                Route::get('/{image}/edit', 'edit')->name('edit');
                Route::patch('/{image}/update', 'update')->name('update');
            });

        // comments section control
        Route::prefix('/comments')
            ->name('comments.')
            ->controller(CommentsController::class)
            ->group(function () {

                // url /admin/comments
                // name admin.comments.
                Route::get('/index', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::delete('/{comment}/delete', 'delete')->name('delete');
                Route::get('/{comment}/edit', 'edit')->name('edit');
                Route::put('/{comment}/update', 'update')->name('update');
            });

        // settings section control
        Route::prefix('/settings')
            ->name('settings.')
            ->controller(SettingsController::class)
            ->group(function () {

                // url /admin/comments
                // name admin.comments.
                Route::get('/general', 'general')->name('general');
                Route::put('/general', 'updateGeneral')->name('general.update');
                Route::get('/comment', 'comment')->name('comment');
                Route::put('/comment', 'updateComment')->name('comment.update');
            });
    });
