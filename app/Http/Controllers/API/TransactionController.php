<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepositRequest;
use App\Http\Requests\WithdrawlRequest;
use App\Repositories\TransactionRepositoryInterface;
use Illuminate\Http\Request;

class TransactionController extends Controller {

    // initialization repository
    private $transactionRepository;

    public function __construct(TransactionRepositoryInterface $transactionRepository) {
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * get user wise get all transactions data with current balance
     */
    public function allTransaction(Request $request) {
        return $this->transactionRepository->all($request);
    }

    /**
     * get user wise deposit transaction data
     */
    public function getDepositData(Request $request) {
        return $this->transactionRepository->getDeposit($request);
    }

    /**
     * store user diposit transaction. when store diposit user's balance auto increase.
     */
    public function storeDeposit(DepositRequest $request) {
        return $this->transactionRepository->deposit($request);
    }

    /**
     * get user wise withdrawl transaction data
     */
    public function getWithdrawalData(Request $request) {
        return $this->transactionRepository->getWithdrawal($request);
    }

    /**
     * store user withdrawl transaction. when store withdrawl user's balance auto decrease.
     */
    public function storeWithdrawal(WithdrawlRequest $request) {
        return $this->transactionRepository->withdrawl($request);
    }
}
