<?php

namespace App\Http\Controllers\Admin;

use App\Models\Content;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class ContentController extends Controller
{
    public function __construct()
    {
        $this->lang = getAllLanguage();
    }

    public function index()
    {
        if (request()->ajax()) {
            if (Auth::user()->type == 'entity'){
                $users = Content::where('user_id', Auth::id())->where("lang", get_constants('default_lang'))->get();
            }else{
                $users = Content::where('user_id', Auth::id())->whereIn('slug', ['privacy-policy', 'term-condition'])->where("lang", get_constants('default_lang'))->get();
            }

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('content', function ($row) {
                    // Remove HTML tags and limit the content to 100 characters
                    return Str::limit(strip_tags($row->content), 100);
                })
                ->addColumn('action', function ($row) {
                    $params = [
                        'id' => $row->id,
                        'url' => [
                            'edit' => route('admin.contents.edit', $row->unique_id),
                            'view' => route('admin.contents.show', $row->unique_id),
                        ]
                    ];

                    // Generate action buttons using the helper function
                    return generate_actions_buttons($params);
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.contents.index', [
            'title' => "Contents",
            'breadcrumbs' => [
                'Contents' => route('admin.contents.index'),
            ]
        ]);
    }

    public function show($id)
    {
        $contents = Content::where("unique_id", $id)->get();
        return view('admin.contents.show', [
            'title' => "Content",
            'contents' => $contents,
            'breadcrumbs' => [
                'Content' => route('admin.contents.index'),
                'View' => route('admin.contents.show', $id),
            ]
        ]);
    }

    public function edit($id)
    {
        $contents = Content::where("unique_id", $id)->get();
        return view('admin.contents.edit', [
            'title' => "Content",
            'contents' => $contents,
            "unique_id" => $id,
            'breadcrumbs' => [
                'Content' => route('admin.contents.index'),
                'edit' => route('admin.contents.edit', $id),
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $contents = Content::where("unique_id", $id)->get();
        $user = $request->user();
        if ($contents) {
            if ($user->type == 'admin') {
                $request->validate([
                    'title_en' => 'required|string|max:255',
                    'title_es' => 'required|string|max:255',
                    'content_en' => 'required',
                    'content_es' => 'required',
                ]);
            } else {
                $request->validate([
                    'content_en' => 'required',
                    'content_es' => 'required',
                ]);
            }
            if (isset($contents) && $contents->count() > 0) {
                foreach ($contents as $content) {
                    if ($content->slug == 'waiver-policy' && $user->type == 'entity') {
                        $title = 'Waiver Policy';
                    } else {
                        $title = $request['title_' . $content->lang];
                    }
                    $save_data['title'] = $title;
                    $save_data['content'] = $request['content_' . $content->lang];
                    $content->fill($save_data);
                    $content->save();
                }
            }
            flash_session('success', 'Content updated successfully.');
        } else {
            flash_session('error', 'content not found');
        }

        return redirect()->route('admin.contents.index');
    }

    public function create()
    {
        return view('admin.contents.create', [
            'title' => "Add Content",
            'breadcrumbs' => [
                'Content' => route('admin.contents.index'),
                'Create' => route('admin.contents.create'),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $rules = [];
        $messages = [];

        foreach ($this->lang as $key => $value) {
//            $rules['title_' . $value->code] = 'required|string|max:255';
            $rules['content_' . $value->code] = 'required|string';

//            $messages['title_' . $value->code . '.required'] = __('Title is required for language: ') . strtoupper($value->code);
//            $messages['title_' . $value->code . '.max'] = __('Title must not exceed 255 characters for language: ') . strtoupper($value->code);
            $messages['content_' . $value->code . '.required'] = __('Content is required for language: ') . strtoupper($value->code);
        }

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $user = $request->user();

        $unique_id = genUniqueStr('contents', 'unique_id', 10, 'UN', false);
        if (!empty($this->lang)) {
            foreach ($this->lang as $key => $value) {
                $content = new Content();
                $content->user_id = $user->id;
                $content->unique_id = $unique_id;
                $content->lang = $value->code;
                $content->title = 'Waiver Policy';
                $content->content = $request['content_' . $value->code];
                $content->slug = 'waiver-policy';
                $content->save();
            }
        }
        flash_session('success', 'Content created successfully.');
        return redirect()->route('admin.contents.index');
    }
}
