<?php


use App\Models\Payment;
use App\Models\Role;
use App\Models\Transmission;
use App\Models\Utopia;
use App\Models\VouchersBank;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

use Illuminate\Http\Client\Response;
use Illuminate\Http\Client\RequestException;
use AyubIRZ\PerfectMoneyAPI\PerfectMoneyAPI;

Route::middleware('guest')->group(function () {
    Route::name('login.')->prefix('login')->group(function () {
        Route::get('', [App\Http\Controllers\Auth\LoginController::class, 'index'])->name('index');
        Route::post('simple-login', [App\Http\Controllers\Auth\LoginController::class, 'simpleLoginPost'])->name('simple-post');
    });
    Route::get('register', [App\Http\Controllers\Auth\LoginController::class, 'register'])->name('register');
    Route::post('register/{otp:token}', [App\Http\Controllers\Auth\LoginController::class, 'registerUser'])->name('register.post');
    Route::get('forgot-password', [App\Http\Controllers\Auth\LoginController::class, 'forgotPassword'])->name('forgotPassword');
    Route::post('forgot-password', [App\Http\Controllers\Auth\LoginController::class, 'sendLinkForget'])->name('post.forgotPassword');
    Route::get('forgot-password/{otp:token}', [App\Http\Controllers\Auth\LoginController::class, 'forgotPasswordToken'])->name('forgotPassword.token');
    Route::get('update-password/{otp:token}', [App\Http\Controllers\Auth\LoginController::class, 'updatePassword'])->name('update.Password');
    Route::post('update-password/{otp:token}', [App\Http\Controllers\Auth\LoginController::class, 'updatePasswordPost'])->name('post.update.Password');
    Route::post('create-cod', [App\Http\Controllers\Auth\LoginController::class, 'createCode'])->name('createCode');

});
Route::get('logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
Route::get('', [App\Http\Controllers\Panel\PanelController::class, 'index'])->name('panel.index');


Route::middleware(['auth'])->group(function () {
    Route::withoutMiddleware('IsEmptyUserInformation')->group(function () {
        Route::get('error/{payment}', [App\Http\Controllers\Panel\PanelController::class, 'error'])->name('panel.error');
        Route::prefix('user')->group(function () {
            Route::get('information', [App\Http\Controllers\Panel\UserController::class, 'index'])->name('panel.user.information');
            Route::get('completion-of-information', [App\Http\Controllers\Panel\UserController::class, 'completionOfInformation'])->name('panel.user.completionOfInformation');
            Route::post('completion-of-information', [App\Http\Controllers\Panel\UserController::class, 'register'])->name('panel.user.register');
            Route::get('edit', [App\Http\Controllers\Panel\UserController::class, 'edit'])->name('panel.user.edit');
        });
    });
    Route::get('contact-us', [App\Http\Controllers\Panel\PanelController::class, 'contactUs'])->name('panel.contactUs');
    Route::get('purchase', [App\Http\Controllers\Panel\PanelController::class, 'purchase'])->name('panel.purchase.view');


    Route::get('transmission', [App\Http\Controllers\Panel\TransmissionController::class, 'index'])->name('panel.transmission.view');
    Route::get('remittance-transfer-information/{transitionDelivery}', [App\Http\Controllers\Panel\TransmissionController::class, 'information'])->name('panel.transfer.information');
    Route::get('transmission-fail', [App\Http\Controllers\Panel\TransmissionController::class, 'transmissionFail'])->name('panel.transfer.fail');

    Route::middleware('LimitedPurchase')->group(function () {
        Route::post('purchase', [App\Http\Controllers\Panel\PanelController::class, 'store'])->name('panel.purchase');
        Route::post('Purchase-through-the-bank', [App\Http\Controllers\Panel\PanelController::class, 'PurchaseThroughTheBank'])->name('panel.PurchaseThroughTheBank');
        Route::post('transmission', [App\Http\Controllers\Panel\TransmissionController::class, 'store'])->name('panel.transmission');
        Route::post('voucher-transfer-through-bank', [App\Http\Controllers\Panel\TransmissionController::class, 'transferFromThePaymentGateway'])->name('panel.transferFromThePaymentGateway');
        Route::post('back/voucher-transfer-through-bank', [App\Http\Controllers\Panel\TransmissionController::class, 'transferFromThePaymentGatewayBack'])->name('panel.back.transferFromThePaymentGateway')->withoutMiddleware(Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
//        Route::post('transfer', [App\Http\Controllers\Panel\TransmissionController::class, 'transferConnectionBank'])->name('panel.transfer.external.post');
//        Route::post('transfer/back-bank', [App\Http\Controllers\Panel\TransmissionController::class, 'transferConnectionBackBank'])->name('panel.transfer.external.back-bank')->withoutMiddleware(Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);;


    });
//    Route::get('transfer', [App\Http\Controllers\Panel\TransmissionController::class, 'transfer'])->name('panel.transfer.external');

    Route::post('back/Purchase-through-the-bank', [App\Http\Controllers\Panel\PanelController::class, 'backPurchaseThroughTheBank'])->name('panel.Purchase-through-the-bank')->withoutMiddleware(Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    Route::get('wallet-charging', [App\Http\Controllers\Panel\PanelController::class, 'walletCharging'])->name('panel.wallet.charging');
    Route::get('wallet-charging-Preview', [App\Http\Controllers\Panel\PanelController::class, 'walletChargingPreview'])->name('panel.wallet.charging-Preview');
    Route::post('wallet-charging', [App\Http\Controllers\Panel\PanelController::class, 'walletChargingStore'])->name('panel.wallet.charging.store');
    Route::post('back/wallet-charging', [App\Http\Controllers\Panel\PanelController::class, 'walletChargingBack'])->name('panel.wallet.charging.back')->withoutMiddleware(Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);;
    Route::get('delivery-bank/{invoice}/{payment}', [App\Http\Controllers\Panel\PanelController::class, 'deliveryVoucherBankView'])->name('panel.deliveryVoucherBankView');
    Route::post('delivery-bank/{invoice}/{payment}', [App\Http\Controllers\Panel\PanelController::class, 'deliveryVoucherBank'])->name('panel.deliveryVoucherBank');
    Route::middleware('IsEmptyUserInformation')->group(function () {
        Route::get('orders', [App\Http\Controllers\Panel\OrderController::class, 'index'])->name('panel.order');
        Route::get('order/{financeTransaction}/details', [App\Http\Controllers\Panel\OrderController::class, 'details'])->name('panel.order.details');
        Route::get('expectation', [App\Http\Controllers\Panel\OrderController::class, 'Expectation'])->name('panel.order.expectation');
        Route::get('expectation/{invoice}/details', [App\Http\Controllers\Panel\OrderController::class, 'ExpectationDetails'])->name('panel.order.expectation.details');

    });
    Route::get('delivery', [App\Http\Controllers\Panel\PanelController::class, 'delivery'])->name('panel.delivery');
    Route::get('rules', [App\Http\Controllers\Panel\PanelController::class, 'rules'])->name('panel.rules');


    Route::get('tickets', [App\Http\Controllers\Panel\TicketController::class, 'index'])->name('panel.ticket');
    Route::get('contact-us', [App\Http\Controllers\Panel\PanelController::class, 'contactUs'])->name('panel.contactUs');

    Route::get('ticket-chat/{ticket}', [App\Http\Controllers\Panel\TicketController::class, 'ticketChat'])->name('panel.ticket-chat');
    Route::post('ticket-client-message', [App\Http\Controllers\Panel\TicketController::class, 'ticketMessage'])->name('panel.ticket-client-message');
    Route::get('ticket-add', [App\Http\Controllers\Panel\TicketController::class,'ticketAdd'])->name('panel.ticket-add');
    Route::post('ticket-add-submit', [App\Http\Controllers\Panel\TicketController::class, 'ticketAddSubmit'])->name('panel.ticket-add-submit');
    Route::get('download/{file}', [App\Http\Controllers\Panel\TicketController::class, 'download'])->name('panel.ticket.download');
    Route::get('faq', [App\Http\Controllers\Panel\FaqController::class, 'index'])->name('panel.faq');

});

// Admin


Route::prefix('admin')->middleware(['auth', 'AdminLogin'])->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\AdminController::class, 'index'])->name('panel.admin');
    Route::get('login-another-user/{user}', [App\Http\Controllers\Admin\AdminController::class, 'loginAnotherUser'])->name('panel.admin.login-another-user');
    Route::get('tickets', [App\Http\Controllers\Admin\TicketController::class, 'index'])->name('panel.admin.tickets');
    Route::get('ticket/close/{ticket}', [App\Http\Controllers\Admin\TicketController::class, 'closeTicket'])->name('panel.admin.tickets.close');
    Route::get('ticket-page', [App\Http\Controllers\Admin\TicketController::class, 'ticketPage'])->name('panel.admin.ticket-page');
    Route::get('ticket-chat/{ticket_id}', [App\Http\Controllers\Admin\TicketController::class, 'ticketChat'])->name('panel.admin.ticket-chat');
    Route::post('ticket-message', [App\Http\Controllers\Admin\TicketController::class, 'ticketMessage'])->name('panel.admin.ticket-message');
    Route::get('dollar-price', [App\Http\Controllers\Admin\DollarController::class, 'index'])->name('panel.admin.dollar-price');
    Route::get('dollar-price-submit', [App\Http\Controllers\Admin\DollarController::class, 'priceSubmit'])->name('panel.admin.dollar-price-submit');

    Route::prefix('user')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('panel.admin.user.index');
        Route::get('inactive/{user}', [App\Http\Controllers\Admin\UserController::class, 'inactive'])->name('panel.admin.user.inactive');
        Route::post('search', [App\Http\Controllers\Admin\UserController::class, 'search'])->name('panel.admin.user.search');
    });
});

Route::get('voucher-price', [App\Http\Controllers\Panel\TransmissionController::class, 'voucher_price'])->name('voucher-price');
Route::get('voucher', [App\Http\Controllers\Panel\TransmissionController::class, 'voucher'])->name('voucher');
Route::post('mobile-submit', [App\Http\Controllers\Panel\TransmissionController::class, 'mobile_submit'])->name('transfer.mobile-submit');
Route::post('verification-code-submit', [App\Http\Controllers\Panel\TransmissionController::class, 'verification_code_submit'])->name('transfer.verification-code-submit');
Route::post('transfer-logout', [App\Http\Controllers\Panel\TransmissionController::class, 'transfer_logout'])->name('transfer.logout');

Route::fallback(function () {
    abort(404);
});


