<?php
//Users/apple/Documents/Focuz Solution/betamore-admin/routes/api.php
use App\Http\Controllers\API\ABAController;
use App\Http\Controllers\API\AddressController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BannerController;
use App\Http\Controllers\API\CompanyController;
use App\Http\Controllers\API\DeliveryController;
use App\Http\Controllers\API\ItemController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\PaymentMethodController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\PromotionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\TelegramWebhookController;
use App\Mail\Email;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\PayWayController;
use App\Notifications\OrderStatusUpdated;
use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use App\Http\Controllers\API\FcmTokenController;
use App\Http\Controllers\API\NotificationController;

Route::prefix('auth')->group(function () {
    

    Route::post('/send-otp', [AuthController::class, 'sendOTP']);
    Route::post('/resend-otp', [AuthController::class, 'resendOTP']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOTP']);
    Route::post('/register', [AuthController::class, 'completeRegistration']);
    Route::post('/login', [AuthController::class, 'login']);

});
Route::post('/image', [ImageController::class, 'store'])->name('api.image.store');
Route::post('/telegram/send', [TelegramController::class, 'send']);
Route::post('/send-email', [EmailController::class, 'send']);

Route::get('/banners', [ BannerController::class, 'index']);
Route::get('/companies', [ CompanyController::class, 'index']);
Route::get('/companies/{company}/show', [CompanyController::class, 'show']);

Route::prefix('items')->group(function () {
    Route::get('/', [ItemController::class, 'index']); // Get all items
    Route::get('/discounted', [ItemController::class, 'getDiscountedItems']); // Get only discounted items
    Route::get('/{id}', [ItemController::class, 'show']);
});

Route::prefix('promotions')->group(function () {
    Route::get('/', [ PromotionController::class, 'index']);
    Route::get('/{promotion}', [PromotionController::class, 'show']);
});

Route::get('/deliveries', [DeliveryController::class, 'index']);
        Route::get('/payment-method', [PaymentMethodController::class, 'index']);


// Route::get('/payway/checkout', [PayWayController::class, 'showCheckout']);
// Route::post('/payway/check-status', [PayWayController::class, 'checkStatus']);


Route::post('/payway/check-status', [OrderController::class, 'checkStatus']);


Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
    });
    
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
    });

    Route::post('/fcm-token', [FcmTokenController::class, 'update']);

    Route::get('/profile', [ProfileController::class, 'profile']);
    Route::put('/profile', [ProfileController::class, 'updateProfile']);

    Route::get('/addresses', [AddressController::class, 'index']);
    Route::post('/addresses', [AddressController::class, 'store']);
    Route::get('/addresses/{address}', [AddressController::class, 'show']);
    Route::put('/addresses/{address}', [AddressController::class, 'update']);

    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/place-order', [OrderController::class, 'makeOrder']);
    });

    Route::prefix('cart')->group(function () {
        Route::get('/', [OrderController::class, 'getCart'])->name('cart.index');
        Route::post('/items', [OrderController::class, 'addToCart'])->name('cart.items.store');
        Route::put('/items/{orderItemId}', [OrderController::class, 'updateCartItem'])->name('cart.items.update');
        Route::delete('/items/{orderItemId}', [OrderController::class, 'removeCartItem'])->name('cart.items.destroy');
    });

});



Route::middleware('auth:sanctum')->post('/user/fcm-token', function (Request $request) {
    // Validate the request
    $request->validate([
        'fcm_token' => 'required|string',
    ]);

    // Save FCM token for the authenticated user
    $user = $request->user();
    $user->update([
        'fcm_token' => $request->fcm_token,
    ]);

    return response()->json([
        'message' => 'FCM token saved successfully',
    ]);
});

// Update order status and send notification
