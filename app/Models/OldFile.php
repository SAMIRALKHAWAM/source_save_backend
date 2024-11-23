<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OldFile extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $fillable = [
        'file_id',
        'group_user_id',
        'name',
        'description',
        'size_MB',
        'url',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'size_MB' => 'float',
    ];

    public function File(): BelongsTo
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    public function GroupUser(): BelongsTo
    {
        return $this->belongsTo(GroupUser::class, 'group_user_id');
    }

}
