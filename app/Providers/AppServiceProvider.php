<?php

namespace App\Providers;

use App\Interfaces\ContractServiceInterface;
use App\Interfaces\PhongAssetServiceInterface;
use App\Interfaces\PhongServiceInterface;
use App\Interfaces\PhongSupplyServiceInterface;
use App\Interfaces\RegistrationServiceInterface;
use App\Interfaces\StudentServiceInterface;
use App\Repositories\Interfaces\PhongRepositoryInterface;
use App\Repositories\Interfaces\SinhvienRepositoryInterface;
use App\Repositories\PhongRepository;
use App\Repositories\SinhvienRepository;
use App\Models\Baohong;
use App\Models\Cauhinh;
use App\Models\Danhgia;
use App\Models\Hopdong;
use App\Models\Hoadon;
use App\Models\Kyluat;
use App\Models\Lichsubaotri;
use App\Models\Lienhe;
use App\Models\Phong;
use App\Models\Sinhvien;
use App\Models\Taisan;
use App\Models\Thongbao;
use App\Models\Vattu;
use App\Observers\BaohongObserver;
use App\Observers\CauhinhObserver;
use App\Observers\DanhgiaObserver;
use App\Observers\HopdongObserver;
use App\Observers\HoadonObserver;
use App\Observers\KyluatObserver;
use App\Observers\LichsubaotriObserver;
use App\Observers\LienheObserver;
use App\Observers\PhongObserver;
use App\Observers\SinhvienObserver;
use App\Observers\TaisanObserver;
use App\Observers\ThongbaoObserver;
use App\Observers\VattuObserver;
use App\Services\ContractService;
use App\Services\PhongAssetService;
use App\Services\PhongService;
use App\Services\PhongSupplyService;
use App\Services\RegistrationService;
use App\Services\StudentService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use App\View\Components\Badge;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(StudentServiceInterface::class, StudentService::class);
        $this->app->bind(RegistrationServiceInterface::class, RegistrationService::class);
        $this->app->bind(ContractServiceInterface::class, ContractService::class);
        $this->app->bind(PhongServiceInterface::class, PhongService::class);
        $this->app->bind(PhongAssetServiceInterface::class, PhongAssetService::class);
        $this->app->bind(PhongSupplyServiceInterface::class, PhongSupplyService::class);
        // Repository bindings
        $this->app->bind(SinhvienRepositoryInterface::class, SinhvienRepository::class);
        $this->app->bind(PhongRepositoryInterface::class, PhongRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sinhvien::observe(SinhvienObserver::class);
        Hopdong::observe(HopdongObserver::class);
        Hoadon::observe(HoadonObserver::class);
        Phong::observe(PhongObserver::class);
        Taisan::observe(TaisanObserver::class);
        Vattu::observe(VattuObserver::class);
        Kyluat::observe(KyluatObserver::class);
        Danhgia::observe(DanhgiaObserver::class);
        Lichsubaotri::observe(LichsubaotriObserver::class);
        Lienhe::observe(LienheObserver::class);
        Cauhinh::observe(CauhinhObserver::class);
        Baohong::observe(BaohongObserver::class);
        Thongbao::observe(ThongbaoObserver::class);

        Blade::directive('badge', function ($expression) {
            return "<?php echo \\App\\View\\Components\\Badge::renderDirect($expression); ?>";
        });
    }
}
