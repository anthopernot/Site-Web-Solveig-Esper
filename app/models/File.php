<?php

namespace app\models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 * @package app\models
 */
class File extends Model {
    public $timestamps = false;
    protected $table = "file";
    protected $primaryKey = "id";
    protected $fillable = [
        'nom',
        'description',
        'path',
        'size'
    ];

}