<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Lophoc;
use App\Monhoc;
use App\Coso;

class LophocController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        if (Lophoc::get()->count() == 0){
            return response()->json(['code' => 401, 'message' => 'Không tìm thấy'],200);
        } else {
            $lophoc = Lophoc::join('DANHSACHMONHOC', 'LOPHOC.Mã Môn Học', '=', 'DANHSACHMONHOC.mamon')->join('GIAOVIEN', 'LOPHOC.Mã Giáo Viên', '=', 'GIAOVIEN.Mã Giáo Viên')
            ->join('COSO', 'LOPHOC.branch', '=', 'COSO.Cơ Sở')
            ->select('Mã Lớp', 'Lớp', 'LOPHOC.Mã Môn Học', 'DANHSACHMONHOC.name', 'LOPHOC.Mã Giáo Viên', 'GIAOVIEN.Họ Và Tên', 
            'Ngày Bắt Đầu', 'Ngày Kết Thúc', 'branch', 'COSO.Tên Cơ Sở','Lý Do Kết Thúc', 'Mã Nhân Viên KT Lớp')->orderBy('ID','desc')->paginate(30);
            $custom = collect(['code' => 200]);
            $data = $custom->merge($lophoc);
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
            'lop' => 'required|string',
            'monhoc' => 'required|string',
            'magiaovien' => 'nullable|string',
            'batdau' => 'required|date',
            'ketthuc' => 'required|date',
            'coso' => 'required|string'
        ]);

        $maMonHoc = Monhoc::select('mamon')->where('name',$request->monhoc)->value('mamon');
        $maCoSo = Coso::select('Cơ Sở')->where('Tên Cơ Sở',$request->coso)->value('Cơ Sở');

        $maLopTemp = $maCoSo.$maMonHoc.str_pad($request->lop,'2',0,STR_PAD_LEFT).substr(date('Y', strtotime($request->batdau)),2);
        $findLop = Lophoc::select('Mã Lớp')->where('Mã Lớp','like',$maLopTemp.'%');
        $maLopMax = $findLop->max('Mã Lớp');
        
        if($findLop->get()->count() == 0 ){
            $maLopNew = $maLopTemp.'01';          
        } else {
            $maLopNew = $maLopTemp.str_pad((substr($maLopMax,9) + 1),'2',0,STR_PAD_LEFT);
        }
        $lophoc = new Lophoc([
            'Mã Lớp' => $maLopNew,
            'Lớp' => $request->lop,
            'Mã Môn Học' => $maMonHoc,
            'Mã Giáo Viên' => $request->magiaovien,
            'Ngày Bắt Đầu' => $request->batdau,
            'Ngày Kết Thúc' => $request->ketthuc,
            'branch' => $maCoSo
         ]);

         $lophoc->save();

         return response()->json(['code' => 200, 'message' => 'Tạo thành công'], 200);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($str)
    {
        $lophoc = Lophoc::where('Mã Lớp','like', '%'.$str.'%')->orwhere('Lớp','like','%'.$str.'%')->orwhere('LOPHOC.Mã Môn Học','like','%'.$str.'%')
        ->orwhere('LOPHOC.Mã Giáo Viên','like','%'.$str.'%')
        ->orwhere('Ngày Bắt Đầu','like','%'.$str.'%')->orwhere('Ngày Kết Thúc','like','%'.$str.'%')->orwhere('branch','like','%'.$str.'%');

        $result = $lophoc->join('DANHSACHMONHOC', 'LOPHOC.Mã Môn Học', '=', 'DANHSACHMONHOC.mamon')->leftjoin('GIAOVIEN', 'LOPHOC.Mã Giáo Viên', '=', 'GIAOVIEN.Mã Giáo Viên')
        ->join('COSO', 'LOPHOC.branch', '=', 'COSO.Cơ Sở')
        ->select('Mã Lớp', 'Lớp', 'LOPHOC.Mã Môn Học', 'DANHSACHMONHOC.name', 'LOPHOC.Mã Giáo Viên','GIAOVIEN.Họ Và Tên', 
        'Ngày Bắt Đầu', 'Ngày Kết Thúc', 'branch', 'COSO.Tên Cơ Sở','Lý Do Kết Thúc', 'Mã Nhân Viên KT Lớp')->orderBy('ID','desc')->paginate(30);
     
        if ($lophoc->count() == 0){
            return response()->json(['code' => 401, 'message' => 'Không tìm thấy'], 200);
        } else {
            $custom = collect(['code' => 200]);
            $data = $custom->merge($result);
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
        $request->validate([
            'magiaovien' => 'nullable|string',
            'batdau' => 'required|date',
            'ketthuc' => 'required|date',
            'LyDoKetThuc' => 'nullable|string',
            'NhanVienKT' => 'nullable|string'

        ]);

        $lophoc = Lophoc::where('Mã Lớp', $id);

        if ($lophoc->get()->count() == 0 ){
            return response()->json(['code' => 401, 'message' => 'Không tìm thấy'], 200);
        } else {
            $lophoc->update([
                'Mã Giáo Viên' => $request->magiaovien,
                'Ngày Bắt Đầu' => $request->batdau,
                'Ngày Kết Thúc' => $request->ketthuc,
                'Lý Do Kết Thúc' => $request->LyDoKetThuc,
                'Mã Nhân Viên KT Lớp' => $request->NhanVienKT,
            ]);
     
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
        $lophoc = Lophoc::where('Mã Lớp', $id);
        if ($lophoc->count() == 0){
            return response()->json(['code' => 401, 'message' => 'Không tìm thấy'], 200);
        } else {
            $lophoc->delete();
            return response()->json(['code' => 200, 'message' => 'Xóa thành công'], 200);
        }
    }

    public function classNullTeacher($id){
        $lophoc = Lophoc::where('LOPHOC.Mã Giáo Viên','!=' ,$id);
        if ($lophoc->count() == 0 ){
            return response()->json(['code' => 401, 'message' => 'Không tìm thấy lớp'], 200);
        } else {
            $lophoc = $lophoc->join('DANHSACHMONHOC', 'LOPHOC.Mã Môn Học', '=', 'DANHSACHMONHOC.mamon')->join('GIAOVIEN', 'LOPHOC.Mã Giáo Viên', '=', 'GIAOVIEN.Mã Giáo Viên')
            ->join('COSO', 'LOPHOC.branch', '=', 'COSO.Cơ Sở')
            ->select('Mã Lớp', 'Lớp', 'LOPHOC.Mã Môn Học', 'DANHSACHMONHOC.name', 'LOPHOC.Mã Giáo Viên', 'GIAOVIEN.Họ Và Tên', 
            'Ngày Bắt Đầu', 'Ngày Kết Thúc', 'branch', 'COSO.Tên Cơ Sở')->paginate(30);
            $custom = collect(['code' => 200]);
            $data = $custom->merge($lophoc);
            return response()->json($data, 200)->header('charset','utf-8');
        }
    }
}
