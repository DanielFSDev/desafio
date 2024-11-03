<?php

namespace App\Http\Controllers;

use App\Models\DocumentVariable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Document;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
        return redirect()->route('documents')->with('success', 'Documento enviado com sucesso!');
    }

    public function variablesView(Request $request): View
    {
        $documentId = $request->get('document_id');
        $document = Document::with('variables')->findOrFail($documentId);
        if (!$document) {
            return view('documents.documents')->with('error', 'Documento não encontrado!');
        }
        $variables = json_decode($document->variables()->first()->variables ?? '[]', true);
        return view('documents.variables', compact('document', 'variables'));
    }

    public function saveVariables(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'document_id' => 'required|exists:documents,id',
            'variables' => 'required|array'
        ]);
        $documentVariable = DocumentVariable::firstOrNew([
            'document_id' => $validated['document_id']
        ]);
        $existingVariables = json_decode($documentVariable->variables ?? '[]', true);
        foreach ($validated['variables'] as $key => $value) {
            $existingVariables[$key] = $value;
        }
        $documentVariable->variables = json_encode($existingVariables);
        $documentVariable->save();
        return redirect()->route('documents')->with('success', 'Documento editado com sucesso!');
    }

    public function downloadPdf(Request $request): BinaryFileResponse
    {
        $documentId = $request->get('document_id');
        $isToShow = filter_var($request->get('is_to_show', false), FILTER_VALIDATE_BOOLEAN);
        $document = Document::findOrFail($documentId);
        $docxPath = storage_path('app/public/' . $document->file_path);
        $command = sprintf(
            "HOME=/tmp libreoffice --headless --convert-to pdf --outdir %s %s",
            dirname($docxPath),
            $docxPath
        );
        exec($command);
        $pdfOutputPath = dirname($docxPath) . '/' . pathinfo($docxPath)['filename'] . '.pdf';
        return $isToShow
            ? response()->file($pdfOutputPath, ['Content-Type' => 'application/pdf'])->deleteFileAfterSend()
            : response()->download($pdfOutputPath)->deleteFileAfterSend();
    }

    public function delete(Request $request): RedirectResponse
    {
        $request->validate(['document_id' => 'required|exists:documents,id']);
        $document = Document::findOrFail(request('document_id'));
        $documentName = $document->file_name;
        $document->delete();
        return redirect()->route('documents')->with('success', "$documentName removido com sucesso!");
    }
}
