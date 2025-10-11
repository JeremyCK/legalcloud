<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CHKT extends Model
{
    use HasFactory;

    protected $fillable = ["case_id", "client_id", "last_spa_date", "current_spa_date", "chkt_filled_on", "per3_rpgt_paid", "remark", "status", "created_by", "created_at", "updated_at", "case_ref", "client_name", "branch", "received", "file_ori_name", "file_new_name", "s3_file_name", "is_migrated", "no_file", "deleted_at", "deleted_by"];

    protected $table = 'chkt';
    protected $primaryKey = 'id';
}
