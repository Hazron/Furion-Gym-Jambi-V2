import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                // Admin Assets
                'resources/js/Admin/dashboardAdmin.js',
                'resources/js/Admin/Member.js',    
                'resources/js/Admin/MembershipPayment.js',
                'resources/js/Admin/OrderBarang.js',
                'resources/js/Admin/ListPaymentBarang.js',
                // Owner Assets
                'resources/js/Owner/dashboardOwner.js',
                'resources/js/Owner/MemberFurion.js',
                'resources/js/Owner/LaporanKeuangan.js',
                'resources/js/Owner/LaporanMembership.js',
                'resources/js/Owner/AktivitasAdmin.js',
            ],
            refresh: true,
        }),
    ],
    // Tambahan: Agar build lebih stabil di beberapa env
    build: {
        chunkSizeWarningLimit: 1600,
    },
});