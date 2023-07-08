<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\Permission;

class ApiController extends Controller
{

    protected $statusCode = 200;

    /**
     * @param $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * The response status code
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }


     /**
     * @param $data
     * @return mixed
     */
    public function respondWithData($data)
    {
        return ( $data )
            ->response()
            ->setStatusCode($this->getStatusCode());
    }


    /**
     * @param string $message
     * @return array
     */
    public function respondWithError($message = "There was an error")
    {
        $data = [
            'error'         => true,
            'message'       => $message,
            'status_code'   => $this->getStatusCode()
        ];

        return (\Response::json($data))->setStatusCode($this->getStatusCode());
    }


    /**
     * When a missing resource is requested
     * @param string $message
     * @return mixed
     */
    public function respondNotFound($message = "Not Found !")
    {
        return $this->setStatusCode(404)->respondWithError($message);
    }

    /**
     * Provided json body is not formatted as per api requirement.
     * @param string $message
     * @return mixed
     */
    public function respondWrongFormat($message = "JSON data is not well formatted.")
    {
        return $this->setStatusCode(400)->respondWithError($message);
    }

    /**
     * When a non supported search parameter is requested
     * @param string $message
     * @return mixed
     */
    public function respondWrongParameter ($message = "You requested a non supported search parameter!")
    {
        return $this->setStatusCode(400)->respondWithError($message);
    }

    /**
     * There was an internal error
     * @param string $message
     * @return mixed
     */
    public function respondInternalError($message = "Internal Server Error !!")
    {
        return $this->setStatusCode(500)->respondWithError($message);
    }

    /**
     * Some operation (save) failed.
     * @param string $message
     * @return mixed
     */
    public function respondNotSaved($message = "Not Saved !")
    {
        return $this->setStatusCode(400)->respondWithError($message);
    }

    /**
     * @param string $message
     * @return array
     */
    public function respondWithSuccess($message = 'Success !!')
    {
        $data = [
            'error'         => false,
            'message'       => $message,
            'status_code'   => $this->getStatusCode()
        ];

        return (\Response::json($data))->setStatusCode($this->getStatusCode());
    }

    /**
     * Cleans up url variables to eliminate spaces
     * @param $string
     * @return array
     */
    public function formatFields($string)
    {
        return explode(",", preg_replace('/\s*,\s*/', ',', rtrim(trim($string), ',')));
    }

    /**
     * Checks if current active user has all available permissions (Thus admin)
     * @return bool
     */
    public function isAdmin() {
        // System wide permissions
        $allPermissions = Permission::all()->toArray();
        $allPermissions = array_map(function($allPermission) {
            return $allPermission['name'];
        }, $allPermissions);

        // Current user permissions
        $userPerms = [];
        if(auth()->user()){
            $userPerms = auth()->user()->role->permissions->toArray();
            $userPerms = array_map(function($userPerm) {
                return $userPerm['name'];
            }, $userPerms);
        }

        if(empty(array_diff($allPermissions, $userPerms))) {
            return true;
        }
        return false;
    }

    /**
     * @param $amount
     * @return string
     */
    public function formatMoney($amount) {
        return number_format($amount, $this->amountDecimal(), $this->amountDecimalSeparator(), $this->amountThousandSeparator());
    }

    /**
     * @param $date
     * @return false|string
     */
    public function formatDate($date){
        return $new_date_format = date($this->dateFormat(), strtotime($date));
    }


    /**
     * @return string
     */
    private function dateFormat(){
        $format = GeneralSetting::select('date_format')->first()->date_format;

        if(isset($format))
            return $format;
        return 'd-m-Y';
    }

    /**
     * @return string
     */
    private function amountThousandSeparator() {
        $separator = GeneralSetting::select('amount_thousand_separator')->first()->amount_thousand_separator;

        if(isset($separator))
            return $separator;
        return ',';
    }

    /**
     * @return string
     */
    private function amountDecimalSeparator() {
        $separator = GeneralSetting::select('amount_decimal_separator')->first()->amount_decimal_separator;

        if(isset($separator))
            return $separator;
        return '.';
    }

    /**
     * @return int
     */
    private function amountDecimal() {
        $separator = GeneralSetting::select('amount_decimal')->first()->amount_decimal;

        if(isset($separator))
            return (int)$separator;
        return 2;
    }
}
