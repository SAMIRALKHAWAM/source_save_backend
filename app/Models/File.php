<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class File extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $appends = [
        'user_id',
        'user_name',
    ];

    protected $fillable = [
        'group_user_id',
        'name',
        'description',
        'size_MB',
        'url',
        'availability',
        'status',
        'reserved_by',
    ];

    protected $hidden = [

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

    public function ReservedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reserved_by');
    }


    public function GroupUser(): BelongsTo
    {
        return $this->belongsTo(GroupUser::class, 'group_user_id');
    }

    /** @noinspection PhpUnused */
    public function OldFiles(): HasMany
    {
        return $this->hasMany(OldFile::class, 'file_id');
    }
}
