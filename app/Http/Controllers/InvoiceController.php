<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Models\Counter;
use App\Models\Invoice;
use App\Models\InvoiceItem;
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

    public function create_invoice() {
        $counter = Counter::where('key', 'invoice')->first();
        $random = Counter::where('key', 'invoice')->first();

        $invoice = Invoice::orderBy('id', 'DESC')->first();

        if ($invoice) {
            $invoice = $invoice->id+1;
            $counters = $counter->value + $invoice;
        } else {
            $counters = $counter->value;
        }

        $formData = [
            'number' => $counter->prefix.$counters,
            'customer_id' => null,
            'customer' => null,
            'date' => date('Y-m-d'),
            'due_date' => null,
            'reference' => null,
            'discount' => 0,
            'term_and_conditions' => 'Default Terms and Conditions',
            'items' => [
                [
                    'product_id' => null,
                    'product' => null,
                    'unit_price' => 0,
                    'quantity' => 1
                ]
            ]
        ];

        return response()->json($formData, 200);
    }

    public function add_invoice(InvoiceRequest $request) {
        $validatedInvoice = $request->safe()->except(['invoice_item']);
        $invoice = Invoice::create($validatedInvoice);
        foreach (json_decode($request->safe()->only('invoice_item')['invoice_item']) as $item) {
            $invoiceItem['invoice_id'] = $invoice->id;
            $invoiceItem['product_id'] = $item->id;
            $invoiceItem['unit_price'] = $item->unit_price;
            $invoiceItem['quantity'] = $item->quantity;

            InvoiceItem::create($invoiceItem);
        }


    }

    public function get_invoice($id) {
        $invoice = Invoice::with(['customer', 'invoice_items','invoice_items.product'])->find($id);

        return response()->json(['invoice' => $invoice], 200);
    }

    public function delete_invoice_items($id) {
        $invoiceItem = InvoiceItem::findOrFail($id);
        $invoiceItem->delete();
    }

    public function delete_invoice($id) {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();
    }

    public function update_invoice (InvoiceRequest $request, $id) {
        $invoice = Invoice::where('id', $id)->first();

        $validatedInvoice = $request->safe()->except(['invoice_item']);
        $invoice->update($validatedInvoice);
        $invoice->invoice_items()->delete();
        foreach (json_decode($request->safe()->only('invoice_item')['invoice_item']) as $item) {
            $invoiceItem = new InvoiceItem;
            $invoiceItem->invoice_id = $invoice->id;
            $invoiceItem->product_id = $item->product_id;
            $invoiceItem->unit_price = $item->unit_price;
            $invoiceItem->quantity = $item->quantity;

            $invoice->invoice_items()->save($invoiceItem);
        }
    }
}
