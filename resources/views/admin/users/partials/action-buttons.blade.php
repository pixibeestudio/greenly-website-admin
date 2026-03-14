<div class="flex items-center justify-center gap-2">
    <!-- Nút Xem chi tiết -->
    <button onclick="openShowUserModal(this)"
        data-user="{{ json_encode([
            'id' => $user->id,
            'fullname' => $user->fullname,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'role' => $user->role,
            'status' => $user->status,
            'avatar_url' => $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->fullname) . '&background=e8f5e9&color=2e7d32&rounded=true&bold=true&size=200',
            'created_at' => $user->created_at->format('d/m/Y H:i'),
        ]) }}"
        class="w-8 h-8 rounded-full text-gray-400 hover:bg-blue-50 hover:text-blue-600 transition-all flex items-center justify-center" title="Xem chi tiết">
        <i class="fa-solid fa-eye text-xs"></i>
    </button>

    <!-- Nút Sửa -->
    <button onclick="openEditUserModal(this)"
        data-user="{{ json_encode([
            'id' => $user->id,
            'fullname' => $user->fullname,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'role' => $user->role,
        ]) }}"
        data-user-id-edit="{{ $user->id }}"
        class="w-8 h-8 rounded-full text-gray-400 hover:bg-blue-50 hover:text-blue-600 transition-all flex items-center justify-center" title="Sửa thông tin">
        <i class="fa-solid fa-pen text-xs"></i>
    </button>

    <!-- Nút Khóa / Mở khóa -->
    @if($user->status === 'locked')
        <form action="{{ route('admin.users.toggleLock', $user) }}" method="POST" class="inline">
            @csrf
            @method('PATCH')
            <button type="submit"
                class="w-8 h-8 rounded-full text-green-600 bg-green-50 hover:bg-green-100 transition-all flex items-center justify-center" title="Mở khóa tài khoản">
                <i class="fa-solid fa-unlock text-xs"></i>
            </button>
        </form>
    @else
        <form action="{{ route('admin.users.toggleLock', $user) }}" method="POST" class="inline">
            @csrf
            @method('PATCH')
            <button type="submit"
                class="w-8 h-8 rounded-full text-gray-400 hover:bg-orange-50 hover:text-orange-500 transition-all flex items-center justify-center" title="Khóa tài khoản">
                <i class="fa-solid fa-lock text-xs"></i>
            </button>
        </form>
    @endif

    <!-- Nút Xóa -->
    @if($user->role === 'admin')
        <button class="w-8 h-8 rounded-full text-gray-300 cursor-not-allowed flex items-center justify-center" title="Không thể xóa Admin" disabled>
            <i class="fa-solid fa-trash-can text-xs"></i>
        </button>
    @else
        <button onclick="openDeleteUserModal(this)"
            data-user-id="{{ $user->id }}"
            data-user-name="{{ $user->fullname }}"
            class="w-8 h-8 rounded-full text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all flex items-center justify-center" title="Xóa tài khoản">
            <i class="fa-solid fa-trash-can text-xs"></i>
        </button>
    @endif
</div>
