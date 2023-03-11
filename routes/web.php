<?php

use App\Http\Controllers\ProfileController;
use App\Models\Note;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('my-notes', function() {
    $users = User::join('notes', 'notes.user_id', '=', 'users.id')
    ->select(['users.id', 'users.name', 'users.email', 
    'notes.title', 'notes.body', DB::raw("count(notes.user_id) as user_count")])
    ->groupBy('notes.id')        
    ->orderBy('user_count', 'desc')
    ->limit(5)->get();

    $users = DB::select('SELECT name, email, Count(*) as user_notes FROM users as u 
    INNER JOIN notes ON notes.user_id = u.id GROUP 
    BY email ORDER BY user_notes DESC LIMIT 5');

    $user = User::where('email', 'bill01@example.org')->first();
    $notes = Note::whereUserId($user->id)->get();

    dd($users);
    foreach($users as $user) {
        dd($user);
    }

});

Route::get('join', function() {

    # inner join
    # neste select só aparecem selects bem sucedidos quem não tiver nenhuma nota não aparece por ex
    # só quando há relação com sucesso
    $query1 = DB::select('SELECT users.id, users.name, users.email, notes.title, notes.body 
    FROM users INNER JOIN notes ON notes.user_id = users.id limit 5');

    # dd($query1);

    # left join
    # retorna todos resultados da tabela da esquerda haja relação ou não...
    $query2 = DB::select('SELECT users.name, users.email, notes.title, notes.body 
    FROM users LEFT JOIN notes ON notes.user_id = users.id limit 5');

    # dd($query2);

    # right join
    # retorna todos resultados da tabela a direita haja relação ou não...
    $query3 = DB::select('SELECT users.id as userId, users.name, users.email, notes.title, 
    notes.body, notes.id as note_id 
    FROM users RIGHT JOIN notes ON notes.user_id = users.id limit 5');

    dd($query3);

});

require __DIR__.'/auth.php';
