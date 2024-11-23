<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroupUser extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $appends = [
        'user_name',
        'user_email',
    ];

    protected $fillable = [
        'group_id',
        'user_id',
        'is_admin',
    ];

    protected $hidden = [
        'group_id',
        'created_at',
        'updated_at',
    ];

    public function getUserNameAttribute()
    {
        $user_name = $this->User->name;
        unset($this->User);
        return $user_name;
    }

    public function getUserEmailAttribute()
    {
        $user_email = $this->User->email;
        unset($this->User);
        return $user_email;
    }

    public function Group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function User(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function Files(): HasMany
    {
        return $this->hasMany(File::class, 'group_user_id');
    }

    /** @noinspection PhpUnused */
    public function OldFiles(): HasMany
    {
        return $this->hasMany(OldFile::class, 'group_user_id');
    }
}
