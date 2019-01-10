<?php
namespace App\Http\Controllers\api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Account;
use App\Quanly;
use App\Giaovien;
use App\Hocvien;
use App\Http\Controllers\Controller;
use Lcobucci\JWT\Parser;
use Illuminate\Support\Facades\Hash;
use DB;

class AccountController extends Controller
{
    
    public function register(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string',
            'permission' => 'required|string',
            'khuvuc' => 'required|string',
            'loginID' => 'required|string',
            'loginPASS' => 'required|string'
        ]);

        if($request->permission == 'giaovien'){
            $account_id = 'GV';
        } else {
            $account_id = 'QL';
        }

        if(Account::where('loginID',$request->loginID)->count() == 0){
            $acount_id_max = Account::select('account_id')->where('account_id','like', ''.$account_id.'%')->max('account_id');
            $account_id_new = str_pad(substr($acount_id_max,-4) + 1,'6',''.$account_id.'0000', STR_PAD_LEFT);
            $account = new Account([
                'account_id' => $account_id_new,
                'fullname' => $request->fullname,
                'permission' => $request->permission,
                'khuvuc' => $request->khuvuc,
                'loginID' => $request->loginID,
                'loginPASS' => bcrypt($request->loginPASS)
            ]);

            $account->save();
            $detail = Account::where('account_id',$account_id_new)->first();
            return response()->json(array('code' => 200, 'account_id' => $detail->account_id));
        } else {
            return response()->json(['code' => 422, 'message' => 'Tên tài khoản đã được sử dụng']);
        }
        
    }

    public function register_info(Request $request){
        $request->validate([
            'account_id' => 'required|string',
            'Mã Quản Lý' => 'unique:quanly',
            'available' => 'numeric',
            'hinhanh' => 'image',
            'sdt' => 'required|string',
            'diachi' => 'required|string',
            'email' => 'email',
            'cmnd' => 'required|numeric'
        ]);

        if(QuanLy::where('Mã Quản Lý',$request->account_id)->count() >0){
            return response()->json(['code' => 422, 'message' => 'Tài khoản đã tồn tại']);
        } else  if(Giaovien::where('Mã Giáo Viên',$request->account_id)->count() >0){
            return response()->json(['code' => 422, 'message' => 'Tài khoản đã tồn tại']);
        } 

        $detail = Account::where('account_id',$request->account_id)->first();
        $type = substr($request->account_id,0,-4);
        $path_image = $request->file('hinhanh')->store('public/avatar_user');
        if($type == 'QL'){
            $quanly = new Quanly([
                        'Mã Quản Lý' => $request->account_id,
                        'Họ Và Tên' => $detail->fullname,
                        'Hình Ảnh' => str_replace('public/avatar_user/','', $path_image),
                        'Số Điện Thoại' => $request->sdt,
                        'Địa chỉ' => $request->diachi,
                        'Email' => $request->email,
                        'CMND' => $request->cmnd,
                        'Chức Vụ' => $request->loaiquanly,
                        'Cơ Sở' => $detail->khuvuc
                    ]);
                
            $quanly->save();
            $account = Account::where('account_id',$request->account_id)->update(['available' => $request->available, 'hinhanh' => str_replace('public/avatar_user/','', $path_image),
         'loaiquanly' => $request->loaiquanly]);
        return response()->json(['code' => 200, 'message' => 'Tạo quản lý thành công']);
        } else if ($type == 'GV'){
            $giaovien = new Giaovien([
                'Mã Giáo Viên' => $request->account_id,
                'Họ Và Tên' => $detail->fullname,
                'Hình Ảnh' => str_replace('public/avatar_user/','', $path_image),
                'Số Điện Thoại' => $request->sdt,
                'Địa Chỉ' => $request->diachi,
                'Email' => $request->email,
                'CMND' => $request->cmnd,
                'Cơ Sở' => $detail->khuvuc
            ]);

            $giaovien->save();
            $account = Account::where('account_id',$request->account_id)->update(['available' => $request->available, 'hinhanh' => $request->hinhanh,
         'loaiquanly' => $request->loaiquanly]);
        return response()->json(['code' => 200, 'message' => 'Tạo giáo viên thành công']);
        } else{
            return response()->json(['code' => 400, 'message' => 'Không thành công']);
        }

    }
    
    public function login(Request $request){
        $request->validate([
            'loginID' => 'required|string',
            'loginPASS' => 'required|string'
        ]);
        $pass_verify = Account::select('loginPASS')->where('loginID', $request->loginID)->first();
        if (!is_null($pass_verify)){
            if (!Hash::check($request->loginPASS, $pass_verify->loginPASS)) {
                return response()->json(['code' => 400, 'message' => 'Mật khẩu không hợp lệ']);
            }
        } else{
            return response()->json(['code' => 401,'message' => "Tài khoản không tồn tại"]);
        }

        $account = Account::where('loginID', $request->loginID)->first();
        $account_id = Account::select('account_id')->where('loginID', $request->loginID)->first();
        $tokenResult = $account->createToken('Houston App')->accessToken;
        $id = (new Parser())->parse($tokenResult)->getHeader('jti');
        DB::table('oauth_access_tokens')->where('id', $id)->update(['user_id' => $account_id->account_id]);
        return response()->json(['code' => 200, 'token' => $tokenResult]);
    }

    public function logout(Request $request){   
        $value = $request->bearerToken();
        $id = (new Parser())->parse($value)->getHeader('jti');
        DB::table('oauth_access_tokens')->where('id', $id)->update(['revoked' => true]);
        return response()->json(['code' => 200, 'message' => "Đăng xuất thành công"]);
    }

    public function index(Request $request){
        $value = $request->bearerToken();
        if ($value == null) {
            return response()->json(['code' => 401, 'message' => 'Không được chứng thực']);
        }
        $id = (new Parser())->parse($value)->getHeader('jti');
        if(DB::table('oauth_access_tokens')->where('id',$id)->count() == 1){
            $account_token = DB::table('oauth_access_tokens')->select('user_id', 'revoked')->where('id',$id)->first();
            if($account_token->revoked == 0){
                
                if( Giaovien::where('Mã Giáo Viên', $account_token->user_id)->count() == 1){
                    $account = Account::where('account_id',$account_token->user_id)->join('giaovien','account_id', '=', 'giaovien.Mã Giáo Viên')
                    ->select('account.account_id','giaovien.*','account.permission')->get();
                    return response()->json(['code' => 200, 'data' => $account])->header('charset','utf-8');
                } else if (Quanly::where('Mã Quản Lý', $account_token->user_id)->count() == 1){
                    $account = Account::where('account_id',$account_token->user_id)->join('quanly','account_id', '=', 'quanly.Mã Quản Lý')
                    ->select('account.account_id','quanly.*','account.permission')->get();
                    return response()->json(['code' => 200, 'data' => $account])->header('charset','utf-8');
                } 
            } else{
                return response()->json(['code' => 401, 'message' => 'Lần đăng nhập trước đã hết hạn'], 401);
            }
        } else {
            return response()->json(['code' => 401, 'message' => 'Chứng thực không thành công'], 401);
        }
       
    }

    public function changePassword(Request $request)
    {   
        $value = $request->bearerToken();
        $id = (new Parser())->parse($value)->getHeader('jti');
       
        if (DB::table('oauth_access_tokens')->where('id',$id)->count() == 1){
            $account_token = DB::table('oauth_access_tokens')->select('user_id', 'revoked')->where('id',$id)->first();
        }
        else{
            return response()->json(['code' => 422, 'message' => 'Lần đăng nhập trước đã hết hạn'], 422);
        }

        $request->validate([
            'pass_old' => 'required|string',
            'pass_new' => 'required|string',
            'pass_confirm' => 'required|string',
        ]);
        
        
        $pass_verify = Account::select('loginPASS')->where('account_id', $account_token->user_id)->first();
       
        if (!is_null($pass_verify)){
            if (!Hash::check($request->pass_old, $pass_verify->loginPASS)) {
                return response()->json(['code' => 422, 'message' => 'Mật khẩu cũ không đúng'], 422);
            }
        } 

        if ($request->pass_new != $request->pass_confirm) {
            return response()->json(['code' => 422, 'message' => 'Mật khẩu xác nhận không trùng nhau'], 422);
        } else {
            Account::where('account_id', $account_token->user_id)->update(['loginPASS' => bcrypt($request->pass_new)]);
            return response()->json(['code' => 200, 'message' => 'Thay đổi mật khẩu thành công'], 200);
        }

    }

    public function destroy($id){
        $account = Account::where('account_id',$id);
        if ($account->count() == 1 ){
            $account->delete();
            Quanly::where('Mã Quản Lý',$id)->delete();
            Giaovien::where('Mã Giáo Viên',$id)->delete();
            return response()->json(['code' => 200, 'message' => 'Xóa thành công'], 200);
        } else {
            return response()->json(['code' => 401, 'message' => 'Không tìm thấy'], 200);
        }
    }

    public function update(Request $request){
        $value = $request->bearerToken();
        $id = (new Parser())->parse($value)->getHeader('jti');
       
        if (DB::table('oauth_access_tokens')->where('id',$id)->count() == 1){
            $account_token = DB::table('oauth_access_tokens')->select('user_id', 'revoked')->where('id',$id)->first();
        }
        else{
            return response()->json(['code' => 422, 'message' => 'Lần đăng nhập trước đã hết hạn'], 422);
        }

        $account = Account::where('account_id',$account_token->user_id);

        if($account->get()->count() == 0){
            return response()->json(['code' => 401, 'message' => 'Không tìm thấy'], 200);

        } else {
            $account->update(['fullname' => $request->HoVaTen]);
        }

        $ql = Quanly::where('Mã Quản Lý',$account_token->user_id);
        $gv = Giaovien::where('Mã Giáo Viên',$account_token->user_id);

        if ($ql->get()->count() == 1){
            $ql->update(['Họ Và Tên' => $request->HoVaTen, 'Số Điện Thoại' => $request->Sdt,
             'Địa Chỉ' => $request->DiaChi, 'Email' => $request->Email, 'CMND' => $request->Cmnd]);
             //return response()->json(['code' => 200, 'message' => 'Cập nhật thành công'], 200);
             return $this->index($request);
        } else if ($gv->get()->count() == 1){
            $gv->update(['Họ Và Tên' => $request->HoVaTen, 'Số Điện Thoại' => $request->Sdt,
             'Địa Chỉ' => $request->DiaChi, 'Email' => $request->Email, 'CMND' => $request->Cmnd]);
            // return response()->json(['code' => 200, 'message' => 'Cập nhật thành công'], 200);
             return $this->index($request);
        }
    }

}
