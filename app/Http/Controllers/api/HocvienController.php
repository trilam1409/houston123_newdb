<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Hocvien;
use App\Coso;

class HocvienController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        if (Hocvien::get()->count() == 0) {
            return response()->json(['code' => 401, 'message' => 'Không tìm thấy'], 200);
        } else {
            $hocvien = Hocvien::join('COSO', 'USERS.Cơ Sở', '=', 'COSO.Cơ Sở')->select('USERS.*', 'COSO.Tên Cơ Sở')->orderBy('ID','desc')->paginate(30);
            $custom = collect(['code' => 200]);
            $data = $custom->merge($hocvien);
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
            'hovaten' => 'required|string',
            'hinhanh' => 'nullable|string',
            'lop' => 'required|string',
            'sdt' => 'nullable|numeric',
            'diachi' => 'nullable|string',
            'ngaysinh' => 'nullable|date',
            'hoclucvao' => 'nullable|string',
            'ngaynhaphoc' => 'nullable|date',
            'truonghocchinh' => 'nullable|string',
            'tenNT1' => 'nullable|string',
            'ngheNT1' => 'nullable|string',
            'sdtNT1' => 'nullable|numeric',
            'tenNT2' => 'nullable|string',
            'ngheNT2' => 'nullable|string',
            'sdtNT2' => 'nullable|numeric',
            'lydobietHouston' => 'required|string',
            'chinhthuc' => 'required|numeric',
            'coso' => 'required|string'
        ]);

        $id = Hocvien::max('User ID');
        $id_new = str_pad(substr($id, -5) + 1, '7', 'HT00000', STR_PAD_LEFT);
        $maCoSo = CoSo::select('Cơ Sở')->where('Tên Cơ Sở',$request->coso)->value('Cơ Sở');
         $hocvien = new Hocvien ([
            'User ID' => $id_new,
            'Họ Và Tên' => $request->hovaten,
            'Hình Ảnh' => $request->hinhanh,
            'Lớp' => $request->lop,
            'Số Điện Thoại' => $request->sdt,
            'Địa Chỉ' => $request->diachi,
            'Ngày Sinh' => $request->ngaysinh,
            'Học Lực Đầu Vào' => $request->hoclucvao,
            'Ngày Nhập Học' => $request->ngaynhaphoc,
            'Tên Trường' => $request->truonghocchinh,
            'Họ Và Tên (NT1)' => $request->tenNT1,
            'Số Điện Thoại (NT1)' => $request->sdtNT1,
            'Nghề Nghiệp (NT1)' => $request->ngheNT1,
            'Họ Và Tên (NT2)' => $request->tenNT2,
            'Số Điện Thoại (NT2)' => $request->sdtNT2,
            'Nghề Nghiệp (NT2)' => $request->ngheNT2,
            'Biết Houston123 Như Thế Nào' => $request->lydobietHouston,
            'Chính Thức' => $request->chinhthuc,
            'Cơ Sở' => $maCoSo
         ]);
        

        $hocvien->save();
        return response()->json(['code' => 200, 'message' => 'Tạo học viên thành công'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($str)
    {   
        $hocvien = Hocvien::where('User ID','like','%'.$str.'%')->orwhere('Họ Và Tên', 'like','%'.$str.'%')->orwhere('USERS.Cơ Sở','like','%'.$str.'%');
        if ($hocvien->get()->count() == 0) {
            return response()->json(['code' => 401, 'message' => 'Không tìm thấy'], 200);
        } else {
            $result = $hocvien->join('COSO', 'USERS.Cơ Sở', '=', 'COSO.Cơ Sở')->select('USERS.*', 'COSO.Tên Cơ Sở')->orderBy('ID','desc')->paginate(30);
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
            'hovaten' => 'required|string',
            'hinhanh' => 'nullable|string',
            'lop' => 'required|string',
            'sdt' => 'nullable|numeric',
            'diachi' => 'nullable|string',
            'ngaysinh' => 'nullable|date',
            'hoclucvao' => 'nullable|string',
            'ngaynhaphoc' => 'nullable|date',
            'truonghocchinh' => 'nullable|string',
            'tenNT1' => 'nullable|string',
            'ngheNT1' => 'nullable|string',
            'sdtNT1' => 'nullable|numeric',
            'tenNT2' => 'nullable|string',
            'ngheNT2' => 'nullable|string',
            'sdtNT2' => 'nullable|numeric',
            'lydobietHouston' => 'required|string',
            'chinhthuc' => 'required|numeric',
            'coso' => 'required|string',
            'NgayNghiHoc' => 'nullable|date',
            'LyDoNghi' => 'nullable|string'
        ]);
            
   

        $hocvien = Hocvien::where('User ID',$id);
        if($hocvien->get()->count() == 0){
            return response()->json(['code' => 401, 'message' => 'Không tìm thấy'], 200);
        } else {
            $maCoSo = Coso::select('Cơ Sở')->where('Tên Cơ Sở',$request->coso)->value('Cơ Sở');
            $hocvien->update(['Họ Và Tên' => $request->hovaten,
            'Hình Ảnh' => $request->hinhanh,
            'Lớp' => $request->lop,
            'Số Điện Thoại' => $request->sdt,
            'Địa Chỉ' => $request->diachi,
            'Ngày Sinh' => $request->ngaysinh,
            'Học Lực Đầu Vào' => $request->hoclucvao,
            'Ngày Nhập Học' => $request->ngaynhaphoc,
            'Tên Trường' => $request->truonghocchinh,
            'Họ Và Tên (NT1)' => $request->tenNT1,
            'Số Điện Thoại (NT1)' => $request->sdtNT1,
            'Nghề Nghiệp (NT1)' => $request->ngheNT1,
            'Họ Và Tên (NT2)' => $request->tenNT2,
            'Số Điện Thoại (NT2)' => $request->sdtNT2,
            'Nghề Nghiệp (NT2)' => $request->ngheNT2,
            'Biết Houston123 Như Thế Nào' => $request->lydobietHouston,
            'Chính Thức' => $request->chinhthuc,
            'Cơ Sở' => $maCoSo,
            'Ngày Nghỉ Học' => $request->NgayNghiHoc,
            'Lý Do Nghỉ' => $request->LyDoNghi
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
        $hocvien = Hocvien::where('User ID', $id);
        if($hocvien->count() == 0){
            return response()->json(['code' => 401, 'message' => 'Không tìm thấy'],200);
            
        } else {
            $hocvien->delete();
            return response()->json(['code' => 200, 'message' => 'Xóa thành công'],200);
        }
        
    }

    
    public function DataTraining(){
     
        if (Hocvien::get()->count() == 0){
            return response()->json(['code' => 401, 'message' => 'Không tìm thấy'], 200);
        } else {
            $hocvien = Hocvien::join('COSO', 'USERS.Cơ Sở', '=', 'COSO.Cơ Sở')
            ->select('USERS.Họ Và Tên', 'USERS.Ngày Sinh','USERS.Lớp',  'USERS.Số Điện Thoại', 'USERS.Học Lực Đầu Vào', 'USERS.Ngày Nhập Học','USERS.Tên Trường', 'USERS.Biết Houston123 Như Thế Nào', 
            'USERS.Địa Chỉ', 'COSO.Tên Cơ Sở', 'USERS.Họ Và Tên (NT1)', 'USERS.Số Điện Thoại (NT1)','USERS.Nghề Nghiệp (NT1)',
            'USERS.Họ Và Tên (NT2)','USERS.Số Điện Thoại (NT2)','USERS.Nghề Nghiệp (NT2)')
            ->orderBy('ID', 'desc')->paginate(30);
            $custom = collect(['code' => 200]);
            $data = $custom->merge($hocvien);
            return response()->json($data, 200)->header('charset','utf8');
        }
    }
}
