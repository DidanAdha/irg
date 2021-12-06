<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
function getController($con, $fun){
	return "Apiv2\\".$con.'@'.$fun;
}
Route::name('v1')->group(function(){
	Route::middleware('auth:api')->get('/user', function (Request $request) {
	    return $request->user();
	});

	// Route::middleware(['throttle:60,1'])->group(function() {

	//======================================================================
	// USER COMPONENT
	//======================================================================

	# auth route
	Route::post('user/register', 'Api\User\AuthController@register');
	Route::post('user/login', 'Api\User\AuthController@login');
	Route::post('user/forgot', 'Api\User\AuthController@forgot');
	Route::get('user/detail/{id}', 'Api\User\UserController@detail');

	# resto route
	Route::get('user/resto/near', 'Api\User\RestoController@near');
	// Route::get('user/resto/open', 'Api\User\RestoController@openNow');
	Route::get('user/resto/{id}', 'Api\User\RestoController@detail');

	# menu route
	Route::get('user/menu/', 'Api\User\MenuController@index');
	Route::get('user/menu/{id}', 'Api\User\MenuController@detail');

	# follow route
	Route::get('user/follow', 'Api\User\FollowController@index');
	Route::post('user/follow', 'Api\User\FollowController@follow');

	# bookmark route
	Route::get('user/bookmark', 'Api\User\BookmarkController@index');
	Route::post('user/bookmark', 'Api\User\BookmarkController@bookmark');

	# cart route
	Route::get('user/cart', 'Api\User\CartController@index');
	Route::post('user/cart/add', 'Api\User\CartController@add');
	Route::put('user/cart/{id}', 'Api\User\CartController@edit');
	Route::delete('user/cart/{id}', 'Api\User\CartController@delete');
	Route::delete('user/cart', 'Api\User\CartController@deleteAll');

	# transaction route
	Route::get('user/transaction', 'Api\User\TransactionController@index');
	Route::get('user/transaction/{id}', 'Api\User\TransactionController@detail');
	Route::get('user/transaction/edit/{id}', 'Api\User\TransactionController@restoEdit');

	# reservation route
	Route::post('user/reservation', 'Api\User\ReservationController@store');

	# chat route
	Route::get('user/chat', 'Api\User\ChatController@index');

	# history transaction route
	Route::get('user/history', 'Api\User\HistoryController@index');
	Route::get('user/history/{id}', 'Api\User\HistoryController@detail');
	Route::post('user/history/delete', 'Api\User\HistoryController@remove');
	Route::post('user/history/deleteAll', 'Api\User\HistoryController@removeAll');

	# checkout route
	Route::post('user/checkout', 'Api\User\TransactionController@checkout');

	# promo route
	Route::get('user/promo', 'Api\User\PromoController@index');

	# search route
	Route::get('user/search', 'Api\User\SearchController@index');

	Route::put('user/profile/edit', 'Api\User\UserController@index');

	//======================================================================
	// END OF USER COMPONENT
	//======================================================================

	//======================================================================
	// RESTO COMPONENT
	//======================================================================

	# auth route
	Route::post('resto/register', 'Api\Resto\AuthController@register');
	Route::post('resto/login', 'Api\Resto\AuthController@login');

	# resto route
	Route::get('resto/list', 'Api\Resto\RestoController@index');
	Route::get('resto/list/{id}', 'Api\Resto\RestoController@detail');
	Route::post('resto/list', 'Api\Resto\RestoController@add');
	Route::put('resto/list/{id}', 'Api\Resto\RestoController@edit');
	Route::delete('resto/list/{id}', 'Api\Resto\RestoController@delete');
	Route::get('resto/search', 'Api\Resto\SearchController@resto');
	Route::post('resto/image', 'Api\Resto\RestoController@addImage');
	Route::delete('resto/image/{id}', 'Api\Resto\RestoController@deleteImage');

	# logo route
	Route::post('resto/logo/{id}', 'Api\Resto\RestoController@addLogo');

	# menu route
	Route::get('resto/menu', 'Api\Resto\MenuController@index');
	Route::get('resto/menu/{id}', 'Api\Resto\MenuController@detail');
	Route::post('resto/menu', 'Api\Resto\MenuController@add');
	Route::put('resto/menu/{id}', 'Api\Resto\MenuController@edit');
	Route::delete('resto/menu/{id}', 'Api\Resto\MenuController@delete');
	Route::get('resto/search-menu', 'Api\Resto\SearchController@menu');

	# menu favorite route
	// Route::get('resto/menu/favorite', 'Api\Resto\FavoriteController@index');
	Route::post('resto/menu/favorite', 'Api\Resto\FavoriteController@favorite');

	# transaction route
	Route::get('resto/trans', 'Api\Resto\TransactionController@index');
	Route::get('resto/trans/{id}', 'Api\Resto\TransactionController@detail');
	Route::post('resto/trans/{id}', 'Api\Resto\TransactionController@acceptConfirm');
	Route::post('resto/trans/process/{id}', 'Api\Resto\TransactionController@processConfirm');
	Route::post('resto/trans/ready/{id}', 'Api\Resto\TransactionController@readyConfirm');
	Route::post('resto/trans/decline/{id}', 'Api\Resto\TransactionController@declineConfirm');
	Route::put('resto/trans/edit/{id}', 'Api\Resto\TransactionController@editTransaction');
	Route::get('resto/trans/table/{id}', 'Api\Resto\TransactionController@tableListPrice');
	Route::post('resto/trans/table/{id}/pay', 'Api\Resto\TransactionController@payTable');

	# reservation route
	Route::get('resto/reservation', 'Api\Resto\ReservationController@index');
	Route::post('resto/reservation/{id}', 'Api\Resto\ReservationController@acceptConfirm');
	Route::post('resto/reservation/process/{id}', 'Api\Resto\ReservationController@processConfirm');
	Route::post('resto/reservation/decline/{id}', 'Api\Resto\ReservationController@declineConfirm');

	# chat route
	Route::get('resto/chat', 'Api\Resto\ChatController@index');

	# history route
	Route::get('resto/history', 'Api\Resto\HistoryController@index');
	Route::get('resto/history/{id}', 'Api\Resto\HistoryController@detail');
	Route::post('resto/history/delete', 'Api\Resto\HistoryController@remove');
	Route::post('resto/history/deleteAll', 'Api\Resto\HistoryController@removeAll');

	# employee route
	Route::get('resto/employee', 'Api\Resto\EmployeeController@index');
	Route::post('resto/employee', 'Api\Resto\EmployeeController@add');
	Route::put('resto/employee/{id}', 'Api\Resto\EmployeeController@edit');
	Route::delete('resto/employee/{id}', 'Api\Resto\EmployeeController@delete');

	# promo route
	Route::get('resto/promo', 'Api\Resto\PromoController@index');
	Route::post('resto/promo', 'Api\Resto\PromoController@add');
	Route::put('resto/promo/{id}', 'Api\Resto\PromoController@edit');
	Route::delete('resto/promo/{id}', 'Api\Resto\PromoController@delete');

	# table route
	Route::get('resto/table', 'Api\Resto\TableController@index');
	Route::post('resto/table', 'Api\Resto\TableController@add');
	Route::put('resto/table/{id}', 'Api\Resto\TableController@edit');
	Route::delete('resto/table/{id}', 'Api\Resto\TableController@delete');
	Route::delete('resto/table/delete/{id}', 'Api\Resto\TableController@deleteAll');
	Route::get('resto/table/download/{id}', 'Api\Resto\TableController@downloadBarcode');

	Route::put('resto/profile/owner', 'Api\Resto\UserController@index');

	Route::post('resto/schedule', 'Api\Resto\ScheduleController@store');

	//======================================================================
	// END OF RESTO COMPONENT
	//======================================================================

	// MISCELLANEOUS

	Route::post('feedback', 'Api\FeedbackController@index');
	Route::get('logout', 'Api\LogoutController@index');
	Route::get('type/cuisine', 'Api\CuisineController@index');
	Route::get('type/menu', 'Api\MenuTypeController@index');
	Route::get('type/facility', 'Api\FacilityController@index');
	Route::get('city', 'Api\IndonesiaController@city');


	// });
});

Route::prefix('v2')->group(function(){
	//auth prefix
	Route::prefix('auth')->group(function(){
		Route::post('/login', getController('AuthController', 'login'));
		Route::post('/login/google', getController('AuthController', 'loginGoogle'));
		Route::post('/edit/user', getController('AuthController', 'editProfile'))->middleware('auth:api');
		Route::post('/logout', getController('AuthController', 'logout'))->middleware('auth:api');
	});
	//page part
	Route::prefix('page')->middleware('auth:api')->group(function(){
		Route::get('/home', getController('PageController', 'home'));
		Route::get('/promo', getController('PageController', 'promo'));
		Route::get('/favresto', getController('PageController', 'favResto'));
		Route::get('/history', getController('PageController', 'history'));
		Route::get('/search', getController('PageController', 'search'));
		// Route::post('/search', getController('PageController', 'searchResult'));
	});
	//Resto prefix
	Route::prefix('resto')->middleware('auth:api')->group(function(){
		Route::get('/detail/{id}', getController('RestoController', 'detail'));
		Route::post('/fav', getController('RestoController', 'makefav'));
		Route::post('/menu', getController('RestoController', 'createMenu'));
		Route::post('/img', getController('RestoController', 'createImage'));
		Route::post('/', getController('RestoController', 'store'));
		Route::get('/', getController('RestoController', 'getDetailResto'));	
		Route::get('/trans', getController('TransactionController', 'getTrans'));
		Route::get('/qrcode', getController('RestoController', 'generateQr'));
	});
	//Cart
	Route::prefix('cart')->middleware('auth:api')->group(function(){
		Route::get('/', getController('CartController', 'index'));
		Route::get('/delete', getController('CartController', 'delete'));
		Route::post('/', getController('CartController', 'store'));
	});
	//Transaction prefix
	Route::prefix('trans')->middleware('auth:api')->group(function(){
		Route::post('/', getController('TransactionController', 'store'));
		Route::get('/{id}', getController('TransactionController', 'index'));
		Route::post('/check', getController('TransactionController', 'check'));
		Route::get('/op/{operation}/{id}', getController('TransactionController', 'op'));
		// Route::post('/reservation', getController('TransactionController', 'reserve'));
	});
	Route::prefix('reservation')->middleware('auth:api')->group(function(){
		Route::post('/', getController('ReservationController', 'store'));
	});
	Route::prefix('util')->middleware('auth:api')->group(function(){
		Route::get('/data', getController('UtilController', 'getData'));
	});
});
