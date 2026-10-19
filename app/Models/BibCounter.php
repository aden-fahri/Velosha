<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BibCounter extends Model
{
    protected $table = 'bib_counters';

    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['code', 'counter'];
}