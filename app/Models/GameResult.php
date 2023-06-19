<?php

namespace App\Models;

use App\Services\GameService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class GameResult extends Model
{
    protected $table = 'game_result';
    protected $guarded = ['id'];
    use HasFactory;
}
