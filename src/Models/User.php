<?php

namespace MattDaneshvar\Survey\Models;

use App\Library\Constants;
use Carbon\Carbon;
use function config;
use function session;
use function abort_unless;
use App\Models\V2\PaObject;
use App\Models\V2\MasterBudget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Notifications\Notifiable;
use Lab404\Impersonate\Models\Impersonate;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\CausesActivity;
use Silber\Bouncer\Database\HasRolesAndAbilities;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Iworking\IworkingBoilerplate\Traits\ActivityLogTrait;
use Iworking\IworkingBoilerplate\Traits\AutoGenerateUuid;
use Iworking\IworkingBoilerplate\Models\V2\MasterBudgetAnnualRelationship;


class User extends Authenticatable
{
    use Notifiable, SoftDeletes;
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'first_name',
        'last_name',
        'country',
        'timezone',
        'mobile_phone',
        'date_format',
        'numbers_format',
        'default_locale',
        'company_id',
        'samaccountname',
        'guid',
        'domain',
        'adpwd',
        'company_name',
        'company_address',
        'address',
        'company_city',
        'company_state',
        'state',
        'company_country',
        'company_postal_code',
        'customer_id',
        'postal_code',
        'department_id',
        'population',
        'main_specialty',
        'secondary_specialty',
        'city',
        'type',
        'treatment_id',
        'consent',
        'mail_contact',
        'other_contact_information',
        'blocked',
        'nif',
        'prefix_phone',
        'prefix_mobile',
        'job_title_id',
        'consent_request',
        'collegiate_number'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getFullNameAttribute()
    {
        return $this->name . ' ' . $this->last_name;
    }


    public function scopePeopleFilter($query, $filters)
    {
        if (!empty($filters['account_id'])) {
            $query->whereHas('centers', function ($query) use ($filters) {
                $query->where('account_id', $filters['account_id']);
            });
        }

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['email'])) {
            $query->where('email', 'like', '%' . $filters['email'] . '%');
        }

        if (!empty($filters['main_specialty'])) {
            $query->where('main_specialty', $filters['main_specialty']);
        }

        if (!empty($filters['secondary_specialty'])) {
            $query->where('secondary_specialty', $filters['secondary_specialty']);
        }

        if (!empty($filters['population'])) {
            $query->where('population', $filters['population']);
        }

        if (!empty($filters['type_id'])) {
            $query->whereHas('userType', function ($query) use ($filters) {
                $query->where('id', $filters['type_id']);
            });

            return $query;
        }
    }

    /**
     * @return string
     */
    public function getUserMediatorCustomNameAttribute(): string
    {
        $parts = [];

        if ($this->paPerson) {
            $parts[] = $this->paPerson->value;
        }

        if ($this->name) {
            $parts[] = $this->name;
        }

        if ($this->email) {
            $parts[] = $this->email;
        }

        return trim(implode(' - ', $parts), ' - ');
    }

    public function setCustomerIdAttribute($value)
    {
        $this->attributes['customer_id'] = ($value == "") ? null : $value;
    }

    public function setCountryIdAttribute($value)
    {
        $this->attributes['country_id'] = ($value == "") ? null : $value;
    }

    public function storeRolesSession()
    {
        $roles = [];
        foreach ($this->roles as $role) {
            $roles[] = $role->key_value;
        }
        session(['user.roles' => $roles]);
    }

    public function authorizeRoles($roles)
    {
        abort_unless($this->hasAnyRole($roles), 401);
        return true;
    }

    public function hasAnyRole($roles)
    {
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }
        } else {
            if ($this->hasRole($roles)) {
                return true;
            }
        }
        return false;
    }

    public function hasRole($role, $direct = null)
    {
        if ($direct) {
            if ($this->roles()->where('key_value', $role)->first()) {
                return true;
            }
            return false;
        }
        if (!session()->has('user.roles')) {
            $this->storeRolesSession();
        }
        if (in_array($role, session()->get('user.roles'))) {
            return true;
        }
        /*
        if ($this->roles()->where('key_value', $role)->first()) {

        }
        */
        return false;
    }

    public function hasNotRole($role)
    {
        return !$this->hasRole($role);
    }

    public function getRandomUser()
    {
        return User::inRandomOrder()->value('email');
    }

    public function applyTimeZone($dateTime)
    {
        if ($this->timezone == '') {
            $this->timezone = config('app.timezone');
            $this->save();
        }
        return Carbon::parse($dateTime)->timezone($this->timezone)->format('d/m/yy');
    }

    public function applyDateFormat($date = null, $canBeEmpty = false)
    {
        if ($date != null) {
            if (App::isLocale('en')) {
                return Carbon::parse($date)->format('m/d/Y');
            }
            return Carbon::parse($date)->format('d/m/Y');
        } elseif ($canBeEmpty) {
            return '';
        } else {
            return 'dd/mm/yyyy';
        }
    }

}
