<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EmployeeController extends Controller
{
    /**
     * Get all employees
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('employee_access');

        $employees = Employee::all();
        return response()->json($employees);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\EmployeeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EmployeeRequest $request)
    {
        Gate::authorize('employee_access');
        Gate::authorize('employee_create');

        $employee = Employee::create($request->all());

        if ($request->hasFile('photo')) {
            $employee->addMediaFromRequest('photo')->toMediaCollection('employees');
        }

        return response()->json($employee, 201);
    }

    /**
     * Get a single employee
     *
     * @param  App\Models\Employee $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
        Gate::authorize('employee_access');
        Gate::authorize('employee_show');

        return response()->json($employee);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\EmployeeRequest  $request
     * @param  App\Models\Employee $employee
     * @return \Illuminate\Http\Response
     */
    public function update(EmployeeRequest $request, Employee $employee)
    {
        Gate::authorize('employee_access');
        Gate::authorize('employee_edit');

        $employee->update($request->all());

        if ($request->hasFile('photo')) {
            $existingMedia = $employee->getFirstMedia('employees');
            if ($existingMedia) {
                $existingMedia->delete();
            }
            $employee->addMediaFromRequest('photo')->toMediaCollection('employees');
        }

        return response()->json(Employee::find($employee->id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\Models\Employee $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        Gate::authorize('employee_access');
        Gate::authorize('employee_delete');

        if ($employee->delete()) {
            return response()->json(['success' => 'Employee deleted successfully']);
        }
    }

    /**
     * Delete multitple employees 
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy_multiple(Request $request)
    {
        Gate::authorize('employee_access');
        Gate::authorize('employee_delete');

        foreach ($request->ids as $id) {
            Employee::find($id)->delete();
        }

        return response()->json(['success' => 'Employees deleted successfully']);
    }
}
