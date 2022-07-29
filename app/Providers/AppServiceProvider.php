<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\ProductType;
use App\Models\Cart;
use App\Models\Wishlist;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Paginator::useBootstrap();
        View::composer(['layout.header','banhang.product_type'],function($view){
            $producttypes=ProductType::all();
            $view->with(compact('producttypes'));
        });

        View::composer(['layout.header','banhang.checkout'],function($view){
            if(Session('cart')){
                $oldCart=Session::get('cart'); //session cart được tạo trong method addToCart của PageController
                $cart=new Cart($oldCart);
                $view->with(['cart'=>Session::get('cart'),'productCarts'=>$cart->items,
                'totalPrice'=>$cart->totalPrice,'totalQty'=>$cart->totalQty]);
            }
        });

        //wishlist
        View::composer(['layout.header'],function($view){
            if (Session('user')) {
                $user = Session::get('user');
                $wishlists = Wishlist::where('id_user', $user->id)->get();
                $sumWishlist = 0;
                $totalWishlist = 0;
                $productsInWishlist = [];
                if (isset($wishlists)) {
                    foreach ($wishlists as $item) {
                        $sumWishlist += $item->quantity;
                        $product = Product::find($item->id_product);
                        $productsInWishlist[] = $product;
                        if ($product->promotion_price == 0) {
                            $totalWishlist += (intval($item->quantity) * intval($product->unit_price));
                        } else {
                            $totalWishlist += (intval($item->quantity) * intval($product->promotion_price));
                        }
                    }
                }

                $view->with(['user' => $user, 'wishlists' => $wishlists, 'sumWishlist' => $sumWishlist, 'productsInWishlist' => $productsInWishlist, 'totalWishlist' => $totalWishlist]);
            }
        });
       
    }
}
