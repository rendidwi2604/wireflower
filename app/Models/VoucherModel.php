<?php

namespace App\Models;

use App\Core\Model;

class VoucherModel extends Model
{
    // Tipe voucher yang valid
    public const TYPES = [
        'free_shipping' => 'Gratis Ongkir',
        'percent'       => 'Diskon Persen (%)',
        'fixed'         => 'Potongan Harga (Rp)',
    ];

    // ---- Public: validasi kode voucher ----

    /**
     * Cari voucher aktif berdasarkan kode.
     * Return array voucher atau null jika tidak valid / sudah habis / expired.
     */
    public function findValid(string $code): ?array
    {
        $v = $this->fetchOne(
            "SELECT * FROM vouchers
             WHERE UPPER(code) = UPPER($1)
               AND is_active = TRUE
               AND (valid_from IS NULL OR valid_from <= CURRENT_DATE)
               AND (valid_until IS NULL OR valid_until >= CURRENT_DATE)
               AND (usage_limit IS NULL OR used_count < usage_limit)",
            [trim($code)]
        );
        return $v ?: null;
    }

    /**
     * Hitung nilai diskon berdasarkan voucher + subtotal + ongkir.
     * Return array: [discount_amount, new_shipping, new_total]
     */
    public function calculate(array $voucher, float $subtotal, float $shipping): array
    {
        $discount = 0.0;
        $newShipping = $shipping;

        switch ($voucher['type']) {
            case 'free_shipping':
                $discount    = $shipping;
                $newShipping = 0.0;
                break;

            case 'percent':
                // discount_value = persentase (0–100)
                $pct      = min(100, max(0, (float) $voucher['discount_value']));
                $discount = round($subtotal * $pct / 100);
                // Batasi maksimum jika ada
                if (!empty($voucher['max_discount']) && $discount > (float) $voucher['max_discount']) {
                    $discount = (float) $voucher['max_discount'];
                }
                break;

            case 'fixed':
                $discount = min((float) $voucher['discount_value'], $subtotal);
                break;
        }

        $total = max(0, $subtotal + $newShipping - $discount);

        return [
            'discount_amount' => $discount,
            'shipping_cost'   => $newShipping,
            'total'           => $total,
        ];
    }

    /**
     * Tandai voucher sudah dipakai (increment used_count).
     */
    public function markUsed(int $voucherId): void
    {
        $this->run(
            'UPDATE vouchers SET used_count = used_count + 1 WHERE id = $1',
            [$voucherId]
        );
    }

    // ---- Admin CRUD ----

    public function all(): array
    {
        return $this->fetchAll(
            'SELECT * FROM vouchers ORDER BY created_at DESC'
        );
    }

    public function find(int $id): ?array
    {
        return $this->fetchOne('SELECT * FROM vouchers WHERE id = $1', [$id]);
    }

    public function create(array $data): int
    {
        return (int) $this->fetchValue(
            "INSERT INTO vouchers
               (code, type, discount_value, max_discount, min_order,
                usage_limit, valid_from, valid_until, is_active, description)
             VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10)
             RETURNING id",
            [
                strtoupper(trim($data['code'])),
                $data['type'],
                (float) ($data['discount_value'] ?? 0),
                $data['max_discount'] !== '' ? (float) $data['max_discount'] : null,
                $data['min_order']    !== '' ? (float) $data['min_order']    : null,
                $data['usage_limit']  !== '' ? (int)   $data['usage_limit']  : null,
                $data['valid_from']   !== '' ? $data['valid_from']   : null,
                $data['valid_until']  !== '' ? $data['valid_until']  : null,
                isset($data['is_active']) ? 'TRUE' : 'FALSE',
                trim($data['description'] ?? ''),
            ]
        );
    }

    public function update(int $id, array $data): void
    {
        $this->run(
            "UPDATE vouchers
             SET code           = $1,
                 type           = $2,
                 discount_value = $3,
                 max_discount   = $4,
                 min_order      = $5,
                 usage_limit    = $6,
                 valid_from     = $7,
                 valid_until    = $8,
                 is_active      = $9,
                 description    = $10
             WHERE id = $11",
            [
                strtoupper(trim($data['code'])),
                $data['type'],
                (float) ($data['discount_value'] ?? 0),
                $data['max_discount'] !== '' ? (float) $data['max_discount'] : null,
                $data['min_order']    !== '' ? (float) $data['min_order']    : null,
                $data['usage_limit']  !== '' ? (int)   $data['usage_limit']  : null,
                $data['valid_from']   !== '' ? $data['valid_from']   : null,
                $data['valid_until']  !== '' ? $data['valid_until']  : null,
                isset($data['is_active']) ? 'TRUE' : 'FALSE',
                trim($data['description'] ?? ''),
                $id,
            ]
        );
    }

    public function delete(int $id): void
    {
        $this->run('DELETE FROM vouchers WHERE id = $1', [$id]);
    }

    public function toggleActive(int $id): void
    {
        $this->run(
            'UPDATE vouchers SET is_active = NOT is_active WHERE id = $1',
            [$id]
        );
    }
}
