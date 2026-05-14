<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\AutomaticEmailController;
use App\Http\Controllers\Api\BarangaysController;
use App\Http\Controllers\Api\RegionsController;
use App\Livewire\Auth\RegisterPage;
use App\Livewire\Pages\AppointmentPage;
use App\Livewire\Pages\CasePage;
use App\Livewire\Pages\CaseStagePage;
use App\Livewire\Pages\CaseSubType;
use App\Livewire\Pages\ClientPage;
use App\Livewire\Pages\CourtPage;
use App\Models\AppointmentsModel;
use App\Models\HouseModel;
use App\Models\PHRegions;
use App\Models\User;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/api/regions', [ApiController::class, 'getRegions'])->name('api.regions.index');
Route::get('/api/provinces', [ApiController::class, 'getProvinces'])->name('api.provinces.index');
Route::get('/api/municipalities', [ApiController::class, 'getMunicipalities'])->name('api.municipalities.index');
Route::get('/api/barangays', [ApiController::class, 'getBarangays'])->name('api.barangays.index');

Route::get('/api/users', function (Request $request) {

    return User::query()
        ->when($request->search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        })
        ->where('role', 'user')
        ->select('id', 'name', 'email', 'profile_picture')
        ->limit(20)
        ->get();

})->name('api.users.index');

Route::get('/api/house-models', function (Request $request) {

    return HouseModel::query()
        ->when($request->search, function ($query, $search) {
            $query->where('model_name', 'like', "%{$search}%");
        })
        ->select('id', 'model_name', 'price', 'floor_area', 'image')
        ->limit(20)
        ->get()
        ->map(function ($model) {
            return [
                'id' => $model->id,
                'name' => $model->model_name,
                'description' => "₱{$model->price} • {$model->floor_area} sqm",
                'image' => $model->image
                    ? asset('storage/' . $model->image)
                    : null,
            ];
        });

})->name('api.house-models.index');

// EDIT CLIENT
Route::get('/api/client/regions', [ApiController::class, 'getRegions'])->name('api.regions.client');
Route::get('/api/client/provinces', [ApiController::class, 'getProvinces'])->name('api.provinces.client');
Route::get('/api/client/municipalities', [ApiController::class, 'getMunicipalities'])->name('api.municipalities.client');
Route::get('/api/client/barangays', [ApiController::class, 'getBarangays'])->name('api.barangays.client');

Route::get('/location/province/{regionCode}', [ApiController::class, 'getProvincesByRegion'])->name('location.province');
Route::get('/location/municipalities/{provinceCode}', [ApiController::class, 'getMunicipalitiesByProvince'])->name('location.municipality');
Route::get('/location/barangay/{municipalityCode}', [ApiController::class, 'getBarangaysByMunicipality'])->name('location.barangay');

// CASE SUB TYPE
// Route::get('/case/types', [ApiController::class, 'getCaseTypes'])->name('api.case.types');
// Route::get('/api/case/sub-types', [ApiController::class, 'getCaseSubTypes'])->name('api.case.sub-types');

// // CASE STAGE 
// Route::get('/api/case/stage', [ApiController::class, 'getCaseStage'])->name('api.case.stage');

// // COURT TYPE
// Route::get('/court/types', [ApiController::class, 'getCourtTypes'])->name('api.court.types');
// Route::get('/courts', [ApiController::class, 'getCourts'])->name('api.courts');
// Route::get('/courts/{courtTypeId}', [ApiController::class, 'getCourtsByType'])->name('api.court.byType');

// // USER
// Route::get('/api/user/participant', [ApiController::class, 'getParticipant'])->name('api.user.participant');
// Route::post('/law-sched-admin/appointment', [AppointmentPage::class, 'createMeeting']);

// // SERVICES
// Route::get('/api/services/types', [ApiController::class, 'getServiceTypes'])->name('api.services.types');
// Route::get('/api/services', [ApiController::class, 'getServices'])->name('api.services');

// // ZOOM
// // Route::get('/zoom/authorize', [AppointmentPage::class, 'redirectToZoom'])->name('zoom.authorize');
// // Route::get('/zoom/callback', [AppointmentPage::class, 'handleZoomCallback']);
// // Route::post('/zoom-meetings', [AppointmentPage::class, 'createMeeting']);

// Route::get('/location/province/{regionCode}', [ApiController::class, 'getProvincesByRegion'])->name('location.province');
// Route::get('/location/municipalities/{provinceCode}', [ApiController::class, 'getMunicipalitiesByProvince'])->name('location.municipality');
// Route::get('/location/barangay/{municipalityCode}', [ApiController::class, 'getBarangaysByMunicipality'])->name('location.barangay');

// Route::get('/appointments', [AutomaticEmailController::class, 'index']);
// Route::get('/cases', [AutomaticEmailController::class, 'getCaseDetails']);
// Route::get('/zooms', [AutomaticEmailController::class, 'getZoomDetails']);

// Route::post('/send-appointment-reminder', [AutomaticEmailController::class, 'sendAppointmentReminder']);
// Route::post('/send-case-reminder', [AutomaticEmailController::class, 'sendCaseReminder']);
// Route::post('/send-zoom-reminder', [AutomaticEmailController::class, 'sendZoomReminder']);