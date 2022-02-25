<?php


namespace App\Watched\Traits;


use App\Models\Episode;
use App\Models\Watched;

trait Watchable
{
    /**
     * Boot watchable trait.
     */
    protected static function bootWatchable()
    {
        static::deleting(function ($model) {
            $model->watched->each->delete();
        });
    }

    /**
     * Title, Episode or series can be watched.
     *
     * @return mixed
     */
    public function watched()
    {
        return $this->morphMany(Watched::class, 'tconst', 'title_type', null, 'tconst');
    }

    /**
     * Return watched state of model.
     *
     * @return bool
     */
    public function isWatched(): bool
    {
        return (bool)$this->watched->where('user_id', auth()->id())->count();
    }

    /**
     * Return watched status of model for user.
     *
     * @return bool
     */
    public function getIsWatchedAttribute(): bool
    {
        return $this->isWatched();
    }

    public function watch()
    {
        return auth()->user()->watched()->firstOrcreate([
            'title_type' => __CLASS__,
            'tconst_id' => $this->tconst,
            'user_id' => auth()->id(),
        ],[
            'watched_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
