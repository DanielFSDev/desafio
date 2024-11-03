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
        .button-container {
            display: flex;
            margin-left: 10px;
        }
        .documents-list {
            margin-bottom: 20px;
        }
        .document-item {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
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
            <h3>Seus Documentos</h3>
            @foreach ($documents as $document)
                <div class="document-item">
                    <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank">{{ $document->file_name }}</a>
                    - Enviado em: {{ $document->created_at->format('d/m/Y H:i:s') }}
                    <div class="button-container">
                        <a href="{{ route('documents.variables.view', ['document_id' => $document->id]) }}" class="button">Editar</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>
