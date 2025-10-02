<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\DocumentChecklist;
use Config;
use Illuminate\Validation\Rule;

class DocumentChecklistController extends Controller
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
     * Display a listing of the matters.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = DocumentChecklist::where('id', '!=', '')->where('status',1);
        $totalData = $query->count(); // for all data
        $lists = $query->sortable(['id' => 'desc'])->paginate(100);
        return view('Admin.feature.documentchecklist.index', compact(['lists', 'totalData']));
    }

    public function create(Request $request)
    {
        return view('Admin.feature.documentchecklist.create');
    }

    public function store(Request $request)
    {
        if ($request->isMethod('post'))
        {
            // Validation rules with unique check for nick_name and optional fields
            $this->validate($request, [
                'name' => ['required','max:255',
                    Rule::unique('document_checklists')->where(function ($query) {
                        return $query->where('doc_type', request('doc_type'));
                    })
                ],
                'doc_type' => 'required'
            ]);

            $requestData = $request->all();
            $obj = new DocumentChecklist;
            $obj->name = $requestData['name'];
            $obj->doc_type = $requestData['doc_type'];
            $saved = $obj->save();
            if (!$saved) {
                return redirect()->back()->with('error', Config::get('constants.server_error'));
            } else {
                return Redirect::to('/admin/documentchecklist')->with('success', 'Checklist Added Successfully');
            }
        }
        return view('Admin.feature.documentchecklist.create');
    }

    public function edit(Request $request, $id = NULL)
    {
        if ($request->isMethod('post'))
        {
            $requestData = $request->all();
            $checklistId = $requestData['id'];
            $doc_type = $requestData['doc_type'];
            $this->validate($request, [
                'doc_type' => 'required',
                'name' => [
                    'required',
                    'max:255',
                    Rule::unique('document_checklists')->where(function ($query) use ($request) {
                        return $query->where('doc_type', $request->doc_type); // Correct access to doc_type from the request
                    })->ignore($checklistId) // Ignore the current record being edited
                ]
            ]);

            $obj = DocumentChecklist::find($requestData['id']);
            $obj->name = $requestData['name'];
            $obj->doc_type = $requestData['doc_type'];
            $saved = $obj->save();
            if (!$saved) {
                return redirect()->back()->with('error', Config::get('constants.server_error'));
            } else {
                return Redirect::to('/admin/documentchecklist')->with('success', 'Checklist Edited Successfully');
            }
        }
        else
        {
            if (isset($id) && !empty($id)) {
                $id = $this->decodeString($id);
                if (DocumentChecklist::where('id', '=', $id)->exists()) {
                    $fetchedData = DocumentChecklist::find($id);
                    return view('Admin.feature.documentchecklist.edit', compact(['fetchedData']));
                } else {
                    return Redirect::to('/admin/documentchecklist')->with('error', 'Checklist Not Exist');
                }
            } else {
                return Redirect::to('/admin/documentchecklist')->with('error', Config::get('constants.unauthorized'));
            }
        }
    }
}
