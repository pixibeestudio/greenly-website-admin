<!-- Modal Xem chi tiết Shipper -->
<div id="shipperDetailModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4 transition-opacity opacity-0">
    <div class="bg-white rounded-2xl w-full max-w-3xl shadow-2xl overflow-hidden transform scale-95 transition-transform duration-300 max-h-[90vh] flex flex-col" id="shipperModalContent">

        <!-- Modal Header (dynamic) -->
        <div class="bg-forest-800 p-6 flex justify-between items-start shrink-0" id="modalHeader">
            <div class="animate-pulse flex items-center gap-4">
                <div class="w-16 h-16 bg-white/20 rounded-full"></div>
                <div class="space-y-2">
                    <div class="h-5 bg-white/20 rounded w-32"></div>
                    <div class="h-3 bg-white/20 rounded w-48"></div>
                </div>
            </div>
        </div>

        <!-- Close button (fixed position) -->
        <button onclick="closeShipperModal()" class="absolute top-6 right-6 w-8 h-8 rounded-full bg-white/10 hover:bg-red-500 hover:text-white text-forest-100 flex items-center justify-center transition-colors z-10">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        <!-- Modal Body (dynamic, scrollable) -->
        <div class="p-6 overflow-y-auto" id="modalBody">
            <div class="flex items-center justify-center py-20">
                <i class="fa-solid fa-spinner fa-spin text-3xl text-forest-600"></i>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="border-t border-gray-100 px-6 py-4 flex justify-end gap-3 bg-white shrink-0">
            <button onclick="closeShipperModal()" class="px-5 py-2 rounded-lg border border-gray-200 text-gray-600 font-bold hover:bg-gray-50 transition-colors">Đóng</button>
        </div>
    </div>
</div>
