<?php

namespace App\Models;

use App\Enums\IsAdminEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class Group extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $appends = [
        'approved_admin_name',
        'approved_admin_email',
    ];

    protected $fillable = [
        'name',
        'approved_by',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /** @noinspection PhpUnused */
    public function getApprovedAdminNameAttribute()
    {
        $name = $this->Admin?->name;
        unset($this->Admin);
        return $name;
    }

    /** @noinspection PhpUnused */
    public function getApprovedAdminEmailAttribute()
    {
        $email = $this->Admin?->email;
        unset($this->Admin);
        return $email;
    }

    /** @noinspection PhpUnused */
    public function ScopeGroupAdmin($query,$group_id)
    {
        return $query->where('id',$group_id)->first()->GroupUsers()->where('is_admin',IsAdminEnum::ADMIN)->first();
    }

    public function Admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }

    /** @noinspection PhpUnused */
    public function GroupUsers(): HasMany
    {
        return $this->hasMany(GroupUser::class, 'group_id');
    }
}
