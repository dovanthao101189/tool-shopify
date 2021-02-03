<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        return view("products.create");
    }

    public function create(Request $request)
    {
        $link = $request->input('link', '');
        $link = trim($link);
        if ($link !== '') {
            $link = str_replace('.json', '', $link) . '.json';
            $product = $this->getProductByLink($link);
            if ($product['success']) {
                $results = $this->addProductShopify($product['data']);
                if ($results['success']) {
                    return view("products.create", ['success' => true]);
                }
            }
        }

        return view("products.create");
    }

    private function getProductByLink($link)
    {
        $client = new Client();
        $request = $client->get($link);
        if ($request->getStatusCode() === 200) {
            return [
                'success' => true,
                'data' => $request->getBody()->getContents()
            ];
        }

        return [
            'success' => false,
            'data' => []
        ];
    }

    private function addProductShopify($product)
    {
        $data = [];
        $product = json_decode($product, true);
        foreach ($product['product'] as $k=>$v) {
           if ($k === 'variants') {
                foreach ($product['product'][$k] as $sk=>$sv) {
                    $data[$k][$sk] = $sv;
                    unset($data[$k][$sk]['image_id']);
                }
            } else {
                $data[$k] = $v;
            }
        }
        $apiKey = config('app.api_key');
        $secretKey = config('app.secret_key');
        $store = config('app.shopify_store');
        $client = new Client();
        $endpoint = "https://${apiKey}:${secretKey}@${store}.myshopify.com/admin/api/2021-01/products.json";
        $request = $client->post($endpoint, [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => json_encode(['product' => $data])
        ]);

        if ($request->getStatusCode() === 200 || $request->getStatusCode() === 201) {
            return [
                'success' => true,
                'data' => $request->getBody()->getContents()
            ];
        }

        return [
            'success' => false,
            'data' => []
        ];
    }
}
