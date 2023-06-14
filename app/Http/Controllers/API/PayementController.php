<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Pack;
use Illuminate\Support\Facades\DB;
use App\Models\Purchase;
use App\Models\Course;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Subscriber;
use App\Models\Subscribtion;

use App\Models\User;
use Carbon\Carbon;


class PayementController extends Controller
{


    // ...

    function getPriceForItem($itemId, $itemType)
    {
        if ($itemType === 'course') {
            $course = Course::find($itemId);
            if ($course) {
                return $course->price;
            }
        } elseif ($itemType === 'pack') {
            $pack = Pack::find($itemId);
            if ($pack) {
                return $pack->price;
            }
        }

        // If the item is not found or the type is not recognized, you can return a default or error value
        return 0; // Or you can throw an exception or handle the error as per your requirement
    }

    public function processPayment(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*.id' => 'required|exists:courses,id|exists:packs,id',
            'items.*.type' => 'required|in:course,pack,live',
            /* 'card_number' => 'required|numeric',
            'expiration_month' => 'required|numeric|digits_between:1,12',
            'expiration_year' => 'required|numeric', */

        ]);

        if ($validator->fails()) {
            // Handle validation errors
            return response()->json(['errors' => $validator->errors()], 422);
        }



        $items = $request->input('items');
        /* $cardNumber = $request->input('card_number');
        $expirationMonth = $request->input('expiration_month');
        $expirationYear = $request->input('expiration_year');
        $totalAmount = 0;
        
 */
        $totalAmount = 0;
        foreach ($items as $item) {
            $itemId = $item['id'];
            $itemType = $item['type'];
            if ($itemType === 'course') {
                $course = Course::find($itemId);
                if ($course) {
                    $totalAmount = $totalAmount + $course->price;
                }
            } elseif ($itemType === 'pack') {
                $pack = Pack::find($itemId);
                if ($pack) {
                    $totalAmount = $totalAmount + $pack->price;
                }
            }
        }


        /*Make a request to the payment gateway API
        $response = Http::post('https://api.payment-gateway.com/process-payment', [
            'amount' => $totalAmount,
            'card_number' => $cardNumber,
            'expiration_month' => $expirationMonth,
            'expiration_year' => $expirationYear,
            // ...
        ]);*/

        // Process the response from the payment gateway
        if ($request->input('transaction_id')) {
            $transactionId = $request->input('transaction_id');
            $purchase = new Purchase();
            $purchase->transaction_id = $transactionId;
            $purchase->montant = $totalAmount;
            $purchase->client_id = auth()->user()->id;
            $purchase->payement_gateway    = 'paypal';
            $purchase->type = 'purchase_items';

            $purchase->save();

            foreach ($items as $item) {
                $itemType = $item['type'];
                $itemId = $item['id'];

                if ($itemType === 'pack') {
                    $pack = Pack::findOrFail($itemId);
                    if ($pack) {
                        DB::table('purchase_item')->insert([
                            'purchase_id' => $purchase->id,
                            'item_id' => $itemId,
                            'item_type' => $itemType,
                        ]);
                        // Increase the number of sales for the course
                        $pack->increment('sells_number');

                        $coach = $pack->user;
                        // Increase the number of sales for the instructors
                        if ($coach) {
                            $coach->increment('total_sells');
                        }
                    }
                } elseif ($itemType === 'course') {
                    $course = Course::findOrFail($itemId);
                    if ($course) {
                        DB::table('purchase_item')->insert([
                            'purchase_id' => $purchase->id,
                            'item_id' => $itemId,
                            'item_type' => $itemType,
                        ]);
                        // Increase the number of sales for the course
                        $course->increment('sells_number');

                        $coach = $course->user;
                        // Increase the number of sales for the instructors
                        if ($coach) {
                            $coach->increment('total_sells');
                        }
                    }
                }
            }
            return response()->json(['success' => true, 'transaction_id' => $transactionId]);
        } else {
            //$errorMessage = $response->json('error_message');
            return response()->json(['success' => false, 'error_message' => 'nope']);
        }
    }



    public function subscribe(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'subscription_id' => 'required|exists:subscribtions,id',

            /* 'card_number' => 'required|numeric',
            'expiration_month' => 'required|numeric|digits_between:1,12',
            'expiration_year' => 'required|numeric', */

        ]);
        if ($validator->fails()) {
            // Handle validation errors
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Get the subscription ID, client ID, and payment information from the request
        $subscriptionId = $request->input('subscription_id');
        $clientId =  auth()->user()->id;
        $paymentInfo = $request->input('payment_info');

        // Retrieve the subscription information
        $subscription = Subscribtion::findOrFail($subscriptionId);


        // Calculate the start and end dates for the subscription
        $startDate = Carbon::now();

        $endDate = $startDate->copy()->addDays($subscription->duration);


        if ($request->input('transaction_id')) {
            $transactionId = $request->input('transaction_id');
            $purchase = new Purchase();
            $purchase->transaction_id = $transactionId;
            $purchase->montant = $subscription->price;
            $purchase->client_id = auth()->user()->id;
            $purchase->payement_gateway    = 'paypal';
            $purchase->type = 'subscription';

            $purchase->save();

            // Create a new subscriber record
            $subscriber = new Subscriber();
            $subscriber->user_id = $clientId;
            $subscriber->start_date = $startDate;
            $subscriber->subscribtions_id = $subscription->id;
            $subscriber->end_date = $endDate;
            $subscriber->save();

            // Update the user's role and permissions
            $user = User::findOrFail($clientId);
            $user->givePermissionTo('access-all-content');




            // Return a response indicating success
            return response()->json(['success' => true, 'message' => 'Subscription successful']);
        }


        return response()->json(['success' => false, 'message' => 'something went wrong']);
    }
}
