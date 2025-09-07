<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CreditReportDownloadController;

Route::get('/credit-reports/{report}/download', [CreditReportDownloadController::class, 'download'])
    ->name('credit-reports.download')
    ->middleware(['auth']);

Route::get('/', function () {
    return redirect()->to('/admin'); 
});