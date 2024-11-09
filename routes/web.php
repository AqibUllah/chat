<?php

use App\Http\Controllers\ProfileController;
use \App\Http\Controllers\ConversationController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth','verified'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\UserController::class,'index'])->name('dashboard');
    Route::get('/chats', fn() => Inertia::render('Chats',['users' => \App\Models\User::all()]))->name('chats');
    Route::get('/status', fn() => Inertia::render('Status'))->name('status');
    Route::get('/account', fn() => Inertia::render('Account'))->name('account');
    Route::get('/groups', fn() => Inertia::render('Groups'))->name('groups');
    Route::get('/calls', fn() => Inertia::render('Calls'))->name('calls');

    Route::get('/conversations', [ConversationController::class, 'index'])->name('conversations.index');
    Route::get('/conversations/{id}', [ConversationController::class, 'show'])->name('conversations.show');
    Route::post('/conversations', [ConversationController::class, 'store'])->name('conversations.store');
    Route::delete('/conversations/{id}', [ConversationController::class, 'destroy'])->name('conversations.delete');
    Route::post('/conversations/{conversationId}/messages', [ConversationController::class, 'sendMessage'])->name('conversations.sendMessage');
    Route::post('/conversations/{conversationId}/typing', [ConversationController::class, 'userTyping']);

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
