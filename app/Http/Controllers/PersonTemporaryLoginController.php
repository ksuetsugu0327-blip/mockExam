<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use App\Http\Requests\PersonTemporaryLoginRequest;
use App\Services\MPersonService;
use App\Utils\PasswordUtil;
use Illuminate\Http\Request;

/**
 * 仮パスワードログインコントローラ
 */
class PersonTemporaryLoginController extends Controller
{
    // サービスクラス(個人用)
    private $mPersonService;

    /**
     * コンストラクタ
     *
     * @param  MPersonService  $mPersonService  個人マスタアクセス用インスタンス
     */
    public function __construct(MPersonService $mPersonService)
    {
        $this->mPersonService = $mPersonService;
    }

    /**
     * 初期表示処理
     *
     * @param  type  $schCode  塾コード
     * @return type
     */
    public function index($schCode): type
    {
        // フラッシュデータにない場合は、不正なアクセスの為、ログイン画面に戻す
        $psn_code = session()->get('psn_code');
        if (! isset($psn_code)) {
            return redirect(route('personIdCheck.index', ['schCode' => $schCode]));
        }

        // 「仮パスワードを忘れたとき」リンクの表示有無判定
        // 塾生コードから塾生データを取得し、生年月日・郵便番号・電話番号が空でなかったら表示する
        $bool_temp_pass = false;

        // 個人マスタ検索
        $persons = $this->mPersonService->selectOneByKey((int) $schCode, (string) $psn_code);
        if ($persons->count() == 0) {
            // ありえないので、発生時はログイン画面に強制的に戻す
            return redirect(route('personIdCheck.index', ['schCode' => $schCode]));
        }
        $person = $persons->get(0);

        if ((string) $person['birthlow4'] !== '' && (string) $person['postallow4'] !== '' && (string) $person['tellow4'] !== '') {
            $bool_temp_pass = true;
        }

        return view('login-kpass')->with('psn_code', $psn_code)->with('temp_pass', $bool_temp_pass);
    }

    /**
     * 仮パスワードログインチェック
     *
     * @param  PersonIDLoginCheckRequest  $request
     */
    public function login(PersonTemporaryLoginRequest $request)
    {

        $schCode = $request->session()->get(config('constants.SESSION_KEY_SCHCODE'));
        $psnCode = $request->get('psn_code');
        // 個人マスタ検索
        $persons = $this->mPersonService->selectOneByKey((int) $schCode, (string) $psnCode);
        if ($persons->count() == 0) {
            // ありえないので、発生時はログイン画面に強制的に戻す
            return redirect(route('personIdCheck.index', ['schCode' => $schCode]));
        }
        $person = $persons->get(0);

        // 仮パスワードでのログイン認証
        if (! PasswordUtil::temporaryValidateCredentials($request->get('password'), $person->temppassword)) {
            // パスワード不正メッセージを返却
            return back()->withErrors(['message' => trans('auth.failed')])->with('psn_code', $psnCode);
        }

        return redirect()->route('changePassword.index', ['sch_code' => $schCode])->with('psn_code', $psnCode);
    }

    /**
     * 仮パスワードが不明な場合のログイン処理
     *
     * @return type
     */
    public function unknownAuth(Request $request): RedirectResponse
    {
        $schCode = $request->session()->get(config('constants.SESSION_KEY_SCHCODE'));
        $psnCode = $request->get('psn_code');

        return redirect()->route('personUnknownLogin.index', ['sch_code' => $schCode])->with('psn_code', $psnCode);
    }
}
