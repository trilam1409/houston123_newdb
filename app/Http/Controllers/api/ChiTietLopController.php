<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\ChiTietLop;

class ChiTietLopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (ChiTietLop::get()->count() == 0) {
            return response()->json(['code' => 401, 'message' => 'Không tìm thấy'], 200);
        } else {
            $chiTietLop = ChiTietLop::join('USERS', 'MaHocVien', '=', 'USERS.User ID')//->join('LOPHOC','MaLop','=','LOPHOC.Mã Lớp')
            ->select('CHITIETLOPHOC.*', 'USERS.Họ Và Tên')->orderBy('ID','desc')->paginate(30);
            $custom = collect(['code' => 200]);
            $data = $custom->merge($chiTietLop);
            return response()->json($data, 200)->header('charset','utf-8');
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
        $request->validate([
            'MaLop' => 'required|string',
            'MaHocVien' => 'required|string'
        ]);
        
        $validate = ChiTietLop::where('MaLop',$request->MaLop)->where('MaHocVien',$request->MaHocVien);
        if($validate->count() > 0){
            return response()->json(['code' => 401, 'message' => 'Học viên đang học tại lớp'], 200);
        } else {
            $chiTietLop = new ChiTietLop ([
                'MaLop' => $request->MaLop,
                'MaHocVien' => $request->MaHocVien
            ]);
    
            $chiTietLop->save();
            return response()->json(['code' => 200, 'message' => 'Thêm học viên thành công'], 200);
        }

        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($str)
    {   
        $chiTietLop = ChiTietLop::where('CHITIETLOPHOC.ID', $str)->orwhere('MaLop', $str)->orwhere('MaHocVien', $str);
        if ($chiTietLop->count() == 0) {
            return response()->json(['code' => 401, 'message' => 'Không tìm thấy'], 200);
        } else {
            $chiTietLop = $chiTietLop->join('USERS', 'MaHocVien', '=', 'USERS.User ID')
            ->select('CHITIETLOPHOC.*', 'USERS.Họ Và Tên')->orderBy('ID','desc')->paginate(30);
            $custom = collect(['code' => 200]);
            $data = $custom->merge($chiTietLop);
            return response()->json($data, 200)->header('charset','utf-8');
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

    // public function show_id($id)
    // {   
    //     $chiTietLop = ChiTietLop::where('ID','like',$id);
    //     if ($chiTietLop->get()->count() == 0) {
    //         return response()->json(['code' => 401, 'message' => 'Không tìm thấy'], 200);
    //     } else {
    //         $chiTietLop = ChiTietLop::join('USERS', 'MaHocVien', '=', 'USERS.User ID')
    //         ->select('CHITIETLOPHOC.*', 'USERS.Họ Và Tên')->orderBy('ID','desc')->paginate(30);
    //         $custom = collect(['code' => 200]);
    //         $data = $custom->merge($chiTietLop);
    //         return response()->json($data, 200)->header('charset','utf-8');
    //     }
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'MaLop' => 'required|string',
            'MaHocVien' => 'required|string',
            'Diem' => 'nullable|string',
            'DanhGia' => 'nullable|string'
        ]);

        $chiTietLop = ChiTietLop::where('ID',$id)->update([
            'MaLop' => $request->MaLop,
            'MaHocVien' => $request->MaHocVien,
            'Diem' => $request->Diem,
            'DanhGia' => $request->DanhGia
        ]);

  
        return $this->show($id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $chiTieLop = ChiTietLop::where('ID', $id);
        if($chiTieLop->count() == 0){
            return response()->json(['code' => 401, 'message' => 'Không tìm thấy'],200);
            
        } else {
            $chiTieLop->delete();
            return response()->json(['code' => 200, 'message' => 'Xóa thành công'],200);
        }
    }
}
