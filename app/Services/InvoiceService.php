<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Models\Cauhinh;
use App\Models\Hoadon;
use App\Models\Phong;

class InvoiceService
{
    private const DEFAULT_ELECTRICITY_RATE = 3500;
    private const DEFAULT_WATER_RATE = 15000;

    public function getPriceList(): array
    {
        return [
            'electricity_rate' => (int) $this->getSettingPrice('gia_dien', (string) self::DEFAULT_ELECTRICITY_RATE),
            'water_rate' => (int) $this->getSettingPrice('gia_nuoc', (string) self::DEFAULT_WATER_RATE),
        ];
    }

    public function processInvoice(array $data): array
    {
        if ((int) $data['chisodienmoi'] < (int) $data['chisodiencu']) {
            return $this->createErrorResult('Electricity reading must be >= previous reading.', ['chisodienmoi' => 'Electricity reading must be >= previous reading.']);
        }

        if ((int) $data['chisonuocmoi'] < (int) $data['chisonuoccu']) {
            return $this->createErrorResult('Water reading must be >= previous reading.', ['chisonuocmoi' => 'Water reading must be >= previous reading.']);
        }

        $phong = Phong::find((int) $data['phong_id']);
        if (! $phong) {
            return $this->createErrorResult('Room not found.');
        }

        $priceList = $this->getPriceList();
        $electricityCost = ((int) $data['chisodienmoi'] - (int) $data['chisodiencu']) * $priceList['electricity_rate'];
        $waterCost = ((int) $data['chisonuocmoi'] - (int) $data['chisonuoccu']) * $priceList['water_rate'];
        $roomRent = (int) $phong->giaphong;
        $serviceFee = 0;
        $totalAmount = $roomRent + $electricityCost + $waterCost + $serviceFee;

        $invoiceData = [
            'chisodiencu' => (int) $data['chisodiencu'],
            'chisodienmoi' => (int) $data['chisodienmoi'],
            'chisonuoccu' => (int) $data['chisonuoccu'],
            'chisonuocmoi' => (int) $data['chisonuocmoi'],
            'tienphong' => $roomRent,
            'tiendien' => $electricityCost,
            'tiennuoc' => $waterCost,
            'phidichvu' => $serviceFee,
            'tongtien' => $totalAmount,
            'ngayxuat' => now()->format('Y-m-d'),
        ];

        $existingInvoice = Hoadon::where('phong_id', (int) $data['phong_id'])->where('thang', (int) $data['thang'])->where('nam', (int) $data['nam'])->first();

        if ($existingInvoice) {
            $existingInvoice->update($invoiceData);
        } else {
            Hoadon::create(array_merge($invoiceData, ['phong_id' => (int) $data['phong_id'], 'thang' => (int) $data['thang'], 'nam' => (int) $data['nam'], 'trangthaithanhtoan' => InvoiceStatus::PENDING->value]));
        }

        return $this->createSuccessResult('Invoice processed successfully.');
    }

    private function getSettingPrice(string $key, string $defaultValue): string
    {
        $item = Cauhinh::where('ten', $key)->first();
        return $item ? $item->giatri : $defaultValue;
    }

    private function createErrorResult(string $message, array $errors = []): array
    {
        return ['success' => false, 'message' => $message, 'errors' => $errors];
    }

    private function createSuccessResult(string $message): array
    {
        return ['success' => true, 'message' => $message];
    }
}
