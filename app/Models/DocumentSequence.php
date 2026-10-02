<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DocumentSequence extends Model
{
    protected $guarded = ['id'];

    /**
     * Atomically get the next sequential number with row locking to prevent race conditions.
     */
    public static function getNextSequence(string $documentType, string $fyCode, string $prefix, int $padLength = 4): string
    {
        return DB::transaction(function () use ($documentType, $fyCode, $prefix, $padLength) {
            $seq = static::where('document_type', $documentType)
                ->where('fy_code', $fyCode)
                ->where('prefix', $prefix)
                ->lockForUpdate()
                ->first();

            if (!$seq) {
                $seq = static::create([
                    'document_type' => $documentType,
                    'fy_code' => $fyCode,
                    'prefix' => $prefix,
                    'current_number' => 0,
                ]);
                $seq = static::where('id', $seq->id)->lockForUpdate()->first();
            }

            $seq->current_number += 1;
            $seq->save();

            $num = str_pad((string)$seq->current_number, $padLength, '0', STR_PAD_LEFT);
            return "{$prefix}/{$fyCode}/{$num}";
        });
    }
}
