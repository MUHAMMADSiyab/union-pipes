<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalaryRequest;
use App\Models\Employee;
use App\Models\Payment;
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

        if (!$request->boolean('loan')) {
            $total_paid = ($request->total_paid  + $request->deducted_amount) - $request->additional_amount;
        } else {
            $total_paid = ($request->total_paid  + $request->deducted_amount);
        }
        $data['balance'] = (Employee::find($request->employee_id)->salary - $total_paid);

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

        $salaries = Salary::query()
            ->with('payments')
            ->whereEmployeeId($employee->id)
            ->when(request()->boolean('loan'), function ($q) {
                $q->where('loan', true);
            })
            ->get();

        return response()->json($salaries);
    }

    public function getTotals(Employee $employee)
    {
        // Totals
        $employeeSalaries = Salary::query()
            ->when(request()->boolean('loan'), function ($q) {
                $q->where('loan', true);
            })
            ->whereEmployeeId($employee->id);

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
    public function show(int $salary)
    {
        Gate::authorize('salary_access');
        Gate::authorize('salary_show');

        $salary = Salary::with(['payments' => function ($q) {
            $q->where('first_payment', true)->with('bank');
        }])->find($salary);

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

        $data = $request->all();

        $afterPaymentsAmount = Payment::query()
            ->where('model', Salary::class)
            ->where('paymentable_id', $salary->id)
            ->where('first_payment', false)
            ->sum('amount');

        if (!$request->boolean('loan')) {
            $total_paid = ($request->total_paid + $afterPaymentsAmount  + $request->deducted_amount) - $request->additional_amount;
        } else {
            $total_paid = ($request->total_paid + $afterPaymentsAmount  + $request->deducted_amount);
        }

        $data['total_paid'] = $total_paid;
        $data['balance'] = (Employee::find($salary->employee_id)->salary - $total_paid);

        $old_salary = $salary->getOriginal();

        $salary->update($data);

        return response()->json([
            'updated_salary' =>  Salary::with('payments')->find($salary->id),
            'old_salary' =>  $old_salary
        ]);
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
            Payment::where('model', Salary::class)
                ->where('paymentable_id', $salary->id)
                ->delete();

            return response()->json(['message' => "Salary payment deleted successfully"]);
        }
    }
}
