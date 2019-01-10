<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Truongtiemnang;
use App\Http\Controllers\Controller;
class TruongtiemnangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        if (Truongtiemnang::get()->count() == 0){
            return response()->json(['code' => 401],200);
        } else {
            $truong = Truongtiemnang::join('coso', 'truongtiemnang.Cơ Sở','=','coso.Cơ Sở')->select('truongtiemnang.*', 'coso.Tên Cơ Sở')->paginate(15);
            $custom = collect(['code' => 200]);
            $data = $custom->merge($truong);
            return response()->json($data,200)->header('charser','utf-8');
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
            'ten' => 'required',
            'diadiem',
            'coso' => 'required'
        ]);

        if (Truongtiemnang::where('Tên Trường',$request->ten)->get()->count() == 0 ){
            $truong = new Truongtiemnang([
                'Tên Trường' => $request->ten,
                'Địa Điểm' => $request->diadiem,
                'Cơ Sở' => $request->coso
            ]);

            $truong->save();
            //return response()->json(['code' => 200, 'messsage' => 'Tạo thành công'], 200);
            return $this->show($request->ten);
        } else {
            return response()->json(['code' => 422, 'messsage' => 'Đã tồn tại'], 422);
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
        $truong = Truongtiemnang::where('ID','like','%'.$str.'%')->orwhere('Tên Trường','like','%'.$str.'%')->orwhere('truongtiemnang.Cơ Sở', 'like', '%'.$str.'%');
        if ($truong->get()->count() == 0){
            return response()->json(['code' => 401, 'message' => "Không tìm thấy"], 200);
        } else {
            $truong = $truong->join('coso', 'truongtiemnang.Cơ Sở','=','coso.Cơ Sở')->select('truongtiemnang.*', 'coso.Tên Cơ Sở')->paginate(15);
            $custom = collect(['code' => 200]);
            $data = $custom->merge($truong);
            return response()->json($data, 200)->header('charser','utf-8');
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
            'ten' => 'required',
            'diadiem',
            'coso' => 'required'
        ]);

        $truong = Truongtiemnang::where('ID', $id);
        if ($truong->get()->count() == 0 ){
            return response()->json(['code' => 401, 'messsage' => 'Không tìm thấy'], 200);
        } else {
            $truong->update(['Tên Trường' => $request->ten, 'Địa Điểm' => $request->diadiem, 'Cơ Sở' => $request->coso]);
            //return response()->json(['code' => 200, 'messsage' => 'Cập nhật thành công'], 200);
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
        $truong = Truongtiemnang::where('ID', $id);
        if ($truong->get()->count() == 0 ){
            return response()->json(['code' => 401, 'messsage' => 'Không tìm thấy'], 200);
        } else {
            $truong->delete();
            return response()->json(['code' => 200, 'messsage' => 'Xóa thành công'], 200);
        }
    }
}
