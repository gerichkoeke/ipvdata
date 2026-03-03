<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class BackupRetentionOption extends Model
{
    protected $table = 'backup_retention_options';
    protected $fillable = ['name','days','is_full','price_multiplier','is_active','sort_order'];
    protected $casts = ['is_full'=>'boolean','is_active'=>'boolean','price_multiplier'=>'decimal:2'];
}
