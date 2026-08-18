<?php

namespace App\Services;

use App\Models\Organization;
use Illuminate\Support\Str;

class ReferenceService
{
    public static function organizationSlug(Organization $org): string
    {
        return strtoupper(Str::slug($org->name, '_')) ?: 'ORG';
    }

    public static function tender(Organization $org): string
    {
        return 'RFQ-' . self::organizationSlug($org) . '-' . date('Y') . '-' . strtoupper(Str::random(4));
    }

    public static function bid(Organization $org): string
    {
        return 'BID-' . self::organizationSlug($org) . '-' . date('Y') . '-' . strtoupper(Str::random(4));
    }

    public static function contract(Organization $org): string
    {
        return 'CON-' . self::organizationSlug($org) . '-' . date('Y') . '-' . strtoupper(Str::random(4));
    }

    public static function po(Organization $org): string
    {
        return 'PO-' . self::organizationSlug($org) . '-' . date('Y') . '-' . strtoupper(Str::random(4));
    }

    public static function invoice(Organization $org): string
    {
        return 'INV-' . date('Y') . '-' . strtoupper(Str::random(6));
    }

    public static function payment(Organization $org): string
    {
        return 'PAY-' . date('Ymd') . '-' . strtoupper(Str::random(8));
    }

    public static function request(Organization $org): string
    {
        return 'PR-' . $org->id . '-' . date('Y') . '-' . str_pad((string) (\App\Models\ProcurementRequest::where('organization_id', $org->id)->count() + 1), 4, '0', STR_PAD_LEFT);
    }

    public static function supplierInvoice(Organization $org): string
    {
        return 'SINV-' . date('Y') . '-' . strtoupper(Str::random(6));
    }
}
