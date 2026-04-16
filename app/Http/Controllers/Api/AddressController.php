<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Lấy danh sách địa chỉ của người dùng
     * GET /api/addresses
     */
    public function index()
    {
        $userId = auth()->id();

        $addresses = Address::where('user_id', $userId)
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($address) {
                return [
                    'id'            => $address->id,
                    'receiver_name' => $address->receiver_name,
                    'phone'         => $address->phone,
                    'province'      => $address->province,
                    'district'      => $address->district,
                    'ward'          => $address->ward,
                    'street'        => $address->street,
                    'house_number'  => $address->house_number,
                    'full_address'  => $address->full_address,
                    'label'         => $address->label,
                    'is_default'    => $address->is_default,
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $addresses,
        ], 200);
    }

    /**
     * Thêm địa chỉ mới
     * POST /api/addresses
     */
    public function store(Request $request)
    {
        $userId = auth()->id();

        $request->validate([
            'receiver_name' => 'required|string|max:255',
            'phone'         => 'required|string|max:15',
            'province'      => 'required|string|max:255',
            'district'      => 'required|string|max:255',
            'ward'          => 'required|string|max:255',
            'street'        => 'required|string|max:255',
            'house_number'  => 'required|string|max:255',
            'label'         => 'in:home,office,other',
        ]);

        // Nếu đây là địa chỉ đầu tiên → tự động đặt làm mặc định
        $isFirstAddress = Address::where('user_id', $userId)->count() === 0;

        $address = Address::create([
            'user_id'       => $userId,
            'receiver_name' => $request->receiver_name,
            'phone'         => $request->phone,
            'province'      => $request->province,
            'district'      => $request->district,
            'ward'          => $request->ward,
            'street'        => $request->street,
            'house_number'  => $request->house_number,
            'label'         => $request->label ?? 'home',
            'is_default'    => $isFirstAddress,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thêm địa chỉ thành công',
            'data'    => [
                'id'            => $address->id,
                'receiver_name' => $address->receiver_name,
                'phone'         => $address->phone,
                'province'      => $address->province,
                'district'      => $address->district,
                'ward'          => $address->ward,
                'street'        => $address->street,
                'house_number'  => $address->house_number,
                'full_address'  => $address->full_address,
                'label'         => $address->label,
                'is_default'    => $address->is_default,
            ],
        ], 201);
    }

    /**
     * Cập nhật địa chỉ
     * PUT /api/addresses/{id}
     */
    public function update(Request $request, $id)
    {
        $userId = auth()->id();

        $address = Address::where('id', $id)->where('user_id', $userId)->first();

        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy địa chỉ'], 404);
        }

        $request->validate([
            'receiver_name' => 'required|string|max:255',
            'phone'         => 'required|string|max:15',
            'province'      => 'required|string|max:255',
            'district'      => 'required|string|max:255',
            'ward'          => 'required|string|max:255',
            'street'        => 'required|string|max:255',
            'house_number'  => 'required|string|max:255',
            'label'         => 'in:home,office,other',
        ]);

        $address->update($request->only([
            'receiver_name', 'phone', 'province', 'district', 'ward', 'street', 'house_number', 'label',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật địa chỉ thành công',
            'data'    => [
                'id'            => $address->id,
                'receiver_name' => $address->receiver_name,
                'phone'         => $address->phone,
                'province'      => $address->province,
                'district'      => $address->district,
                'ward'          => $address->ward,
                'street'        => $address->street,
                'house_number'  => $address->house_number,
                'full_address'  => $address->full_address,
                'label'         => $address->label,
                'is_default'    => $address->is_default,
            ],
        ], 200);
    }

    /**
     * Xóa địa chỉ
     * DELETE /api/addresses/{id}
     */
    public function destroy($id)
    {
        $userId = auth()->id();

        $address = Address::where('id', $id)->where('user_id', $userId)->first();

        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy địa chỉ'], 404);
        }

        $wasDefault = $address->is_default;
        $address->delete();

        // Nếu xóa địa chỉ mặc định → đặt địa chỉ đầu tiên còn lại làm mặc định
        if ($wasDefault) {
            $nextDefault = Address::where('user_id', $userId)->first();
            if ($nextDefault) {
                $nextDefault->update(['is_default' => true]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Xóa địa chỉ thành công',
        ], 200);
    }

    /**
     * Đặt địa chỉ làm mặc định
     * POST /api/addresses/{id}/set-default
     */
    public function setDefault($id)
    {
        $userId = auth()->id();

        $address = Address::where('id', $id)->where('user_id', $userId)->first();

        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy địa chỉ'], 404);
        }

        // Bỏ mặc định tất cả địa chỉ cũ
        Address::where('user_id', $userId)->update(['is_default' => false]);

        // Đặt địa chỉ mới làm mặc định
        $address->update(['is_default' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Đã đặt làm địa chỉ mặc định',
        ], 200);
    }
}
