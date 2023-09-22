<?php
namespace App\Repositories;

use App\Models\Transaction;
use App\Models\User;
use App\Repositories\TransactionRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionRepository implements TransactionRepositoryInterface {

    public function all($request) {
        $query = Transaction::select('id', 'user_id', 'transaction_type', 'amount', 'fee', 'date')
            ->where('user_id', Auth::id());
        $datas = $query->paginate(request()->limit ?? 20);

        if (count($datas) > 0) {
            return response()->json(
                [
                    'success' => true,
                    'message' => "Data Found!",
                    'balance' => Auth::user()->balance,
                    'data' => $datas,
                ], 200
            );
        }

        return response()->json(
            [
                'success' => true,
                'message' => "Data Not Found!",
                'balance' => Auth::user()->balance,
                'data' => $datas,
            ], 404
        );
    }

    public function getDeposit($request) {
        $query = Transaction::select('id', 'user_id', 'transaction_type', 'amount', 'fee', 'date')
            ->where('user_id', Auth::id())
            ->where('transaction_type', 'deposit');

        $datas = $query->paginate(request()->limit ?? 20);

        if (count($datas) > 0) {
            return response()->json(
                [
                    'success' => true,
                    'message' => "Data Found!",
                    'data' => $datas,
                ], 200
            );
        }

        return response()->json(
            [
                'success' => true,
                'message' => "Data Not Found!",
                'data' => $datas,
            ], 404
        );
    }

    public function deposit($request) {
        $requestAll = $request->all();

        try {

            DB::beginTransaction();
            $requestAll['date'] = date('Y-m-d');
            $requestAll['transaction_type'] = "deposit";

            $transaction = Transaction::create($requestAll);

            if ($transaction) {
                User::balanceUpdate($transaction->amount, 'deposit');
            }

            DB::commit();

            return response()->json(
                [
                    'success' => true,
                    'message' => "Data Saved!",
                    'data' => $transaction,
                ], 201
            );

        } catch (\Exception $ex) {

            DB::rollBack();

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

    public function getWithdrawal($request) {
        $query = Transaction::where('user_id', Auth::id())->where('transaction_type', 'withdrawal');
        $datas = $query->paginate(request()->limit ?? 20);

        if (count($datas) > 0) {

            return response()->json(
                [
                    'success' => true,
                    'message' => "Data Found!",
                    'data' => $datas,
                ], 200
            );
        }

        return response()->json(
            [
                'success' => true,
                'message' => "Data Not Found!",
                'data' => $datas,
            ], 404
        );
    }

    public function withdrawl($request) {
        $requestAll = $request->all();

        try {

            DB::beginTransaction();
            $requestAll['date'] = date('Y-m-d');
            $requestAll['transaction_type'] = "withdrawal";

            // check account type wise fee declare
            $account_type = Auth::user()->account_type;
            $user_balance = Auth::user()->balance;

            // check user balance
            if ($requestAll['amount'] > $user_balance) {
                return response([
                    'success' => false,
                    'message' => "Balance Not available!",
                ]);
            }

            if ($account_type == 'Individual') {
                $requestAll['fee'] = ($requestAll['amount'] * Transaction::$individual_rate) / 100;

                // get current day of week
                $today = date('Y-m-d');
                $dayOfWeek = date('l', strtotime($today));

                //================ for friday fee charge free======================
                if ($dayOfWeek == 'Friday') {
                    $requestAll['fee'] = 0;
                } else {

                    // =========eatch month 5k charge free================
                    $first_date = date('Y-m-d', strtotime('first day of this month'));
                    $last_date = date('Y-m-d', strtotime('last day of this month'));

                    $check_first_month_amount = Transaction::where('user_id', Auth::id())->where('transaction_type', 'withdrawal')->whereBetween('date', [$first_date, $last_date])->sum('amount');

                    // if privious withdrawl amount is less than 5k
                    if ($check_first_month_amount < 5000) {

                        // total payable amount with previous & current widthdrawl amount
                        $currentTotalPayableAmount = $check_first_month_amount + $request->amount;

                        if ($currentTotalPayableAmount > 5000) {
                            $remainAmountPayable = $currentTotalPayableAmount - 5000;

                            if ($remainAmountPayable > 1000) {
                                $remainAmount = $remainAmountPayable - 1000;
                                $requestAll['fee'] = ($remainAmount * Transaction::$individual_rate) / 100;
                            } else {
                                $requestAll['fee'] = 0;
                            }

                        } else {
                            $requestAll['fee'] = 0;
                        }

                    } else {

                        // =============first 1k free charge & remain amount will charge ==============
                        if ($requestAll['amount'] > 1000) {
                            $remainAmount = $request['amount'] - 1000;
                            $requestAll['fee'] = ($remainAmount * Transaction::$individual_rate) / 100;
                        } else {
                            $requestAll['fee'] = 0;
                        }
                    }

                }

            } else if ($account_type == 'Business') {

                // =============get privious withdrawl amount sum==============
                $total_withdrawal_amount = Transaction::where('user_id', Auth::id())->where('transaction_type', 'withdrawal')->sum('amount');
                // when privious withdrawl is gather than 50k
                if ($total_withdrawal_amount > 50000) {
                    $requestAll['fee'] = ($requestAll['amount'] * Transaction::$individual_rate) / 100;
                } else {
                    $requestAll['fee'] = ($requestAll['amount'] * Transaction::$business_rate) / 100;
                }
            }

            $totalAmount = $requestAll['amount'] + $requestAll['fee'];
            $requestAll['amount'] = $totalAmount;

            // check user balance
            if ($totalAmount > $user_balance) {
                return response([
                    'success' => false,
                    'message' => "Balance Not available!",
                ]);
            }

            $transaction = Transaction::create($requestAll);

            if ($transaction) {
                User::balanceUpdate($transaction->amount, 'withdrawal');
            }

            DB::commit();

            return response()->json(
                [
                    'success' => true,
                    'message' => "Data Saved!",
                    'data' => $transaction,
                ], 201
            );

        } catch (\Exception $ex) {

            DB::rollBack();

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