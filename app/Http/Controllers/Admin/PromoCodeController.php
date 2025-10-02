<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\PromoCode;

use Auth;
use Config;

class PromoCodeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
	/**
     * All Vendors.
     *
     * @return \Illuminate\Http\Response
     */
	public function index(Request $request)
	{
		//check authorization end
        $query 		= PromoCode::where('status', 1);
        $totalData 	= $query->count();	//for all data
        $lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));
        return view('Admin.feature.promocode.index',compact(['lists', 'totalData']));
    }

	public function create(Request $request)
	{
		return view('Admin.feature.promocode.create');
	}

	public function store(Request $request)
	{
		//check authorization end
		if ($request->isMethod('post'))
		{
			$this->validate($request,
                [
                'title' => 'required',
                'code' => 'required|unique:promo_codes,code',
                //'discount_percentage' => 'required|numeric|max:100'
                ]
            );

            $requestData  = 	$request->all(); //dd($requestData);
            $obj		 = 	new PromoCode;
			$obj->title	=	@$requestData['title'];
			$obj->code	=	@$requestData['code'];
            $obj->discount_percentage	=	@$requestData['discount_percentage'];
            $obj->status	= 1;
			$saved	=	$obj->save();
            if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/promo-code')->with('success', 'Promo code Added Successfully');
			}
		}
        return view('Admin.feature.promocode.create');
	}

	public function edit(Request $request, $id = NULL)
	{
        //check authorization end
        if ($request->isMethod('post')) {
            $requestData 		= 	$request->all(); //dd($requestData);
            $this->validate($request,
                [
                'title' => 'required',
                'code' => 'required|unique:promo_codes,code,'.$requestData['id'],
                //'discount_percentage' => 'required|numeric|max:100'
                ]
            );

            $obj		= 	PromoCode::find(@$requestData['id']);
            $obj->title	=	@$requestData['title'];
            $obj->code	=	@$requestData['code'];
            $obj->discount_percentage	=	@$requestData['discount_percentage'];
			$saved	=	$obj->save();
            if(!$saved) {
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			} else {
				return Redirect::to('/admin/promo-code')->with('success', 'Promo Code Edited Successfully');
			}
		} else {
			if(isset($id) && !empty($id)) {
                $id = $this->decodeString($id);
				if(PromoCode::where('id', '=', $id)->exists()) {
					$fetchedData = PromoCode::find($id);
                    return view('Admin.feature.promocode.edit', compact(['fetchedData']));
				} else {
					return Redirect::to('/admin/promo-code')->with('error', 'Promo Code Not Exist');
				}
			} else {
				return Redirect::to('/admin/promo-code')->with('error', Config::get('constants.unauthorized'));
			}
		}
    }


    public function checkpromocode(Request $request)
    {
        //check promo code is exist or not
        $promoCodeIsExist = PromoCode::where('code', $request->promo_code_val)->select('id')->first();
        //dd($promoCodeIsExist);
	    if(isset($promoCodeIsExist) && $promoCodeIsExist['id']){
            $promocodeUsesCount = DB::table('promocode_uses')->where('promocode_id',$promoCodeIsExist['id'])->where('client_id',$request->client_id)->count();
            //dd($promocodeUsesCount);
            if(isset($promocodeUsesCount) && $promocodeUsesCount >0){
                return json_encode(array('success'=>false, 'msg' =>'This Promo Code is already used for this user.Pls try other'));
            } else {
                /*DB::table('promocode_uses')->insert([
                    'client_id' => $request->client_id,
                    'promocode_id' => $promoCodeIsExist['id'],
                    'promocode' => $request->promo_code_val
                ]);*/
                return json_encode(array('success'=>true, 'msg' =>'Promo Code is successfully used.Now you can book your appointment in free.','promocode_id' => $promoCodeIsExist['id'] ));
	        }
        } else {
            return json_encode(array('success'=>false, 'msg' =>'Promo Code is not exist'));
        }
    }
}
