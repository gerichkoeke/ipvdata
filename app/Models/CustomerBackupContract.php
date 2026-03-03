<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CustomerBackupContract extends Model {
    protected $fillable = [
        'customer_id','type','machines','total_disk_gb',
        'network_type_id','bandwidth_option_id','retention_id',
        'backup_software_id','machines_detail','monthly_value',
    ];
    protected $casts = ['machines_detail' => 'array'];

    public function getNameAttribute(): string
    {
        return $this->software?->name ?? 'Backup Gerenciado';
    }

    public function getNetworkLabelAttribute(): string
    {
        if ($this->networkType) return $this->networkType->name;
        return 'VPN Client';
    }

    public function getBackupStorageGbAttribute(): float
    {
        return $this->total_disk_gb * 0.5;
    }

    public function getMachineCountAttribute(): int
    {
        return (int) $this->machines;
    }
    public function customer()        { return $this->belongsTo(Customer::class); }
    public function networkType()     { return $this->belongsTo(NetworkType::class); }
    public function bandwidthOption() { return $this->belongsTo(BandwidthOption::class); }
    public function retention()       { return $this->belongsTo(BackupRetentionOption::class, 'retention_id'); }
    public function software()        { return $this->belongsTo(BackupSoftwareOption::class, 'backup_software_id'); }
}
