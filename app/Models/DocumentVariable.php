<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\JsonResponse;

class DocumentVariable extends Model
{
    protected $fillable = ['document_id', 'file_name'];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function getVariables($id): JsonResponse
    {
        $document = Document::findOrFail($id);
        $variables = json_decode($document->variables, true) ?? [];
        $defaultVariables = [
            'user_role' => '',
            'user_document' => '',
            'product_brand' => '',
            'product_model' => '',
            'product_serial_number' => '',
            'product_processor' => '',
            'product_memory' => '',
            'product_disk' => '',
            'product_price' => '',
            'product_price_string' => '',
            'local' => '',
            'date' => date('Y-m-d H:i:s')
        ];
        $variables = array_merge($defaultVariables, $variables);
        return response()->json($variables);
    }
}
