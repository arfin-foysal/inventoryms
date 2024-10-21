<?php

namespace App\Imports;

use App\Models\ProductSerial;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductSerialImport implements ToModel, WithHeadingRow
{
    /**
     * @param  array  $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    protected $productId;

    public function __construct($productId)
    {
        $this->productId = $productId;
    }

    public function model(array $row)
    {
        // Validate if the required fields exist
        if (empty($row['serial_number'])) {
            return null; // Skip the row if required fields are empty
        }

        return new ProductSerial([
            'product_id' => $this->productId, // Pass the product_id from the constructor
            'serial_number' => $row['serial_number'],
            'description' => $row['description'],
        ]);
    }
}
