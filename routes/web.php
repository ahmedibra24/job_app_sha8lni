<?php

use App\Http\Controllers\dashboardController;
use App\Http\Controllers\jobApplicationsController;
use App\Http\Controllers\jobVacancyController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth','role:applicant'])->group(function () {
    Route::get('/dashboard', [dashboardController::class, 'index'])->name('dashboard');
    Route::get('/job-applications', [jobApplicationsController::class, 'index'])->name('job-applications.index');
    Route::get('/job-vacancies/{id}', [jobVacancyController::class, 'show'])->name('job-vacancies.show');
    Route::get('/job-vacancies/{id}/apply', [jobVacancyController::class, 'applyForm'])->name('job-vacancies.apply');
    Route::post('/job-vacancies/{id}/apply', [jobVacancyController::class, 'applicationProcessing'])->name('job-vacancies.application-processing');


    Route::get('/ask', [jobVacancyController::class, 'ask']);
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
