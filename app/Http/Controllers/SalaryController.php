<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalaryRequest;
use App\Models\Employee;
use App\Models\Salary;
use Illuminate\Support\Facades\Gate;

class SalaryController extends Controller
{
    /**
     * Pay salary first payment
     * @param App\Requests\SalaryRequest 
     * @return \Illuminate\Http\Response
     */
    public function store(SalaryRequest $request)
    {
        Gate::authorize('salary_access');
        Gate::authorize('salary_create');

        $data = $request->all();

        $recordExists = Salary::whereMonth('month', date('m', strtotime($request->month)))
            ->whereYear('month', date('Y', strtotime($request->month)))
            ->whereEmployeeId($request->employee_id)
            ->exists();

        if ($recordExists) {
            return response()->json([
                'duplicate_error' => "The First Payment of this month has already been done"
            ], 400);
        };

        $salary = Salary::create($data);

        return response()->json($salary);
    }

    /**
     * Show the view for employee salary records
     * @param App\Employee
     * @return \Illuminate\Http\Response
     */
    public function salaryRecords(Employee $employee)
    {
        Gate::authorize('salary_access');

        $salaries = Salary::with('payments')
            ->whereEmployeeId($employee->id)
            ->get();

        return response()->json($salaries);
    }

    public function getTotals(Employee $employee)
    {
        // Totals
        $employeeSalaries = Salary::whereEmployeeId($employee->id);

        $total_additional = (int)$employeeSalaries->sum('additional_amount');
        $total_deducted = (int)$employeeSalaries->sum('deducted_amount');
        $total_paid = (int)$employeeSalaries->sum('total_paid');
        $total_balance = 0;

        foreach ($employeeSalaries->get() as $employeeSalary) {
            $total_balance += $employeeSalary->balance;
        }

        return response()->json(compact(
            'total_additional',
            'total_deducted',
            'total_paid',
            'total_balance'
        ));
    }

    /**
     * Show the form for edit salary record
     * @param App\Salary
     * @return \Illuminate\Http\Response
     */
    public function show(Salary $salary)
    {
        Gate::authorize('salary_access');
        Gate::authorize('salary_show');

        return response()->json($salary);
    }

    /**
     * Update the salary record
     * @param \App\Requests\SalaryRequest
     * @param App\Salary
     * @return \Illuminate\Http\Response
     */
    public function update(SalaryRequest $request, Salary $salary)
    {
        Gate::authorize('salary_access');
        Gate::authorize('salary_edit');

        $salary->update($request->all());

        return response()->json(Salary::with('payments')->find($salary->id));
    }

    /**
     * Delete the salary record
     * @param \App\Requests\SalaryRequest
     * @param App\Salary
     * @return \Illuminate\Http\Response
     */
    public function destroy(Salary $salary)
    {
        Gate::authorize('salary_access');
        Gate::authorize('salary_delete');

        if ($salary->delete()) {
            return response()->json(['message' => "Salary payment deleted successfully"]);
        }
    }
}
