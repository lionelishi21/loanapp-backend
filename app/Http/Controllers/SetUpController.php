<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 14/01/2020
 * Time: 21:44
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\InstallationRequest;
use App\Http\Requests\InstallationUserRequest;
use App\Models\Branch;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\SmartMicro\Repositories\Contracts\UserInterface;
use App\Traits\CommunicationMessage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class SetUpController extends ApiController
{
    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * InstallationController constructor.
     * @param UserInterface $user
     */
    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * @return array
     */
    public function checkRequirements()
    {
        $allSet = false;
        $alreadyInstalled = false;

        try {
            if (!is_null(Permission::first()))
                $alreadyInstalled = true;

        } catch (\PDOException $exception) {
            $alreadyInstalled = false;
        } catch (Exception $e) {
            $alreadyInstalled = false;
        }

        // 'GD Extension'              => extension_loaded('gd'),

        $requirements = [
            'PHP Version (>= 7.2.0)' => version_compare(phpversion(), '7.2.0', '>='),
            'BCMath PHP Extension' => extension_loaded('bcmath'),
            'Ctype PHP Extension' => extension_loaded('ctype'),
            'JSON PHP Extension' => extension_loaded('json'),
            'Mbstring PHP Extension' => extension_loaded('mbstring'),
            'OpenSSL PHP Extension' => extension_loaded('openssl'),
            'PDO PHP Extension' => extension_loaded('PDO'),
            'Tokenizer PHP Extension' => extension_loaded('tokenizer'),
            'XML PHP Extension' => extension_loaded('xml'),
            'PDO MySQL Extension' => extension_loaded('pdo_mysql'),
            'Fileinfo Extension' => extension_loaded('fileinfo')
        ];

        $requirementDisplay = [];

        foreach ($requirements as $key => $value) {
            $object = new \stdClass();

            $object->display_name = $key;
            $object->status = $value;

            $requirementDisplay[] = $object;
        }

        if (!in_array(false, $requirements, true)) {
            $allSet = true;
        }

        $data['requirements'] = $requirementDisplay;
        $data['all_set'] = $allSet;
        $data['already_installed'] = $alreadyInstalled;

        return $data;
    }

    /**
     * @return mixed
     */
    public function checkPermissions()
    {
        $allPermissionSet = false;

        $permissions = [
            'storage/app' => is_writable(storage_path('app')),
            'storage/framework/cache' => is_writable(storage_path('framework/cache')),
            'storage/framework/sessions' => is_writable(storage_path('framework/sessions')),
            'storage/logs' => is_writable(storage_path('logs')),
            'storage' => is_writable(storage_path('')),
            'bootstrap/cache' => is_writable(base_path('bootstrap/cache')),
            '.env file' => is_writable(base_path('.env')),
        ];

        $permDisplay = [];

        foreach ($permissions as $key => $value) {
            $object = new \stdClass();

            $object->display_name = $key;
            $object->status = $value;

            $permDisplay[] = $object;
        }

        if (!in_array(false, $permissions, true)) {
            $allPermissionSet = true;
        }

        $data['permissions'] = $permDisplay;
        $data['all_permission_set'] = $allPermissionSet;

        return $data;
    }


    /**
     * @param InstallationRequest $request
     * @return mixed
     */
    public function databaseSetup(InstallationRequest $request)
    {
        if ($this->testDbConnection()) {
            return $this->respondNotSaved('Aborted... Existing database details already connect.');
        }

        $data = $request->all();

        $connection = 'mysql';
        $host = empty($data['host']) ? '' : $data['host'];
        $database = empty($data['database']) ? null : $data['database'];
        $username = empty($data['username']) ? null : $data['username'];
        $password = empty($data['password']) ? null : $data['password'];
        $port = empty($data['port']) ? null : $data['port'];

        $settings = compact('connection', 'host', 'port', 'database', 'username', 'password');

        $this->updateEnvironmentFile($settings);

        if ($this->testDbConnection()) {
            try {
                $this->setDatabase();
            } catch (Exception $e) {
                return $this->respondNotSaved($e->getMessage());
            }
        } else
            return $this->respondNotSaved('Could not connect to database...');

    }


    /**
     * @param $settings
     * @return bool|mixed
     */
    private function updateEnvironmentFile($settings)
    {
        try {
            $env_path = base_path('.env');
            DB::purge(DB::getDefaultConnection());

            foreach ($settings as $key => $value) {
                $key = 'DB_' . strtoupper($key);
                $line = $value ? ($key . '=' . $value) : $key;
                putenv($line);
                file_put_contents($env_path, preg_replace(
                    '/^' . $key . '.*/m',
                    $line,
                    file_get_contents($env_path)
                ));
            }

            config(['database.connections.mysql.host' => $settings['host']]);
            config(['database.connections.mysql.port' => $settings['port']]);
            config(['database.connections.mysql.database' => $settings['database']]);
            config(['database.connections.mysql.username' => $settings['username']]);
            config(['database.connections.mysql.password' => $settings['password']]);

        } catch (Exception $exception) {
            return $this->respondNotSaved($exception->getMessage());
        }
        return true;
    }

    /**
     * @param InstallationUserRequest $request
     * @return array|mixed
     */
    public function userSetup(InstallationUserRequest $request)
    {
        //if there is an existing user, abort
        // Only a single user may be created via this route

        $data = $request->all();

        try {
            if ($this->testDbConnection()) {

                if (is_null(User::first())) {
                    $data['role_id'] = Role::inRandomOrder()->select('id')->first()['id'];
                    $data['branch_id'] = Branch::inRandomOrder()->select('id')->first()['id'];

                    $save = $this->user->create($data);
                    if (!is_null($save) && $save['error']) {
                        return $this->respondNotSaved($save['message']);
                    } else {
                        // New user email / sms
                        CommunicationMessage::send('new_user_welcome', $save, $save);
                        return $this->respondWithSuccess('Success !! User has been created.');
                    }
                } else
                    return $this->respondNotSaved('A default user already exists.');
            } else
                return $this->respondNotSaved('Could not connect to database...Check your database name, user and password.');
        } catch (\PDOException $exception) {
            return $this->respondNotSaved($exception->getMessage());
        } catch (Exception $exception) {
            return $this->respondNotSaved($exception->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    private function setDatabase()
    {
        try {
            set_time_limit(0);

            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            //  Artisan::call('migrate', ['--path' => 'vendor/laravel/passport/database/migrations']);
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);
            Artisan::call('passport:install', ['--force' => true]);
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            Artisan::call('migrate:rollback');
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @return bool
     */
    private function testDbConnection()
    {
        try {
            DB::connection(DB::getDefaultConnection())->reconnect();
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

}