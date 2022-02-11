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
     * Get the cards for the column.
     */
    public function cards()
    {
        return $this->hasMany(Card::class);
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
