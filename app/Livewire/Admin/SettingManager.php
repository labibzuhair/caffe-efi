<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
#[Title('Pengaturan Toko - CaffePOS')]
class SettingManager extends Component
{
    use WithFileUploads;

    public $store_name;
    public $tax_percentage;

    public $contact_phone;
    public $contact_email;
    public $address;
    public $footer_text;

    // Properti Geofencing
    public $latitude;
    public $longitude;
    public $max_distance;

    public $social_media = [
        'instagram' => '',
        'facebook' => '',
        'tiktok' => '',
        'youtube' => ''
    ];

    public $logo;
    public $newLogo;
    public $seo_thumbnail;
    public $newSeoThumbnail;

    public function mount()
    {
        $setting = Setting::firstOrCreate(
            ['id' => 1],
            ['store_name' => 'CaffePOS', 'tax_percentage' => 0]
        );

        $this->store_name = $setting->store_name;
        $this->tax_percentage = $setting->tax_percentage;
        $this->contact_phone = $setting->contact_phone;
        $this->contact_email = $setting->contact_email;
        $this->address = $setting->address;
        $this->footer_text = $setting->footer_text;

        // Load GPS & Toleransi Jarak
        $this->latitude = $setting->latitude;
        $this->longitude = $setting->longitude;
        $this->max_distance = $setting->max_distance ?? 150;

        $this->logo = $setting->logo;
        $this->seo_thumbnail = $setting->seo_thumbnail;

        if ($setting->social_media) {
            $this->social_media = array_merge($this->social_media, json_decode($setting->social_media, true));
        }
    }

    public function save()
    {
        $this->validate([
            'store_name' => 'required|string|max:100',
            'tax_percentage' => 'required|numeric|min:0|max:100',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:500',
            'footer_text' => 'nullable|string|max:255',

            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',

            'max_distance' => 'required|integer|min:10|max:5000',
            'newLogo' => 'nullable|image|max:2048',
            'newSeoThumbnail' => 'nullable|image|max:4096',
        ]);

        $setting = Setting::find(1);

        if ($this->newLogo) {
            if ($setting->logo)
                Storage::disk('public')->delete($setting->logo);
            $this->logo = $this->newLogo->store('settings', 'public');
        }

        if ($this->newSeoThumbnail) {
            if ($setting->seo_thumbnail)
                Storage::disk('public')->delete($setting->seo_thumbnail);
            $this->seo_thumbnail = $this->newSeoThumbnail->store('settings', 'public');
        }

        $setting->update([
            'store_name' => $this->store_name,
            'tax_percentage' => $this->tax_percentage,
            'contact_phone' => $this->contact_phone,
            'contact_email' => $this->contact_email,
            'address' => $this->address,
            'footer_text' => $this->footer_text,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'max_distance' => $this->max_distance,
            'social_media' => json_encode($this->social_media),
            'logo' => $this->logo,
            'seo_thumbnail' => $this->seo_thumbnail,
        ]);

        $this->newLogo = null;
        $this->newSeoThumbnail = null;

        session()->flash('message', 'Pengaturan toko berhasil diperbarui secara menyeluruh!');
    }

    public function render()
    {
        return view('livewire.admin.setting-manager');
    }
}
?>
