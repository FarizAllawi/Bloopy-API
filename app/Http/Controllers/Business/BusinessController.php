<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
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
use App\Models\User;
use Mail;


class BusinessController extends Controller
{
    public function businessDetail($id)
    {
        $business = BusinessBranch::find($id);
        if (!$business) {
            return response()->json([
                'status' => 'error',
                'message' => 'Business Not Found'
            ], 422);     
        }

        return response()->json([
            'status' => 'success',
            'data' => $business
        ]);

    }

    public function updateBusiness(Request $request, $id) 
    {
        $business = BusinessBranch::find($id);
        if (!$business) {
            return response()->json([
                'status' => 'error',
                'message' => 'Business Not Found'
            ], 422);     
        }

        $data = $request->all();
        $validator = Validator::make($data,[
            'business_name' => 'required|string|max:255|unique:business',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => 'the given data invalid',
                'errors' => $validator->errors()
            ], 422);       
        }

        $business->fill($data);
        $business->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Business Updated',
        ]);
    }

    public function listBusiness(Request $request) 
    {
        $userBusiness = DB::table('user_business')
                            ->join('users','user_business.userBusiness_user','=','users.id') //Join table users
                            ->join('business','user_business.userBusiness_business','=','business.id') // Join table business
                            ->join('business_branch', 'business_branch.businessBranch_business','=','business.id') // Join table business branch
                            ->where('user_business.userBusiness_user','=',$request->user()->id)
                            ->select('business_branch.id as business_id', 'business_branch.businessBranch_name as business_name', 'user_business.userBusiness_status as user_status')
                            ->get();

        return response()->json([
            'status' => 'success',
            'data' =>  $userBusiness
        ]);
    }

    public function selectBusiness(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'platform' => 'required|string|in:works,finance,links,store',
            "business_id" => 'required|integer'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => 'the given data invalid',
                'errors' => $validator->errors()
            ], 422);       
        }

        // Check Business
        $business = BusinessBranch::find($request->business_id);
        if (!$business) {
            return response()->json([
                'status' => 'error',
                'message' => 'Business Branch Not Found'
            ], 404);    
        }

        // is the user a business member
        $userBusiness = DB::table('user_business')
                          ->join('users','user_business.userBusiness_user','=','users.id') //Join table users
                          ->join('business','user_business.userBusiness_business','=','business.id') // Join table business
                          ->join('business_branch', 'business_branch.businessBranch_business','=','business.id') // Join table business branch
                          ->where('user_business.userBusiness_user','=',$request->user()->id)
                          ->where('business_branch.id','=',$request->business_id)
                          ->select('business_branch.id as businessBranch_id', 'business_branch.businessBranch_name', 'user_business.userBusiness_user', 'user_business.userBusiness_status')
                          ->first();
        
        if (!$userBusiness)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'User Not in Business'
            ], 401);    
        }

        $accessToken = auth()->user()->token();

        

        $employeeData = DB::table('business_jobLevel')
                          ->join('employee','business_jobLevel.id','=','employee.employee_jobLevel')
                          ->where('employee.employee_userBusiness','=',$userBusiness->userBusiness_user)
                          ->select('business_jobLevel.businessJobLevel_name as job_level')
                          ->first();


        $scopes = ['bloopy-owner'];
        if ($userBusiness->userBusiness_status != 'owner')
        {
            if ($employeeData->job_level === 'C-Level') $scopes = ['bloopy-works-c-level'];
            if ($employeeData->job_level === 'Middle Management') $scopes = ['bloopy-works-middle-management'];
            if ($employeeData->job_level === 'First-Level Management') $scopes = ['bloopy-works-first-level-management'];
            if ($employeeData->job_level === 'Intermediate or Experienced') $scopes = ['bloopy-works-intermediate-or-experienced'];
            if ($employeeData->job_level === 'Entry Level') $scopes = ['bloopy-works-entry-level'];
        }

        $accessToken = auth()->user()->token();

        // For user Login in web
        if (!$accessToken) {
            $user = User::find(auth()->user()->id);
            return response()->json([
                'status' => 'success',
                'message' => 'Token Created',
                'data' => [
                    'token' => $user->createToken('bloopy-'.$request->platform.'-token', $scopes)->accessToken
                ]
            ]);
        }

        $oauthAccessToken = DB::table('oauth_access_tokens')
                            ->where('id','=',$accessToken->id)
                            ->first();
        
                            $oauthScope = json_decode($oauthAccessToken->scopes);
        array_push($oauthScope, $scopes);

        $oauthAccessToken = DB::table('oauth_access_tokens')
                               ->where('id','=',$accessToken->id)
                               ->update([
                                    'name' => 'bloopy-'.$request->platform.'-token',
                                    'scopes' => $scopes,
                                ]);


        return response()->json([
            'status' => 'success',
            'message' => 'Access Token updated'
        ]); 
    }

    public function createBusiness(Request $request) 
    {        
        $validator = Validator::make($request->all(),[
            'business_name'      => 'required|string|max:255|unique:business',
            "business_employee"  => 'required|integer',
            "business_email"     => 'required|string|email',
            "business_phone"     => 'required|string',
            "business_npwp"      => 'required|integer',
            "business_klu"       => 'string|max:4',
            'business_status'    => 'required|string|in:central,branch',
            'address_name'       => 'required|string',
            'address_city'       => 'required|string',
            'address_state'      => 'required|string',
            'address_postalCode' => 'required|integer',
            'address_countryCode'=> 'required|string',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => 'the given data invalid',
                'errors' => $validator->errors()
            ], 422);       
        }

        $business = new Business();
        $business->business_name = Str::upper($request->business_name);
        $business->save();

        // Create user business data
        $userBusiness = new BusinessUser();
        $userBusiness->userBusiness_user = Auth::id();
        $userBusiness->userBusiness_business = $business->id;
        $userBusiness->userBusiness_status = 'owner';
        $userBusiness->save();

        // Create business branch
        $businessBranch = new BusinessBranch();
        $businessBranch->businessBranch_name = Str::upper($request->business_name.' - CENTRAL');
        $businessBranch->businessBranch_business = $business->id;
        $businessBranch->businessBranch_employee = $request->business_employee;
        $businessBranch->businessBranch_email = $request->business_email;
        $businessBranch->businessBranch_phone = $request->business_phone;
        $businessBranch->businessBranch_NPWPCode = $request->business_npwp;
        $businessBranch->businessBranch_status = 'central';
        $businessBranch->save();

        // Create business branch address
        $address = new Address();
        $address->address_name = str::ucfirst($request->address_name);
        $address->address_city = ucwords($request->address_city);
        $address->address_state = ucwords($request->address_state);
        $address->address_postalCode = $request->address_postalCode;
        $address->address_countryCode = Str::upper($request->address_countryCode);
        $address->save();

        $businessAddress = new BusinessAddress();
        $businessAddress->businessAddress_address = $address->id;
        $businessAddress->businessAddress_business = $businessBranch->id;
        $businessAddress->businessAddress_type = 'business';
        $businessAddress->save();

        // Generate business Organization & business job position data
        $this->generateData($businessBranch->id);

        return response()->json([
            'status' => 'success',
            'message' => 'business branch created'
        ]);
    }

    public function generateData($businessId) 
    {
        // Insert Job Level
        $this->generateJobLevel($businessId);

        // Insert Organization
        $this->generateOrganization($businessId);
        
        // Insert Job Position Data
        $this->generateJobPosition();

        // Insert Office Schedulle
        $this->generateOfficeSchedule($businessId);

        // Insert Payroll Schedule
        $payrollSchedule = new payrollSchedule();
        $payrollSchedule->payrollSchedule_type = 'monthly';
        $payrollSchedule->payrollSchedule_date = 25;
        $payrollSchedule->payrollSchedule_attendance = 20;
        $payrollSchedule->payrollSchedule_business = $businessId;
        $payrollSchedule->save();

    }

    private function generateOfficeSchedule($businessId) 
    {
        $schedule = [
            'monday', 'tuesday','wednesday','thursday','friday'
        ];

        $businessSchedule = new BusinessSchedule();
        $businessSchedule->businessSchedule_name = 'OFFICE SCHEDULE';
        $businessSchedule->businessSchedule_business = $businessId;
        $businessSchedule->save();

        foreach ($schedule as $data) {
            $attendance = new BusinessAttendance();
            $attendance->businessAttendance_name = $data;
            $attendance->businessAttendance_clockIn = '09:00';
            $attendance->businessAttendance_clockOut = '17:00';
            $attendance->businessAttendance_breakOut = '12:00';
            $attendance->businessAttendance_breakIn = '13:30';
            $attendance->businessAttendance_businessSchedule = $businessSchedule->id;
            $attendance->save();
        }
    }

    private function generateOrganization($businessId)
    {
        $organization = [
            [
                'parentOrganization' => 'Chief Executive Officer (CEO)',
                'childOrganization'  => [
                    'Finance Department', 'Marketing Department', 'IT Department', 'HRD & GA'
                ]
            ],
            [
                'parentOrganization' => 'Finance Department',
                'childOrganization'  => [
                    'Accounting' , 'Finance'
                ]
            ],
            [
                'parentOrganization' => 'Marketing Department',
                'childOrganization' => [
                    'Marketing' , 'Sales'
                ]
            ],
            [
                'parentOrganization' =>  'IT Department',
                'childOrganization' => [
                    'IT Product Development' , 'IT Infrastructure'
                ]
            ],
            [
                'parentOrganization' =>  'HRD & GA',
                'childOrganization' => [
                    'Human Resource Development (HRD)' , 'General Affair (GA)'
                ]
            ]
        ];

        foreach($organization as $data)
        {
            $parentOrganization = BusinessOrganization::where('businessOrganization_name','=', $data['parentOrganization'])->first();
            
            $organization = new BusinessOrganization();
            $organization->businessOrganization_name = $data['parentOrganization'];
            $organization->businessOrganization_parent = $parentOrganization ? $parentOrganization->id : 0;
            $organization->businessOrganization_business = $businessId;
            $organization->save();

            foreach($data['childOrganization'] as $childData) 
            {
                $child = new BusinessOrganization();
                $child->businessOrganization_name = $childData;
                $child->businessOrganization_parent = $organization->id;
                $child->businessOrganization_business = $businessId;
                $child->save();
            }

        }
    }

    private function generateJobPosition() 
    {
        $jobPosition = [
            [
                'jobLevel'     => 'C-Level',
                'organization' => 'Chief Executive Officer (CEO)',
                'parentPosition' => '',
                'positionName' => ['Chief Executive Officer (CEO)']
            ],
            [
                'jobLevel'     => 'C-Level',
                'organization' => 'Finance Department',
                'parentPosition' => 'Chief Executive Officer (CEO)',
                'positionName' => ['Chief Financial Officer (CFO)']
            ],
            [
                'jobLevel'     => 'C-Level',
                'organization' => 'IT Department',
                'parentPosition' => 'Chief Executive Officer (CEO)',
                'positionName' => ['Chief Technology Officer (CTO)'],
            ],
            [
                'jobLevel'     => 'C-Level',
                'organization' => 'Marketing Department',
                'parentPosition' => 'Chief Executive Officer (CEO)',
                'positionName' => ['Chief Marketing Officer (CMO)'],
            ],
            [
                'jobLevel'     => 'C-Level',
                'organization' => 'HRD & GA',
                'parentPosition' => 'Chief Executive Officer (CEO)',
                'positionName' => ['Chief Human Resource Officer (CHRO)'],
            ],
            [
                'jobLevel'     => 'First-Level Management',
                'organization' =>  'Accounting',
                'parentPosition' => 'Chief Financial Officer (CFO)',
                'positionName' =>  ['Accounting Manager']
            ],
            [
                'jobLevel'     => 'First-Level Management',
                'organization' =>  'Finance',
                'parentPosition' => 'Chief Financial Officer (CFO)',
                'positionName' =>  ['Finance Manager']
            ],
            [
                'jobLevel'     => 'First-Level Management',
                'organization' =>  'IT Product Development',
                'parentPosition' => 'Chief Technology Officer (CTO)',
                'positionName' =>  ['Product Manager']
            ],
            [
                'jobLevel'     => 'First-Level Management',
                'organization' =>  'IT Infrastructure',
                'parentPosition' => 'Chief Technology Officer (CTO)',
                'positionName' =>  ['Infrastructure Manager']
            ],
            [
                'jobLevel'     => 'First-Level Management',
                'organization' =>   'Marketing',
                'parentPosition' => 'Chief Marketing Officer (CMO)',
                'positionName' =>  ['Marketing Manager']
            ],
            [
                'jobLevel'     => 'First-Level Management',
                'organization' =>   'Sales',
                'parentPosition' => 'Chief Marketing Officer (CMO)',
                'positionName' =>  ['Sales Manager']
            ],
            [
                'jobLevel'     => 'First-Level Management',
                'organization' =>  'Human Resource Development (HRD)',
                'parentPosition' => 'Chief Human Resource Officer (CHRO)',
                'positionName' =>  ['HR Manager']
            ],
            [
                'jobLevel'     => 'First-Level Management',
                'organization' =>  'General Affair (GA)',
                'parentPosition' => 'Chief Human Resource Officer (CHRO)',
                'positionName' =>  ['GA Manager']
            ],
            [
                'jobLevel'     => 'Intermediate or Experienced',
                'organization' =>  'Accounting',
                'parentPosition' => 'Accounting Manager',
                'positionName' => ['Accounting Supervisor']
            ],
            [
                'jobLevel'     => 'Intermediate or Experienced',
                'organization' =>  'Finance',
                'parentPosition' => 'Finance Manager',
                'positionName' =>  ['Finance Supervisor']
            ],
            [
                'jobLevel'     => 'Intermediate or Experienced',
                'organization' =>  'IT Product Development',
                'parentPosition' => 'Product Manager',
                'positionName' =>  ['Product Supervisor']
            ],
            [
                'jobLevel'     => 'Intermediate or Experienced',
                'organization' =>  'IT Infrastructure',
                'parentPosition' => 'Infrastructure Manager',
                'positionName' => ['Infrastructure Supervisor']
            ],
            [
                'jobLevel'     => 'Intermediate or Experienced',
                'organization' =>  'Marketing',
                'parentPosition' => 'Marketing Manager',
                'positionName' =>  [ 'Marketing Supervisor']
            ],
            [
                'jobLevel'     => 'Intermediate or Experienced',
                'organization' =>  'Sales',
                'parentPosition' => 'Sales Manager',
                'positionName' =>  ['Sales Supervisor']
            ],
            [
                'jobLevel'     => 'Intermediate or Experienced',
                'organization' =>  'Human Resource Development (HRD)',
                'parentPosition' => 'HR Manager',
                'positionName' =>  ['HR Supervisor']
            ],
            [
                'jobLevel'     => 'Intermediate or Experienced',
                'organization' =>  'General Affair (GA)',
                'parentPosition' => 'GA Manager',
                'positionName' =>  ['GA Supervisor' ]
            ],
            [
                'jobLevel'     => 'Entry-Level',
                'organization' => 'Accounting',
                'parentPosition' => 'Accounting Supervisor',
                'positionName' =>  ['Accounting Staf' ]
            ],
            [
                'jobLevel'     => 'Entry-Level',
                'organization' => 'Finance',
                'parentPosition' => 'Finance Supervisor',
                'positionName' =>  ['Finance Staf']
            ],
            [
                'jobLevel'     => 'Entry-Level',
                'organization' =>  'IT Product Development',
                'parentPosition' => 'Product Supervisor',
                'positionName' =>  ['Product Staf']
            ],
            [
                'jobLevel'     => 'Entry-Level',
                'organization' =>  'IT Infrastructure',
                'parentPosition' => 'Infrastructure Supervisor',
                'positionName' =>  ['Infrastructure Staf']
            ],
            [
                'jobLevel'     => 'Entry-Level',
                'organization' =>  'Marketing',
                'parentPosition' => 'Marketing Supervisor',
                'positionName' =>  ['Marketing Staf']
            ],
            [
                'jobLevel'     => 'Entry-Level',
                'organization' =>  'Sales',
                'parentPosition' => 'Sales Supervisor',
                'positionName' =>  ['Sales Staf']
            ],
            [
                'jobLevel'     => 'Entry-Level',
                'organization' =>  'Human Resource Development (HRD)',
                'parentPosition' => 'HR Supervisor',
                'positionName' =>  ['HR Staf']
            ],
            [
                'jobLevel'     => 'Entry-Level',
                'organization' =>  'General Affair (GA)',   
                'parentPosition' => 'GA Supervisor',
                'positionName' =>  ['GA Staf']
            ]
        ];

        foreach($jobPosition as $data) 
        {
            $organization = BusinessOrganization::where('businessOrganization_name','=',$data['organization'])->first();
            $level = BusinessJobLevel::where('businessJobLevel_name','=',$data['jobLevel'])->first();
            $parentPosition = BusinessJob::where('businessJobPosition_name','=',$data['parentPosition'])->first();

            foreach($data['positionName'] as $position) 
            {
                $businessPosition = new BusinessJob();
                $businessPosition->businessJobPosition_name = $position;
                $businessPosition->businessJobPosition_parent = (!$parentPosition) ? 0 : $parentPosition->id;
                $businessPosition->businessJobPosition_jobLevel = $level->id;
                $businessPosition->businessJobPosition_organization = $organization->id;
                $businessPosition->save();
            }
        }
    }

    private function generateJobLevel($businessId)
    {
        $jobLevel = [
            'C-Level', 'Middle Management', 'First-Level Management', 'Intermediate or Experienced', 'Entry-Level'
        ];

        foreach($jobLevel as $data)
        {
            $businessJobLevel = new BusinessJobLevel();
            $businessJobLevel->businessJobLevel_name = $data;
            $businessJobLevel->businessJobLevel_business = $businessId;
            $businessJobLevel->save();
        }
    }

}
