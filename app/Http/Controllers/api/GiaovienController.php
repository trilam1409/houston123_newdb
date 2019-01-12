<?php

namespace App\Http\Controllers\api;
use Illuminate\Http\Request;
use App\Giaovien;
use App\Account;
use DB;
use App\Http\Controllers\Controller;

class GiaovienController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
 
        if (Giaovien::get()->count() == 0) {
            return response()->json(['code' => 401, 'message' => 'Không tìm thấy'], 200);
        } else {
            $GIAOVIEN = Giaovien::join('ACCOUNT', 'GIAOVIEN.Mã Giáo Viên', '=','ACCOUNT.ACCOUNT_id')->join('COSO', 'GIAOVIEN.Cơ Sở', '=', 'COSO.Cơ Sở')
            ->select('GIAOVIEN.*', 'COSO.Tên Cơ Sở', 'ACCOUNT.available')->paginate(15);
            $custom = collect(['code' => 200]);
            $data = $custom->merge($GIAOVIEN);
            return response()->json($data, 200)->header('charset', 'utf-8');
        }
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($str)
    {   
      
        $user = Giaovien::where('Mã Giáo Viên','like','%'.$str.'%')->orwhere('Họ Và Tên','like','%'.$str.'%')->orwhere('GIAOVIEN.Cơ Sở','like', '%'.$str.'%');
        if($user->count() == 0){
            return response()->json(['code' => 401, 'message' => 'Không tìm thấy'], 200);
        } else {
            $result = $user->join('ACCOUNT', 'GIAOVIEN.Mã Giáo Viên', '=','ACCOUNT.ACCOUNT_id')->join('COSO', 'GIAOVIEN.Cơ Sở', '=', 'COSO.Cơ Sở')
            ->select('GIAOVIEN.*', 'COSO.Tên Cơ Sở', 'ACCOUNT.available')->paginate(15);
            $custom = collect(['code' => 200]);
            $data = $custom->merge($result);
            return response()->json($data, 200)->header('charset', 'utf-8');
        }
      
        
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'hovaten' => 'nullable|string',
    //         'hinhanh' => 'nullable|string',
    //         'permission' => 'string',
    //         'available' => 'numeric',
    //         'sdt' => 'nullable|numeric',
    //         'diachi' => 'nullable|string',
    //         'email' => 'nullable|email',
    //         'cmnd' => 'numeric',
    //         'ngaynghi' => 'nullable|date',
    //         'lydonghi' => 'nullable|string',
    //         'COSO' => 'nullable|string'
    //     ]);

    //     if(Giaovien::where('Mã Giáo Viên',$id)->count() == 1){
    //         Giaovien::where('Mã Giáo Viên',$id)->update(['Họ Và Tên' => $request->hovaten, 'Hình Ảnh' => $request->hinhanh, 'Số Điện Thoại' => $request->sdt,
    //             'Địa Chỉ' => $request->diachi, 'email' => $request->email, 'CMND' => $request->cmnd, 'Ngày Nghỉ' => $request->ngaynghi,
    //             'Lý Do Nghỉ' => $request->lydonghi, 'Cơ Sở' => $request->COSO]);
    //         Account::where('ACCOUNT_id',$id)->update(['fullname' => $request->hovaten,'permission' => $request->permission, 'khuvuc' => $request->COSO,
    //             'available' => $request->available, 'hinhanh' => $request->hinhanh]);
    //     return response()->json(['code' => 200, 'message' => 'Cập nhật thành công'], 200);
    //     } else {
    //         return response()->json(['code' => 401, 'message' => 'Không tìm thấy'], 200);
    //     }

        
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy($id)
    // {
    //     $exist = Giaovien::where('Mã Giáo Viên',$id)->count();
    //     if($exist == 0){
    //         return response()->json(['code' => 401, 'message' => "Không tìm thấy"], 200);
    //     } else if ($exist == 1){
    //         Giaovien::where('Mã Giáo Viên',$id)->delete();
    //         Account::where('ACCOUNT_id', $id)->delete();
    //         return response()->json(['code' => 200, 'message' => "Xóa thành công"], 200);
    //     }
    // }
}
