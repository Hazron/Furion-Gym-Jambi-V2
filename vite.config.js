import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 
                'resources/js/app.js',
                'resources/js/Admin/dashboardAdmin.js',
                'resources/js/Admin/Member.js',    
                'resources/js/Admin/MembershipPayment.js',
                'resources/js/Admin/OrderBarang.js',
                'resources/js/Admin/ListPaymentBarang.js',   

                'resources/js/Owner/dashboardOwner.js'
            ],
            refresh: true,
        }),
    ],
});
