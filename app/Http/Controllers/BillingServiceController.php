<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillingQuota;
use App\Models\Token;
use App\Models\User;
use App\Models\Workspace;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use MongoDB\Driver\Session;

class BillingServiceController extends Controller
{
    public function importCSV(Request $request){
        $request->validate([
            'file' => 'required'
        ]);

        $file = $request->file('file');
        $fileContents = file($file->getPathname());

        $user = User::query()->where('username', 'demo1')->first();
        $workspace = Workspace::query()->where('title', 'My App')->first();
        foreach ($fileContents as $line) {
            $data = str_getcsv($line);
            if($data[0] != 'username'){
                error_log($data[2]);
                $token = Token::query()->where('name', $data[2])->first();
                $ms = ((float) $data[3])/1000;
                $created_at = Carbon::parse($data[4]);
                $per_sec = ((float) $data[6]);

                $new_bill = new Bill();
                $new_bill->per_sec = $per_sec;
                $new_bill->created_at = Carbon::parse($created_at);
                $new_bill->time = $ms;
                $new_bill->total = $ms*$per_sec;
                $new_bill->token = $token;
                $new_bill->save();

                error_log($new_bill->id);
                error_log($token->id);
                error_log($new_bill->created_at->month);
            }
        }

        return response()->json([
            'status' => 'success'
        ], 201);
    }

    public function registerPage(Request $request){
        if(Auth::check()){
            return redirect('/');
        }
        return view('pages.register', []);
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }

        $exist_user = User::all()->where('username', $request->get('username'))->first();
        if($exist_user){
            return response()->json([
                'error' => 'User already registered with same username'
            ], 401);
        }

        $new_user = new User();
        $new_user->username = $request->get('username');
        $new_user->password = Hash::make($request->get('password'));
        $new_user->save();

        $cred = $request->only('username', 'password');
        if(Auth::attempt($cred)){
            return redirect('/');
        }

        return redirect('/register');
    }



    public function loginPage(Request $request){
        if(Auth::check()){
            return redirect('/');
        }
        return view('pages.login', []);
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }


        $cred = $request->only('username', 'password');
        if(Auth::attempt($cred)){
            return redirect('/');
        }

        return redirect('/login')->with(['message' => 'Username or password is wrong']);
    }

    public function logout(Request $request){
        Auth::logout();
        return redirect('/login');
    }



    public function homePage(Request $request){

        $allWorkpaces = Workspace::query()->where('user', $request->user()->id)->get();
        return view('pages.home', [
            'workspaces' => $allWorkpaces
        ]);
    }




    public function createWorkspace(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }

        $new_workspace = new Workspace();
        $new_workspace->title = $request->get('name');
        $new_workspace->description = $request->get('description');
        $new_workspace->user = $request->user()->id;
        $new_workspace->save();



        return redirect('/');
    }



    public function workspacePage(Request $request, Workspace $workspace){
        $allTokens = Token::query()->where('workspace', $workspace->id)->get();

        $billingQuota = BillingQuota::query()->where('workspace', $workspace->id)->first();

        return view('pages.workspace', [
            'workspace' => $workspace,
            'all_tokens' => $allTokens,
            'token_id' => \Illuminate\Support\Facades\Session::get('token_id'),
            'quota' => $billingQuota
        ]);
    }

    public function createToken(Request $request, Workspace $workspace){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }

        $new_token = new Token();
        $new_token->name = $request->get('name');
        $new_token->token = Str::random(60);
        $new_token->workspace = $workspace->id;
        $new_token->save();



        return redirect()->back()->with(['token_id' => $new_token->id]);
    }




    public function deactivateToken(Request $request, Token $token){

        $token->deactivated = True;
        $token->deactivated_at = Carbon::now();
        $token->save();


        return redirect()->back();
    }




    public function createBillingQuota(Request $request, Workspace $workspace){
        $validator = Validator::make($request->all(), [
            'limit' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }

        $new_quota = new BillingQuota();
        $new_quota->workspace = $workspace->id;
        $new_quota->limit = $request->get('limit');
        $new_quota->save();



        return redirect()->back();
    }


    public function deleteBillingQuota(Request $request, Workspace $workspace){


        $quota = BillingQuota::query()->where('workspace', $workspace->id)->first();
        if($quota){
            $quota->delete();
        }



        return redirect()->back();
    }




    public function billsPage(Request $request, Workspace $workspace){

        $seletedMonth = Carbon::parse($request->get('month', now()->toString()));

        $allTokens = Token::query()->where('workspace', $workspace->id)->get();

        $targetTokens = [];
        $total_cost = 0;

        foreach ($allTokens as $token){
            $billsOfToken = Bill::query()->whereMonth('created_at', $seletedMonth->month)->where('token', $token->id)->get();

            if($billsOfToken->count() > 0){
                $targetTokens[] = [
                    'title' => $token->name,
                    'bills' =>  $billsOfToken
                ];
            }


            foreach ($billsOfToken as $bill){
                $total_cost = $total_cost + $bill->total;
            }
        }




        $targetMonths = [];
        for ($i = 0; $i < 10; $i++){
            $targetMonthItem = now()->subtract('month', $i);
            array_push($targetMonths, $targetMonthItem);
        }

//        $new_bill = new Bill();
//        $new_bill->per_sec = 0.05;
//        $new_bill->total = 0.05 * 2.4;
//        $new_bill->time = 2.4;
//        $new_bill->token = Token::all()->first()->id;
//        $new_bill->created_at = $seletedMonth;
//        $new_bill->save();


        $billingQuota = BillingQuota::query()->where('workspace', $workspace->id)->first();

        return view('pages.bills', [
            'workspace' => $workspace,
            'target_tokens' => $targetTokens,
            'total_cost' => $total_cost,
            'quota' => $billingQuota,
            'target_months' => $targetMonths,
            'selected_month' => $seletedMonth
        ]);
    }
}
