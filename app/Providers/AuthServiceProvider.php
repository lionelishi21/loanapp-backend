<?php

namespace App\Providers;

use App\Models\Permission;
use Carbon\Carbon;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\Passport;
use Exception;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
            $this->registerPolicies();

            Passport::routes();

            Passport::tokensExpireIn(Carbon::now()->addMinutes(300));

            Passport::refreshTokensExpireIn(Carbon::now()->addMinutes(300));

            $data = [];

            try{
                if(Schema::hasTable('permissions')){
                    //Fetch all available permissions to be used for tokensCan
                    $permissions = Permission::all();
                    if(!is_null($permissions)){
                        foreach ($permissions->toArray() as $key => $value)
                            $data[trim($value['name'])] =  trim($value['display_name'] );
                    }
                    if (!is_null($data))
                        Passport::tokensCan($data);
                }
            }catch (\PDOException $exception ){
                Passport::tokensCan([]);
            }catch (Exception $exception){
                Passport::tokensCan([]);
            }
    }
}
