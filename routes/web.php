<?php

use App\Http\Controllers\Admin\dashboardAdminController;
use App\Http\Controllers\Admin\memberAdminController;
use App\Http\Controllers\Admin\MembershipPaymentController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\Admin\ListPaymentController;
use App\Http\Controllers\Admin\PaketMemberController;
use App\Http\Controllers\landingPageFurionController;
use App\Http\Controllers\Owner\dashboardOwnerController;
use App\Http\Controllers\Owner\LaporanMembership;
use App\Http\Controllers\owner\monitoringEtalaseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Owner\MemberFurionController;
use App\Http\Controllers\Owner\PaketMemberFurionController;
use App\Http\Controllers\Owner\PromoMemberFurion;
use App\Http\Controllers\Owner\LaporanKeuanganController;
use App\Http\Controllers\Owner\AktivitasAdminController;
use App\Http\Controllers\Owner\BroadcastController;
use App\Http\Controllers\memberDashboardController;
use App\Http\Controllers\WebHookController;
use App\Models\membershipPayment;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('landingPageFurion');
// });

route::get('/', [landingPageFurionController::class,'index'])->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard-member', [memberDashboardController::class, 'index'])->name('member');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->group(function () {
    //DASHBOARD CONTROLLER
    Route::get('dashboardAdmin', dashboardAdminController::class . '@dashboardAdmin')->name('dashboardAdmin');

    //MEMBER CONTROLLER
    Route::get('memberAdmin', [memberAdminController::class, 'memberAdmin'])->name('memberAdmin');
    Route::get('/memberAdmin/data', [memberAdminController::class, 'getData'])->name('member.data');
    Route::post('registerMember', [memberAdminController::class, 'registerMember'])->name('registerMember');
    Route::delete('deleteMember/{id}', [memberAdminController::class, 'deleteMember'])->name('deleteMember');
    Route::put('editMember/{id}', [memberAdminController::class, 'editMember'])->name('member.update');
    Route::post('perpanjangMember/{id}', [memberAdminController::class, 'perpanjangMember'])->name('member.perpanjang');
    Route::post('reaktivasiMember/{id}', [memberAdminController::class, 'reaktivasiMember'])->name('membership.reactivate');

    //MEMBERSHIP PAYMENT CONTROLLER
    Route::get('/membershipPayment', [MembershipPaymentController::class, 'view'])->name('membershipPayment');
    Route::get('/membershipPayment/data', [MembershipPaymentController::class, 'getData'])->name('payment.data');
    Route::get('/payment/stats', [MembershipPaymentController::class, 'getStats'])->name('payment.stats');

    //PRODUK CONTROLLER
    Route::get('/orderBarang', [ProdukController::class, 'index'])->name('indexBarang');
    Route::post('/produk/store', [ProdukController::class, 'tambahProduk'])->name('tambahProduk');
    Route::delete('/produk/{id}', [ProdukController::class, 'destroy'])->name('produk.destroy');
    //Keranjang & Transaksi
    Route::patch('/produk/{id}/status', [ProdukController::class, 'toggleStatus'])->name('produk.toggleStatus');
    Route::post('/transaksi/simpan', [ProdukController::class, 'store'])->name('transaksi.simpan');
    Route::put('/produk/{id}', [ProdukController::class, 'editProduk'])->name('produk.update');

    //LIST PAYMENT CONTROLLER
    Route::get('/listPaymentBarang', [ListPaymentController::class, 'index'])->name('listPaymentBarang');
    Route::put('/listPaymentBarang/{id}/update', [ListPaymentController::class, 'updatePaymentStatus'])->name('order.updatePayment');
    Route::post('/order/send-invoice/{id}', [ListPaymentController::class, 'sendInvoice'])->name('order.sendInvoice');

    Route::get('/admin/paket-member', [PaketMemberController::class, 'index'])->name('admin.paketmember');
});

Route::middleware(['auth', 'owner'])->group(function () {
    Route::get('/ownerDashboard', [dashboardOwnerController::class, 'dashboardOwner'])->name('owner.dashboard');
    Route::get('/memberFurion', [MemberFurionController::class, 'index'])->name('owner.memberFurion');
    Route::get('/owner/member/{id}/detail', [MemberFurionController::class, 'getMemberDetail'])->name('owner.memberFurion.detail');

    Route::get('/owner/paketmemberfurion', [PaketMemberFurionController::class, 'index'])->name('owner.paketmemberfurion');
    Route::get('/paket-member', [PaketMemberFurionController::class, 'index'])->name('owner.paket.index');
    Route::post('/paket-member', [PaketMemberFurionController::class, 'store'])->name('owner.paket.store');
    Route::put('/paket-member/{id}', [PaketMemberFurionController::class, 'update'])->name('owner.paket.update');
    Route::delete('/paket-member/{id}', [PaketMemberFurionController::class, 'destroy'])->name('owner.paket.destroy');
    Route::patch('/paket-member/{id}/toggle', [PaketMemberFurionController::class, 'toggleStatus'])->name('owner.paket.toggle');

    Route::get('/owner/promo-member-furion', [PromoMemberFurion::class, 'index'])->name('owner.promomemberfurion');
    Route::post('/owner/promo-member-furion', [PromoMemberFurion::class, 'store'])->name('owner.promo.store');
    Route::put('/owner/promo-member-furion/{id}', [PromoMemberFurion::class, 'update'])->name('owner.promo.update');
    Route::patch('/owner/promo-member-furion/{id}/toggle', [PromoMemberFurion::class, 'toggleStatus'])->name('owner.promo.toggle');
    Route::post('/push-notification', [PromoMemberFurion::class, 'pushNotification'])->name('push.notification');
    Route::get('/owner/promo/progress', [PromoMemberFurion::class, 'progressBroadcast'])->name('owner.promo.progress');

    Route::get('/owner/laporan-keuangan', [LaporanKeuanganController::class, 'index'])->name('owner.laporankeuangan');
    Route::get('owner/laporan-membership', [LaporanMembership::class,'index'])->name('owner.laporan-Membership');

    Route::get('/owner/aktivitasadmin', [AktivitasAdminController::class, 'index'])->name('owner.aktivitasadmin');
    Route::get('/owner/aktivitas-detail', [AktivitasAdminController::class, 'getAktivitasDetail'])->name('owner.aktivitas-detail');
    Route::post('/owner/admin', [AktivitasAdminController::class, 'storeAdmin'])->name('owner.admin.store');
    Route::put('/owner/admin/{id}', [AktivitasAdminController::class, 'updateAdmin'])->name('owner.admin.update');
    Route::delete('/owner/admin/{id}', [AktivitasAdminController::class, 'deleteAdmin'])->name('owner.admin.delete');

    Route::get('/owne/monitoring-Etalase', [monitoringEtalaseController::class,'index'])->name('owner.monitoringEtalase');

    Route::get('/owner/broadcast', [BroadcastController::class, 'index'])->name('owner.broadcast');
    Route::post('/broadcast/send', [BroadcastController::class, 'send'])->name('owner.broadcast.send');

    Route::get('/owner/broadcast', [BroadcastController::class, 'index'])->name('owner.broadcast');
});

// Pastikan ini berada di dalam Group Route Owner Anda
Route::prefix('owner')->middleware(['auth'])->group(function () {
    
    Route::get('/promo-member-furion', [PromoMemberFurion::class, 'index'])->name('owner.promomemberfurion');

    Route::post('/promo-member-furion/store', [PromoMemberFurion::class, 'store'])->name('owner.promo.store');

    Route::put('/promo-member-furion/update/{id}', [PromoMemberFurion::class, 'update'])->name('owner.promo.update');

    Route::patch('/promo-member-furion/toggle/{id}', [PromoMemberFurion::class, 'toggleStatus'])->name('owner.promo.toggle');

    Route::post('/promo-member-furion/push-notification', [PromoMemberFurion::class, 'pushNotification'])->name('push.notification');

    Route::get('/promo-member-furion/progress', [PromoMemberFurion::class, 'progressBroadcast'])->name('owner.promo.progress');

});

// Route::post('/fonnte/webhook', [WebhookController::class, 'handle']);
// Route::post('/webhook/fonnte', [PromoMemberFurion::class, 'handleWebhook']);

Route::post('/admin/member/register', [memberAdminController::class, 'registerMember'])
    ->name('admin.member.register')
    ->middleware(['auth', 'admin']);

Route::get('/member/story-view/{member_id}', [MemberDashboardController::class, 'storyView'])->name('member.story.view');
Route::get('/member/story-download/{member_id}', [MemberDashboardController::class, 'downloadStory'])->name('member.story.download');
Route::post('/member/update-target', [memberDashboardController::class, 'updateTarget'])->name('member.update.target');


route::get('/landingpage', function () {
    return view('landingPage');
});

Route::get('/cek-path', function() {
    return base_path();
});





require __DIR__ . '/auth.php';
