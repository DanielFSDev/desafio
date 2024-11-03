<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background-color: #f7f7f7;
        }
        .container {
            width: 100%;
            max-width: 600px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
        }
        button {
            padding: 10px 20px;
            margin: 0 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        input[type="text"] {
            width: 80%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .logout-button {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .welcome-message {
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 16px;
            color: #333;
        }
        .button {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            margin-left: 5px;
            text-decoration: none;
        }
        .button:hover {
            background-color: #45a049;
        }
        .documents-list {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            overflow-y: auto;
            max-height: 500px;
        }
        .documents-list h3 {
            text-align: center;
        }
        .document-item {
            padding: 15px;
            margin: 10px 0;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .button-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .button-container .button {
            padding: 8px 12px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            text-align: center;
            transition: background-color 0.2s;
        }
        .button-container .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    @include('messages')
    <div class="welcome-message">
        Bem-vindo, {{ Auth::user()->name }}
    </div>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="logout-button">
        @csrf
        <button type="submit">Desconectar</button>
    </form>
    <div class="container">
        <h2>Documentos</h2>
        <form action="{{ route('document.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="file" id="file" required>
            <button type="submit">Adicionar Documento</button>
        </form>
        <div class="documents-list">
            <h3>Documentos adicionados</h3>
            @foreach ($documents as $document)
                <div class="document-item">
                    {{ $document->file_name }} <br> Data: {{ $document->created_at->format('d/m/Y') }} <br> Hora: {{ $document->created_at->format('H:i:s') }}
                    <div class="button-container">
                        <a href="{{ route('documents.variables.view', ['document_id' => $document->id]) }}" class="button">Editar</a>
                        <a href="{{ route('documents.download.pdf', ['document_id' => $document->id, 'is_to_show' => true]) }}" class="button">Visualizar PDF</a>
                        <a href="{{ route('documents.download.pdf', ['document_id' => $document->id]) }}" class="button">Baixar .pdf</a>
                        <a href="{{ asset('storage/' . $document->file_path) }}" class="button">Baixar .docx</a>
                        <form action="{{ route('documents.delete', ['document_id' => $document->id]) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja deletar este documento?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="button">Excluir</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>
