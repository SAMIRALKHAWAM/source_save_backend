<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OldFile extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $appends = [
        'user_id',
        'user_name',
    ];

    protected $fillable = [
        'file_id',
        'group_user_id',
        'name',
        'description',
        'size_MB',
        'url',
        'diff',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'size_MB' => 'float',
    ];


    /** @noinspection PhpUnused */
    public function getUserIdAttribute()
    {
        $id = $this->GroupUser?->user_id;
        unset($this->GroupUser);
        return $id;
    }

    /** @noinspection PhpUnused */
    public function getUserNameAttribute()
    {
        $name = $this->GroupUser?->user_name;
        unset($this->GroupUser);
        return $name;
    }

    public function File(): BelongsTo
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    /** @noinspection PhpUnused */
    public function GroupUser(): BelongsTo
    {
        return $this->belongsTo(GroupUser::class, 'group_user_id');
    }

}
