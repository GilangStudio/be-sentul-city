<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CareerApplication extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'applied_at' => 'datetime',
    ];

    // Relationships
    public function position()
    {
        return $this->belongsTo(CareerPosition::class, 'career_position_id');
    }

    // Accessors
    public function getCvFileUrlAttribute()
    {
        return $this->cv_file_path ? asset('storage/' . $this->cv_file_path) : null;
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => 'Pending Review',
            'reviewed' => 'Reviewed',
            'shortlisted' => 'Shortlisted',
            'rejected' => 'Rejected',
            'hired' => 'Hired',
            default => 'Pending Review'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'reviewed' => 'info',
            'shortlisted' => 'primary',
            'rejected' => 'danger',
            'hired' => 'success',
            default => 'warning'
        };
    }

    public function getAppliedAgoAttribute()
    {
        return $this->applied_at->diffForHumans();
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('applied_at', 'desc');
    }
}
