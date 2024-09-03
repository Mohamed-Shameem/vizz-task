<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt|max:4096',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $file = $request->file('file');
        $filePath = $file->getRealPath();
        $handle = fopen($filePath, 'r');
        fgetcsv($handle);
        $data = [];
        while (($row = fgetcsv($handle)) !== false) {
            $data[] = [
                'name' => $row[0],
                'dob' => date('Y-m-d', strtotime($row[1])),
                'address' => $row[2],
                'profession' => $row[3],
                'salary' => $row[4],
            ];
        }
        fclose($handle);
        $inserted = Customer::insert($data);
        if ($inserted) {
            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully!',
                'inserted_rows' => count($data),
            ]);
        }
        return redirect()->route('customers.index');
    }

    public function index()
    {
        return view('customers.index');
    }

    public function getCustomers()
    {
        $customers = Customer::select(['id', 'name', 'dob', 'address', 'profession', 'salary', 'gst_salary'])
            ->orderBy('salary', 'desc')
            ->get();
        return DataTables::of($customers)
            ->addColumn('age', function ($customer) {
                return Carbon::parse($customer->dob)->age;
            })
            ->addColumn('gst', function ($customer) {
                return $customer->gst_salary;
            })
            ->make(true);
    }

    public function getCalculation(Request $request)
    {
        $id = $request->id;
        $customer = Customer::find($id);
        $salary = $customer->salary;
        $gst = $salary * 0.18;

        $updated = Customer::where('id', $id)
            ->update(['gst_salary' => $gst]);
        return $updated;
    }
}
