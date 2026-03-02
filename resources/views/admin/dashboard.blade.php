@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="border-4 border-dashed border-gray-200 rounded-lg h-96">
        <div class="p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Dashboard</h2>
            <p class="text-gray-600 mb-6">Chào mừng đến với hệ thống quản lý Greenly!</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Sản phẩm</h3>
                    <p class="text-3xl font-bold text-green-600">0</p>
                    <p class="text-sm text-gray-500">Tổng số sản phẩm</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Đơn hàng</h3>
                    <p class="text-3xl font-bold text-blue-600">0</p>
                    <p class="text-sm text-gray-500">Đơn hàng mới</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Khách hàng</h3>
                    <p class="text-3xl font-bold text-purple-600">0</p>
                    <p class="text-sm text-gray-500">Tổng số khách hàng</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
