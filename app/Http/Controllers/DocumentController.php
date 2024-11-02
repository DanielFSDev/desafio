<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Document;

class DocumentController extends Controller
{
    public function documentsView(): View
    {
        $documents = Document::where('user_id', Auth::id())->get();
        return view('documents.documents', ['documents' => $documents]);
    }

    public function upload(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:doc,docx|max:2048',
        ]);
        $file = $request->file('file');
        $path = $file->store('documents');
        $fileName = $file->getClientOriginalName();
        Document::create([
            'user_id' => Auth::id(),
            'file_name' => $fileName,
            'file_path' => $path
        ]);
        return redirect()->route('documents')->with('success', 'Arquivo enviado com sucesso!');
    }
}
