<?php

use App\Http\Controllers\Api\BrideGroom\BrideController;
use App\Http\Controllers\Api\BrideGroom\FamilyMemberBrideController;
use App\Http\Controllers\Api\BrideGroom\FamilyMemberGroomController;
use App\Http\Controllers\Api\BrideGroom\GroomController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\RundownController;
use App\Http\Controllers\Api\Todolist\CategoryTodolistController;
use App\Http\Controllers\Api\Todolist\SubTodolistController;
use App\Http\Controllers\Api\Todolist\TodolistController;
use App\Http\Controllers\Api\Vendor\CategoryVendorController;
use App\Http\Controllers\Api\Vendor\ListVendorController;
use App\Http\Controllers\Authentication\AuthController;
use App\Http\Controllers\Controller;
use App\Models\CategoryVendors;
use App\Models\ListVendors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group([
    'middleware' => 'api',
], function ($router) {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);

    //middleware member affliasi
    Route::middleware(['check.role:user'])->group(function () {
        // Bride
        Route::get('/bride', [BrideController::class, 'getBride']);
        Route::post('/bride/create', [BrideController::class, 'createBride']);
        Route::get('/bride-project-id/{project_id}', [BrideController::class, 'getBrideByProjectId']);
        Route::get('/bride/{id}', [BrideController::class, 'getBrideById']);
        Route::put('/bride/update/{id}', [BrideController::class, 'updateBride']);
        Route::delete('/bride/delete/{id}', [BrideController::class, 'deleteBride']);
        // Groom
        Route::get('/groom', [GroomController::class, 'getGroom']);
        Route::post('/groom/create', [GroomController::class, 'createGroom']);
        Route::get('/groom-project-id/{project_id}', [GroomController::class, 'getGroomByProjectId']);
        Route::get('/groom/{id}', [GroomController::class, 'getGroomById']);
        Route::put('/groom/update/{id}', [GroomController::class, 'updateGroom']);
        Route::delete('/groom/delete/{id}', [GroomController::class, 'deleteGroom']);
        // Family Member Bride
        Route::get('/family-member-bride', [FamilyMemberBrideController::class, 'getFamilyMemberBrides']);
        Route::post('/family-member-bride/create', [FamilyMemberBrideController::class, 'createFamilyMemberBrides']);
        Route::get('/family-member-bride-by-bride-id/{bride_id}', [FamilyMemberBrideController::class, 'getFamilyMemberBridesByBrideId']);
        Route::get('/family-member-bride/{id}', [FamilyMemberBrideController::class, 'getFamilyMemberBridesById']);
        Route::put('/family-member-bride/update/{id}', [FamilyMemberBrideController::class, 'updateFamilyMemberBrides']);
        Route::delete('/family-member-bride/delete/{id}', [FamilyMemberBrideController::class, 'deleteFamilyMemberBrides']);
        // Family Member Groom
        Route::get('/family-member-groom', [FamilyMemberGroomController::class, 'getFamilyMemberGrooms']);
        Route::post('/family-member-groom/create', [FamilyMemberGroomController::class, 'creategetFamilyMemberGrooms']);
        Route::get('/family-member-groom-by-groom-id/{groom_id}', [FamilyMemberGroomController::class, 'getgetFamilyMemberGroomsByGroomId']);
        Route::get('/family-member-groom/{id}', [FamilyMemberGroomController::class, 'getgetFamilyMemberGroomsById']);
        Route::put('/family-member-groom/update/{id}', [FamilyMemberGroomController::class, 'updategetFamilyMemberGrooms']);
        Route::delete('/family-member-groom/delete/{id}', [FamilyMemberGroomController::class, 'deletegetFamilyMemberGrooms']);
        // Event
        Route::get('/event', [EventController::class, 'getEvents']);
        Route::post('/event/create', [EventController::class, 'createEvents']);
        Route::get('/event-project-id/{project_id}', [EventController::class, 'getEventsByProjectId']);
        Route::get('/event/{id}', [EventController::class, 'getEventsById']);
        Route::put('/event/update/{id}', [EventController::class, 'updateEvents']);
        Route::delete('/event/delete/{id}', [EventController::class, 'deleteEvents']);
        // Rundown
        Route::get('/rundown', [RundownController::class, 'getRundowns']);
        Route::post('/rundown/create', [RundownController::class, 'createRundowns']);
        Route::get('/rundown-project-id/{project_id}', [RundownController::class, 'getRundownsByProjectId']);
        Route::get('/rundown/{id}', [RundownController::class, 'getRundownsById']);
        Route::put('/rundown/update/{id}', [RundownController::class, 'updateRundowns']);
        Route::delete('/rundown/delete/{id}', [RundownController::class, 'deleteRundowns']);
        //Get All Todolist By project
        Route::get('/all-todolist-by-project', [CategoryTodolistController::class, 'getAllTodolistsByProject']);
        Route::get('/count-completed-status', [CategoryTodolistController::class, 'countCompletedStatuses']);
        Route::get('/count-not-completed-status', [CategoryTodolistController::class, 'countNotCompletedStatuses']);
        // Category TodoList
        Route::get('/category-todolist', [CategoryTodolistController::class, 'getCategoryTodolists']);
        Route::get('/category-todolist-status-complete', [CategoryTodolistController::class, 'statusCompleteCategoryTodolists']);
        Route::get('/category-todolist-status-not-complete', [CategoryTodolistController::class, 'statusNotCompleteCategoryTodolists']);
        Route::post('/category-todolist/create', [CategoryTodolistController::class, 'createCategoryTodolists']);
        Route::get('/category-todolist-project-id/{project_id}', [CategoryTodolistController::class, 'getCategoryTodolistsByProjectId']);
        Route::get('/category-todolist/{id}', [CategoryTodolistController::class, 'getCategoryTodolistsById']);
        Route::put('/category-todolist/update/{id}', [CategoryTodolistController::class, 'updateCategoryTodolists']);
        Route::delete('/category-todolist/delete/{id}', [CategoryTodolistController::class, 'deleteCategoryTodolists']);
        // TodoList
        Route::get('/todolist', [TodolistController::class, 'getTodolists']);
        Route::get('/todolist-all-category', [TodolistController::class, 'getTodolistsByAllCategoryTodolistsId']);
        Route::get('/todolist-status-complete/{category_todolist_id}', [TodolistController::class, 'statusCompleteTodolists']);
        Route::get('/todolist-status-not-complete/{category_todolist_id}', [TodolistController::class, 'statusNotCompleteTodolists']);
        Route::post('/todolist/create', [TodolistController::class, 'createTodolists']);
        Route::get('/todolist-category-todolist-id/{category_todolist_id}', [TodolistController::class, 'getTodolistsByCategoryTodolistsId']);
        Route::get('/todolist/{id}', [TodolistController::class, 'getTodolistsById']);
        Route::put('/todolist/update/{id}', [TodolistController::class, 'updateTodolists']);
        Route::delete('/todolist/delete/{id}', [TodolistController::class, 'deleteTodolists']);
        // Sub TodoList
        Route::get('/sub-todolist', [SubTodolistController::class, 'getSubTodolists']);
        Route::get('/sub-todolist-status-complete/{todolist_id}', [SubTodolistController::class, 'statusCompleteSubTodolists']);
        Route::get('/sub-todolist-status-not-complete/{todolist_id}', [SubTodolistController::class, 'statusNotCompleteSubTodolists']);
        Route::post('/sub-todolist/create', [SubTodolistController::class, 'createSubTodolists']);
        Route::get('/sub-todolist-todolist-id/{todolist_id}', [SubTodolistController::class, 'getSubTodolistsByTodolistsId']);
        Route::get('/sub-todolist/{id}', [SubTodolistController::class, 'getSubTodolistsById']);
        Route::put('/sub-todolist/update/{id}', [SubTodolistController::class, 'updateSubTodolists']);
        Route::delete('/sub-todolist/delete/{id}', [SubTodolistController::class, 'deleteSubTodolists']);
        // Category Vendor
        Route::get('/category-vendor', [CategoryVendorController::class, 'getCategoryVendors']);
        Route::post('/category-vendor/create', [CategoryVendorController::class, 'createCategoryVendors']);
        Route::get('/category-vendor-project-id/{project_id}', [CategoryVendorController::class, 'getCategoryVendorsByProjectId']);
        Route::get('/category-vendor/{id}', [CategoryVendorController::class, 'getCategoryVendorsById']);
        Route::put('/category-vendor/update/{id}', [CategoryVendorController::class, 'updateCategoryVendors']);
        Route::delete('/category-vendor/delete/{id}', [CategoryVendorController::class, 'deleteCategoryVendors']);
        // List Vendor
        Route::get('/list-vendor', [ListVendorController::class, 'getListVendors']);
        Route::post('/list-vendor/create', [ListVendorController::class, 'createListVendors']);
        Route::get('/list-vendor-category-vendor-id/{category_vendor_id}', [ListVendorController::class, 'getListVendorsByCategoryVendorId']);
        Route::get('/list-vendor/{id}', [ListVendorController::class, 'getListVendorsById']);
        Route::put('/list-vendor/update/{id}', [ListVendorController::class, 'updateListVendors']);
        Route::delete('/list-vendor/delete/{id}', [ListVendorController::class, 'deleteListVendors']);
    });
});
