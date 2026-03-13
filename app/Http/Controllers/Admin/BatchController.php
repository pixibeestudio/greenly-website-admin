<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Batch;
use App\Models\Product;
use App\Models\Supplier;

class BatchController extends Controller
{
    public function index(Request $request)
    {
        // 1. Truy vấn danh sách lô hàng, sử dụng Eager Loading
        $query = Batch::with(['product', 'supplier']);

        // 2. Tìm kiếm theo mã lô, tên sản phẩm hoặc tên nhà cung cấp
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('batch_code', 'like', '%' . $search . '%')
                  ->orWhereHas('product', function ($q2) use ($search) {
                      $q2->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('supplier', function ($q2) use ($search) {
                      $q2->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // 3. Lọc theo sản phẩm
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // 4. Lọc theo nhà cung cấp
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // 5. Lọc theo trạng thái tồn kho
        if ($request->filled('status')) {
            if ($request->status === 'available') {
                $query->where('current_quantity', '>', 10);
            } elseif ($request->status === 'low') {
                $query->where('current_quantity', '>', 0)->where('current_quantity', '<=', 10);
            } elseif ($request->status === 'empty') {
                $query->where('current_quantity', 0);
            }
        }

        // 6. Sắp xếp mới nhất
        $query->latest();

        // 7. Phân trang với số lượng tùy chỉnh
        $perPage = $request->input('per_page', 10);
        $batches = $query->paginate($perPage)->appends($request->query());

        // 8. Thống kê cho các Card
        $totalBatches = Batch::count();
        $totalInvestment = Batch::selectRaw('SUM(import_price * quantity) as total')->value('total') ?? 0;
        $warningBatches = Batch::where('current_quantity', '>', 0)->where('current_quantity', '<=', 10)->count()
                        + Batch::where('current_quantity', 0)->count();

        // 9. Lấy danh sách Products và Suppliers cho select
        $products = Product::orderBy('name')->get();
        $suppliers = Supplier::where('is_active', 1)->orderBy('name')->get();

        // 10. Trả về view
        return view('admin.batches.index', compact(
            'batches', 'totalBatches', 'totalInvestment', 'warningBatches',
            'products', 'suppliers'
        ));
    }

    public function store(Request $request)
    {
        // 0. Loại bỏ dấu chấm format tiền tệ trước khi validate
        $request->merge(['import_price' => str_replace('.', '', $request->input('import_price'))]);

        // 1. Validate dữ liệu đầu vào
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'batch_code' => 'required|string|max:50|unique:batches,batch_code',
            'import_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
        ], [
            'product_id.required' => 'Vui lòng chọn sản phẩm.',
            'product_id.exists' => 'Sản phẩm không hợp lệ.',
            'supplier_id.required' => 'Vui lòng chọn nhà cung cấp.',
            'supplier_id.exists' => 'Nhà cung cấp không hợp lệ.',
            'batch_code.required' => 'Vui lòng nhập mã lô hàng.',
            'batch_code.unique' => 'Mã lô hàng đã tồn tại.',
            'batch_code.max' => 'Mã lô hàng không được quá 50 ký tự.',
            'import_price.required' => 'Vui lòng nhập giá nhập.',
            'import_price.numeric' => 'Giá nhập phải là số.',
            'import_price.min' => 'Giá nhập không được âm.',
            'quantity.required' => 'Vui lòng nhập số lượng.',
            'quantity.integer' => 'Số lượng phải là số nguyên.',
            'quantity.min' => 'Số lượng phải lớn hơn 0.',
        ]);

        // 2. Lưu vào bảng Batches (current_quantity = quantity khi mới nhập)
        Batch::create([
            'product_id' => $request->product_id,
            'supplier_id' => $request->supplier_id,
            'batch_code' => $request->batch_code,
            'import_price' => $request->import_price,
            'quantity' => $request->quantity,
            'current_quantity' => $request->quantity,
        ]);

        // 3. Redirect về trang danh sách với thông báo thành công
        return redirect()->route('admin.batches.index')->with('success', 'Nhập lô hàng thành công!');
    }

    public function update(Request $request, string $id)
    {
        // 1. Tìm lô hàng theo ID
        $batch = Batch::findOrFail($id);

        // 0. Loại bỏ dấu chấm format tiền tệ trước khi validate
        $request->merge(['import_price' => str_replace('.', '', $request->input('import_price'))]);

        // 2. Validate dữ liệu đầu vào
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'batch_code' => ['required', 'string', 'max:50', Rule::unique('batches')->ignore($batch->id)],
            'import_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'current_quantity' => 'required|integer|min:0',
        ], [
            'product_id.required' => 'Vui lòng chọn sản phẩm.',
            'supplier_id.required' => 'Vui lòng chọn nhà cung cấp.',
            'batch_code.required' => 'Vui lòng nhập mã lô hàng.',
            'batch_code.unique' => 'Mã lô hàng đã tồn tại.',
            'import_price.required' => 'Vui lòng nhập giá nhập.',
            'quantity.required' => 'Vui lòng nhập số lượng ban đầu.',
            'current_quantity.required' => 'Vui lòng nhập số lượng tồn kho.',
            'current_quantity.min' => 'Số lượng tồn kho không được âm.',
        ]);

        // 3. Cập nhật lô hàng
        $batch->update($request->only([
            'product_id', 'supplier_id', 'batch_code',
            'import_price', 'quantity', 'current_quantity',
        ]));

        // 4. Redirect về trang danh sách với thông báo thành công
        return redirect()->route('admin.batches.index')->with('success', 'Cập nhật lô hàng thành công!');
    }

    public function destroy(string $id)
    {
        $batch = Batch::findOrFail($id);

        // Xóa lô hàng
        $batch->delete();

        return redirect()->route('admin.batches.index')->with('success', 'Đã xóa lô hàng thành công!');
    }
}
