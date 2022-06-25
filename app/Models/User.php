<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'privileged'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getTableColumns() {
        //Atgriež visas kolonnas tabulā ar tipiem formātā, piem.,
        //['id' => 'nofill', 'name' => 'text', 'count' => 'integer']

        $builder = $this->getConnection()->getSchemaBuilder();
        $names = $builder->getColumnListing($this->getTable());
        $type_name = [];
        foreach($names as $name)
        {
            if(!in_array($name, $this->fillable))
            {
                $type_name[$name] = 'nofill';
            }
            else {
                $type_name[$name] = $builder->getColumnType($this->getTable(), $name);
            }
        }
        return $type_name;
    }
}
