<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Phonghoc;
class PhonghocController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        if (Phonghoc::get()->count() == 0) {
            return response()->json(['code' => 401, 'message' => 'Không tìm thấy'], 200);
        } else {
            $phonghoc = Phonghoc::join('coso','phonghoc.branch','=','coso.Cơ Sở')->select('phonghoc.*','coso.Tên Cơ Sở')->paginate(15);
            $custom = collect(['code' => 200]);
            $data = $custom->merge($phonghoc);
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
            'coso' => 'required|string',
            'succhua' => 'numeric',
            'ghichu' => 'string',
        ]);

        $max = Phonghoc::where('branch', $request->coso)->max('Mã Phòng Học');
        $ma_phong_hoc = str_pad(substr($max, -3) + 1, '7', ''.$request->coso.'P000', STR_PAD_LEFT);

        $phonghoc = new Phonghoc([
            'Mã Phòng Học' => $ma_phong_hoc,
            'Sức Chứa' => $request->succhua,
            'Ghi Chú' => $request->ghichu,
            'branch' => $request->coso
        ]);

        $phonghoc->save();
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
        $phonghoc = Phonghoc::where('Mã Phòng Học','like', '%'.$str.'%')->orwhere('Sức Chứa','like','%'.$str.'%')->orwhere('branch','like','%'.$str.'%');
        
        if ($phonghoc->get()->count() == 0) {
            return response()->json(['code' => 401, 'message' => 'Không tìm thấy'], 200);
        } else {
            $result = $phonghoc->join('coso','phonghoc.branch','=','coso.Cơ Sở')->select('phonghoc.*','coso.Tên Cơ Sở')->paginate(15);
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
            'succhua' => 'numeric',
            'ghichu' => 'string',
        ]);


        if (Phonghoc::where('Mã Phòng Học',$id)->count() == 0 ){
            return response()->json(['code' => 401, 'message' => 'Không tìm thấy'], 200);
        } else {
            Phonghoc::where('Mã Phòng Học',$id)->update(['Sức Chứa' => $request->succhua, 'Ghi Chú' => $request->ghichu]);
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
        if (Phonghoc::where('Mã Phòng Học',$id)->count() == 0 ){
            return response()->json(['code' => 401, 'message' => 'Không tìm thấy'], 200);
        } else {
            Phonghoc::where('Mã Phòng Học',$id)->delete();
            return response()->json(['code' => 200, 'message' => 'Xóa thành công'], 200);
        }
    }
}
