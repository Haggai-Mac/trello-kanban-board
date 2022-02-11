<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Card extends Model
{
    use SoftDeletes;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'show_modal',
        'update_path',
        'delete_path',
    ];

    /**
     * Generates the url path for deleteing the column
     *
     * @return {string}
     */
    public function getShowModalAttribute()
    {
        return false;
    }

    /**
     * Generates the url path for updating the card
     *
     * @return {string}
     */
    public function getUpdatePathAttribute()
    {
        return route('api.cards.update', [$this->id]);
    }

    /**
     * Generates the url path for deleteing the card
     *
     * @return {string}
     */
    public function getDeletePathAttribute()
    {
        return route('api.cards.destroy', [$this->id]);
    }

    /**
     * Get the column that owns the card.
     */
    public function column()
    {
        return $this->belongsTo(Column::class);
    }
}
