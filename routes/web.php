<?php

use App\Http\Controllers\Backend\Accounts\Ledger\LedgerController;
use App\Http\Controllers\Backend\Accounts\Master_Ledger\MasterLedgerController;
use App\Http\Controllers\Backend\Accounts\Sub_Ledger\SubLedgerController;
use App\Http\Controllers\Backend\Accounts\Transaction\TransactionController;
use App\Http\Controllers\Backend\Admin\AdminController;
use App\Http\Controllers\Backend\Customer\CustomerController;
use App\Http\Controllers\Backend\Student\ExamRoutine_controller;
use App\Http\Controllers\Backend\Supplier\Supplier_invoiceController;
use App\Http\Controllers\Backend\Supplier\Supplier_returnController;
use App\Http\Controllers\Backend\Customer\InvoiceController;
use App\Http\Controllers\Backend\Customer\PackageController;
use App\Http\Controllers\Backend\Customer\PoolController;
use App\Http\Controllers\Backend\Customer\TicketController;
use App\Http\Controllers\Backend\Pop\PopController;
use App\Http\Controllers\Backend\Pop\Area\AreaController;
use App\Http\Controllers\Backend\Product\BrandController;
use App\Http\Controllers\Backend\Product\CategoryController;
use App\Http\Controllers\Backend\Product\SubCateogryController;
use App\Http\Controllers\Backend\Product\ColorController;
use App\Http\Controllers\Backend\Product\ProductController;
use App\Http\Controllers\Backend\Product\TempImageController;
use App\Http\Controllers\Backend\Product\ChildCategoryController;
use App\Http\Controllers\Backend\Product\SizeController;
use App\Http\Controllers\Backend\Product\StockController;
use App\Http\Controllers\Backend\Product\StoreController;
use App\Http\Controllers\Backend\Product\UnitController;
use App\Http\Controllers\Backend\Router\RouterController;
use App\Http\Controllers\Backend\Settings\Others\SettingsController;
use App\Http\Controllers\Backend\Sms\SmsController;
use App\Http\Controllers\Backend\Supplier\SupplierController;
use App\Http\Controllers\Backend\Teacher\TeacherAttendance_controller;
use App\Http\Controllers\Backend\Teacher\TeacherController;
use App\Http\Controllers\Backend\Teacher\Transaction\TeacherTransaction_controller;
use App\Http\Controllers\Backend\Tickets\Assign_controller;
use App\Http\Controllers\Backend\Tickets\Complain_typeController;
use App\Http\Controllers\Backend\Tickets\Ticket_controller;
use App\Models\Product_Category;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use RouterOS\Client;
use RouterOS\Query;
/*Backend Route*/
Route::get('/admin/login', [AdminController::class, 'login_form'])->name('admin.login');
Route::post('login-functionality',[AdminController::class,'login_functionality'])->name('login.functionality');
Route::group(['middleware'=>'admin'],function(){
    Route::get('/',[AdminController::class,'dashboard'])->name('admin.dashboard');
    Route::get('admin/logout',[AdminController::class,'logout'])->name('admin.logout');
    Route::post('/admin/get_dashboard_data',[AdminController::class,'get_data'])->name('admin.dashboard_get_all_data');


     /** Tickets  Route **/
     Route::prefix('admin/ticket')->group(function(){
        /*Complain Type */
        Route::prefix('complain_type')->group(function(){
            Route::controller(Complain_typeController::class)->group(function(){
                Route::get('/list', 'index')->name('admin.tickets.complain_type.index');
                Route::get('/all-data', 'get_all_data')->name('admin.tickets.complain_type.get_all_data');
                Route::get('/edit/{id}', 'edit')->name('admin.tickets.complain_type.edit');
                Route::post('/delete', 'delete')->name('admin.tickets.complain_type.delete');
                Route::post('/store', 'store')->name('admin.tickets.complain_type.store');
                Route::post('/update/{id}', 'update')->name('admin.tickets.complain_type.update');
            });
        });
        /*Assign To */
        Route::prefix('assign')->group(function(){
            Route::controller(Assign_controller::class)->group(function(){
                Route::get('/list', 'index')->name('admin.tickets.assign.index');
                Route::get('/all-data', 'get_all_data')->name('admin.tickets.assign.get_all_data');
                Route::get('/edit/{id}', 'edit')->name('admin.tickets.assign.edit');
                Route::post('/delete', 'delete')->name('admin.tickets.assign.delete');
                Route::post('/store', 'store')->name('admin.tickets.assign.store');
                Route::post('/update/{id}', 'update')->name('admin.tickets.assign.update');

            });
        });
         /*Ticket Route To */
        Route::controller(Ticket_controller::class)->group(function(){
            Route::get('/list', 'index')->name('admin.tickets.index');
            Route::get('/all-data', 'get_all_data')->name('admin.tickets.get_all_data');
            Route::get('/edit/{id}', 'edit')->name('admin.tickets.edit');
            Route::post('/delete', 'delete')->name('admin.tickets.delete');
            Route::post('/store', 'store')->name('admin.tickets.store');
            Route::post('/update/{id}', 'update')->name('admin.tickets.update');
            Route::post('/change_status/{id}', 'change_status')->name('admin.tickets.change_status');
        });
     });
    /** Accounts Management  Route **/
    Route::prefix('admin/accounts')->group(function(){

        /** Master Ledger Route **/
        Route::prefix('master_ledger')->group(function(){
            Route::controller(MasterLedgerController::class)->group(function(){
                Route::get('/list','index')->name('admin.master_ledger.index');
                Route::get('/get_all_data','get_all_data')->name('admin.master_ledger.all_data');
                Route::get('/edit/{id}','edit')->name('admin.master_ledger.edit');
                Route::post('/update','update')->name('admin.master_ledger.update');
                Route::post('/store','store')->name('admin.master_ledger.store');
                Route::post('/delete','delete')->name('admin.master_ledger.delete');
            });
        });
        /**Ledger Route **/
        Route::prefix('ledger')->group(function(){
            Route::controller(LedgerController::class)->group(function(){
                Route::get('/list','index')->name('admin.ledger.index');
                Route::get('/get_all_data','get_all_data')->name('admin.ledger.all_data');
                Route::get('/edit/{id}','edit')->name('admin.ledger.edit');
                Route::post('/store','store')->name('admin.ledger.store');
                Route::post('/update','update')->name('admin.ledger.update');
                Route::post('/delete','delete')->name('admin.ledger.delete');
                /*get  ledger from master ledger id*/
                Route::get('/get/{id}','get_ledger')->name('admin.ledger.get_ledger');
            });
        });
        /**Sub Ledger Route **/
        Route::prefix('sub_ledger')->group(function(){
            Route::controller(SubLedgerController::class)->group(function(){
                Route::get('/list','index')->name('admin.sub_ledger.index');
                Route::get('/get_all_data','get_all_data')->name('admin.sub_ledger.all_data');
                Route::get('/edit/{id}','edit')->name('admin.sub_ledger.edit');
                Route::post('/store','store')->name('admin.sub_ledger.store');
                Route::post('/update','update')->name('admin.sub_ledger.update');
                Route::post('/delete','delete')->name('admin.sub_ledger.delete');
                /*get sub ledger from ledger id*/
                Route::get('/get/{id}','get_sub_ledger')->name('admin.sub_ledger.get_sub_ledger');
            });
        });
        /*Transaction Route*/
        Route::prefix('transaction')->group(function(){
            Route::controller(TransactionController::class)->group(function(){
                Route::get('/list','index')->name('admin.transaction.index');
                Route::post('/store','store')->name('admin.transaction.store');
                Route::get('/report','transaction_report')->name('admin.transaction.report.index');
                Route::post('/report_generate','report_generate')->name('admin.accounts.transaction.report_generate');
                Route::get('/show','show_account_transaction')->name('admin.transaction.show');
                Route::post('/finished','finished_account_transaction')->name('admin.transaction.finished');
            });
        });
    });
    /** Customer Route **/
    Route::prefix('admin/customer')->group(function() {
        Route::controller(CustomerController::class)->group(function() {
            Route::get('/create', 'create')->name('admin.customer.create');
            Route::get('/list', 'index')->name('admin.customer.index');
            Route::get('/all-data', 'get_all_data')->name('admin.customer.get_all_data');
            Route::get('/edit/{id}', 'edit')->name('admin.customer.edit');
            Route::get('/view/{id}', 'view')->name('admin.customer.view');
            Route::post('/delete', 'delete')->name('admin.customer.delete');
            Route::post('/store', 'store')->name('admin.customer.store');
            Route::post('/update/{id}', 'update')->name('admin.customer.update');

            /***** Customer Recharge *******/
            Route::post('/recharge/store', 'customer_recharge')->name('admin.customer.recharge.store');
            Route::get('/recharge/undo/{id}', 'customer_recharge_undo')->name('admin.customer.recharge.undo');
            /***** Customer Payment History *******/
            Route::get('/payment/history','customer_payment_history')->name('admin.customer.payment.history');
            Route::get('/payment/history/get_all_data','customer_payment_history_get_all_data')->name('admin.customer.payment.history.get_all_data');
             /***** Customer Log *******/
            Route::get('/customer/log', 'customer_log')->name('admin.customer.log.index');
            Route::get('/customer/log/get_all_data', 'customer_log_get_all_data')->name('admin.customer.log.get_all_data');
            /***** Customer Credit Recharge List *******/
            Route::get('/credit/recharge/list','customer_credit_recharge_list')->name('admin.customer.customer_credit_recharge_list');
            /***** Customer Backup *******/
             Route::get('/customer/restore', 'customer_restore')->name('admin.customer.restore.index');
             Route::get('/customer/restore/get_all_data', 'customer_restore_get_all_data')->name('admin.customer.restore.get_all_data');
             Route::post('/customer/restore/back', 'customer_restore_back')->name('admin.customer.restore.back');
              /***** Customer Import *******/
              Route::get('/import/index','customer_import')->name('admin.customer.customer_import');
              Route::post('/import/store','customer_csv_file_import')->name('admin.customer.customer_csv_file_import');
              Route::get('/delete-csv-file/{file}', [CustomerController::class, 'delete_csv_file'])->name('admin.customer.delete_csv_file');
              Route::get('/upload/csv-file', [CustomerController::class, 'upload_csv_file'])->name('admin.customer.upload_csv_file');
               /***** Customer Mikrotik Re-connect *******/
               Route::get('/mikrotik/reconnect/{customer_id}','customer_mikrotik_reconnect')->name('admin.customer.mikrotik.reconnect');
        });
        /* IP POOL Route */
        Route::prefix('ip-pool')->group(function() {
            Route::controller(PoolController::class)->group(function() {
                Route::get('/list', 'index')->name('admin.customer.ip_pool.index');
                Route::get('/all-data', 'get_all_data')->name('admin.customer.ip_pool.get_all_data');
                Route::get('/edit/{id}', 'edit')->name('admin.customer.ip_pool.edit');
                Route::get('/view/{id}', 'pop_view')->name('admin.customer.ip_pool.view');
                Route::post('/delete', 'delete')->name('admin.customer.ip_pool.delete');
                Route::post('/store', 'store')->name('admin.customer.ip_pool.store');
                Route::post('/update/{id}', 'update')->name('admin.customer.ip_pool.update');
            });
        });
        /* Package Route */
        Route::prefix('package')->group(function() {
            Route::controller(PackageController::class)->group(function() {
                Route::get('/list', 'index')->name('admin.customer.package.index');
                Route::get('/all-data', 'get_all_data')->name('admin.customer.package.get_all_data');
                Route::get('/edit/{id}', 'edit')->name('admin.customer.package.edit');
                Route::get('/view/{id}', 'pop_view')->name('admin.customer.package.view');
                Route::post('/delete', 'delete')->name('admin.customer.package.delete');
                Route::post('/store', 'store')->name('admin.customer.package.store');
                Route::post('/update/{id}', 'update')->name('admin.customer.package.update');
            });
        });
    });
    /** Supplier Route **/
    Route::prefix('admin/supplier')->group(function(){
        Route::controller(SupplierController::class)->group(function(){
            Route::get('/list', 'index')->name('admin.supplier.index');
            Route::get('/all-data', 'get_all_data')->name('admin.supplier.get_all_data');
            Route::get('/create', 'create')->name('admin.supplier.create');
            Route::get('/edit/{id}', 'edit')->name('admin.supplier.edit');
            Route::get('/view/{id}', 'view')->name('admin.supplier.view');
            Route::post('/delete', 'delete')->name('admin.supplier.delete');
            Route::post('/store', 'store')->name('admin.supplier.store');
            Route::post('/update/{id}', 'update')->name('admin.supplier.update');
        });
        /** Supplier Invoice Route **/
        Route::prefix('invoice')->controller(Supplier_invoiceController::class)->group(function() {
            Route::get('/create', 'create_invoice')->name('admin.supplier.invoice.create_invoice');
            Route::get('/get_all_data', 'show_invoice_data')->name('admin.supplier.invoice.show_invoice_data');
            Route::post('/search_data', 'search_product_data')->name('admin.supplier.invoice.search_product_data');
            Route::get('/show', 'show_invoice')->name('admin.supplier.invoice.show_invoice');
            Route::post('/pay', 'pay_due_amount')->name('admin.supplier.invoice.pay_due_amount');
            Route::post('/store', 'store_invoice')->name('admin.supplier.invoice.store_invoice');
            Route::get('/view/{id}', 'view_invoice')->name('admin.supplier.invoice.view_invoice');
            Route::get('/edit/{id}', 'edit_invoice')->name('admin.supplier.invoice.edit_invoice');
            Route::post('/update', 'update_invoice')->name('admin.supplier.invoice.update_invoice');
            Route::post('/delete', 'delete_invoice')->name('admin.supplier.invoice.delete_invoice');
        });
    });

    /* Product Route */
    Route::prefix('admin/product')->group(function() {
        /* Sub Category Route */
        Route::prefix('sub-category')->controller(SubCateogryController::class)->group(function() {
            Route::get('/', 'index')->name('admin.subcategory.index');
            Route::post('/store', 'store')->name('admin.subcategory.store');
            Route::get('/edit/{id}', 'edit')->name('admin.subcategory.edit');
            Route::post('/delete', 'delete')->name('admin.subcategory.delete');
            Route::post('/update/{id}', 'update')->name('admin.subcategory.update');
            Route::get('/get-sub_category/{id}', 'get_sub_category');
        });

        /* Child Category Route */
        Route::prefix('child-category')->controller(ChildCategoryController::class)->group(function() {
            Route::get('/', 'index')->name('admin.childcategory.index');
            Route::post('/store', 'store')->name('admin.childcategory.store');
            Route::get('/edit/{id}', 'edit')->name('admin.childcategory.edit');
            Route::post('/delete', 'delete')->name('admin.childcategory.delete');
            Route::post('/update/{id}', 'update')->name('admin.childcategory.update');
            Route::get('/get-child_category/{id}', 'get_child_category');
        });

        /** Product Color Management Route **/
        Route::prefix('color')->controller(ColorController::class)->group(function() {
            Route::get('/', 'index')->name('admin.product.color.index');
            Route::get('/get_all_data', 'get_all_data')->name('admin.product.color.all_data');
            Route::post('/store', 'store')->name('admin.product.color.store');
            Route::get('/edit/{id}', 'edit')->name('admin.product.color.edit');
            Route::post('/update', 'update')->name('admin.product.color.update');
            Route::post('/delete', 'delete')->name('admin.product.color.delete');
        });

        /** Product Size Management Route **/
        Route::prefix('size')->controller(SizeController::class)->group(function() {
            Route::get('/', 'index')->name('admin.product.size.index');
            Route::get('/get_all_data', 'get_all_data')->name('admin.product.size.all_data');
            Route::post('/store', 'store')->name('admin.product.size.store');
            Route::get('/edit/{id}', 'edit')->name('admin.product.size.edit');
            Route::post('/update', 'update')->name('admin.product.size.update');
            Route::post('/delete', 'delete')->name('admin.product.size.delete');
        });
         /** Product Unit Management Route **/
        Route::prefix('unit')->controller(UnitController::class)->group(function() {
            Route::get('/list', 'index')->name('admin.unit.index');
            Route::get('/all-data', 'get_all_data')->name('admin.unit.get_all_data');
            Route::get('/edit/{id}', 'edit')->name('admin.unit.edit');
            Route::post('/delete', 'delete')->name('admin.unit.delete');
            Route::post('/store', 'store')->name('admin.unit.store');
            Route::post('/update/{id}', 'update')->name('admin.unit.update');
        });
         /** Product Category Management Route **/
        Route::prefix('category')->controller(CategoryController::class)->group(function() {
            Route::get('/list', 'index')->name('admin.category.index');
            Route::get('/all-data', 'get_all_data')->name('admin.category.get_all_data');
            Route::get('/edit/{id}', 'edit')->name('admin.category.edit');
            Route::post('/delete', 'delete')->name('admin.category.delete');
            Route::post('/store', 'store')->name('admin.category.store');
            Route::post('/update/{id}', 'update')->name('admin.category.update');
        });
         /** Product Brand Management Route **/
        Route::prefix('brand')->controller(BrandController::class)->group(function() {
            Route::get('/list', 'index')->name('admin.brand.index');
            Route::get('/all-data', 'get_all_data')->name('admin.brand.get_all_data');
            Route::get('/edit/{id}', 'edit')->name('admin.brand.edit');
            Route::post('/delete', 'delete')->name('admin.brand.delete');
            Route::post('/store', 'store')->name('admin.brand.store');
            Route::post('/update/{id}', 'update')->name('admin.brand.update');
        });
        /** Product Store Management Route **/
        Route::prefix('store')->controller(StoreController::class)->group(function() {
            Route::get('/list', 'index')->name('admin.store.index');
            Route::get('/all-data', 'get_all_data')->name('admin.store.get_all_data');
            Route::get('/edit/{id}', 'edit')->name('admin.store.edit');
            Route::post('/delete', 'delete')->name('admin.store.delete');
            Route::post('/store', 'store')->name('admin.store.store');
            Route::post('/update/{id}', 'update')->name('admin.store.update');
        });

        /* Product Route */
        Route::controller(ProductController::class)->group(function() {
            Route::get('/list', 'index')->name('admin.product.index');
            Route::get('/all-data', 'get_all_data')->name('admin.product.get_all_data');
            Route::get('/edit/{id}', 'edit')->name('admin.product.edit');
            Route::get('/view/{id}', 'product_view')->name('admin.product.view');
            Route::post('/delete', 'delete')->name('admin.product.delete');
            Route::post('/store', 'store')->name('admin.product.store');
            Route::post('/update/{id}', 'update')->name('admin.product.update');
            Route::post('/check_product_qty', 'check_product_qty')->name('admin.product.check_product_qty');
        });

        /* Product Image */
        Route::prefix('photo')->controller(ProductController::class)->group(function() {
            Route::post('/upload-temp-image', [TempImageController::class, 'create'])->name('tempimage.create');
            Route::post('/update', 'photo_update')->name('admin.product.photo.update');
            Route::post('/delete', 'delete_photo')->name('admin.product.delete.photo');
        });

        /* Stock Route */
        Route::get('/stock', [StockController::class, 'index'])->name('admin.product.stock.index');
    });
    /*  POP/Branch Route */
    Route::prefix('admin/pop-branch')->group(function() {
         /* POP/BRANCH Route */
         Route::controller(PopController::class)->group(function() {
            Route::get('/list', 'index')->name('admin.pop.index');
            Route::get('/all-data', 'get_all_data')->name('admin.pop.get_all_data');
            Route::get('/edit/{id}', 'edit')->name('admin.pop.edit');
            Route::get('/view/{id}', 'view')->name('admin.pop.view');
            Route::post('/delete', 'delete')->name('admin.pop.delete');
            Route::post('/store', 'store')->name('admin.pop.store');
            Route::post('/update/{id}', 'update')->name('admin.pop.update');

            Route::post('/change/status/{id}','pop_change_status')->name('admin.pop.change_status');

            /*****Branch Package *******/
            Route::post('/package/store', 'branch_package_store')->name('admin.pop.brnach.package.store');
            Route::get('/package/view/{id}', 'branch_package_edit')->name('admin.pop.branch.package.edit');
            Route::post('/package/update/{id}', 'branch_package_update')->name('admin.pop.branch.package.update');
            /*GET POP/BRANCH Area */
            Route::get('/package/{id}','get_pop_wise_package')->name('admin.pop.branch.get_pop_wise_package');
            Route::get('/package/price/{id}','get_pop_wise_package_price')->name('admin.pop.branch.get_pop_wise_package_price');

            /*****Branch Recharge *******/
            Route::post('/recharge/store', 'branch_recharge_store')->name('admin.pop.brnach.recharge.store');
            Route::get('/recharge/undo/{id}', 'branch_recharge_undo')->name('admin.pop.brnach.recharge.undo');

        });

         /* POP/Area Route */
        Route::prefix('area')->group(function() {
            Route::controller(AreaController::class)->group(function() {
                Route::get('/list', 'index')->name('admin.pop.area.index');
                Route::get('/all-data', 'get_all_data')->name('admin.pop.area.get_all_data');
                Route::get('/edit/{id}', 'edit')->name('admin.pop.area.edit');
                Route::get('/view/{id}', 'view')->name('admin.pop.area.view');
                Route::post('/delete', 'delete')->name('admin.pop.area.delete');
                Route::post('/store', 'store')->name('admin.pop.area.store');
                Route::post('/update/{id}', 'update')->name('admin.pop.area.update');

                /*GET POP/BRANCH Area */
                Route::get('/pop-brnach/{id}','get_pop_wise_area')->name('admin.pop.area.get_pop_wise_area');
                /* Change Area Status */
                Route::post('/change/status/{id}','area_change_status')->name('admin.pop.area.change_status');
            });
        });

    });
    /* SMS Management Route */
    Route::prefix('admin/sms')->group(function() {
        /* SMS Configration Route */
        Route::prefix('configration')->group(function() {
            Route::controller(SmsController::class)->group(function() {
                Route::get('/config', 'config')->name('admin.sms.config');
                Route::post('/config_store', 'config_store')->name('admin.sms.config.store');
            });
        });
        /* SMS Template Route */
        Route::prefix('template')->group(function() {
            Route::controller(SmsController::class)->group(function() {
                Route::get('/list', 'sms_template_list')->name('admin.sms.template_list');
                Route::get('/get_all_data', 'sms_template_get_all_data')->name('admin.sms.template_get_all_data');
                Route::post('/Store', 'sms_template_Store')->name('admin.sms.template_Store');
                Route::post('/delete', 'sms_template_delete')->name('admin.sms.template_delete');
                Route::get('/get/{id}', 'sms_template_get')->name('admin.sms.template_get');
            });
        });
        /* Send SMS Template Route */
        Route::prefix('send_message')->group(function() {
            Route::controller(SmsController::class)->group(function() {
                Route::get('/list', 'message_send_list')->name('admin.sms.message_send_list');
                Route::get('/get_all_data', 'send_message_get_all_data')->name('admin.sms.send_message_get_all_data');
                Route::post('/Store', 'send_message_store')->name('admin.sms.send_message_store');
                Route::post('/delete', 'send_message_delete')->name('admin.sms.send_message_delete');
            });
        });
    });
    /** Settings Management  Route **/
    Route::prefix('admin/settings/')->group(function(){
        Route::controller(SettingsController::class)->group(function(){
            Route::get('/information','index')->name('admin.settings.information.index');
            Route::post('/store', 'store')->name('admin.settings.information.store');
        });
    });
     /* Mikrotik Router Management Route */
    Route::prefix('admin/mikrotik')->group(function() {
        /* mikrotik Route */
        Route::prefix('router')->group(function() {
            Route::controller(RouterController::class)->group(function() {
                Route::get('/list', 'index')->name('admin.router.index');
                Route::get('/edit/{id}', 'edit')->name('admin.router.edit');
                Route::post('/update/{id}', 'update')->name('admin.router.update');
                Route::post('/delete', 'delete')->name('admin.router.delete');
                Route::post('/store', 'store')->name('admin.router.store');
                /* mikrotik Log */
                Route::get('/log', 'router_log')->name('admin.router.log.index');
                Route::get('/user-list/{router_id}', 'router_user_list')->name('admin.router.ppp.users.index');
            });
        });
    });
     /* Network Diagram Router Management Route */
    // Route::prefix('admin/network')->group(function() {

    //     Route::prefix('diagram')->group(function() {
    //         Route::controller(RouterController::class)->group(function() {
    //             Route::get('/list', 'index')->name('admin.router.index');
    //             Route::get('/edit/{id}', 'edit')->name('admin.router.edit');
    //             Route::post('/update/{id}', 'update')->name('admin.router.update');
    //             Route::post('/delete', 'delete')->name('admin.router.delete');
    //             Route::post('/store', 'store')->name('admin.router.store');
    //         });
    //     });
    // });
    Route::get('/optimize',function(){
        Artisan::call('optimize:clear');
        return 'Optimize Clear Completed';
    });
    Route::get('/admin/network/diagram',function(){
        return view('Backend.Pages.Network.diagram');
    })->name('admin.network.diagram');

});
