<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        "balance",
        "account_type",
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * user when transction then balance update
     * @param $amount total amount that will be deposit or withdrawal
     * @param $transaction_type: deposit, withdrawal
     */
    public static function balanceUpdate($amount, $transaction_type) {
        $user = User::find(Auth::id());

        if ($user) {

            try {
                if ($transaction_type == 'deposit') {
                    $user->balance += $amount;
                } else if ($transaction_type == 'withdrawal') {
                    $user->balance -= $amount;
                }
                $user->save();

            } catch (\Exception $ex) {

                $message = env('APP_ENV') !== 'production' ? $ex->getMessage() : "";
                $code = $ex->getCode();
                
                return response()->json(
                    [
                        'success' => false,
                        'message' => $message,
                    ], $code
                );
            } 

        }

    }
}