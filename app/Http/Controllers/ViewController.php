<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Model\Product;
use App\Model\Preference;
use App\Model\Payment_method;
use Illuminate\Support\Str;

class ViewController extends Controller
{
    public function index()
    {
        if ($redirect = $this->redirectIfAuthenticated()) {
            return $redirect;
        }

        session()->put('type', 'fedex');

        $exchange = Preference::first();
        if (!$exchange) {
            Log::warning('Preference data not found.');
            $exchange = new Preference(); // 空のオブジェクトを渡しておく
        }

        $categorys = $this->getCategories();
        $groups = $this->getGroups();
        $items = $this->getItemsGroupedByGroup($groups);

        $this->storeBankInformationToSession();

        $user = $this->getDefaultUserInformation();

        return view('view', compact('categorys', 'groups', 'items', 'exchange', 'user'));
    }

    private function redirectIfAuthenticated()
    {
        if (Auth::check()) {
            $type = session('type');

            $routes = [
                'fedex' => 'fedex',
                'air' => 'air',
                'ship' => 'ship',
            ];

            if (isset($routes[$type])) {
                return redirect(route($routes[$type]));
            }
        }
        return null;
    }

    private function getCategories()
    {
        return Product::where('hidden_item', '!=', '1')
            ->where('category', 'Air Stocking')
            ->groupBy('category')
            ->orderBy('sort_order', 'asc')
            ->get(['category']);
    }

    private function getGroups()
    {
        return Product::where('hidden_item', '!=', '1')
            ->where('category', 'Air Stocking')
            ->groupBy('group')
            ->orderBy('sort_order', 'asc')
            ->get(['group']);
    }

    private function getItemsGroupedByGroup($groups)
    {
        $items = [];

        if ($groups->isEmpty()) {
            return $items;
        }

        foreach ($groups as $group) {
            if (!empty($group->group)) {
                $products = Product::where('hidden_item', '!=', '1')
                    ->where('group', $group->group)
                    ->orderBy('sort_order', 'asc')
                    ->get();
                $items[] = $products;
            }
        }
        return $items;
    }

    private function storeBankInformationToSession()
    {
        $payee = Payment_method::where('selection', '選択')->first();

        if ($payee) {
            session([
                'bank' => $payee->bank,
                'branch' => $payee->branch,
                'swift_code' => $payee->swift_code,
                'account' => $payee->account,
                'name' => $payee->name,
            ]);
        } else {
            Log::warning('Payment_method with selection "選択" not found.');
        }
    }

    private function getDefaultUserInformation()
    {
        $user = Auth::user();

        if (!$user) {
            return [
                'user_id' => '',
                'consignee' => '',
                'address_line1' => '',
                'address_line2' => '',
                'city' => '',
                'state' => '',
                'country' => '',
                'country_codes' => '',
                'phone' => '',
                'fax' => '',
            ];
        }

        $address = $user->address;

        return [
            'user_id' => $user->id,
            'consignee' => $user->consignee ?? '',
            'address_line1' => $address->address_line1 ?? '',
            'address_line2' => $address->address_line2 ?? '',
            'city' => $address->city ?? '',
            'state' => $address->state ?? '',
            'country' => $address->country ?? '',
            'country_codes' => $address->country_codes ?? '',
            'phone' => $address->phone ?? '',
            'fax' => $address->fax ?? '',
        ];
    }
}
