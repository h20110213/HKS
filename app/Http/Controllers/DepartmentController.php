<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use App\Department;
use App\Objective;
use App\Http\Requests\ObjectiveRequest;
use App\Charts\SampleChart;
use App\User;

class DepartmentController extends Controller
{
    /**
     * 要登入才能用的Controller
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listOKR($departmentId)
    {
        $department = Department::where('id', $departmentId)->first();
        $colors = ['#06d6a0', '#ef476f', '#ffd166', '#6eeb83', '#f7b32b', '#fcf6b1', '#a9e5bb', '#59c3c3', '#d81159'];
        $okrs = [];

        $objectives = $department->objectives()->get();
        foreach ($objectives as $obj) {
            //  單一OKR圖表
            $datas = $obj->getRelatedKrRecord();
            $chart = new SampleChart;
            if (!$datas) {
                $chart->labels([0]);
                $chart->dataset('None', 'line', [0]);
            }
            $chart->title('KR 達成率變化圖', 22, '#216869', true, "'Helvetica Neue','Helvetica','Arial',sans-serif");
            foreach ($datas as $data) {
                $chart->labels($data['update']);
                $chart->dataset($data['kr_id'], 'bar', $data['accomplish']);
            }

            // 打包單張OKR
            $okrs[] = [
                "objective" => $obj,
                "keyresults" => $obj->keyresults()->getResults(),
                "actions" => $obj->actions()->getResults(),
                "chart" => $chart,
            ];
        }

        $data = [
            'user' => auth()->user(),
            'owner' => $department,
            'okrs' => $okrs,
            'colors' => $colors,
        ];

        return view('organization.department.okr', $data);
    }

    public function storeObjective(ObjectiveRequest $request, Department $department)
    {
        $department->addObjective($request);
        return redirect()->route('department.okr', $department->id);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createRoot()
    {
        $company = Company::where('id', auth()->user()->company_id)->first();
        $departments = Department::where('company_id', $company->id)->get();
        $data = [
            'parent' => $company,
            'self' => '',
            'children' => $departments,
        ];

        return view('organization.department.create', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Department $department)
    {
        $data = [
            'parent' => '',
            'self' => $department,
            'children' => $department->children,
        ];

        return view('organization.department.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $attr['name'] = $request->department_name;
        $attr['description'] = $request->department_description;
        $attr['user_id'] = auth()->user()->id;
        $attr['company_id'] = auth()->user()->company_id;
        if (substr($request->department_parent, 0, 4)=="self" || substr($request->department_parent, 0, 10) === "department") {
            $attr['parent_department_id'] = preg_replace('/[^\d]/', '', $request->department_parent);
        }
        $department = Department::create($attr);

        if ($request->hasFile('department_img_upload')) {
            $file = $request->file('department_img_upload');
            $filename = date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/department/' . $department->id, $filename);
            $department->update(['avatar' => '/storage/department/' . $department->id . '/' . $filename]);
        }

        return redirect()->route('organization');
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
     * Show the form for editing the specified resource.
     *
     * @param  Department $department
     * @return \Illuminate\Http\Response
     */
    public function edit(Department $department)
    {
        return view('organization.department.edit', ['department'=>$department]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Department $department
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Department $department)
    {
        $attr['name'] = $request->department_name;
        $attr['description'] = $request->department_description;
        $department->update($attr);

        if($request->hasFile('department_img_upload')){
            $file = $request->file('department_img_upload');
            $filename = date('YmdHis').'.'.$file->getClientOriginalExtension();
            $file->storeAs('public/department/'.$department->id, $filename);
            $department->update(['avatar'=>'/storage/department/'.$department->id.'/'.$filename]);
        }

        return redirect()->route('organization');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Department $department
     * @return \Illuminate\Http\Response
     */
    public function destroy(Department $department)
    {
        $users = User::where(['company_id'=>auth()->user()->company_id,'department_id'=>$department->id])->get();
        foreach ($users as $user) {
            $user->update(['department_id'=>null]);            
        }
        $department->delete();

        return redirect('organization');
    }
}
