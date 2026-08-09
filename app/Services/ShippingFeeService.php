<?php

namespace App\Services;

use App\Models\Region;
use App\Models\ShippingArea;

/**
 * Service untuk menghitung ongkir berdasarkan jarak atau area.
 * 
 * Business Rules (dari spesifikasi):
 * - Jarak < 10km → Rp 10.000
 * - Jarak 10-14km → Rp 15.000
 * - Jarak > 14km → Fallback ke escalation contact (manual)
 */
class ShippingFeeService
{
    /**
     * Calculate shipping fee berdasarkan area name (kelurahan/kecamatan).
     * 
     * @param int $regionId
     * @param string $areaName
     * @return array ['fee' => int, 'distance' => float, 'needs_manual_check' => bool, 'message' => string]
     */
    public function calculateByArea(int $regionId, string $areaName): array
    {
        $area = ShippingArea::where('region_id', $regionId)
            ->where('area_name', 'LIKE', "%{$areaName}%")
            ->first();

        if ($area) {
            return [
                'fee' => $area->shipping_fee,
                'distance' => (float) $area->distance_km,
                'needs_manual_check' => $area->distance_km > 14,
                'message' => $area->distance_km > 14 
                    ? $this->getEscalationMessage($regionId)
                    : "Ongkir untuk area {$area->area_name}: Rp " . number_format($area->shipping_fee, 0, ',', '.'),
                'area' => $area,
            ];
        }

        // Area tidak ditemukan di database - fallback ke manual
        return [
            'fee' => null,
            'distance' => null,
            'needs_manual_check' => true,
            'message' => $this->getEscalationMessage($regionId, true),
            'area' => null,
        ];
    }

    /**
     * Calculate shipping fee berdasarkan jarak langsung (km).
     * 
     * @param float $distanceKm
     * @param int|null $regionId
     * @return array ['fee' => int|null, 'needs_manual_check' => bool, 'message' => string]
     */
    public function calculateByDistance(float $distanceKm, ?int $regionId = null): array
    {
        if ($distanceKm < 10) {
            return [
                'fee' => 10000,
                'distance' => $distanceKm,
                'needs_manual_check' => false,
                'message' => 'Ongkir: Rp 10.000 (jarak < 10km)',
            ];
        }

        if ($distanceKm >= 10 && $distanceKm <= 14) {
            return [
                'fee' => 15000,
                'distance' => $distanceKm,
                'needs_manual_check' => false,
                'message' => 'Ongkir: Rp 15.000 (jarak 10-14km)',
            ];
        }

        // > 14km → manual check
        return [
            'fee' => null,
            'distance' => $distanceKm,
            'needs_manual_check' => true,
            'message' => $this->getEscalationMessage($regionId),
        ];
    }

    /**
     * Get escalation message dengan kontak person dari region.
     */
    protected function getEscalationMessage(?int $regionId, bool $areaNotFound = false): string
    {
        $region = $regionId ? Region::find($regionId) : null;
        
        $contactName = $region?->escalation_contact_name ?? 'tim kami';
        $contactPhone = $region?->escalation_contact_phone ?? $region?->contact_phone ?? '';

        if ($areaNotFound) {
            $message = "Mohon maaf, area Anda belum terdaftar dalam sistem ongkir kami. ";
        } else {
            $message = "Untuk jarak >14km, ongkir perlu konfirmasi khusus. ";
        }

        $message .= "Silakan hubungi {$contactName}";
        
        if ($contactPhone) {
            $message .= " di {$contactPhone}";
        }

        $message .= " untuk perhitungan ongkir yang tepat.";

        return $message;
    }

    /**
     * Get semua area shipping untuk region tertentu.
     */
    public function getAreasByRegion(int $regionId): \Illuminate\Database\Eloquent\Collection
    {
        return ShippingArea::where('region_id', $regionId)
            ->orderBy('area_name')
            ->get();
    }
}
