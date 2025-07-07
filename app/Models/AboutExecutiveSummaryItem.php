<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutExecutiveSummaryItem extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getIconUrlAttribute()
    {
        return $this->icon_path ? asset('storage/' . $this->icon_path) : null;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('created_at');
    }
}
