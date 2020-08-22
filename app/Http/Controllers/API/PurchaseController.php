<?php

namespace App\Http\Controllers\API;

use LVR\CreditCard\CardCvc;
use LVR\CreditCard\CardNumber;
use LVR\CreditCard\CardExpirationYear;
use LVR\CreditCard\CardExpirationMonth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMailUser;
use App\Purchase;
use App\Product;
use App\GoogleUpload;


class PurchaseController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'quantity_purchased' => 'required|integer',
            'card.owner' => 'required|string',
        ]);
	    if ($validator->fails()) {
	    	return response()->json([ 'Erro nos dados enviados'], 400);
        }
        $product = Product::where('id', $request->product_id)
                            ->where('qty_stock', '>', 0)
                            ->first();
        if(empty($product))
        {
            return response()->json(['Erro nos dados enviados'], 400);
        }

        $purchase = Purchase::create([
            'product_id' => $request->product_id,
            'purchase_date' => \Carbon\Carbon::now(),
            'card' => json_encode($request->card),
            'quantity_purchased' => $request->quantity_purchased,
            'total' => ($request->quantity_purchased * $product->amount),            
        ]);
        
        $google = new GoogleUpload();
        $data = new \stdClass();
        $data->product_id = $request->product_id;
        $data->quantity_purchased = $request->quantity_purchased;
        $data->card_owner = $request->card['owner'];
        $data->card_number = $request->card['card_number'];
        $data->card_date_expiration = $request->card['date_expiration'];
        $data->card_flag = $request->card['flag'];
        $data->card_cvv = $request->card['cvv'];

        $xml = $this->createXmlFile($data);
        Storage::disk('google')->put('purchase'.$purchase->id.'.xml', $xml);
        $url = $google->generateUrl();
        
        Mail::to('f.donaadelli@gmail.com')->send(new SendMailUser($url));
        
        return response()->json([''], 200);
    }

    protected function create_file($data){
        $xml = $this->createXmlFile($data);
        return $xml;
    }

    protected function createXmlFile($data)
    {
        $xml = <<<XML
        <?xml version="1.0" encoding="UTF-8" ?>
        <dados>
            <product_id>$data->product_id</product_id>
            <quantity_purchased>$data->quantity_purchased</quantity_purchased>
            <card>
                <owner>$data->card_owner</owner>
                <card_number>$data->card_number</card_number>
                <date_expiration>$data->card_date_expiration</date_expiration>
                <flag>$data->card_flag</flag>
                <cvv>$data->card_cvv</cvv>
            </card>
        </dados>
        XML;
        
        return $xml;
    }

    
}
