<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Name;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MovieController extends Controller
{

    public function index(): \Inertia\Response
    {
        $movies = Movie::with(['poster'])->filter()->simplePaginate(8)->through(function ($movie) {
            $movie->poster->fetched = true;
            if ($movie->poster->image === 'movie.png') {
                $movie->poster->image = Movie::tmdb($movie->tconst);
                $movie->poster->fetched = false;
            }
            return $movie;
        });

        return Inertia::render('Movies/MovieIndex', [
            'movies' => $movies,
            'type' => 'movies'
        ]);
    }

    public function show($id): \Inertia\Response
    {
        $movie = Movie::with('crew', 'principal', 'principal.name', 'poster', 'watched', 'rating')
            ->where('tconst', $id)
            ->firstOrFail();

        $directors = Name::whereIn('nconst', explode(',', $movie->crew->directors))->get();
        $writers = Name::whereIn('nconst', explode(',', $movie->crew->writers))->get();

        return Inertia::render('Movies/MovieShow', [
            'movie' => $movie,
            'directors' => $directors,
            'writers' => $writers,
        ]);
    }

    public function watch(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        $user->watched()->firstOrCreate(
            [
                'tconst_id' => $request->get('tconst'),
                'title_type' => Movie::class,
                'user_id' => auth()->id(),
            ],
            [
            'watched_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
            ]
        );

        return back();
    }

    public function unwatch(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        $user->watched()->where('tconst_id', $request->get('tconst'))
            ->where('title_type', Movie::class)->delete();

        return back();
    }
}
