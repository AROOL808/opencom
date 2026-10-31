<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RajaOngkirService
{
    protected $apiKey;
    protected $baseUrl;
    
    // Kredensial khusus Payment
    protected $paymentApiKey;
    protected $paymentUrl;

    public function __construct()
    {
        $this->apiKey = env('RAJAONGKIR_API_KEY');
        $this->baseUrl = env('RAJAONGKIR_BASE_URL', 'https://rajaongkir.komerce.id/api/v1');
        
        $this->paymentApiKey = env('KOMERCE_PAYMENT_API_KEY');
        $this->paymentUrl = env('KOMERCE_PAYMENT_URL', 'https://api-sandbox.collaborator.komerce.id');
    }

    // 1. Cek Ongkos Kirim
    public function getCost($originCityId, $destinationCityId, $weightInGrams, $courier)
    {
        $response = Http::withHeaders(['key' => $this->apiKey])->post($this->baseUrl . '/cost', [
            'origin' => $originCityId,
            'destination' => $destinationCityId,
            'weight' => $weightInGrams,
            'courier' => strtolower($courier)
        ]);
        return $response->json();
    }

    // 3. Cek Lacak Resi
    public function trackWaybill($waybillNumber, $courier)
    {
        $response = Http::withHeaders(['key' => $this->apiKey])->post($this->baseUrl . '/waybill', [
            'waybill' => $waybillNumber,
            'courier' => strtolower($courier)
        ]);
        return $response->json();
    }

   public function getLocationIdByAddress($address)
    {
        // Ubah spasi menjadi delimiter "+" sesuai instruksi
        $searchKeyword = str_replace(' ', '+', $address);

        $response = Http::withHeaders(['key' => $this->apiKey])
            ->get($this->baseUrl . '/destination/domestic-destination', [
                'search' => $searchKeyword,
                'limit' => 5
            ]);
            
        $data = $response->json();

        // Ambil ID dari hasil pertama (index 0) jika datanya ada
        if (isset($data['data']) && count($data['data']) > 0) {
            return $data['data'][0]['id'];
        }

        return null; // Kembalikan null jika alamat tidak ditemukan
    }

    //  Hitung Ongkos Kirim (URL-Encoded)
    public function calculateCost($originId, $destinationId, $weightInGrams, $courier)
    {
        $response = Http::asForm()
            ->withHeaders(['key' => $this->apiKey])
            ->post($this->baseUrl . '/calculate/domestic-cost', [
                'origin' => $originId,
                'destination' => $destinationId,
                'weight' => $weightInGrams,
                'courier' => strtolower($courier),
                'price' => 'lowest'
            ]);
            
        return $response->json();
    }
    
    public function createPayment($orderAnotherId, $amount, $customer, $items, $paymentType = 'va', $channelCode = 'BCA')
    {
        $response = Http::withHeaders([
            'x-api-key' => $this->paymentApiKey,
            'Content-Type' => 'application/json'
        ])->post($this->paymentUrl . '/user/api/v1/user/payment/create', [
            'order_id' => $orderAnotherId, // Menggunakan order_another_id yang unique
            'payment_type' => $paymentType, // contoh: 'va' atau 'qris'
            'channel_code' => $channelCode, // contoh: 'BCA' atau '' (kosong untuk qris)
            'amount' => (int) $amount,
            'customer' => $customer,
            'items' => $items,
            'expiry_duration' => 3600,
            'callback_url' => url('/api/payment/callback'),
            'callback_api_key' => '123456789'
        ]);

        return $response->json();
    }
}