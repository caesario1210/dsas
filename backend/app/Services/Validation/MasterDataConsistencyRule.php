<?php

namespace App\Services\Validation;

use App\Models\Dealer;

class MasterDataConsistencyRule
{
    public function validate(array $row): ?array
    {
        $data = $row['data'];
        
        $dealerCode = strtoupper(trim($data['dealer_code'] ?? ''));
        $dealerName = trim($data['dealer_name'] ?? '');
        $productCode = strtoupper(trim($data['product_code'] ?? ''));
        $productName = trim($data['product_name'] ?? '');

        if (empty($dealerCode)) {
            return [
                'rule' => 'master_data_consistency',
                'field' => 'dealer_code',
                'message' => 'Dealer code is required',
                'action' => 'rejected',
            ];
        }

        if (empty($dealerName)) {
            return [
                'rule' => 'master_data_consistency',
                'field' => 'dealer_name',
                'message' => 'Dealer name is required',
                'action' => 'rejected',
            ];
        }

        $existingDealer = Dealer::where('dealer_code', $dealerCode)->first();

        if ($existingDealer && $existingDealer->dealer_name !== $dealerName) {
            return [
                'rule' => 'master_data_consistency',
                'field' => 'dealer_name',
                'dealer_code' => $dealerCode,
                'existing_name' => $existingDealer->dealer_name,
                'csv_name' => $dealerName,
                'message' => "Dealer name conflict (Q3-C): Code '{$dealerCode}' exists as '{$existingDealer->dealer_name}', but CSV has '{$dealerName}'",
                'action' => 'rejected',
            ];
        }

        if (empty($productCode)) {
            return [
                'rule' => 'master_data_consistency',
                'field' => 'product_code',
                'message' => 'Product code is required',
                'action' => 'rejected',
            ];
        }

        if (empty($productName)) {
            return [
                'rule' => 'master_data_consistency',
                'field' => 'product_name',
                'message' => 'Product name is required',
                'action' => 'rejected',
            ];
        }

        return null;
    }

    public function getDescription(): string
    {
        return 'Q3-C: Dealer/Product master data must be consistent (no name conflicts for same code)';
    }
}
