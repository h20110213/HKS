<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasObjectiveTrait;
use App\Traits\HasAvatarTrait;
use App\Interfaces\HasObjectiveInterface;

class Company extends Model implements HasObjectiveInterface
{
    use HasObjectiveTrait, HasAvatarTrait;

    protected $fillable = [
        'name', 'description', 'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getOKrRoute()
    {
        return route('company.okr');
    }
}
