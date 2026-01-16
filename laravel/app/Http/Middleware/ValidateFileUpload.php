<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ValidateFileUpload
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if request has files (support both 'file' and 'excel_file' field names)
        $fileField = $request->hasFile('excel_file') ? 'excel_file' : 'file';
        
        if ($request->hasFile($fileField)) {
            $file = $request->file($fileField);
            $maxSize = config('uploads.question_import.max_file_size', 10485760); // 10MB default
            
            // Validate file size
            if ($file->getSize() > $maxSize) {
                $maxSizeMB = round($maxSize / 1048576, 1);
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => "O arquivo excede o tamanho máximo permitido de {$maxSizeMB}MB.",
                        'max_size' => $maxSize,
                        'file_size' => $file->getSize()
                    ], 413);
                }
                
                return redirect()->back()
                    ->withErrors([$fileField => "O arquivo excede o tamanho máximo permitido de {$maxSizeMB}MB."])
                    ->withInput();
            }
            
            // Validate file extension for question imports
            if ($request->is('admin/import/*')) {
                $allowedExtensions = config('uploads.question_import.allowed_extensions', ['xls', 'xlsx']);
                $extension = strtolower($file->getClientOriginalExtension());
                
                if (!in_array($extension, $allowedExtensions)) {
                    $allowedList = implode(', ', $allowedExtensions);
                    
                    if ($request->expectsJson()) {
                        return response()->json([
                            'error' => "Formato de arquivo não suportado. Formatos permitidos: {$allowedList}",
                            'allowed_extensions' => $allowedExtensions,
                            'file_extension' => $extension
                        ], 422);
                    }
                    
                    return redirect()->back()
                        ->withErrors([$fileField => "Formato de arquivo não suportado. Formatos permitidos: {$allowedList}"])
                        ->withInput();
                }
                
                // Validate MIME type
                $allowedMimeTypes = config('uploads.question_import.allowed_mime_types', []);
                $mimeType = $file->getMimeType();
                
                if (!empty($allowedMimeTypes) && !in_array($mimeType, $allowedMimeTypes)) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'error' => 'Tipo de arquivo não suportado.',
                            'allowed_mime_types' => $allowedMimeTypes,
                            'file_mime_type' => $mimeType
                        ], 422);
                    }
                    
                    return redirect()->back()
                        ->withErrors([$fileField => 'Tipo de arquivo não suportado.'])
                        ->withInput();
                }
            }
        }

        return $next($request);
    }
}