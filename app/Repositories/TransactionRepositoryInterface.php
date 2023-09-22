<?php
namespace App\Repositories;

interface TransactionRepositoryInterface {
    public function all($request);
    public function getDeposit($request);
    public function deposit($request);
    public function getWithdrawal($request);
    public function withdrawl($request);
}