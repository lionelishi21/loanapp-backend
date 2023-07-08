<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 02/02/2020
 * Time: 17:14
 */

namespace App\Rules;

use App\Models\PaymentMethod;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class MpesaNumberRule implements Rule
{
    protected $disbursementId;

    /**
     * MoreThanAccountBalance constructor.
     * @param $disbursementId
     */
    public function __construct($disbursementId)
    {
        $this->disbursementId = $disbursementId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $paymentMethod = PaymentMethod::where('id', $this->disbursementId)
            ->select('name')
            ->first()['name'];

        if ($paymentMethod == 'MPESA'){

            if (strlen($value) >=9){
                return true;
            }
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Valid Mpesa Number is required for disbursement.';
    }
}
