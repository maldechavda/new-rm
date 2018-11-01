<?php

namespace App\Http\Controllers;

use App\Http\Requests\DesignationRequest;
use Illuminate\Http\Request;
use App\Designation;
use Yajra\DataTables\DataTables;

class DesignationController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Designation::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function index()
    {
        if(request()->ajax()) {
            return Datatables::of(Designation::query())
                ->addColumn('action', function (Designation $designation) {
                    return view('shared.dtAction', [
                        'showUrl' => route('designations.show', $designation),
                        'deleteUrl' => route('designations.destroy', $designation),
                        'editUrl' => route('designations.edit', $designation)
                    ]);
                })
                ->make(true);
        }
        return view('designation.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('designation.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DesignationRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        Designation::create($data);

        if(request()->wantsJson()) {
            return response([], 200);
        }

        session()->flash('alert-success', 'Designation has been created.');

        return redirect('/designations');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return void
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Designation $designation
     * @return \Illuminate\Http\Response
     */
    public function edit(Designation $designation)
    {
        return view('designation.update', compact('designation'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param DesignationRequest $request
     * @param Designation $designation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Designation $designation)
    {
        $data = $request->all();

        $designation->fill($data)->save();

        if(request()->wantsJson()) {
            return response([], 200);
        }

        session()->flash('alert-success', 'Designation has been updated.');

        return redirect('/designations');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Designation $designation
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Designation $designation)
    {
        $designation->delete();

        session()->flash('alert-danger', 'Designation has been deleted.');

        return back();
    }
}
