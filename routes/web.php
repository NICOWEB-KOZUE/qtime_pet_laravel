<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\AdminTicketController;
use App\Http\Middleware\AdminAuth;

// トップページ（受付状況 & 受付導線）
Volt::route('/', 'reception.index')->name('home');

// 初回登録フォーム
Volt::route('/register', 'reception.register')->name('register');

// 再診ログイン
Volt::route('/login', 'reception.login')->name('patient.login');

// 受付完了画面
Volt::route('/done/{ticket}', 'reception.done')->name('done');

// 管理者ログイン
Volt::route('/admin/login', 'admin.login')->name('admin.login');

// 管理者ダッシュボードと操作
Route::middleware(AdminAuth::class)->prefix('admin')->name('admin.')->group(function () {
    Volt::route('/', 'admin.dashboard')->name('dashboard');
    Route::post('/next', [AdminTicketController::class, 'next'])->name('next');
    Route::post('/undo', [AdminTicketController::class, 'undo'])->name('undo');
    Route::post('/manual', [AdminTicketController::class, 'manual'])->name('manual');
    Route::post('/logout', [AdminTicketController::class, 'logout'])->name('logout');
});

// ディスプレイ画面（Blade 単独ビューを後で用意）
Route::view('/display', 'display')->name('display');

// 診察状況ページ
Volt::route('/status', 'reception.status')->name('status');

// 診察状況 API（後でコントローラを実装）
Route::get('/api/status', [StatusController::class, 'json'])
    ->name('status.json');
