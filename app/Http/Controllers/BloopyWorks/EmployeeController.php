<?php

namespace App\Http\Controllers\BloopyWorks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Business\BusinessUser;
use App\Models\Business\BusinessBranch;
use App\Models\BloopyWorks\Employee;
use App\Models\BloopyWorks\EmployeeSalary;
use App\Models\BloopyWorks\EmployeeSchedule;
use App\Models\BloopyWorks\EmployeeTax;
use App\Models\BloopyWorks\EmployeeBPJS;
use App\Models\User;

class EmployeeController extends Controller
{
    public function list(Request $request) 
    {
        if(!$request->query('business'))
        {
            return response()->json([
                'status' => 'error',
                'message' => 'Business Not Found'
            ], 404);
        }

        $employee = DB::table('user_business')
                      ->join('users','user_business.userBusiness_user','=','users.id') //Join table users
                      ->join('business','user_business.userBusiness_business','=','business.id') // Join table business
                      ->join('business_branch', 'business_branch.businessBranch_business','=','business.id') // Join table business branch
                      ->join('employee', 'employee.employee_userBusiness','=','user_business.id') // Join table employee
                      ->join('business_jobPosition', 'employee.employee_jobPosition','=','business_jobPosition.id') // Join table business organization
                      ->where('business_branch.id','=',$request->query('business'))
                      ->select('employee.id as employee_id', 'users.user_name as employee_name', 'business_jobPosition.businessJobPosition_name as job_position')
                      ->get();

        return response()->json([
            'status' => 'success',
            'data' => $employee
        ]);
    }

    public function create(Request $request) 
    {
        if (!$request->query('business'))
        {
            return response()->json([
                'status' => 'error',
                'message' => 'Business Not Found',
            ], 404);   
        }

        // Validate Business
        $business = BusinessBranch::find( $request->query('business'));
        
        if (!$business)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'Business Not Found',
            ], 404);    
        }

        $validator = Validator::make($request->all(),[
            'employee_id' => 'required|string|max:255|unique:employee',
            'employee_barcode' => 'required|string|max:255|unique:employee',
            "employee_name" => 'required|string',
            "employee_email" => 'required|string|email',
            "employee_status" => 'required|string|in:permanent,contract,probation',
            "employee_joinDate"  => 'required|string',
            "employee_endDate"  => 'string',
            "employee_maritalStatus" => 'required|string|in:singgle,married,widow,widower',
            "employee_religion" => 'required|string|in:islam,christian,buddha,hindu,confucius,other',
            "employee_birthPlace" => 'string',
            "employee_birthDate" => 'required|string',
            "employee_gender" => 'required|string|in:male,female,other',
            "employee_organization" => 'required',
            "employee_jobLevel" => 'required',
            "employee_jobPosition" => 'required',
            "employee_schedule" => 'required',
            "employee_basicSalary" => 'required',
            "employee_salaryType" => 'required|string|in:monthly,weekly',
            "employee_prorateSetting" => 'required|string|in:based-on-working-day,based-on-calendar-day,custom-on-working-day,custom-on-calendar-day',
            "employee_overtime" => 'required|string|in:yes,no',
            "employee_npwp" => 'string',
            "employee_taxStatus" => "integer",
            "employee_BPJSKetenagakerjaanNumber" => 'string',
            "employee_nppBPJSKetenagakerjaanNumber" => 'string',
            "employee_BPJSKetenagakerjaanrDate" => 'string'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => 'the given data invalid',
                'errors' => $validator->errors()
            ], 422);       
        }
        
        // Create Users
        $user = new User();
        $user->user_name = $request->employee_name;
        $user->email = $request->employee_email;
        $user->password = Hash::make(implode(explode('-',$request->employee_birthDate)));
        $user->user_gender = $request->employee_gender;
        $user->user_birthPlace = $request->employee_birthPlace;
        $user->user_birthDate = $request->employee_birthDate;
        $user->save();

        // Create Users
        $userBusiness = new BusinessUser();
        $userBusiness->userBusiness_business = $business->businessBranch_business;
        $userBusiness->userBusiness_user = $user->id;
        $userBusiness->userBusiness_status = 'employee';
        $userBusiness->save();

        // Create Employee Data 
        $employee = new Employee();
        $employee->employee_id = $request->employee_id;
        $employee->employee_barcode = $request->employee_barcode;
        $employee->employee_userBusiness = $userBusiness->id;
        $employee->employee_business = $business->id;
        $employee->employee_status = $request->employee_status;
        $employee->employee_joinDate = $request->employee_joinDate;
        $employee->employee_endDate = $request->employee_endDate;
        $employee->employee_maritalStatus = $request->employee_maritalStatus;
        $employee->employee_religion = $request->employee_religion;
        $employee->employee_organization = $request->employee_organization;
        $employee->employee_jobLevel = $request->employee_jobLevel;
        $employee->employee_jobPosition = $request->employee_jobPosition;
        $employee->employee_schedule = $request->employee_schedule;
        $employee->employee_paymentSchedule = $request->employee_paymentSchedule;
        $employee->save();
        
        // Create Employee Salary
        $employeeSalary = new EmployeeSalary();
        $employeeSalary->employeeSalary_basicSalary = $request->employee_basicSalary;
        $employeeSalary->employeeSalary_type = $request->employee_salaryType;
        $employeeSalary->employeeSalary_prorateSetting = $request->employee_prorateSetting;
        $employeeSalary->employeeSalary_overtime = $request->employee_overtime;
        $employeeSalary->employeeSalary_employee = $employee->id;
        $employeeSalary->save();

        // Create Employee Tax
        $employeeTax = new EmployeeTax();
        $employeeTax->employeeTax_npwp = $request->employee_npwp;
        $employeeTax->employeeTax_status = $request->employee_taxStatus;
        $employeeTax->employeeTax_employee = $employee->id;
        $employeeTax->save();

        // Create Employee BPJS 
        $employeeBPJS = new EmployeeBPJS();
        $employeeBPJS->employeeBPJS_BPJSKetenagakerjaanNumber = $request->employee_BPJSKetenagakerjaanNumber;
        $employeeBPJS->employeeBPJS_NPPBPJSKetenagakerjaan = $request->employee_nppBPJSKetenagakerjaan;
        $employeeBPJS->employeeBPJS_BPJSKetenagakerjaanDate = $request->employee_BPJSKetenagakerjaanDate;
        $employeeBPJS->employeeBPJS_employee = $employee->id;
        $employeeBPJS->save();

        return response()->json([
            'status' => 'success',
            'message' => 'employee created successfully',
        ]);
    }
}
