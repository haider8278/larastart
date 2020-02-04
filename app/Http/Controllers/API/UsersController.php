<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\User;
use Faker\Provider\Image;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $per_page = 5;
        $this->authorize('isAdmin');
        if($request->s){
            return User::where('name','like',"%$request->s%")->where('email','like',"%$request->s%")->paginate($per_page);
        }if($request->orderby){
            return User::orderBy($request->orderby,$request->order)->paginate($per_page);
        }else{
            return User::latest()->paginate($per_page);
        }
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
        $this->authorize('isAdmin');
        $this->validate($request,[
            'name'  => 'required',
            'email' => 'required|email|string|max:191|unique:users',
            'password'=>'required|min:8',
            'type'  => 'required'
        ]);
        return User::Create([
            'name'  => $request->name,
            'email'  => $request->email,
            'type'  => $request->type,
            'bio'  => $request->bio,
            'password'  => bcrypt($request->password),
        ]);
    }

    public function profile(){
        return auth('api')->user();
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {

        $user = auth('api')->user();
        $this->validate($request,[
            'name'  => 'required',
            'email' => 'required|email|string|max:191|unique:users,email,'.$user->id,
            'password'=>'sometimes|required|min:8',
        ]);
        if($request->password != ""){
            $user->password = bcrypt($request->password);
        }
        if($request->passport != ""){
            $user->password = bcrypt($request->password);
        }
        $currentPhoto = $user->photo;
        if($request->photo != $currentPhoto){
            $filename = time().'.' . explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];
            \Image::make($request->photo)->save(public_path('images/profile/').$filename);
            $user->photo = $filename;
            $currentPhotoPath = public_path('images/profile/').$currentPhoto;
            if($currentPhoto != 'profile.png' && file_exists($currentPhotoPath)){
                @unlink($currentPhotoPath);
            }
        }
        $user->name = $request->name;
        $user->email = $request->email;
        $user->bio = $request->bio;

        if($user->update()){

            return response()->json(['type'=>'success','msg'=>'User Updated Succcessfully.','title'=>'Success'], 200);
        }else{
            return response()->json(['type'=>'danger','msg'=>'Ooops something went wrong','title'=>'Error'], 422);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
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
        //
        $this->authorize('isAdmin');
        $user = User::findorFail($id);
        $this->validate($request,[
            'name'  => 'required',
            'email' => 'required|email|string|max:191|unique:users,email,'.$user->id,
            'password'=>'sometimes|required|min:8',
        ]);

        if($request->password != ""){
            $user->password = bcrypt($request->password);
        }
        $user->name = $request->name;
        $user->email = $request->email;
        $user->type = $request->type;
        $user->bio = $request->bio;
        if($user->save()){
            return response()->json(['type'=>'success','msg'=>'User Updated Succcessfully.','title'=>'Success'], 200);
        }else{
            return response()->json(['type'=>'danger','msg'=>'Ooops something went wrong','title'=>'Error'], 422);
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
        //
        $this->authorize('isAdmin');
        $user =  User::findorFail($id);
        if($user->delete()){
            return response()->json(['type'=>'success','msg'=>'User Deleted Succcessfully.','title'=>'Success'], 200);
        }else{
            return response()->json(['type'=>'danger','msg'=>'User Deleted Succcessfully.','title'=>'Error'], 422);
        }

    }
}
