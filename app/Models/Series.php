<?php

namespace App\Models;

use App\Watched\Traits\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Http;
/**
 * @method static filter()
 */
class Series extends Title
{
    use Filterable;

    protected $table = 'titles';

    protected static function booted()
    {
        static::addGlobalScope('titleType', function (Builder $builder) {
            $builder->where('title_type', 'tvSeries');
        });
    }


    /**
     * Return poster
     *
     * @return string
     */
    public function image(): string
    {
        if (config('iwatched.fetch_posters')) {
            return $this->tmdb($this->tconst, 'tv_results');
        }
        return url("/storage/posters/{$this->poster->image}");
    }
}
