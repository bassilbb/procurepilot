<?php

use App\Http\Controllers\AuditController;
use App\Http\Controllers\AwardController;
use App\Http\Controllers\BidController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProcurementPlanController;
use App\Http\Controllers\ProcurementRequestController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleAccessController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TenderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkflowController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::get('/about', [PublicPageController::class, 'about'])->name('about');
Route::get('/features', [PublicPageController::class, 'features'])->name('features');
Route::get('/how-it-works', [PublicPageController::class, 'howItWorks'])->name('how-it-works');
Route::get('/faq', [PublicPageController::class, 'faq'])->name('faq');
Route::get('/contact', [PublicPageController::class, 'contact'])->name('contact');
Route::post('/contact', [PublicPageController::class, 'contactSubmit'])->name('contact.submit');
Route::get('/help', [PublicPageController::class, 'help'])->name('help');
Route::get('/privacy', [PublicPageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PublicPageController::class, 'terms'])->name('terms');

// Payment gateway webhooks (called by Paystack / Flutterwave / Mono servers)
Route::post('/billing/webhook/{gateway}', [BillingController::class, 'webhook'])->name('billing.webhook');

// Public: suppliers / clients can download the organisation's registration requirements as PDF
Route::get('/supplier-requirements.pdf', [SettingsController::class, 'publicRequirementsPdf'])->name('public.requirements.pdf');
Route::get('/supplier-requirements', [SettingsController::class, 'publicRequirements'])->name('public.requirements');

    Route::middleware(['auth', 'verified'])->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Billing (accessible even without an active subscription)
        Route::middleware('permission:billing')->group(function () {
            Route::get('/billing/plan', [BillingController::class, 'plans'])->name('billing.plan');
            Route::get('/billing/checkout/{plan}', [BillingController::class, 'choosePlan'])->name('billing.checkout');
            Route::post('/billing/subscribe/{plan}', [BillingController::class, 'subscribe'])->name('billing.subscribe');
            Route::get('/billing/callback/{gateway}', [BillingController::class, 'callback'])->name('billing.callback');
            Route::get('/billing/invoices', [BillingController::class, 'invoices'])->name('billing.invoices');
            Route::get('/billing/invoices/{invoice}', [BillingController::class, 'invoiceShow'])->name('billing.invoice-show');
            Route::get('/billing/payments', [BillingController::class, 'payments'])->name('billing.payments');
            Route::post('/billing/cancel', [BillingController::class, 'cancel'])->name('billing.cancel');
            Route::post('/billing/resume', [BillingController::class, 'resume'])->name('billing.resume');
        });

        Route::middleware('subscription')->group(function () {

            Route::middleware('role:superadmin,admin,procurement,approver,auditor,staff')->group(function () {

                Route::middleware('permission:suppliers')->group(function () {
                    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
                    Route::middleware('permission:suppliers,create')->group(function () {
                        Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
                        Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
                    });
                    Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
                    Route::middleware('permission:suppliers,edit')->group(function () {
                        Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
                        Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
                        Route::post('/suppliers/{supplier}/status', [SupplierController::class, 'setStatus'])->name('suppliers.status');
                    });
                    Route::post('/suppliers/{supplier}/approve', [SupplierController::class, 'approve'])->name('suppliers.approve')->middleware('permission:suppliers,approve');
                    Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy')->middleware('permission:suppliers,delete');
                    Route::get('/supplier-documents/{document}/download', [SupplierController::class, 'downloadDocument'])->name('supplier-documents.download');
                    Route::delete('/supplier-documents/{document}', [SupplierController::class, 'destroyDocument'])->name('supplier-documents.destroy')->middleware('permission:suppliers,delete');
                });

                Route::middleware('permission:categories')->group(function () {
                    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
                    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
                    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
                    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
                });

                Route::middleware('permission:plans')->group(function () {
                    Route::get('/plans', [ProcurementPlanController::class, 'index'])->name('plans.index');            Route::get('/plans/create', [ProcurementPlanController::class, 'create'])->name('plans.create');
                    Route::post('/plans', [ProcurementPlanController::class, 'store'])->name('plans.store');
                    Route::get('/plans/{plan}', [ProcurementPlanController::class, 'show'])->name('plans.show');
                    Route::get('/plans/{plan}/edit', [ProcurementPlanController::class, 'edit'])->name('plans.edit');
                    Route::put('/plans/{plan}', [ProcurementPlanController::class, 'update'])->name('plans.update');
                    Route::post('/plans/{plan}/items', [ProcurementPlanController::class, 'storeItem'])->name('plans.items.store');
                    Route::delete('/plans/items/{item}', [ProcurementPlanController::class, 'destroyItem'])->name('plans.items.destroy');
                    Route::post('/plans/{plan}/submit', [ProcurementPlanController::class, 'submit'])->name('plans.submit');
                    Route::post('/plans/{plan}/approve', [ProcurementPlanController::class, 'approve'])->name('plans.approve');
                    Route::post('/plans/{plan}/reject', [ProcurementPlanController::class, 'reject'])->name('plans.reject');
                    Route::delete('/plans/{plan}', [ProcurementPlanController::class, 'destroy'])->name('plans.destroy');
                });

                Route::middleware('permission:requests')->group(function () {
                    Route::get('/requests', [ProcurementRequestController::class, 'index'])->name('requests.index');
                    Route::get('/requests/create', [ProcurementRequestController::class, 'create'])->name('requests.create');
                    Route::post('/requests', [ProcurementRequestController::class, 'store'])->name('requests.store');
                    Route::get('/requests/{request}', [ProcurementRequestController::class, 'show'])->name('requests.show');
                    Route::get('/requests/{request}/edit', [ProcurementRequestController::class, 'edit'])->name('requests.edit');
                    Route::put('/requests/{request}', [ProcurementRequestController::class, 'update'])->name('requests.update');
                    Route::post('/requests/{request}/submit', [ProcurementRequestController::class, 'submit'])->name('requests.submit');
                    Route::post('/requests/{request}/approve', [ProcurementRequestController::class, 'approve'])->name('requests.approve');
                    Route::post('/requests/{request}/reject', [ProcurementRequestController::class, 'reject'])->name('requests.reject');
                    Route::delete('/requests/{request}', [ProcurementRequestController::class, 'destroy'])->name('requests.destroy');
                });

                Route::middleware('permission:invoices')->group(function () {
                    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
                    Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
                    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
                    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
                    Route::post('/invoices/{invoice}/match', [InvoiceController::class, 'match'])->name('invoices.match');
                    Route::post('/invoices/{invoice}/verify', [InvoiceController::class, 'verify'])->name('invoices.verify');
                    Route::post('/invoices/{invoice}/approve', [InvoiceController::class, 'approve'])->name('invoices.approve');
                    Route::post('/invoices/{invoice}/pay', [InvoiceController::class, 'pay'])->name('invoices.pay');
                    Route::post('/invoices/{invoice}/reject', [InvoiceController::class, 'reject'])->name('invoices.reject');
                    Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
                });

                Route::middleware('permission:budgets')->group(function () {
                    Route::get('/budgets', [BudgetController::class, 'index'])->name('budgets.index');
                    Route::get('/budgets/create', [BudgetController::class, 'create'])->name('budgets.create');
                    Route::post('/budgets', [BudgetController::class, 'store'])->name('budgets.store');
                    Route::get('/budgets/{budget}', [BudgetController::class, 'show'])->name('budgets.show');
                    Route::get('/budgets/{budget}/edit', [BudgetController::class, 'edit'])->name('budgets.edit');
                    Route::put('/budgets/{budget}', [BudgetController::class, 'update'])->name('budgets.update');
                    Route::post('/budgets/{budget}/commit', [BudgetController::class, 'commit'])->name('budgets.commit');
                    Route::post('/budgets/{budget}/release', [BudgetController::class, 'release'])->name('budgets.release');
                    Route::delete('/budgets/{budget}', [BudgetController::class, 'destroy'])->name('budgets.destroy');
                });

                Route::middleware('permission:reports')->group(function () {
                    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
                    Route::get('/reports/export/{format}', [ReportController::class, 'export'])->name('reports.export');
                });

                Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
                Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
                Route::post('/notifications/{notification}/mark', [NotificationController::class, 'markRead'])->name('notifications.read-mark');
                Route::get('/notifications/{notification}/open', [NotificationController::class, 'read'])->name('notifications.read');

                Route::middleware('permission:workflow')->group(function () {
                    Route::get('/workflow', [WorkflowController::class, 'index'])->name('workflow.index');
                    Route::post('/workflow/departments', [WorkflowController::class, 'storeDepartment'])->name('workflow.departments.store');
                    Route::put('/workflow/departments/{department}', [WorkflowController::class, 'updateDepartment'])->name('workflow.departments.update');
                    Route::delete('/workflow/departments/{department}', [WorkflowController::class, 'destroyDepartment'])->name('workflow.departments.destroy');
                    Route::post('/workflow/levels', [WorkflowController::class, 'storeLevel'])->name('workflow.levels.store');
                    Route::put('/workflow/levels/{level}', [WorkflowController::class, 'updateLevel'])->name('workflow.levels.update');
                    Route::delete('/workflow/levels/{level}', [WorkflowController::class, 'destroyLevel'])->name('workflow.levels.destroy');
                });

                Route::middleware('permission:tenders')->group(function () {
                    Route::get('/tenders', [TenderController::class, 'index'])->name('tenders.index');
                    Route::get('/tenders/create', [TenderController::class, 'create'])->name('tenders.create');
                    Route::post('/tenders', [TenderController::class, 'store'])->name('tenders.store');
                    Route::get('/tenders/{tender}', [TenderController::class, 'show'])->name('tenders.show');
                    Route::get('/tenders/{tender}/edit', [TenderController::class, 'edit'])->name('tenders.edit');
                    Route::put('/tenders/{tender}', [TenderController::class, 'update'])->name('tenders.update');
                    Route::post('/tenders/{tender}/items', [TenderController::class, 'storeItem'])->name('tenders.items.store');
                    Route::delete('/tenders/items/{item}', [TenderController::class, 'destroyItem'])->name('tenders.items.destroy');
                    Route::post('/tenders/{tender}/criteria', [TenderController::class, 'storeCriterion'])->name('tenders.criteria.store');
                    Route::delete('/tenders/criteria/{criterion}', [TenderController::class, 'destroyCriterion'])->name('tenders.criteria.destroy');
                    Route::post('/tenders/{tender}/suppliers', [TenderController::class, 'inviteSupplier'])->name('tenders.suppliers.store');
                    Route::delete('/tenders/{tender}/suppliers/{supplier}', [TenderController::class, 'removeSupplier'])->name('tenders.suppliers.destroy');
                    Route::post('/tenders/{tender}/publish', [TenderController::class, 'publish'])->name('tenders.publish');
                    Route::post('/tenders/{tender}/close', [TenderController::class, 'close'])->name('tenders.close');
                    Route::post('/tenders/{tender}/evaluate', [TenderController::class, 'beginEvaluation'])->name('tenders.evaluate-start');
                    Route::get('/tenders/{tender}/evaluate', [TenderController::class, 'evaluate'])->name('tenders.evaluate');
                    Route::post('/tenders/{tender}/cancel', [TenderController::class, 'cancel'])->name('tenders.cancel');
                    Route::delete('/tenders/{tender}', [TenderController::class, 'destroy'])->name('tenders.destroy');
                });

                Route::middleware('permission:tenders')->group(function () {
                    Route::post('/tenders/{tender}/bids', [BidController::class, 'store'])->name('bids.store');
                    Route::get('/bids/{bid}', [BidController::class, 'show'])->name('bids.show');
                    Route::post('/bids/{bid}/score', [BidController::class, 'score'])->name('bids.score');
                    Route::post('/bids/{bid}/recommend', [BidController::class, 'recommendAward'])->name('bids.recommend');
                    Route::post('/bids/{bid}/withdraw', [BidController::class, 'withdraw'])->name('bids.withdraw');
                });

                Route::middleware('permission:awards')->group(function () {
                    Route::get('/awards', [AwardController::class, 'index'])->name('awards.index');
                    Route::get('/awards/{award}', [AwardController::class, 'show'])->name('awards.show');
                    Route::post('/awards/{award}/approve', [AwardController::class, 'approve'])->name('awards.approve');
                    Route::post('/awards/{award}/decline', [AwardController::class, 'decline'])->name('awards.decline');
                    Route::post('/awards/{award}/contract', [AwardController::class, 'createContract'])->name('awards.contract');
                });

                Route::middleware('permission:contracts')->group(function () {
                    Route::get('/contracts', [ContractController::class, 'index'])->name('contracts.index');
                    Route::get('/contracts/create', [ContractController::class, 'create'])->name('contracts.create');
                    Route::post('/contracts', [ContractController::class, 'store'])->name('contracts.store');
                    Route::get('/contracts/{contract}', [ContractController::class, 'show'])->name('contracts.show');
                    Route::get('/contracts/{contract}/edit', [ContractController::class, 'edit'])->name('contracts.edit');
                    Route::put('/contracts/{contract}', [ContractController::class, 'update'])->name('contracts.update');
                    Route::post('/contracts/{contract}/activate', [ContractController::class, 'activate'])->name('contracts.activate');
                    Route::post('/contracts/{contract}/complete', [ContractController::class, 'complete'])->name('contracts.complete');
                    Route::post('/contracts/{contract}/terminate', [ContractController::class, 'terminate'])->name('contracts.terminate');
                    Route::post('/contracts/{contract}/milestones', [ContractController::class, 'storeMilestone'])->name('contracts.milestones.store');
                    Route::post('/contracts/milestones/{milestone}/complete', [ContractController::class, 'completeMilestone'])->name('contracts.milestones.complete');
                    Route::delete('/contracts/milestones/{milestone}', [ContractController::class, 'destroyMilestone'])->name('contracts.milestones.destroy');
                    Route::post('/contracts/{contract}/documents', [ContractController::class, 'uploadDocument'])->name('contracts.documents.store');
                    Route::get('/contract-documents/{document}/download', [ContractController::class, 'downloadDocument'])->name('contracts.documents.download');
                    Route::delete('/contract-documents/{document}', [ContractController::class, 'destroyDocument'])->name('contracts.documents.destroy');
                    Route::delete('/contracts/{contract}', [ContractController::class, 'destroy'])->name('contracts.destroy');
                });

                Route::middleware('permission:purchase-orders')->group(function () {
                    Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
                    Route::get('/purchase-orders/create', [PurchaseOrderController::class, 'create'])->name('purchase-orders.create');
                    Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])->name('purchase-orders.store');
                    Route::get('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('purchase-orders.show');
                    Route::post('/purchase-orders/{purchaseOrder}/issue', [PurchaseOrderController::class, 'issue'])->name('purchase-orders.issue');
                    Route::post('/purchase-orders/{purchaseOrder}/approve', [PurchaseOrderController::class, 'approve'])->name('purchase-orders.approve');
                    Route::post('/purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');
                    Route::post('/purchase-orders/{purchaseOrder}/cancel', [PurchaseOrderController::class, 'cancel'])->name('purchase-orders.cancel');
                    Route::delete('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'destroy'])->name('purchase-orders.destroy');
                });

                Route::middleware('permission:users')->group(function () {
                    Route::get('/users', [UserController::class, 'index'])->name('users.index');
                    Route::post('/users', [UserController::class, 'store'])->name('users.store');
                    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
                    Route::post('/users/{user}/toggle', [UserController::class, 'toggleActive'])->name('users.toggle');
                    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
                });

                Route::middleware('permission:audit')->group(function () {
                    Route::get('/audit-logs', [AuditController::class, 'index'])->name('audit.index');
                });

                Route::middleware('permission:compliance')->group(function () {
                    Route::get('/compliance', [AuditController::class, 'compliance'])->name('audit.compliance');
                });

                Route::middleware('permission:settings')->group(function () {
                    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
                    Route::put('/settings/organization', [SettingsController::class, 'updateOrganization'])->name('settings.organization.update');
                    Route::put('/settings/gateways', [SettingsController::class, 'updateGateways'])->name('settings.gateways.update');
                    Route::post('/settings/supplier-requirements', [SettingsController::class, 'storeRequirement'])->name('settings.requirements.store');
                    Route::put('/settings/supplier-requirements/{requirement}', [SettingsController::class, 'updateRequirement'])->name('settings.requirements.update');
                    Route::delete('/settings/supplier-requirements/{requirement}', [SettingsController::class, 'destroyRequirement'])->name('settings.requirements.destroy');
                    Route::get('/settings/supplier-requirements/pdf', [SettingsController::class, 'requirementsPdf'])->name('settings.requirements.pdf');
                });

                // Role & access management — super admin only
                Route::middleware('role:superadmin')->group(function () {
                    Route::get('/access-control', [RoleAccessController::class, 'index'])->name('access-control.index');
                    Route::put('/access-control', [RoleAccessController::class, 'update'])->name('access-control.update');
                });
            });

            // Profile routes (accessible without active subscription)
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        });
    });

require __DIR__.'/auth.php';
