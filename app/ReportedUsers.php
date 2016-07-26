<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReportedUsers extends Model
{
    protected $fillable = [
    	'user_id',
    	'report',
    	'blocked'
    ];
}
