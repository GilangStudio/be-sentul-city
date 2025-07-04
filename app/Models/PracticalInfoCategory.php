<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PracticalInfoCategory extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function places()
    {
        return $this->hasMany(PracticalInfoPlace::class, 'category_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }

    // Accessors
    public function getPlacesCountAttribute()
    {
        return $this->places()->count();
    }

    public function getActivePlacesCountAttribute()
    {
        return $this->places()->active()->count();
    }
}
