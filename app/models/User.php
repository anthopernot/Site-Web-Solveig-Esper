<?php

namespace app\models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 * @package app\models
 */
class User extends Model {
    public $timestamps = false;
    protected $table = "user";
    protected $primaryKey = "id";
    protected $fillable = [
        'pseudo',
        'nom',
        'prenom',
        'mail',
        'mdp',
        'role'
    ];

}