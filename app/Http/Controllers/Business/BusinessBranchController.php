<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Business\Business;
use App\Models\Business\BusinessUser;
use App\Models\Business\BusinessBranch;
use App\Models\Business\BusinessAddress;
use App\Models\Business\BusinessJob;
use App\Models\Business\BusinessOrganization;
use App\Models\Business\BusinessJobLevel;
use App\Models\Business\BusinessSchedule;
use App\Models\Business\BusinessAttendance;
use App\Models\BloopyWorks\PayrollSchedule;

use App\Models\Address;

class BusinessBranchController extends Controller
{
    public function businesBranch($id) 
    {
        $business = Business::select('id', 'business_name', 'business_logo')
                            ->find($id);

        $branch = BusinessBranch::select('id','businessBranch_name as business_name','businessBranch_employee as employee')
                            ->where('businessBranch_business','=',$business->id)
                            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'business' => $business,
                'branch' => $branch
            ]
        ]);
    }
}
