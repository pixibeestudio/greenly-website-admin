<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        // 1. Truy vấn danh sách nhà cung cấp
        $query = Supplier::query();

        // 2. Tìm kiếm theo tên hoặc SĐT
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%')
                  ->orWhere('contact_name', 'like', '%' . $search . '%');
            });
        }

        // 3. Lọc theo trạng thái (is_active)
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // 4. Sắp xếp mới nhất
        $query->latest();

        // 5. Phân trang với số lượng tùy chỉnh
        $perPage = $request->input('per_page', 10);
        $suppliers = $query->paginate($perPage)->appends($request->query());

        // 6. Thống kê cho các Card
        $totalSuppliers = Supplier::count();
        $activeSuppliers = Supplier::where('is_active', 1)->count();
        $inactiveSuppliers = Supplier::where('is_active', 0)->count();

        // 7. Trả về view
        return view('admin.suppliers.index', compact(
            'suppliers', 'totalSuppliers', 'activeSuppliers', 'inactiveSuppliers'
        ));
    }

    public function store(Request $request)
    {
        // 1. Validate dữ liệu đầu vào
        $request->validate([
            'name' => 'required|string|max:255|unique:suppliers,name',
            'contact_name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:15|unique:suppliers,phone',
            'address' => 'required|string|max:500',
            'is_active' => 'required|boolean',
            'certificate' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Vui lòng nhập tên nhà cung cấp.',
            'name.unique' => 'Tên nhà cung cấp đã tồn tại.',
            'name.max' => 'Tên nhà cung cấp không được quá 255 ký tự.',
            'phone.unique' => 'Số điện thoại đã được sử dụng.',
            'phone.max' => 'Số điện thoại không được quá 15 ký tự.',
            'address.required' => 'Vui lòng nhập địa chỉ.',
            'address.max' => 'Địa chỉ không được quá 500 ký tự.',
            'certificate.max' => 'Chứng chỉ không được quá 500 ký tự.',
        ]);

        // 2. Lưu vào bảng Suppliers
        Supplier::create($request->only(['name', 'contact_name', 'phone', 'address', 'is_active', 'certificate']));

        // 3. Redirect về trang danh sách với thông báo thành công
        return redirect()->route('admin.suppliers.index')->with('success', 'Thêm nhà cung cấp thành công!');
    }

    public function update(Request $request, string $id)
    {
        // 1. Tìm nhà cung cấp theo ID
        $supplier = Supplier::findOrFail($id);

        // 2. Validate dữ liệu đầu vào
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('suppliers')->ignore($supplier->id)],
            'contact_name' => 'nullable|string|max:100',
            'phone' => ['nullable', 'string', 'max:15', Rule::unique('suppliers')->ignore($supplier->id)],
            'address' => 'required|string|max:500',
            'is_active' => 'required|boolean',
            'certificate' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Vui lòng nhập tên nhà cung cấp.',
            'name.unique' => 'Tên nhà cung cấp đã tồn tại.',
            'phone.unique' => 'Số điện thoại đã được sử dụng.',
            'address.required' => 'Vui lòng nhập địa chỉ.',
        ]);

        // 3. Cập nhật nhà cung cấp
        $supplier->update($request->only(['name', 'contact_name', 'phone', 'address', 'is_active', 'certificate']));

        // 4. Redirect về trang danh sách với thông báo thành công
        return redirect()->route('admin.suppliers.index')->with('success', 'Cập nhật nhà cung cấp thành công!');
    }

    public function destroy(string $id)
    {
        $supplier = Supplier::findOrFail($id);

        // Xóa nhà cung cấp
        $supplier->delete();

        return redirect()->route('admin.suppliers.index')->with('success', 'Đã xóa nhà cung cấp thành công!');
    }
}
