<?php

namespace App\Livewire\Customer;

use App\Models\Table;
use App\Models\TableSession as SessionModel;
use App\Models\SessionCustomer;
use App\Models\Setting;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.customer')]
#[Title('Check-in Meja')]
class TableSession extends Component
{
    public $qrToken;
    public $table;
    public $displayName = '';

    public $existingNames = [];

    public $latitude;
    public $longitude;
    public $locationError;

    public function mount($qr_token)
    {
        $this->qrToken = $qr_token;
        $this->table = Table::where('qr_token', $qr_token)->firstOrFail();

        $activeSession = SessionModel::where('table_id', $this->table->id)->where('status', 'active')->first();
        if ($activeSession) {
            $this->existingNames = $activeSession->customers()->pluck('display_name')->toArray();
        }

        if (session()->has('customer_id')) {
            $customer = SessionCustomer::find(session('customer_id'));
            if ($customer && $customer->tableSession->table_id === $this->table->id && $customer->tableSession->status === 'active') {
                return redirect()->route('customer.menu');
            }
        }
    }

    public function joinTable()
    {
        $this->validate([
            'displayName' => 'required|string|min:2|max:20',
            'latitude' => 'required',
            'longitude' => 'required',
        ], [
            'displayName.required' => 'Tolong isi nama panggilanmu agar pesanan tidak tertukar.',
            'latitude.required' => 'Mohon izinkan akses lokasi (GPS) untuk memesan.',
        ]);

        $setting = Setting::first();

        if ($setting && $setting->latitude && $setting->longitude) {
            $cafeLat = (float) $setting->latitude;
            $cafeLng = (float) $setting->longitude;

            $maxDistance = (int) ($setting->max_distance ?? 150);
            $earthRadius = 6371000;

            $latFrom = deg2rad($cafeLat);
            $lonFrom = deg2rad($cafeLng);
            $latTo = deg2rad((float) $this->latitude);
            $lonTo = deg2rad((float) $this->longitude);

            $latDelta = $latTo - $latFrom;
            $lonDelta = $lonTo - $lonFrom;

            $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

            $distance = $angle * $earthRadius;

            if ($distance > $maxDistance) {
                $jarakDibulatkan = round($distance);
                $this->addError('latitude', "Akses Ditolak! Anda berada {$jarakDibulatkan} meter dari kafe (Batas: {$maxDistance}m). Silakan datang ke area kafe untuk memesan.");
                $this->dispatch('location-rejected');
                return;
            }
        }


        $activeSession = SessionModel::firstOrCreate(
            ['table_id' => $this->table->id, 'status' => 'active']
        );

        $this->table->update(['status' => 'occupied']);

        $customer = SessionCustomer::where('table_session_id', $activeSession->id)
            ->whereRaw('LOWER(display_name) = ?', [strtolower($this->displayName)])
            ->first();

        if (!$customer) {
            $isHost = $activeSession->customers()->count() === 0;
            $customer = SessionCustomer::create([
                'table_session_id' => $activeSession->id,
                'display_name' => $this->displayName,
                'device_identifier' => request()->ip() . '|' . request()->userAgent(),
                'is_host' => $isHost
            ]);
        } else {
            $customer->update([
                'device_identifier' => request()->ip() . '|' . request()->userAgent()
            ]);
        }

        session(['customer_id' => $customer->id]);
        session()->forget('cart');

        event(new \App\Events\CustomerJoinedTable($activeSession->id));

        return redirect()->route('customer.menu');
    }

    public function render()
    {
        return view('livewire.customer.table-session');
    }
}
