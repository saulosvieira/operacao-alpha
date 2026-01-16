<?php

namespace App\Domain\Import\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class FileValidationService
{
    /**
     * Maximum file size in bytes (10MB).
     */
    private const MAX_FILE_SIZE = 10 * 1024 * 1024;

    /**
     * Allowed MIME types for Excel files.
     */
    private const ALLOWED_MIME_TYPES = [
        'application/vnd.ms-excel', // .xls
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
        'application/excel',
        'application/x-excel',
        'application/x-msexcel',
    ];

    /**
     * Allowed file extensions.
     */
    private const ALLOWED_EXTENSIONS = ['xls', 'xlsx'];

    /**
     * Excel file signatures for format detection.
     */
    private const EXCEL_SIGNATURES = [
        // Excel 97-2003 (.xls) - OLE2 signature
        'xls' => [
            "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1", // OLE2 signature
            "\x09\x08\x04\x00\x00\x00\x00\x00", // Alternative XLS signature
        ],
        // Excel 2007+ (.xlsx) - ZIP signature (XLSX is a ZIP file)
        'xlsx' => [
            "\x50\x4B\x03\x04", // ZIP signature
            "\x50\x4B\x05\x06", // ZIP empty archive
            "\x50\x4B\x07\x08", // ZIP spanned archive
        ],
    ];

    /**
     * Validate an uploaded Excel file.
     */
    public function validateFile(UploadedFile $file): array
    {
        $errors = [];

        // Check if file was uploaded successfully
        if (!$file->isValid()) {
            $errors[] = 'Falha no upload do arquivo. Tente novamente.';
            return ['valid' => false, 'errors' => $errors];
        }

        // Validate file size
        if (!$this->validateFileSize($file)) {
            $errors[] = sprintf(
                'O arquivo é muito grande. Tamanho máximo permitido: %s MB.',
                number_format(self::MAX_FILE_SIZE / (1024 * 1024), 1)
            );
        }

        // Validate file extension
        if (!$this->validateFileExtension($file)) {
            $errors[] = 'Formato de arquivo não suportado. Use apenas arquivos .xls ou .xlsx.';
        }

        // Validate MIME type
        if (!$this->validateMimeType($file)) {
            $errors[] = 'Tipo de arquivo inválido. Certifique-se de que é um arquivo Excel válido.';
        }

        // Validate file signature (detect corruption)
        if (!$this->validateFileSignature($file)) {
            $errors[] = 'Arquivo corrompido ou formato inválido. Verifique se o arquivo não está danificado.';
        }

        // Additional Excel-specific validation
        if (empty($errors)) {
            $excelValidation = $this->validateExcelStructure($file);
            if (!$excelValidation['valid']) {
                $errors = array_merge($errors, $excelValidation['errors']);
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'file_info' => $this->getFileInfo($file),
        ];
    }

    /**
     * Validate file size.
     */
    private function validateFileSize(UploadedFile $file): bool
    {
        return $file->getSize() <= self::MAX_FILE_SIZE;
    }

    /**
     * Validate file extension.
     */
    private function validateFileExtension(UploadedFile $file): bool
    {
        $extension = strtolower($file->getClientOriginalExtension());
        return in_array($extension, self::ALLOWED_EXTENSIONS, true);
    }

    /**
     * Validate MIME type.
     */
    private function validateMimeType(UploadedFile $file): bool
    {
        $mimeType = $file->getMimeType();
        return in_array($mimeType, self::ALLOWED_MIME_TYPES, true);
    }

    /**
     * Validate file signature to detect corruption and verify format.
     */
    private function validateFileSignature(UploadedFile $file): bool
    {
        try {
            $handle = fopen($file->getPathname(), 'rb');
            if (!$handle) {
                return false;
            }

            // Read first 8 bytes for signature detection
            $signature = fread($handle, 8);
            fclose($handle);

            if ($signature === false || strlen($signature) < 4) {
                return false;
            }

            // Check against known Excel signatures
            foreach (self::EXCEL_SIGNATURES as $format => $signatures) {
                foreach ($signatures as $expectedSignature) {
                    if (strpos($signature, $expectedSignature) === 0) {
                        return true;
                    }
                }
            }

            return false;
        } catch (\Exception $e) {
            Log::warning('File signature validation failed', [
                'file' => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Validate Excel file structure.
     */
    private function validateExcelStructure(UploadedFile $file): array
    {
        $errors = [];

        try {
            // Try to open the file with a simple check
            $extension = strtolower($file->getClientOriginalExtension());
            
            if ($extension === 'xlsx') {
                // Check if ZipArchive is available
                if (!class_exists('ZipArchive')) {
                    // Fallback: basic file size and extension validation
                    $fileSize = $file->getSize();
                    if ($fileSize < 1024) { // XLSX files are typically at least 1KB
                        $errors[] = 'Arquivo XLSX muito pequeno ou corrompido.';
                    }
                    return [
                        'valid' => empty($errors),
                        'errors' => $errors,
                    ];
                }
                
                // For XLSX files, check if it's a valid ZIP file
                $zip = new \ZipArchive();
                $result = $zip->open($file->getPathname());
                
                if ($result !== true) {
                    $errors[] = 'Arquivo XLSX corrompido ou inválido.';
                } else {
                    // Check for essential XLSX components
                    $hasWorkbook = $zip->locateName('xl/workbook.xml') !== false;
                    $hasSharedStrings = $zip->locateName('xl/sharedStrings.xml') !== false || 
                                       $zip->numFiles > 0; // sharedStrings.xml might not exist in simple files
                    
                    if (!$hasWorkbook) {
                        $errors[] = 'Estrutura do arquivo XLSX inválida.';
                    }
                    
                    $zip->close();
                }
            } elseif ($extension === 'xls') {
                // For XLS files, basic validation was done in signature check
                // Additional validation could be added here if needed
                $fileSize = $file->getSize();
                if ($fileSize < 512) { // XLS files are typically at least 512 bytes
                    $errors[] = 'Arquivo XLS muito pequeno ou corrompido.';
                }
            }
        } catch (\Exception $e) {
            Log::warning('Excel structure validation failed', [
                'file' => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
            ]);
            $errors[] = 'Não foi possível validar a estrutura do arquivo Excel.';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Get file information.
     */
    private function getFileInfo(UploadedFile $file): array
    {
        return [
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'size_formatted' => $this->formatFileSize($file->getSize()),
            'mime_type' => $file->getMimeType(),
            'extension' => strtolower($file->getClientOriginalExtension()),
            'is_valid_upload' => $file->isValid(),
        ];
    }

    /**
     * Format file size in human-readable format.
     */
    private function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;
        $size = $bytes;

        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        return number_format($size, $unitIndex > 0 ? 2 : 0) . ' ' . $units[$unitIndex];
    }

    /**
     * Detect Excel file format.
     */
    public function detectExcelFormat(UploadedFile $file): ?string
    {
        try {
            $handle = fopen($file->getPathname(), 'rb');
            if (!$handle) {
                return null;
            }

            $signature = fread($handle, 8);
            fclose($handle);

            if ($signature === false) {
                return null;
            }

            // Check for XLSX (ZIP) signature
            if (strpos($signature, "\x50\x4B") === 0) {
                return 'xlsx';
            }

            // Check for XLS (OLE2) signature
            if (strpos($signature, "\xD0\xCF\x11\xE0") === 0) {
                return 'xls';
            }

            return null;
        } catch (\Exception $e) {
            Log::warning('Excel format detection failed', [
                'file' => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Check if file is a supported Excel format.
     */
    public function isSupportedExcelFile(UploadedFile $file): bool
    {
        $validation = $this->validateFile($file);
        return $validation['valid'];
    }

    /**
     * Get maximum allowed file size.
     */
    public function getMaxFileSize(): int
    {
        return self::MAX_FILE_SIZE;
    }

    /**
     * Get maximum allowed file size formatted.
     */
    public function getMaxFileSizeFormatted(): string
    {
        return $this->formatFileSize(self::MAX_FILE_SIZE);
    }

    /**
     * Get allowed file extensions.
     */
    public function getAllowedExtensions(): array
    {
        return self::ALLOWED_EXTENSIONS;
    }

    /**
     * Get allowed MIME types.
     */
    public function getAllowedMimeTypes(): array
    {
        return self::ALLOWED_MIME_TYPES;
    }
}