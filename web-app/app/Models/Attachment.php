<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    /**
     * الحقول المسموح بتعبئتها (Mass Assignment)
     * orphan_id: لربط الملف باليتيم
     * document_type: نوع المستند (شهادة ميلاد، تقرير طبي...)
     * file_path: مسار تخزين الملف في السيرفر
     */
    protected $fillable = [
        'orphan_id',
        'document_type',
        'file_path',
    ];

    protected $casts = [
        'file_path' => 'string',
    ];

    public static function safeFilePath(mixed $value): string
    {
        if (is_array($value)) {
            return isset($value[0]) && is_string($value[0]) ? $value[0] : '';
        }
        if (is_string($value)) {
            $trimmed = trim($value);
            if ($trimmed !== '' && $trimmed[0] === '[') {
                $decoded = json_decode($trimmed, true);
                if (is_array($decoded)) {
                    return isset($decoded[0]) && is_string($decoded[0]) ? $decoded[0] : '';
                }
            }
            return $value;
        }
        return '';
    }

    public static function normalizeFile(mixed $value): ?string
    {
        $path = static::safeFilePath($value);
        return $path ?: null;
    }

    public static function normalizeDocument(mixed $value): ?string
    {
        return static::normalizeFile($value);
    }

    public static function getFileUrl(?string $path): ?string
    {
        return $path ? \Illuminate\Support\Facades\Storage::disk('public')->url($path) : null;
    }

    public static function canViewInline(?string $path): bool
    {
        if (!$path) return false;
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf']);
    }

    public function getFilePathAttribute($value): string
    {
        return static::safeFilePath($value);
    }

    /**
     * العلاقة العكسية: المرفق ينتمي ليتيم واحد
     */
    public function orphan()
    {
        return $this->belongsTo(Orphan::class);
    }
}