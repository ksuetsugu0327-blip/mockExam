<?php

namespace App\Http\Controllers;

//useしないと 自動的にnamespaceのパスが付与されるのでuse
use \SplFileObject;
use App\Models\MPerson;
use App\Services\CsvService as CsvService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CsvUploadRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use App\Utils\FileUtil;
use Illuminate\Support\Facades\DB;

class UnivCsvUploadController extends Controller{

    private $csvService;

    public function __construct(CsvService $csvService) {
        $this->middleware('auth:'.config('constants.GUARD_ADMIN'));
        $this->csvService = $csvService;
    }
}
