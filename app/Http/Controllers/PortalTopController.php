<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PortalLoginRequest;
use App\Services\MSchoolService;

class PortalTopController extends Controller
{
    private $m_school;

    public function __construct(MSchoolService $m_school)
    {
        $this->m_school = $m_school;
    }

    public function index()
    {
        return view('portal-top');
    }

    public function account(Request $request, string $account)
    {
        $account_title = $this->setAccountName($account);
        //ログイン画面のパラメータが正しくない場合はポータルトップへ戻す
        if (is_null($account_title)) {
            return redirect(route('PortalTop.index'));
        }
        return view('portal-account-login')->with('account_title', $account_title)->with('account', $account);
    }

    public function check(PortalLoginRequest $request, string $account)
    {
        $sch_code = $this->m_school->getCodeByAccount($request->portal_account);
        $redirect_url = '/'  . $sch_code . '/' . $account . '/login';
        return redirect($redirect_url);
    }

    private function setAccountName($account): ?string
    {
        if ($account == 'psn') {
            return '生徒様';
        } elseif ($account == 'cls') {
            return '教室様';
        } elseif ($account == 'sch') {
            return '本部様';
        }
        return null;
    }

}
