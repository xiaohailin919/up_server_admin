<?php

namespace App;

use App\Helpers\Format;
use App\Models\MySql\DataRole;
use App\Models\MySql\Publisher;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends \Illuminate\Foundation\Auth\User implements JWTSubject
{
    use Notifiable;
    use HasRoles;

    const STATUS_RUNNING = 3;
    const STATUS_DELETED = 99;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

   public function setPasswordAttribute($password)
   {   
        $this->attributes['password'] = bcrypt($password);
   }

    public function getUpdatedAtAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    public function getCreatedAtAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    /**
     * 用户与数据权限角色关联关系
     *
     * @return BelongsToMany
     */
    public function dataRoles(): BelongsToMany
    {
        return $this->belongsToMany(DataRole::class, 'data_role_user_relationship', 'user_id', 'role_id');
    }

    /**
     * 用户与开发者关联关系
     *
     * @return MorphToMany
     */
    public function publishers(): MorphToMany
    {
        return $this->morphToMany(
            Publisher::class,
            'target',
            'data_role_user_permission',
            'target_id',
            'publisher_id'
        )->withPivot('display_type');
    }
}
