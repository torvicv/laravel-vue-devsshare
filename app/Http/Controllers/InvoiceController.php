<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function get_all_invoice() {
        $invoices = Invoice::with('customer')->get();
        return response()->json([
            'invoices' => $invoices
        ], 200);
    }

    public function search_invoice(Request $request) {
        $search = $request->get('search');
        if ($search !== null) {
            $invoices = Invoice::with('customer')
            ->where('id', 'LIKE', "%$search%")
            ->get();
        } else {
            $invoices = Invoice::with('customer')->get();
        }
        return response()->json([
            'invoices' => $invoices
        ], 200);
    }
}
