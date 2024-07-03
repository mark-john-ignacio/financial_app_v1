<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\NavMenuFormsModel;

class NavMenuFormsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'form_code' => '1601C',
                'form_name' => 'Monthly Remittance Return of Income Taxes Withheld on Compensation',
                'filter' => 'Monthly',
                'cstatus' => 'Active',
            ],
            [
                'form_code' => '1601E',
                'form_name' => 'Monthly Remittance Return of Creditable Income Taxes Withheld (Expanded)',
                'filter' => 'Monthly',
                'cstatus' => 'Active',
            ],
            [
                'form_code' => '1601F',
                'form_name' => 'Monthly Remittance Return of Final Income Taxes Withheld',
                'filter' => 'Monthly',
                'cstatus' => 'Active',
            ],
            [
                'form_code' => '1602',
                'form_name' => 'Quarterly Remittance Return of Final Income Taxes Withheld on Interest Paid on Deposits and Yield on Deposit Substitutes/Trusts/Etc.',
                'filter' => 'Quarterly',
                'cstatus' => 'Active',
            ],
            [
                'form_code' => '1603',
                'form_name' => 'Annual Information Return of Creditable Income Taxes Withheld (Expanded)/Income Payments Exempt from Withholding Tax',
                'filter' => 'Annual',
                'cstatus' => 'Active',
            ],
            [
                'form_code' => '1604CF',
                'form_name' => 'Annual Information Return of Income Taxes Withheld on Compensation and Final Withholding Taxes',
                'filter' => 'Annual',
                'cstatus' => 'Active',
            ],
            [
                'form_code' => '1604E',
                'form_name' => 'Annual Information Return of Creditable Income Taxes Withheld (Expanded)',
                'filter' => 'Annual',
                'cstatus' => 'Active',
            ],
            [
                'form_code' => '1604F',
                'form_name' => 'Annual Information Return of Final Income Taxes Withheld',
                'filter' => 'Annual',
                'cstatus' => 'Active',
            ],
            [
                'form_code' => '1606',
                'form_name' => 'Withholding Tax Remittance Return for Onerous Transfer of Real Property Other Than Capital Asset (Including Taxable and
                Exempt)',
                'filter' => 'Annual',
                'cstatus' => 'Active',
            ]
        ];
        $model = new NavMenuFormsModel();
        $model -> insertBatch($data);

    }
}
