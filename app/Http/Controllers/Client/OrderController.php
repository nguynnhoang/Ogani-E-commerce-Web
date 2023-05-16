<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPaymentMethod;

class OrderController extends Controller
{
    public function checkout()
    {
        $cart = session()->get('cart') ?? [];

        return view('clients.pages.checkout', compact('cart'));
    }

    public function placeOrder(Request $request)
    {
        // $request->validate(['name' => required, 'address' => required]);


        $cart = session()->get('cart') ?? [];
        $arrayData = DB::transaction(function () use($request, $cart) {
            $order = Order::create([
                'user_id' => Auth::user()->id,
                'address' => $request->get('address'),
                'status' => 'pending',
                'payment_method' => $request->get('payment_method'),
            ]);

            $totalBalance = 0;
            foreach($cart as $productId => $item) {
                $orderItems = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'name' => $item['name'],
                ]);
                $totalBalance += $item['qty'] * $item['price'];
            }

            $orderPaymentMethod = OrderPaymentMethod::create([
                'order_id' => $order->id,
                'payment_provider' => $request->get('payment_method'),
                'total_balance' => $totalBalance,
                'status' => 'pending',
            ]);

            return compact('order', 'totalBalance', 'orderPaymentMethod');
        });

        if($request->payment_method === 'vnpay')
        {
            $vnp_Url = $this->urlVnpay($arrayData['order'], $arrayData['totalBalance']);
            return Redirect::to($vnp_Url);
        }
        
        session()->put('cart', []);
        return redirect()->route('index')->with('message', 'Ordered successfully'); //tu hien thi lai
    }

    public function urlVnpay($order, $totalBalance)
    {
        $order = Order::create([
            'user_id' => Auth::user()->id,
            'address' => $request->get('address'),
            'status' => 'pending',
            'payment_method' => $request->get('payment_method'),
        ]);

        $totalBalance = 0;
        foreach($cart as $productId => $item) {
            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $productId,
                'qty' => $item['qty'],
                'price' => $item['price'],
                'name' => $item['name'],
            ]);
            $totalBalance += $item['qty'] * $item['price'];
        }

        $vnp_TxnRef = $order->id; //Mã giao dịch thanh toán tham chiếu của merchant
        $vnp_Amount = $totalBalance; // Số tiền thanh toán
        $vnp_Locale = 'vn'; //Ngôn ngữ chuyển hướng thanh toán
        $vnp_BankCode ='VNBANK'; //Mã phương thức thanh toán
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR']; //IP Khách hàng thanh toán

        //Expire
        $startTime = date("YmdHis");
        $expire = date('YmdHis',strtotime('+15 minutes',strtotime($startTime)));

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => config('vnpay.vnp_tmncode'),
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => "Thanh toan GD:" . $vnp_TxnRef,
            "vnp_OrderType" => "other",
            "vnp_ReturnUrl" => config('vnpay.vnp_returnurl'),
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_ExpireDate"=>$expire
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = config('vnpay.vnp_url') . "?" . $query;
        
        $vnpSecureHash =   hash_hmac('sha512', $hashdata, config('vnpay.vnp_hashsecret'));//  
        $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        
        header('Location: ' . $vnp_Url);
    }

    public function callbackVnpay(Request $request)
    {
        if($request->get('vnp_ResponseCode') == '00')
        {

        }
    }
}
