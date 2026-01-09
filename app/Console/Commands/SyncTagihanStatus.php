<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Log;

class SyncTagihanStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tagihan:sync-status {--force : Force sync even manual status}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync tagihan status with pembayaran status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting tagihan status synchronization...');
        
        $force = $this->option('force');
        
        if ($force) {
            $this->warn('Force mode: Will sync all tagihan regardless of manual status');
        }

        try {
            // 1. Sync paid tagihan (yang sudah ada pembayaran lunas)
            $this->syncPaidTagihan($force);
            
            // 2. Sync unpaid tagihan (yang status sudah_bayar tapi tidak ada pembayaran valid)
            $this->syncUnpaidTagihan($force);
            
            // 3. Sync overdue tagihan (yang lewat jatuh tempo)
            $this->syncOverdueTagihan($force);
            
            $this->info('Synchronization completed successfully!');
            
        } catch (\Exception $e) {
            $this->error('Synchronization failed: ' . $e->getMessage());
            Log::error('Tagihan sync command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }

        return 0;
    }

    /**
     * Sync tagihan yang sudah ada pembayaran lunas
     */
    private function syncPaidTagihan($force = false)
    {
        $this->info('Syncing paid tagihan...');
        
        $query = Tagihan::whereHas('pembayaran', function($q) {
                $q->where('status', 'lunas')->where('is_verified', true);
            })
            ->where('status', '!=', Tagihan::STATUS_SUDAH_BAYAR);
            
        if (!$force) {
            $query->where(function($q) {
                $q->where('is_manual_status', false)
                  ->orWhere('manual_updated_at', '<', now()->subHours(24))
                  ->orWhereNull('manual_updated_at');
            });
        }
        
        $paidTagihan = $query->get();
        $paidCount = 0;
        
        foreach ($paidTagihan as $tagihan) {
            if ($force || $tagihan->shouldAutoSync()) {
                $oldStatus = $tagihan->status;
                $tagihan->update(['status' => Tagihan::STATUS_SUDAH_BAYAR]);
                
                $this->line("  Updated tagihan #{$tagihan->id}: {$oldStatus} → sudah_bayar");
                $paidCount++;
                
                Log::info('Command sync: Updated paid tagihan', [
                    'tagihan_id' => $tagihan->id,
                    'old_status' => $oldStatus,
                    'new_status' => Tagihan::STATUS_SUDAH_BAYAR
                ]);
            }
        }
        
        $this->info("Updated {$paidCount} paid tagihan to 'sudah_bayar'");
    }

    /**
     * Sync tagihan yang status sudah_bayar tapi tidak ada pembayaran valid
     */
    private function syncUnpaidTagihan($force = false)
    {
        $this->info('Syncing unpaid tagihan...');
        
        $query = Tagihan::where('status', Tagihan::STATUS_SUDAH_BAYAR)
            ->where(function($q) {
                $q->whereDoesntHave('pembayaran')
                  ->orWhereHas('pembayaran', function($subQ) {
                      $subQ->where(function($payQ) {
                          $payQ->where('status', '!=', 'lunas')
                               ->orWhere('is_verified', '!=', true);
                      });
                  });
            });
            
        if (!$force) {
            $query->where(function($q) {
                $q->where('is_manual_status', false)
                  ->orWhere('manual_updated_at', '<', now()->subHours(24))
                  ->orWhereNull('manual_updated_at');
            });
        }
        
        $unpaidTagihan = $query->get();
        $unpaidCount = 0;
        
        foreach ($unpaidTagihan as $tagihan) {
            if ($force || $tagihan->shouldAutoSync()) {
                $oldStatus = $tagihan->status;
                
                // Tentukan status baru berdasarkan jatuh tempo
                $newStatus = Tagihan::STATUS_BELUM_BAYAR;
                if ($tagihan->tanggal_jatuh_tempo && \Carbon\Carbon::parse($tagihan->tanggal_jatuh_tempo)->isPast()) {
                    $newStatus = Tagihan::STATUS_TERLAMBAT;
                }
                
                $tagihan->update(['status' => $newStatus]);
                
                $this->line("  Updated tagihan #{$tagihan->id}: {$oldStatus} → {$newStatus}");
                $unpaidCount++;
                
                Log::info('Command sync: Updated unpaid tagihan', [
                    'tagihan_id' => $tagihan->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'has_payment' => $tagihan->pembayaran ? true : false,
                    'payment_status' => $tagihan->pembayaran->status ?? null
                ]);
            }
        }
        
        $this->info("Updated {$unpaidCount} unpaid tagihan from 'sudah_bayar'");
    }

    /**
     * Sync tagihan yang lewat jatuh tempo
     */
    private function syncOverdueTagihan($force = false)
    {
        $this->info('Syncing overdue tagihan...');
        
        $query = Tagihan::where('status', Tagihan::STATUS_BELUM_BAYAR)
            ->where('tanggal_jatuh_tempo', '<', now())
            ->whereNotNull('tanggal_jatuh_tempo');
            
        if (!$force) {
            $query->where(function($q) {
                $q->where('is_manual_status', false)
                  ->orWhere('manual_updated_at', '<', now()->subHours(24))
                  ->orWhereNull('manual_updated_at');
            });
        }
        
        $overdueTagihan = $query->get();
        $overdueCount = 0;
        
        foreach ($overdueTagihan as $tagihan) {
            if ($force || $tagihan->shouldAutoSync()) {
                $oldStatus = $tagihan->status;
                $tagihan->update(['status' => Tagihan::STATUS_TERLAMBAT]);
                
                $this->line("  Updated tagihan #{$tagihan->id}: {$oldStatus} → terlambat");
                $overdueCount++;
                
                Log::info('Command sync: Updated overdue tagihan', [
                    'tagihan_id' => $tagihan->id,
                    'old_status' => $oldStatus,
                    'new_status' => Tagihan::STATUS_TERLAMBAT,
                    'due_date' => $tagihan->tanggal_jatuh_tempo
                ]);
            }
        }
        
        $this->info("Updated {$overdueCount} overdue tagihan to 'terlambat'");
    }
}