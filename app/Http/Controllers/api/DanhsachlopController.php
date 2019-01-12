<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Danhsachlop;

class DanhsachlopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Danhsachlop::get()->count() == 0 ){
            return response()->json(['code' => 401, 'message' => 'Không tìm thấy'], 200);
        } else {
            $ds = Danhsachlop::join('USERS', 'DANHSACHHOCSINHTRONGLOP.User ID', '=', 'USERS.User ID')
            ->join('LOPHOC', 'DANHSACHHOCSINHTRONGLOP.Mã Lớp', '=', 'LOPHOC.Mã Lớp')
            ->join('DANHSACHMONHOC', 'LOPHOC.Mã Môn Học', '=', 'DANHSACHMONHOC.mamon')
            ->join('GIAOVIEN', 'LOPHOC.Mã Giáo Viên', '=', 'GIAOVIEN.Mã Giáo Viên')
            ->join('COSO', 'LOPHOC.branch', '=', 'COSO.Cơ Sở')
            ->select('LOPHOC.*', 'COSO.Tên Cơ Sở', 'DANHSACHMONHOC.name', 'GIAOVIEN.Họ Và Tên', 'DANHSACHHOCSINHTRONGLOP.*', 'USERS.Họ Và Tên')
            ->paginate(15);
            $custom = collect(['code' => 200]);
            $data = $custom->merge($ds);
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
            'mahocvien' => 'required|string',
            'malop' => 'required|string',
            'malopchuyen',
            'thoigianchuyen' => 'nullable|date'
        ]);

        $ds = new Danhsachlop([
            'User ID' => $request->mahocvien,
            'Mã Lớp' => $request->malop,
            'Mã Lớp Chuyển' => $request->malopchuyen,
            'Thời Gian Chuyển' => $request->thoigianchuyen
        ]);

        $ds->save();

        //return response()->json(['code' => 200, 'message' => 'Tạo thành công'],200);
        return $this->show($request->malop);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($str)
    {   
        $ds = Danhsachlop::where('DANHSACHHOCSINHTRONGLOP.User ID', 'like', '%'.$str.'%')->orwhere('DANHSACHHOCSINHTRONGLOP.Mã Lớp','like','%'.$str.'%');
        if ($ds->get()->count() == 0 ){
            return response()->json(['code' => 401, 'message' => 'Không tìm thấy'], 200);
        } else {
            $ds = $ds->join('USERS', 'DANHSACHHOCSINHTRONGLOP.User ID', '=', 'USERS.User ID')
            ->join('LOPHOC', 'DANHSACHHOCSINHTRONGLOP.Mã Lớp', '=', 'LOPHOC.Mã Lớp')
            ->join('DANHSACHMONHOC', 'LOPHOC.Mã Môn Học', '=', 'DANHSACHMONHOC.mamon')
            ->join('GIAOVIEN', 'LOPHOC.Mã Giáo Viên', '=', 'GIAOVIEN.Mã Giáo Viên')
            ->join('COSO', 'LOPHOC.branch', '=', 'COSO.Cơ Sở')
            ->select('LOPHOC.*', 'COSO.Tên Cơ Sở', 'DANHSACHMONHOC.name', 'GIAOVIEN.Họ Và Tên', 'DANHSACHHOCSINHTRONGLOP.*', 'USERS.Họ Và Tên')
            ->paginate(15);
            $custom = collect(['code' => 200]);
            $data = $custom->merge($ds);
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {   
        $ds = Danhsachlop::where('ID',$id);
        if ($ds->get()->count() == 0 ){
            return response()->json(['code' => 401, 'message' => 'Không tìm thấy'], 200);
        } else {    
            $request->validate([
                'mahocvien' => 'required|string',
                'malop' => 'require|string',
                'malopchuyen',
                'thoigianchuyen' => 'date'
            ]);
    
            $ds->update([
                'User ID' => $request->mahocvien,
                'Mã Lớp' => $request->malop,
                'Mã Lớp Chuyển' => $request->malopchuyen,
                'Thời Gian Chuyển' => $request->thoigianchuyen
            ]);
            //return response()->json(['code' => 200, 'message' => 'Cập nhật thành công'],200);
            return $this->show($id);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ds = Danhsachlop::where('ID',$id);
        if ($ds->get()->count() == 0 ){
            return response()->json(['code' => 401, 'message' => 'Không tìm thấy'], 200);
        } else {    
            $ds->delete();
            return response()->json(['code' => 200, 'message' => 'Xóa thành công'],200);
        }
    }

    public function classNullStudent($id){
        $LOPHOC = Danhsachlop::where('User ID','!=' ,$id);
        if ($LOPHOC->count() == 0 ){
            return response()->json(['code' => 401, 'message' => 'Không tìm thấy lớp'], 200);
        } else {
            $LOPHOC = $LOPHOC->groupBy('DANHSACHHOCSINHTRONGLOP.Mã Lớp')
            ->join('LOPHOC','DANHSACHHOCSINHTRONGLOP.Mã Lớp','=','LOPHOC.Mã Lớp')
            ->join('DANHSACHMONHOC', 'LOPHOC.Mã Môn Học', '=', 'DANHSACHMONHOC.mamon')
            ->join('GIAOVIEN', 'LOPHOC.Mã Giáo Viên', '=', 'GIAOVIEN.Mã Giáo Viên')
            ->join('COSO', 'LOPHOC.branch', '=', 'COSO.Cơ Sở')
            ->select('LOPHOC.*', 'COSO.Tên Cơ Sở','DANHSACHMONHOC.name','GIAOVIEN.Họ Và Tên')->paginate(15);
            $custom = collect(['code' => 200]);
            $data = $custom->merge($LOPHOC);
            return response()->json($data, 200)->header('charset','utf-8');
        }
    }

   
}

 
