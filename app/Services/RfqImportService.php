<?php

namespace App\Services;

use App\Models\Buyer;
use App\Models\Product;
use App\Models\Rfq;
use App\Models\RfqItem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RfqImportService
{
    /**
     * Import RFQ items from CSV file.
     */
    public function importFromCsv(UploadedFile $file, Buyer $buyer, array $rfqData = []): array
    {
        $results = [
            'success' => false,
            'rfq' => null,
            'total_rows' => 0,
            'imported' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        try {
            // Validate CSV format
            if (!$this->validateCsvFormat($file)) {
                $results['errors'][] = 'تنسيق ملف CSV غير صحيح';
                return $results;
            }

            // Parse CSV
            $csvData = $this->parseCsv($file);
            $results['total_rows'] = count($csvData);

            if (empty($csvData)) {
                $results['errors'][] = 'الملف فارغ أو لا يحتوي على بيانات صالحة';
                return $results;
            }

            DB::beginTransaction();

            // Create RFQ
            $rfq = Rfq::create(array_merge([
                'buyer_id' => $buyer->id,
                'created_by' => Auth::id(),
                'title' => $rfqData['title'] ?? 'طلب عرض سعر مستورد',
                'description' => $rfqData['description'] ?? 'تم استيراد هذا الطلب من ملف CSV',
                'deadline' => $rfqData['deadline'] ?? now()->addDays(7),
                'is_public' => $rfqData['is_public'] ?? true,
                'status' => 'draft',
                'reference_code' => ReferenceCodeService::generateUnique(
                    ReferenceCodeService::PREFIX_RFQ,
                    Rfq::class
                ),
            ], $rfqData));

            // Import items
            foreach ($csvData as $rowIndex => $row) {
                try {
                    $item = $this->importRow($row, $rfq);
                    if ($item) {
                        $results['imported']++;
                    } else {
                        $results['skipped']++;
                        $results['errors'][] = "الصف {$rowIndex}: لم يتم العثور على المنتج أو البيانات غير صحيحة";
                    }
                } catch (\Exception $e) {
                    $results['skipped']++;
                    $results['errors'][] = "الصف {$rowIndex}: {$e->getMessage()}";
                }
            }

            DB::commit();

            $results['success'] = true;
            $results['rfq'] = $rfq;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CSV import error', [
                'buyer_id' => $buyer->id,
                'error' => $e->getMessage(),
            ]);
            $results['errors'][] = 'خطأ في الاستيراد: ' . $e->getMessage();
        }

        return $results;
    }

    /**
     * Validate CSV file format.
     */
    public function validateCsvFormat(UploadedFile $file): bool
    {
        // Check file extension
        if (!in_array($file->getClientOriginalExtension(), ['csv', 'txt'])) {
            return false;
        }

        // Check MIME type
        $allowedMimes = ['text/csv', 'text/plain', 'application/csv', 'text/comma-separated-values'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            return false;
        }

        // Check file is readable
        if (!is_readable($file->getRealPath())) {
            return false;
        }

        // Check headers
        $handle = fopen($file->getRealPath(), 'r');
        $headers = fgetcsv($handle);
        fclose($handle);

        $requiredHeaders = ['product_name', 'quantity'];
        foreach ($requiredHeaders as $required) {
            if (!in_array($required, $headers)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Parse CSV file.
     */
    private function parseCsv(UploadedFile $file): array
    {
        $data = [];
        $handle = fopen($file->getRealPath(), 'r');

        // Get headers
        $headers = fgetcsv($handle);

        // Parse rows
        while (($row = fgetcsv($handle)) !== false) {
            if (empty(array_filter($row))) {
                continue; // Skip empty rows
            }

            $data[] = array_combine($headers, $row);
        }

        fclose($handle);

        return $data;
    }

    /**
     * Import single row.
     */
    private function importRow(array $row, Rfq $rfq): ?RfqItem
    {
        // Find product by name or SKU
        $product = Product::where('name', 'like', "%{$row['product_name']}%")
            ->where('is_active', true)
            ->where('review_status', 'approved')
            ->first();

        if (!$product && isset($row['sku'])) {
            $product = Product::where('sku', $row['sku'])
                ->where('is_active', true)
                ->where('review_status', 'approved')
                ->first();
        }

        if (!$product) {
            return null;
        }

        // Create RFQ item
        return RfqItem::create([
            'rfq_id' => $rfq->id,
            'product_id' => $product->id,
            'item_name' => $product->name,
            'specifications' => $row['specifications'] ?? null,
            'quantity' => (int) $row['quantity'],
            'unit' => $row['unit'] ?? 'وحدة',
        ]);
    }

    /**
     * Generate sample CSV template.
     */
    public function generateSampleCsv(): string
    {
        $headers = ['product_name', 'sku', 'quantity', 'unit', 'specifications'];
        $sampleData = [
            ['جهاز قياس ضغط الدم', 'BP-001', '10', 'جهاز', 'رقمي، ذراع'],
            ['قفازات طبية', 'GLOVE-001', '100', 'علبة', 'مقاس كبير، لاتكس'],
            ['كمامات طبية', 'MASK-001', '500', 'علبة', 'ثلاث طبقات'],
        ];

        $csv = implode(',', $headers) . "\n";
        foreach ($sampleData as $row) {
            $csv .= implode(',', array_map(function($cell) {
                return '"' . str_replace('"', '""', $cell) . '"';
            }, $row)) . "\n";
        }

        return $csv;
    }

    /**
     * Download sample CSV as response.
     */
    public function downloadSampleCsv()
    {
        $csv = $this->generateSampleCsv();
        $filename = 'rfq_import_template_' . date('Y-m-d') . '.csv';

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
