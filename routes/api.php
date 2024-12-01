<?php

use App\Http\Controllers\Api\BrideGroom\BrideController;
use App\Http\Controllers\Api\BrideGroom\FamilyMemberBrideController;
use App\Http\Controllers\Api\BrideGroom\FamilyMemberGroomController;
use App\Http\Controllers\Api\BrideGroom\GroomController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Authentication\AuthController;
use App\Http\Controllers\Controller;
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
        Route::get('/event', [EventController::class, 'getEvent']);
        Route::post('/event/create', [EventController::class, 'createEvent']);
        Route::get('/event/{project_id}', [EventController::class, 'getEventByProjectId']);
        Route::put('/event/update/{id}', [EventController::class, 'updateEvent']);
        Route::delete('/event/delete/{id}', [EventController::class, 'deleteEvent']);
    });
});
