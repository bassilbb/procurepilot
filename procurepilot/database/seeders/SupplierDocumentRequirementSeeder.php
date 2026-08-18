<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\SupplierDocumentRequirement;
use Illuminate\Database\Seeder;

class SupplierDocumentRequirementSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['name' => 'Certificate of Incorporation (CAC)', 'description' => 'Certified true copy of the company registration certificate issued by the Corporate Affairs Commission.', 'is_required' => true],
            ['name' => 'CAC Form 2 & 7 (Particulars of Directors)', 'description' => 'Official CAC forms showing the particulars of the company directors.', 'is_required' => true],
            ['name' => 'Memorandum & Articles of Association', 'description' => 'Certified copies of the company\'s Memorandum and Articles of Association.', 'is_required' => true],
            ['name' => 'Tax Identification Number (TIN)', 'description' => 'Evidence of TIN registration with the Federal Inland Revenue Service.', 'is_required' => true],
            ['name' => 'Tax Clearance Certificate', 'description' => 'Current tax clearance certificate covering the last three years.', 'is_required' => true],
            ['name' => 'VAT Registration Certificate', 'description' => 'Proof of VAT registration with the Federal Inland Revenue Service.', 'is_required' => true],
            ['name' => 'Audited Financial Statements', 'description' => 'Audited accounts for the last two financial years, signed by a certified accountant.', 'is_required' => true],
            ['name' => 'PENCOM Compliance Certificate', 'description' => 'Evidence of compliance with the National Pension Commission (contributory pension).', 'is_required' => true],
            ['name' => 'ITF Compliance Certificate', 'description' => 'Industrial Training Fund (ITF) training contribution compliance certificate.', 'is_required' => true],
            ['name' => 'NSITF Compliance Certificate', 'description' => 'Nigeria Social Insurance Trust Fund (employees\' compensation) compliance certificate.', 'is_required' => true],
            ['name' => 'Bank Reference Letter', 'description' => 'Reference letter from the company\'s bank confirming account relationship and conduct.', 'is_required' => true],
            ['name' => 'References & Past Performance', 'description' => 'Letters of reference or proof of past contract performance from other clients.', 'is_required' => false],
            ['name' => 'Evidence of Relevant Licences / Permits', 'description' => 'Copies of any sector-specific licences, permits or certifications held by the company.', 'is_required' => false],
            ['name' => 'Insurance Cover (Public Liability)', 'description' => 'Certificate of insurance showing adequate public liability or professional indemnity cover.', 'is_required' => false],
        ];

        foreach (Organization::all() as $org) {
            $existing = SupplierDocumentRequirement::where('organization_id', $org->id)
                ->pluck('name')
                ->map(fn ($n) => strtolower(trim($n)))
                ->all();

            $sort = (int) SupplierDocumentRequirement::where('organization_id', $org->id)->max('sort_order') + 1;

            foreach ($defaults as $req) {
                $key = strtolower(trim($req['name']));

                if (in_array($key, $existing, true)) {
                    continue;
                }

                SupplierDocumentRequirement::create([
                    'organization_id' => $org->id,
                    'name'            => $req['name'],
                    'description'     => $req['description'],
                    'is_required'     => $req['is_required'],
                    'is_active'       => true,
                    'sort_order'      => $sort++,
                ]);
            }

            $this->command->info("Verified supplier document requirements for {$org->name}.");
        }
    }
}
