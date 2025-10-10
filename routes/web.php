use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;

Route::get('/', function () {
    return response()->json(['message' => 'Image Restoration API ready']);
});

Route::post('/remove-background', [ImageController::class, 'removeBackground']);
Route::post('/smooth', [ImageController::class, 'smooth']);
