<?php

namespace App\Http\Controllers\AdminConsole;

use App\Http\Controllers\Controller;
use App\Models\DocumentChecklist;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
     * @return Response
     */
    public function index(Request $request)
    {
        $name = trim((string) $request->input('name', ''));
        $docType = (string) $request->input('doc_type', '');

        $query = DocumentChecklist::query()
            ->where('status', 1)
            ->when($name !== '', function ($query) use ($name) {
                $query->where('name', 'like', '%'.$name.'%');
            })
            ->when(in_array($docType, ['1', '2', '3'], true), function ($query) use ($docType) {
                $query->where('doc_type', $docType);
            });

        $totalData = $query->count();
        $lists = $query->sortable(['id' => 'desc'])->paginate(100);

        return view('AdminConsole.features.documentchecklist.index', compact(['lists', 'totalData']));
    }

    public function create(Request $request)
    {
        return view('AdminConsole.features.documentchecklist.create');
    }

    public function store(Request $request)
    {
        if ($request->isMethod('post')) {
            // Validation rules with unique check for nick_name and optional fields
            $this->validate($request, [
                'name' => ['required', 'max:255',
                    Rule::unique('portal_document_checklists')->where(function ($query) {
                        return $query->where('doc_type', request('doc_type'));
                    }),
                ],
                'doc_type' => 'required',
            ]);

            $requestData = $request->all();
            $obj = new DocumentChecklist;
            $obj->name = $requestData['name'];
            $obj->doc_type = $requestData['doc_type'];
            $obj->status = 1; // Set default status to 1 (active)
            $saved = $obj->save();
            if (! $saved) {
                return redirect()->back()->with('error', config('constants.server_error'));
            } else {
                return redirect()->route('adminconsole.features.documentchecklist.index')->with('success', 'Checklist Added Successfully');
            }
        }

        return view('AdminConsole.features.documentchecklist.create');
    }

    /**
     * Show the form for editing the specified document checklist.
     */
    public function edit($id)
    {
        if (isset($id) && ! empty($id)) {
            $id = $this->decodeString($id);
            if (DocumentChecklist::where('id', '=', $id)->exists()) {
                $fetchedData = DocumentChecklist::find($id);

                return view('AdminConsole.features.documentchecklist.edit', compact(['fetchedData']));
            } else {
                return redirect()->route('adminconsole.features.documentchecklist.index')->with('error', 'Checklist Not Exist');
            }
        } else {
            return redirect()->route('adminconsole.features.documentchecklist.index')->with('error', config('constants.unauthorized'));
        }
    }

    /**
     * Update the specified document checklist in storage.
     */
    public function update(Request $request, $id)
    {
        $requestData = $request->all();
        $this->validate($request, [
            'doc_type' => 'required',
            'name' => [
                'required',
                'max:255',
                Rule::unique('portal_document_checklists')->where(function ($query) use ($request) {
                    return $query->where('doc_type', $request->doc_type);
                })->ignore($id),
            ],
        ]);

        $obj = DocumentChecklist::find($id);
        if (! $obj) {
            return redirect()->route('adminconsole.features.documentchecklist.index')->with('error', 'Checklist Not Found');
        }

        $obj->name = $requestData['name'];
        $obj->doc_type = $requestData['doc_type'];
        $saved = $obj->save();
        if (! $saved) {
            return redirect()->back()->with('error', config('constants.server_error'));
        } else {
            return redirect()->route('adminconsole.features.documentchecklist.index')->with('success', 'Checklist Updated Successfully');
        }
    }
}
