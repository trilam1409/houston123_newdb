<?php

namespace App\Http\Controllers\api;
use App\Coso;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CosoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        if (Coso::get()->count() == 0){
            return response()->json(['code' => 401, 'message' => 'Không tìm thấy'], 200);
        } else {
            $coso = Coso::paginate(15);
            $custom = collect(['code' => 200]);
            $data = $custom->merge($coso);
            return response()->json($data, 200)->header('charset', 'utf-8')->header('Access-Control-Allow-Origin', '*');
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
            'macoso' => 'required|string',
            'tencoso' => 'required|string'
        ]);

        if (Coso::where('Cơ Sở',$request->macoso)->get()->count() == 0){
            $coso = new Coso([
                'Cơ Sở' => $request->macoso,
                'Tên Cơ Sở' => $request->tencoso
            ]);
            $coso->save();
            //return response()->json(['code' => 200,'message' => 'Tạo thành công'], 200);
            return $this->show($request->macoso);
        } else {
            return response()->json(['code' => 422, 'message' => 'Mã cơ sở đã tồn tại'], 422);
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
        $coso = Coso::where('Cơ Sở','like','%'.$str.'%')->orwhere('Tên Cơ Sở','like','%'.$str.'%');
        if($coso->count() == 0){
            return response()->json(['code' => 401, 'message' => 'Không tìm thấy'], 200);
        } else {
            $custom = collect(['code' => 200]);
            $data = $custom->merge($coso->paginate(15));
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
            'tencoso' => 'required|string'
        ]);
        $coso = Coso::where('Cơ Sở', $id);

        if($coso->count() == 0){
            return response()->json(['code' => 401, 'message' => 'Không tìm thấy'], 200);
        } else{
            $coso->update(['Tên Cơ Sở' => $request->tencoso]);
            //return response()->json(['code' => 200, 'message' => 'Cập nhật thành công'], 200);
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
        $coso = Coso::where('Cơ Sở', $id);
        if($coso->count() == 0){
            return response()->json(['code' => 401, 'message' => 'Không tìm thấy'], 200);
        } else{
            $coso->delete();
            return response()->json(['code' => 200, 'message' => 'Xóa thành công'], 200);
        }
    }
}
