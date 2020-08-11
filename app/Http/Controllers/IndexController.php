<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contact;
use App\Campaing;
use App\Delivery;

class IndexController extends Controller
{
    public function setupCampaign(Request $request)
    {
        $data = $request->all();
        foreach ($data['contacts'] as $key => $contact) {
            $email = $contact[$data['emailCol']];
            Contact::firstOrCreate(
                ['email' => $email], [
                    'email' => $email,
                    'info' => $contact
                ]
            );
        }

        foreach ($data['templates'] as $key => $tpl) {
            Campaing::firstOrCreate(
                ['name' => $tpl['name']], $tpl
            );
        }

        if (isset($data['deliveries'])) {
            foreach ($data['deliveries'] as $key => $delivery) {
                $contact = Contact::where('email', $delivery[$data['emailCol']]);
                $campaing = Campaing::where('name', $delivery[$data['campaignName']]);
                Delivery::create([
                    'contact_id' => $contact->id,
                    'campaing_id' => $campaing->id,
                    'subject' => $delivery['subject'],
                    'message' => $delivery['message']
                ]);
            }
        }

    }
}
