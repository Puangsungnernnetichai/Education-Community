<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\AdvisorController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\LogicRushController;
use App\Http\Controllers\MemoryRushController;
use App\Http\Controllers\MiniSudokuController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\WordLadderController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\PromptController as AdminPromptController;
use App\Http\Controllers\Admin\ModerationController as AdminModerationController;
use App\Http\Controllers\Admin\PerformanceController as AdminPerformanceController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');

Route::get('/banned', function () {
    return view('banned', [
        'data' => session('banned', []),
    ]);
})->name('banned.notice');

Route::middleware('auth')->group(function () {
    Route::get('/__ban-check', function () {
        return response()->json(['ok' => true]);
    })->name('session.ban_check');

    Route::get('/advisor', [AdvisorController::class, 'index'])->name('advisor.index');
    Route::post('/advisor/message', [AdvisorController::class, 'message'])->name('advisor.message');
});

Route::get('/tags/{slug}', [TagController::class, 'show'])->name('tags.show');
Route::get('/topics/{slug}', [TagController::class, 'show'])->name('topics.show');

Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

Route::middleware('auth')->group(function () {
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::patch('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::patch('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::post('/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('admin.users.role');
    Route::post('/users/{user}/ban', [AdminUserController::class, 'ban'])->name('admin.users.ban');
    Route::post('/users/{user}/unban', [AdminUserController::class, 'unban'])->name('admin.users.unban');
    Route::post('/users/{user}/points', [AdminUserController::class, 'adjustPoints'])->name('admin.users.points');

    Route::get('/prompts', [AdminPromptController::class, 'index'])->name('admin.prompts.index');
    Route::get('/prompts/{prompt}', [AdminPromptController::class, 'show'])->name('admin.prompts.show');
    Route::post('/prompts/{prompt}/versions', [AdminPromptController::class, 'storeVersion'])->name('admin.prompts.versions.store');
    Route::post('/prompts/{prompt}/activate/{version}', [AdminPromptController::class, 'activate'])->name('admin.prompts.activate');

    Route::get('/moderation/posts', [AdminModerationController::class, 'posts'])->name('admin.moderation.posts');
    Route::post('/moderation/posts/{post}/hide', [AdminModerationController::class, 'hidePost'])->name('admin.moderation.posts.hide');
    Route::post('/moderation/posts/{post}/unhide', [AdminModerationController::class, 'unhidePost'])->name('admin.moderation.posts.unhide');

    Route::get('/moderation/comments', [AdminModerationController::class, 'comments'])->name('admin.moderation.comments');
    Route::post('/moderation/comments/{comment}/hide', [AdminModerationController::class, 'hideComment'])->name('admin.moderation.comments.hide');
    Route::post('/moderation/comments/{comment}/unhide', [AdminModerationController::class, 'unhideComment'])->name('admin.moderation.comments.unhide');

    Route::get('/performance', [AdminPerformanceController::class, 'index'])->name('admin.performance.index');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->prefix('games')->name('games.')->group(function () {
    Route::get('/', [GameController::class, 'index'])->name('index');
    Route::get('/{game}/math-sprint/start', [GameController::class, 'startMathSprint'])->name('math_sprint.start');
    Route::get('/logic', [LogicRushController::class, 'show'])->name('logic.show');
    Route::post('/logic/finish', [LogicRushController::class, 'finish'])->name('logic.finish');

    Route::get('/memory', [MemoryRushController::class, 'show'])->name('memory.show');
    Route::post('/memory/finish', [MemoryRushController::class, 'finish'])->name('memory.finish');

    Route::get('/word-ladder', [WordLadderController::class, 'show'])->name('word_ladder.show');
    Route::post('/word-ladder/finish', [WordLadderController::class, 'finish'])->name('word_ladder.finish');

    Route::get('/mini-sudoku', [MiniSudokuController::class, 'show'])->name('mini_sudoku.show');
    Route::post('/mini-sudoku/finish', [MiniSudokuController::class, 'finish'])->name('mini_sudoku.finish');

    Route::post('/submit', [GameController::class, 'submit'])->name('submit');
    Route::get('/{game}', [GameController::class, 'play'])->name('play');
});

require __DIR__.'/auth.php';
