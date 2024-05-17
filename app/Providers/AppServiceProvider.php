<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        // SSL対応
        //\URL::forceScheme('https');
        /* インタフェースと実装の結合
         * インターフェイスを指定された実装に結合する
         */
        $this->app->bind(
            \App\Repositories\MSchool\MSchoolRepositoryInterface::class,
            \App\Repositories\MSchool\MSchoolRepository::class
        );

        $this->app->bind(
            \App\Repositories\MClass\MClassRepositoryInterface::class,
            \App\Repositories\MClass\MClassRepository::class
        );

        $this->app->bind(
            \App\Repositories\MPerson\MPersonRepositoryInterface::class,
            \App\Repositories\MPerson\MPersonRepository::class
        );
        
        $this->app->bind(
            \App\Repositories\MDirectory\MDirectoryRepositoryInterface::class,
            \App\Repositories\MDirectory\MDirectoryRepository::class
        );  
        
        $this->app->bind(
            \App\Repositories\TZipOrder\TZipOrderRepositoryInterface::class,
            \App\Repositories\TZipOrder\TZipOrderRepository::class
        );
        
        $this->app->bind(
            \App\Repositories\MGMKAI\MGMKAIRepositoryInterface::class,
            \App\Repositories\MGMKAI\MGMKAIRepository::class
        );
        
        $this->app->bind(
            \App\Repositories\MTestcount\MTestcountRepositoryInterface::class,
            \App\Repositories\MTestcount\MTestcountRepository::class
        );
        
        $this->app->bind(
            \App\Repositories\TTicket\TTicketRepositoryInterface::class,
            \App\Repositories\TTicket\TTicketRepository::class
        );

        $this->app->bind(
            \App\Repositories\MHighSchool\MHighSchoolRepositoryInterface::class,
            \App\Repositories\MHighSchool\MHighSchoolRepository::class
        );

        $this->app->bind(
            \App\Repositories\MUniversity\MUniversityRepositoryInterface::class,
            \App\Repositories\MUniversity\MUniversityRepository::class
        );

        $this->app->bind(
            \App\Repositories\MFaculty\MFacultyRepositoryInterface::class,
            \App\Repositories\MFaculty\MFacultyRepository::class
        );

        $this->app->bind(
            \App\Repositories\MDepartment\MDepartmentRepositoryInterface::class,
            \App\Repositories\MDepartment\MDepartmentRepository::class
        );

        $this->app->bind(
            \App\Repositories\MPrefecture\MPrefectureRepositoryInterface::class,
            \App\Repositories\MPrefecture\MPrefectureRepository::class
        );

        $this->app->bind(
            \App\Repositories\MUnivCode\MUniversityCodeRepositoryInterface::class,
            \App\Repositories\MUnivCode\MUniversityCodeRepository::class
        );
    }
}
