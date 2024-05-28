<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

class CustomerUser implements JWTSubject
{
    protected array $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->attributes; // Ou tout autre identifiant unique
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return $this->attributes;
    }
}
