<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Facades\TransactionFacade;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Get paginated list of user's transactions.
     */
    public function index(Request $request)
    {
        $filters = $request->input('filters', []);

        $transactions = TransactionFacade::getPaginated(
            $filters
        );

        return $this->sendResponse($transactions, 'Transactions fetched successfully');
    }
}
