<?php

use App\Http\Controllers\CitiesController;
use App\Http\Controllers\DistrictsController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\PoliceStationsController;
use App\Http\Controllers\PoliceUsersController;
use App\Http\Controllers\SalaryIncrementController;
use App\Http\Controllers\SewaPustikaController;
use App\Http\Controllers\punishmentsController;
use App\Http\Controllers\rewardsController;
use App\Http\Controllers\PoliceProfileController;
use App\Http\Controllers\LoginUserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PayMatrixImportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/dasboard', [MainController::class, 'Dashboard'])->name('dashboard')->middleware('check.login')->middleware('setlang');

Route::middleware(['setlang'])->group(function () {
    Route::prefix('districts')->group(function () {
        Route::get('/', [DistrictsController::class, 'index'])->name('districts.index');
        Route::get('/create', [DistrictsController::class, 'create'])->name('districts.create');
        Route::post('/', [DistrictsController::class, 'store'])->name('districts.store');
        Route::get('/{id}/edit', [DistrictsController::class, 'edit'])->name('districts.edit');
        Route::put('/{id}', [DistrictsController::class, 'update'])->name('districts.update');
        Route::delete('/{id}', [DistrictsController::class, 'destroy'])->name('districts.destroy');
    });


    Route::prefix('stations')->group(function () {
        Route::get('/', [PoliceStationsController::class, 'index'])->name('station.index');       // Show all stations
        Route::get('/create', [PoliceStationsController::class, 'create'])->name('stations.create'); // Show create form/modal
        Route::post('/store', [PoliceStationsController::class, 'store'])->name('stations.store');        // Store new station
        Route::get('/{id}/edit', [PoliceStationsController::class, 'edit'])->name('stations.edit');  // Show edit form
        Route::put('/{id}', [PoliceStationsController::class, 'update'])->name('stations.update');   // Update station
        Route::delete('/{id}', [PoliceStationsController::class, 'destroy'])->name('stations.destroy'); // Delete station
    });
    Route::get('/add_sewa_pustika', [SewaPustikaController::class, 'index'])->name('sewa_pustika.index');
    Route::get('sewa_pustika/{id}/add', [SewaPustikaController::class, 'showuploadpage'])->name('sewa_pustika.addshow');


    Route::post('/store_sewa_pustika', [SewaPustikaController::class, 'store'])->name('sewa_pustika.save');
    Route::get('/sewapustika/view/{filename}', [SewaPustikaController::class, 'view'])->name('sewapustika.view');

    Route::prefix('cities')->group(function () {
        Route::get('/', [CitiesController::class, 'index'])->name('city.index');         // Show all cities
        Route::get('/create', [CitiesController::class, 'create'])->name('cities.create'); // Show create modal/form
        Route::post('/', [CitiesController::class, 'store'])->name('cities.store');        // Store new city
        Route::get('/{id}/edit', [CitiesController::class, 'edit'])->name('cities.edit');  // Show edit form
        Route::put('/{id}', [CitiesController::class, 'update'])->name('cities.update');   // Update existing city
        Route::delete('/{id}', [CitiesController::class, 'destroy'])->name('cities.destroy'); // Soft delete
        Route::get('/list', [CitiesController::class, 'city_list'])->name('city.list');         // Show all cities

    });

    Route::get('/cities/by-district/{districtId}', [CitiesController::class, 'getCitiesByDistrict']);

    Route::prefix('police-users')->group(function () {
        Route::get('/', [PoliceUsersController::class, 'index'])->name('police.list.index'); // List all police users
        Route::get('/create', [PoliceUsersController::class, 'create'])->name('police.create'); // Show create modal/form
        Route::post('/', [PoliceUsersController::class, 'store'])->name('police.store');        // Store new city
        Route::get('/{id}/edit', [PoliceUsersController::class, 'edit'])->name('police.edit');  // Show edit form
        Route::put('/{id}', [PoliceUsersController::class, 'update'])->name('police.update');   // Update existing city
        Route::delete('/{id}', [PoliceUsersController::class, 'destroy'])->name('police.destroy');
    });


    Route::get('/show_all_SalaryIncrement', [SalaryIncrementController::class, 'index'])->name('salary_increment.index');

    Route::get('/show_all_punishments', [punishmentsController::class, 'index'])->name('punishments.index');

    Route::get('/show_all_rewards', [rewardsController::class, 'index'])->name('rewards.index');

    Route::get('/stations/by-city/{cityId}', [MainController::class, 'getStations']);


    Route::get('/states/by-country/{countryId}', [MainController::class, 'getStates']);
    Route::get('/districts/by-state/{stateId}', [MainController::class, 'getDistricts']);
    Route::get('/cities/by-district/{districtId}', [MainController::class, 'getCities']);




    //profile routes

    Route::get('/police_profile/{id}', [PoliceProfileController::class, 'index'])->name('police_profile.index');
    Route::get('/police/sewa-pustika/{id}', [PoliceProfileController::class, 'policeSewaPustika']);

    //punishments

    Route::get('punishment/{id}', [punishmentsController::class, 'policePunishmentAdd'])->name('punishment.add');
    Route::post('/punishments/store', [PunishmentsController::class, 'store'])->name('punishments.store');
    Route::get('/punishments/view/{filename}', [PunishmentsController::class, 'view'])->name('punishments.view');
    Route::get('/punishments/history/{id}', [PoliceProfileController::class, 'punishmentHistory']);

    //rewards
    Route::get('rewards/{id}', [rewardsController::class, 'policeRewardAdd'])->name('rewards.add');
    Route::get('aprove/rewards/{id}', [rewardsController::class, 'aproveReward'])->name('aprove.rewards.show');

    Route::post('/rewards/store', [rewardsController::class, 'store'])->name('rewards.store');
    Route::get('/rewards/view/{filename}', [rewardsController::class, 'view'])->name('rewards.view');
    Route::get('/rewards/history/{id}', [PoliceProfileController::class, 'rewardsHistory']);


    //salary increment
    Route::get('salary_increment/{id}', [SalaryIncrementController::class, 'policeSalaryIncrementAdd'])->name('salary_increment.add');
    Route::post('/salary_increment/store', [SalaryIncrementController::class, 'storeSalaryIncrement'])->name('salary.increment.store');
    Route::get('/salary_increment/view/{filename}', [SalaryIncrementController::class, 'view'])->name('salary_increment.view');
    Route::get('/salary_increment/history/{id}', [PoliceProfileController::class, 'salaryIncrementHistory']);

    //login

    Route::get('/stations/search', [PoliceStationsController::class, 'searchstation'])->name('stations.search');

    Route::get('/stations/all_station_list', [PoliceStationsController::class, 'stationTablale'])->name('stations.list.table');


    Route::get('/police-user/search', [PoliceUsersController::class, 'search'])->name('police_users.search_table');
    Route::get('/police-user/all_list', [PoliceUsersController::class, 'indexTable'])->name('police_users.list.table');
    Route::get('/sewapustika/search', [SewaPustikaController::class, 'search'])->name('sevapustika.search');
    Route::get('/salaryincrement/search', [SalaryIncrementController::class, 'search'])->name('SalaryIncrement.search');
    Route::get('/punishment/search', [punishmentsController::class, 'search'])->name('punishments.search');
    Route::get('/reward/search', [rewardsController::class, 'search'])->name('rewards.search');



    Route::get('/newdashboard', [MainController::class, 'newDashboard'])->name('newdashboard');
    // routes/web.php
    Route::get('/police-users/template', [PoliceUsersController::class, 'downloadTemplate'])->name('police-users.template');


    Route::get('/police-users/template', [PoliceUsersController::class, 'downloadTemplate'])
        ->name('police-users.template');

    // ✅ Import Police Users via Excel
    Route::post('/police-users/import', [PoliceUsersController::class, 'import'])
        ->name('import.police.users');
    Route::post('/reward-review/store', [rewardsController::class, 'aproveRewardStore'])->name('reward.review.store');

    Route::post('/pay-matrix/import', [PayMatrixImportController::class, 'import'])->name('paymatrix.import');
    Route::get('/pay-matrix/template/download', [PayMatrixImportController::class, 'downloadTemplate'])->name('pay-matrix.template.download');

    Route::get('/get-salary/bylave-and-grade', [SalaryIncrementController::class, 'getSalary'])->name('get.salary');
    //logout
    Route::post('/logout', [LoginUserController::class, 'logout'])->name('logout');

    // web.php
Route::get('/get-stations-by-user', [MainController::class, 'getStationsByUser'])->name('get.stations');

});

Route::post('/resend-otp', [LoginUserController::class, 'resendOtp'])->name('otp.resend');

Route::get('/', [LoginUserController::class, 'showLoginPage'])->name('login.page');
Route::post('/login', [LoginUserController::class, 'login'])->name('login.user');

Route::get('/otp', [LoginUserController::class, 'showOtpPage'])->name('otp.page'); // added
Route::post('/verify-otp', [LoginUserController::class, 'verifyOtp'])->name('login.verifyOtp');

Route::post('/set-language', function (\Illuminate\Http\Request $request) {
    $locale = $request->input('locale', 'en');
    session(['locale' => $locale]);
    return redirect()->back();
});
