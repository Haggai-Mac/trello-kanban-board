<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Column extends Model
{
    use SoftDeletes;

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
        'cards',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'update_path',
        'delete_path',
    ];

    /**
     * Get the cards for the column.
     */
    public function cards()
    {
        return $this->hasMany(Card::class)->orderby('order');
    }

    /**
     * Generates the url path for updating the column
     *
     * @return {string}
     */
    public function getUpdatePathAttribute()
    {
        return route('api.columns.update', [$this->id]);
    }

    /**
     * Generates the url path for deleteing the column
     *
     * @return {string}
     */
    public function getDeletePathAttribute()
    {
        return route('api.columns.destroy', [$this->id]);
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        self::deleting(function($column) {
            $column->cards()->each(function($card) {
                $card->delete();
            });
       });
    }
}
