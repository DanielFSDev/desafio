<?php

namespace App\Http\Controllers;

use App\Models\DocumentVariable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
        $document = Document::with('variables')
            ->where('id', $documentId)
            ->where('user_id', Auth::id())
            ->firstOrFail();
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
        $document = Document::with('variables')->findOrFail($documentId);
        $docxPath = storage_path('app/' . $document->file_path);
        $command = sprintf(
            "HOME=/tmp libreoffice --headless --convert-to html --outdir %s %s",
            escapeshellarg(dirname($docxPath)),
            escapeshellarg($docxPath)
        );
        exec($command);
        $htmlOutputPath = dirname($docxPath) . '/' . pathinfo($docxPath, PATHINFO_FILENAME) . '.html';
        $html = file_get_contents($htmlOutputPath);
        $variables = json_decode($document->variables->first()?->variables ?? '[]', true);
        foreach ($variables as $variable => $value) {
            $html = str_replace('${' . $variable . '}', $value, $html);
        }
        file_put_contents($htmlOutputPath, $html);
        $command = sprintf(
            "HOME=/tmp libreoffice --headless --convert-to pdf --outdir %s %s",
            escapeshellarg(dirname($htmlOutputPath)),
            escapeshellarg($htmlOutputPath)
        );
        exec($command);
        $imagePattern = dirname($docxPath) . '/' . pathinfo($docxPath, PATHINFO_FILENAME) . '_html_*.png';
        $matchingFiles = glob($imagePattern);
        $imagePath = $matchingFiles[0];
        $pdfOutputPath = dirname($docxPath) . '/' . pathinfo($docxPath, PATHINFO_FILENAME) . '.pdf';
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
        if (file_exists($htmlOutputPath)) {
            unlink($htmlOutputPath);
        }
        return $isToShow
            ? response()->file($pdfOutputPath)->deleteFileAfterSend()
            : response()->download($pdfOutputPath)->deleteFileAfterSend();
    }

    public function delete(Request $request): RedirectResponse
    {
        $request->validate(['document_id' => 'required|exists:documents,id']);
        $document = Document::findOrFail(request('document_id'));
        $documentName = $document->file_name;
        $document->delete();
        $filePath = $document->file_path;
        if (Storage::disk()->exists($filePath)) {
            Storage::disk()->delete($filePath);
        }
        return redirect()->route('documents')->with('success', "$documentName removido com sucesso!");
    }

    public function downloadDocx(Request $request): BinaryFileResponse
    {
        $documentId = $request->get('document_id');
        $document = Document::with('variables')->findOrFail($documentId);
        $docxPath = storage_path('app/' . $document->file_path);
        $docxTempPath = $this->createFileCopy($docxPath);
        $htmlPath = $this->convertTo($docxTempPath, 'html');
        $this->replaceVariables($document, $htmlPath);
        $docxOutputPath = $this->convertTo($htmlPath, 'docx');
        $imagePath = $this->extractImagePath($htmlPath);
        $this->removeIfExists($imagePath);
        $this->removeIfExists($htmlPath);
        return response()->download($docxOutputPath)->deleteFileAfterSend();
    }

    private function convertTo(string $filePath, string $type): string
    {
        $command = sprintf(
            "HOME=/tmp libreoffice --headless --convert-to %s --outdir %s %s",
            $type,
            escapeshellarg(dirname($filePath)),
            escapeshellarg($filePath)
        );
        if ($type === 'docx') {
            dd($command);
        }
        exec($command);
        return dirname($filePath) . '/' . pathinfo($filePath, PATHINFO_FILENAME) . '.' . $type;
    }

    private function replaceVariables(Document $document, string $htmlPath): void
    {
        $variables = json_decode($document->variables->first()?->variables ?? '[]', true);
        $html = file_get_contents($htmlPath);
        foreach ($variables as $variable => $value) {
            $html = str_replace('${' . $variable . '}', $value, $html);
        }
        file_put_contents($htmlPath, $html);
    }

    private function removeIfExists(string $path): void
    {
        if (file_exists($path)) {
            unlink($path);
        }
    }

    private function extractImagePath(string $htmlPath): string
    {
        $imagePattern = dirname($htmlPath) . '/' . pathinfo($htmlPath, PATHINFO_FILENAME) . '_html_*.png';
        $matchingFiles = glob($imagePattern);
        return $matchingFiles[0] ?? '';
    }

    private function createFileCopy(string $filePath): string
    {
        $newFilePath = pathinfo($filePath,PATHINFO_DIRNAME)
            . '/' . pathinfo($filePath, PATHINFO_FILENAME)
            . '_' . uniqid()
            . '.' . pathinfo($filePath, PATHINFO_EXTENSION);
        file_put_contents($newFilePath, file_get_contents($filePath));
        return $newFilePath;
    }
}
